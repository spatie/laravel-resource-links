<?php

namespace Spatie\ResourceLinks\LinkTypes;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class ControllerLinkType extends LinkType
{
    /** @var string */
    private $controller;

    /** @var array */
    private $methods = [];

    /** @var array */
    private static $cachedRoutes = [];

    /** @var array */
    private $names = [];

    public static function make(string $controller): ControllerLinkType
    {
        return new ControllerLinkType($controller);
    }

    public function __construct(string $controller)
    {
        $this->controller = $controller;
    }

    public function methods($methods): ControllerLinkType
    {
        $this->methods = Arr::wrap($methods);

        return $this;
    }

    public function names(array $names): ControllerLinkType
    {
        $this->names = $names;

        return $this;
    }

    public function getLinks(Model $model = null): array
    {
        $methodsToInclude = empty($this->methods)
            ? ['show', 'edit', 'update', 'destroy']
            : $this->methods;

        return $this->resolveLinks($methodsToInclude, $model);
    }

    public function getCollectionLinks(): array
    {
        $methodsToInclude = empty($this->methods)
            ? ['index', 'store', 'create']
            : $this->methods;

        return $this->resolveLinks($methodsToInclude);
    }

    public static function clearCache(): void
    {
        self::$cachedRoutes = [];
    }

    private function resolveLinks(array $methodsToInclude, Model $model = null): array
    {
        $links = self::getRoutesForController($this->controller)
            ->filter(function (Route $route) use ($methodsToInclude) {
                return in_array($route->getActionMethod(), $methodsToInclude);
            })
            ->map(function (Route $route) use ($model) {
                $route = RouteLinkType::make($route)
                    ->parameters($this->parameters)
                    ->name($this->resolveNameForRoute($route))
                    ->prefix($this->prefix)
                    ->query($this->query)
                    ->serializer($this->serializer)
                    ->getLinks($model);

                return $route;
            })
            ->toArray();

        return ! empty($links)
            ? array_merge_recursive(...$links)
            : [];
    }

    private static function getRoutesForController(string $controller): Collection
    {
        if (array_key_exists($controller, self::$cachedRoutes)) {
            return self::$cachedRoutes[$controller];
        }

        $routes = collect(resolve(Router::class)->getRoutes()->getRoutes());

        self::$cachedRoutes[$controller] = $routes
            ->filter(function (Route $route) use ($controller) {
                return $controller === Str::before($route->getActionName(), '@');
            });

        return self::$cachedRoutes[$controller];
    }

    private function resolveNameForRoute(Route $route): string
    {
        $method = $route->getActionMethod();

        if (array_key_exists($method, $this->names)) {
            return $this->names[$method];
        }

        return $method;
    }
}
