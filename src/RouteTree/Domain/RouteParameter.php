<?php

namespace Webflorist\RouteTree\Domain;


use Illuminate\Database\Eloquent\Model;
use Webflorist\RouteTree\Exceptions\NoRouteParameterModelException;
use Webflorist\RouteTree\Interfaces\TranslatableRouteKey;
use Webflorist\RouteTree\RouteTree;

/**
 * Parameter for RouteNodes.
 *
 * Class RouteParameter
 * @package Webflorist\RouteTree
 *
 */
class RouteParameter
{
    /**
     * The parameter name.
     *
     * @var string
     */
    private $name;

    /**
     * The RouteNode this parameter belongs to.
     *
     * @var RouteNode
     */
    private $routeNode;

    /**
     * Class of a model bound to this route parameter.
     *
     * @var string
     */
    private $model;

    /**
     * Specific values for this RouteParameter.
     * (Either one-dimensional for all languages
     * or multi-dimensional per language).
     *
     * @var array
     */
    private $routeKeys;

    /**
     * RouteParameter constructor.
     *
     * @param string $parameter
     * @param RouteNode $routeNode
     */
    public function __construct(string $parameter, RouteNode $routeNode)
    {
        $this->routeNode = $routeNode;
        $this->name = $parameter;
    }

    public function model(string $model)
    {
        if (!isset(class_implements($model)[TranslatableRouteKey::class])) {
            throw new NoRouteParameterModelException("Model '$model' does not implement 'TranslatableRouteKey'");
        }
        $this->model = $model;
    }

    public function routeKeys(array $routeKeys)
    {
        $this->routeKeys = $routeKeys;
    }

    public function getRouteKeys(?string $locale = null, ?array $parameters = null)
    {
        RouteTree::establishLocale($locale);
        RouteTree::establishRouteParameters($parameters);

        if (!is_null($this->routeKeys)) {
            // if $this->routeKeys is multidimensional, we assume it's localised
            if (is_array(array_values($this->routeKeys)[0])) {
                return $this->routeKeys[$locale];
            }
            return $this->routeKeys;
        }

        if (!is_null($this->model)) {
            return $this->model::getAllRouteKeys($locale, $parameters);
        }

        return [];

    }

    public function hasRouteKeys(string $locale, array $parameters = null)
    {
        return count($this->getRouteKeys($locale, $parameters)) > 0;
    }

    public function getName()
    {
        return $this->name;
    }

    public function isActive()
    {
        return \Route::current()->hasParameter($this->name);
    }

    public function getActiveRouteKey(?string $locale = null)
    {
        if ($this->isActive()) {
            $currentParameterValue = \Route::current()->parameter($this->name);
            $translateValue = !is_null($locale) && ($locale !== app()->getLocale());

            // In case parameter is a bound model.
            if ($currentParameterValue instanceof Model) {
                $currentParameterValue = $currentParameterValue->getRouteKey();
            }

            if ($translateValue) {
                return $this->translateRouteKey($currentParameterValue, $locale, app()->getLocale());
            }
            return $currentParameterValue;
        }
        return null;
    }

    private function translateRouteKey($routeKey, string $toLocale, string $fromLocale)
    {
        if (!is_null($this->routeKeys) && is_array(array_values($this->routeKeys)[0])) {
            $valueKey = array_search($routeKey, $this->routeKeys[$fromLocale]);
            if (isset($this->routeKeys[$toLocale][$valueKey])) {
                return $this->routeKeys[$toLocale][$valueKey];
            }
        }

        if (!is_null($this->model)) {
            return $this->model::translateRouteKey($routeKey, $toLocale, $fromLocale);
        }

        return $routeKey;
    }

    public function hasModel()
    {
        return $this->model !== null;
    }

    public function getModel()
    {
        return $this->model;
    }

}
