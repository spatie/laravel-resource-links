<?php

namespace Spatie\LaravelEndpointResources\EndpointTypes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use ReflectionClass;

class ControllerEndpointType extends EndpointType
{
    /** @var string */
    protected $controller;

    /** @var array */
    protected $defaultParameters;

    /** @var array */
    protected static $cachedRoutes = [];

    public function __construct(string $controller, array $defaultParameters = null)
    {
        $this->controller = $controller;
        $this->defaultParameters = $defaultParameters ?? [];
    }

    public function getEndpoints(Model $model = null): array
    {
        $controller = new $this->controller();

        $endpoints = property_exists($controller, 'endPointMethods')
            ? $controller->endPointMethods
            : ['show', 'edit', 'update', 'delete'];

        return $this->resolveEndpoints(
            $endpoints,
            $model
        );
    }

    public function getCollectionEndpoints(): array
    {
        $controller = new $this->controller();

        $endpoints = property_exists($controller, 'globalEndPointMethods')
            ? $controller->globalEndPointMethods
            : ['index', 'store', 'create'];

        return $this->resolveEndpoints($endpoints);
    }

    protected function resolveEndpoints(array $methodsToInclude, Model $model = null) : array
    {
        return self::getRoutesForController($this->controller)
            ->filter(function (Route $route) use ($methodsToInclude) {
                return in_array($route->getActionMethod(), $methodsToInclude);
            })->mapWithKeys(function (Route $route) use ($model) {
                $routesToEndpoint = new RouteEndpointType($route, $this->defaultParameters);

                return $routesToEndpoint->getEndpoints($model);
            })->toArray();
    }

    protected static function getRoutesForController(string $controller): Collection
    {
        if (in_array($controller, self::$cachedRoutes)) {
            return self::$cachedRoutes[$controller];
        }

        $routes = collect(resolve(Router::class)->getRoutes()->getRoutes());

        self::$cachedRoutes[$controller] = $routes
            ->filter(function (Route $route) use ($controller) {
                return $controller === Str::before($route->getActionName(), '@');
            });

        return self::$cachedRoutes[$controller];
    }
}
