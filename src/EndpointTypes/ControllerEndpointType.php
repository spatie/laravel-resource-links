<?php

namespace Spatie\LaravelEndpointResources\EndpointTypes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\LaravelEndpointResources\EndpointTypes\MultiEndpointType;

class ControllerEndpointType extends EndpointType implements MultiEndpointType
{
    /** @var string */
    private $controller;

    /** @var array */
    private $methods = [];

    /** @var array */
    private static $cachedRoutes = [];

    /** @var array */
    private $aliases;

    public static function make(string $controller): ControllerEndpointType
    {
        return new self($controller);
    }

    public function __construct(string $controller)
    {
        $this->controller = $controller;
    }

    public function methods($methods): ControllerEndpointType
    {
        $this->methods = Arr::wrap($methods);

        return $this;
    }

    public function aliases(array $aliases): ControllerEndpointType
    {
        $this->aliases = $aliases;

        return $this;
    }

    public function getEndpoints(Model $model = null): array
    {
        $methodsToInclude = $this->resolveMethodsToInclude(
            'endPointMethods',
            ['show', 'edit', 'update', 'destroy']
        );

        return $this->resolveEndpoints($methodsToInclude, $model);
    }

    public function getCollectionEndpoints(): array
    {
        $methodsToInclude = $this->resolveMethodsToInclude(
            'collectionEndPointMethods',
            ['index', 'store', 'create']
        );

        return $this->resolveEndpoints($methodsToInclude);
    }

    private function resolveMethodsToInclude(string $classProperty, array $fallBackMethods): array
    {
        if (! empty($this->methods)) {
            return $this->methods;
        }

        $controller = new $this->controller();

        if (property_exists($controller, $classProperty)) {
            return $controller->$classProperty;
        }

        return $fallBackMethods;
    }

    private function resolveEndpoints(array $methodsToInclude, Model $model = null): array
    {
        return self::getRoutesForController($this->controller)
            ->filter(function (Route $route) use ($methodsToInclude) {
                return in_array($route->getActionMethod(), $methodsToInclude);
            })->mapWithKeys(function (Route $route) use ($model) {
                return RouteEndpointType::make($route)
                    ->parameters($this->parameters)
                    ->prefix($this->prefix)
                    ->getEndpoints($model);
            })->toArray();
    }

    private static function getRoutesForController(string $controller): Collection
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
