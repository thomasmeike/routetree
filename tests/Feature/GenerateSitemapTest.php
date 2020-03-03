<?php

namespace RouteTreeTests\Feature;

use Illuminate\Filesystem\Filesystem;
use RouteTreeTests\Feature\Models\TestModelTranslatable;
use RouteTreeTests\Feature\Traits\UsesTestRoutes;
use RouteTreeTests\TestCase;
use Webflorist\RouteTree\RouteNode;

class GenerateSitemapTest extends TestCase
{
    use UsesTestRoutes;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        (new Filesystem())->delete(
            $this->getSitemapOutputFile()
        );
    }

    protected function tearDown(): void
    {
        /*
        (new Filesystem())->delete(
            $this->getSitemapOutputFile()
        );
        */
        parent::tearDown();
    }

    private function getSitemapOutputFile()
    {
        return base_path(config('routetree.sitemap.output_file'));
    }


    public function test_simple_sitemap()
    {
        $this->routeTree->root(function (RouteNode $node) {
            $node->get('\RouteTreeTests\Feature\Controllers\TestController@get');
        });

        $this->routeTree->generateAllRoutes();

        $this->artisan('routetree:generate-sitemap')->assertExitCode('0');
        $this->assertFileExists($this->getSitemapOutputFile());
        $this->assertXmlStringEqualsXmlFile($this->getSitemapOutputFile(), '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
        xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <url>
        <loc>http://localhost/de</loc>
    </url>
    <url>
        <loc>http://localhost/en</loc>
    </url>


</urlset>');
        //$this->assertXmlFileEqualsXmlFile();
    }


    public function test_single_language_sitemap()
    {
        $this->config->set('routetree.locales', null);
        $this->config->set('app.locale', 'de');

        $this->routeTree->root(function (RouteNode $node) {
            $node->namespace('\RouteTreeTests\Feature\Controllers');
            $node->get('TestController@get');
            $node->child('parent', function (RouteNode $node) {
                $node->get('TestController@get');
                $node->child('child1', function (RouteNode $node) {
                    $node->get('TestController@get');
                });

                $node->child('child2', function (RouteNode $node) {
                    $node->get('TestController@get');
                });
            });
        });

        $this->routeTree->generateAllRoutes();

        $this->artisan('routetree:generate-sitemap')->assertExitCode('0');
        $this->assertFileExists($this->getSitemapOutputFile());
        $this->assertXmlStringEqualsXmlFile($this->getSitemapOutputFile(), '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
        xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <url>
        <loc>http://localhost/</loc>
    </url>
    <url>
        <loc>http://localhost/parent</loc>
    </url>
    <url>
        <loc>http://localhost/parent/child1</loc>
    </url>
    <url>
        <loc>http://localhost/parent/child2</loc>
    </url>


</urlset>');
        //$this->assertXmlFileEqualsXmlFile();
    }

    public function test_complex_sitemap()
    {
        $this->generateComplexTestRoutes($this->routeTree);

        $this->artisan('routetree:generate-sitemap')
            //->expectsOutput('test')
            ->assertExitCode('0');
        $this->assertFileExists($this->getSitemapOutputFile());
        $this->assertXmlStringEqualsXmlString('<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
        xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
        <url>
            <loc>http://localhost/de</loc>
            <lastmod>2019-11-16T17:46:30+01:00</lastmod>
            <changefreq>monthly</changefreq>
            <priority>1.0</priority>
        </url>
        <url>
            <loc>http://localhost/de/blog-using-parameters/blumen</loc>
        </url>
        <url>
            <loc>http://localhost/de/blog-using-parameters/baeume</loc>
        </url>
        <url>
            <loc>http://localhost/de/blog-using-parameters/blumen/die-rose</loc>
        </url>
        <url>
            <loc>http://localhost/de/blog-using-parameters/blumen/die-tulpe</loc>
        </url>
        <url>
            <loc>http://localhost/de/blog-using-parameters/blumen/die-lilie</loc>
        </url>
        <url>
            <loc>http://localhost/de/blog-using-parameters/baeume/die-laerche</loc>
        </url>
        <url>
            <loc>http://localhost/de/blog-using-parameters/baeume/die-laerche</loc>
        </url>
        <url>
            <loc>http://localhost/de/blog-using-parameters/baeume/die-kastanie</loc>
        </url>
        <url>
            <loc>http://localhost/de/blog-using-resources</loc>
        </url>
        <url>
            <loc>http://localhost/de/blog-using-resources/blumen</loc>
        </url>
        <url>
            <loc>http://localhost/de/blog-using-resources/baeume</loc>
        </url>
        <url>
            <loc>http://localhost/de/blog-using-resources/blumen/articles</loc>
        </url>
        <url>
            <loc>http://localhost/de/blog-using-resources/baeume/articles</loc>
        </url>
        <url>
            <loc>http://localhost/de/blog-using-resources/blumen/articles/die-rose</loc>
        </url>
        <url>
            <loc>http://localhost/de/blog-using-resources/blumen/articles/die-tulpe</loc>
        </url>
        <url>
            <loc>http://localhost/de/blog-using-resources/blumen/articles/die-lilie</loc>
        </url>
        <url>
            <loc>http://localhost/de/blog-using-resources/baeume/articles/die-laerche</loc>
        </url>
        <url>
            <loc>http://localhost/de/blog-using-resources/baeume/articles/die-laerche</loc>
        </url>
        <url>
            <loc>http://localhost/de/blog-using-resources/baeume/articles/die-kastanie</loc>
        </url>
        <url>
            <loc>http://localhost/de/excluded/non-excluded-child</loc>
        </url>
        <url>
            <loc>http://localhost/de/parameter-with-translated-values/parameter-array-wert1</loc>
        </url>
        <url>
            <loc>http://localhost/de/parameter-with-translated-values/parameter-array-wert2</loc>
        </url>
        <url>
            <loc>http://localhost/de/parameter-with-values/parameter-array-value1</loc>
        </url>
        <url>
            <loc>http://localhost/de/parameter-with-values/parameter-array-value2</loc>
        </url>
        <url>
            <loc>http://localhost/de/resource</loc>
        </url>
        <url>
            <loc>http://localhost/de/resource/erstellen</loc>
        </url>
        <url>
            <loc>http://localhost/en</loc>
            <lastmod>2019-11-16T17:46:30+01:00</lastmod>
            <changefreq>monthly</changefreq>
            <priority>1.0</priority>
        </url>
        <url>
            <loc>http://localhost/en/blog-using-parameters/flowers</loc>
        </url>
        <url>
            <loc>http://localhost/en/blog-using-parameters/trees</loc>
        </url>
        <url>
            <loc>http://localhost/en/blog-using-parameters/flowers/the-rose</loc>
        </url>
        <url>
            <loc>http://localhost/en/blog-using-parameters/flowers/the-tulip</loc>
        </url>
        <url>
            <loc>http://localhost/en/blog-using-parameters/flowers/the-lily</loc>
        </url>
        <url>
            <loc>http://localhost/en/blog-using-parameters/trees/the-larch</loc>
        </url>
        <url>
            <loc>http://localhost/en/blog-using-parameters/trees/the-larch</loc>
        </url>
        <url>
            <loc>http://localhost/en/blog-using-parameters/trees/the-chestnut</loc>
        </url>
        <url>
            <loc>http://localhost/en/blog-using-resources</loc>
        </url>
        <url>
            <loc>http://localhost/en/blog-using-resources/flowers</loc>
        </url>
        <url>
            <loc>http://localhost/en/blog-using-resources/trees</loc>
        </url>
        <url>
            <loc>http://localhost/en/blog-using-resources/flowers/articles</loc>
        </url>
        <url>
            <loc>http://localhost/en/blog-using-resources/trees/articles</loc>
        </url>
        <url>
            <loc>http://localhost/en/blog-using-resources/flowers/articles/the-rose</loc>
        </url>
        <url>
            <loc>http://localhost/en/blog-using-resources/flowers/articles/the-tulip</loc>
        </url>
        <url>
            <loc>http://localhost/en/blog-using-resources/flowers/articles/the-lily</loc>
        </url>
        <url>
            <loc>http://localhost/en/blog-using-resources/trees/articles/the-larch</loc>
        </url>
        <url>
            <loc>http://localhost/en/blog-using-resources/trees/articles/the-larch</loc>
        </url>
        <url>
            <loc>http://localhost/en/blog-using-resources/trees/articles/the-chestnut</loc>
        </url>
        <url>
            <loc>http://localhost/en/excluded/non-excluded-child</loc>
        </url>
        <url>
            <loc>http://localhost/en/parameter-with-translated-values/parameter-array-value1</loc>
        </url>
        <url>
            <loc>http://localhost/en/parameter-with-translated-values/parameter-array-value2</loc>
        </url>
        <url>
            <loc>http://localhost/en/parameter-with-values/parameter-array-value1</loc>
        </url>
        <url>
            <loc>http://localhost/en/parameter-with-values/parameter-array-value2</loc>
        </url>
        <url>
            <loc>http://localhost/en/resource</loc>
        </url>
        <url>
            <loc>http://localhost/en/resource/create</loc>
        </url>
    </urlset>', file_get_contents($this->getSitemapOutputFile()));
    }



    public function test_excluded_middleware()
    {
        $this->config->set('routetree.locales', null);
        $this->config->set('routetree.sitemap.excluded_middleware', ['test1']);
        $this->config->set('app.locale', 'de');

        $this->routeTree->root(function (RouteNode $node) {
            $node->namespace('\RouteTreeTests\Feature\Controllers');
            $node->get('TestController@get');
            $node->child('auth', function (RouteNode $node) {
                $node->get('\RouteTreeTests\Feature\Controllers\TestController@get');
                $node->middleware('auth');
                $node->child('auth-child', function (RouteNode $node) {
                    $node->get('\RouteTreeTests\Feature\Controllers\TestController@get');
                });
            });

            $node->child('test1', function (RouteNode $node) {
                $node->get('\RouteTreeTests\Feature\Controllers\TestController@get');
                $node->middleware('test1');
                $node->child('test1-child', function (RouteNode $node) {
                    $node->get('\RouteTreeTests\Feature\Controllers\TestController@get');
                });
            });
        });

        $this->routeTree->generateAllRoutes();

        $this->artisan('routetree:generate-sitemap')->assertExitCode('0');
        $this->assertFileExists($this->getSitemapOutputFile());
        $this->assertXmlStringEqualsXmlFile($this->getSitemapOutputFile(), '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
        xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <url>
        <loc>http://localhost/</loc>
    </url>
    <url>
        <loc>http://localhost/auth</loc>
    </url>
    <url>
        <loc>http://localhost/auth/auth-child</loc>
    </url>


</urlset>');
    }


}