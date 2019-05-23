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

    /** @var array */
    protected $defaultParameters;

    /** @var string|null */
    protected $httpVerb;

    /** @var string|null */
    protected $name;

    public function __construct(Route $route, array $defaultParameters = [], string $httpVerb = null)
    {
        $this->route = $route;
        $this->defaultParameters = $defaultParameters;
        $this->httpVerb = $httpVerb;
    }

    public function getEndpoints(Model $model = null): array
    {
        $parameterResolver = new ParameterResolver($model, $this->defaultParameters);

        $action = null;

        try {
            $action = action(
                "\\{$this->route->getActionName()}",
                $parameterResolver->forRoute($this->route)
            );
        } catch (UrlGenerationException $exception) {
            return [];
        }

        return [
            $this->name ?? $this->route->getActionMethod() => [
                'method' => $this->httpVerb ?? $this->getHttpVerbForRoute($this->route),
                'action' => $action,
            ],
        ];
    }

    public function setName(?string $name) : RouteEndpointType
    {
        $this->name = $name;

        return $this;
    }

    protected function getHttpVerbForRoute(Route $route): string
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
