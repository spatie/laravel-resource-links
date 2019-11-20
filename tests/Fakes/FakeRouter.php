<?php

namespace Spatie\ResourceLinks\Tests\Fakes;

use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\Router;

class FakeRouter
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
        return new self(app(Router::class));
    }

    public function route($methods, $uri, $action): Route
    {
        $route = new Route($methods, $uri, $action);

        $this->addRoute($route->middleware('web'));

        return $route;
    }

    public function get($uri, $action): Route
    {
        return $this->route('GET', $uri, $action);
    }

    public function post($uri, $action): Route
    {
        return $this->route('POST', $uri, $action);
    }

    public function put($uri, $action): Route
    {
        return $this->route('PUT', $uri, $action);
    }

    public function invokableGet($uri, $controller): Route
    {
        $route = $this->get($uri, [
            'uses' => $controller,
            'controller' => $controller,
        ]);

        return $route;
    }

    private function addRoute(Route $route)
    {
        $this->routeCollection->add($route);

        $this->router->setRoutes($this->routeCollection);
    }
}
