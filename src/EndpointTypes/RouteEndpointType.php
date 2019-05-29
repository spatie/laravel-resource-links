<?php

namespace Spatie\LaravelEndpointResources\EndpointTypes;

use Spatie\LaravelEndpointResources\ParameterResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Routing\Route;

class RouteEndpointType extends EndpointType
{
    /** @var \Illuminate\Routing\Route */
    protected $route;

    /** @var string|null */
    protected $httpVerb;

    /** @var string|null */
    protected $name;

    public static function make(Route $route): RouteEndpointType
    {
        return new self($route);
    }

    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    public function httpVerb(?string $httpVerb): RouteEndpointType
    {
        $this->httpVerb = $httpVerb;

        return $this;
    }

    public function name(?string $name): RouteEndpointType
    {
        $this->name = $name;

        return $this;
    }

    public function getEndpoints(Model $model = null): array
    {
        $parameterResolver = new ParameterResolver($model, $this->parameters);

        $action = action("\\{$this->route->getActionName()}", $parameterResolver->forRoute($this->route));

        return [
            $this->resolveName() => [
                'method' => $this->httpVerb ?? $this->getHttpVerbForRoute($this->route),
                'action' => $action,
            ],
        ];
    }

    private function resolveName() : string
    {
        $name = $this->name ?? $this->route->getActionMethod();

        return "{$this->prefix}$name";
    }

    private function getHttpVerbForRoute(Route $route): string
    {
        $httpVerbs = $route->methods;

        if (count($httpVerbs) === 1) {
            return $httpVerbs[0];
        }

        if ($httpVerbs === ['GET', 'HEAD']) {
            return 'GET';
        }

        return $httpVerbs[0];
    }
}
