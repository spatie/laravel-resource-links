<?php

namespace Spatie\LaravelEndpointResources\Tests\Dummy;

use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Router;

final class FakeRouter
{
    /** @var \Illuminate\Routing\Router */
    private $router;

    /** @var \Illuminate\Routing\RouteCollection */
    private $routeCollection;

    public function __construct(Router $router)
    {
        $this->router = $router;
        $this->routeCollection = new RouteCollection();
    }

    public static function setup()
    {
        return new self(resolve(Router::class));
    }

    public function route($methods, $uri, $action): Route
    {
        $route = new Route($methods, $uri, $action);

        $this->addRoute($route->middleware('web'));

        return $route;
    }

    private function addRoute(Route $route)
    {
        $this->routeCollection->add($route);

        $this->router->setRoutes($this->routeCollection);
    }
}
