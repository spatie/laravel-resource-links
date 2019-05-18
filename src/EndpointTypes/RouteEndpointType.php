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
                $this->route->getActionName(),
                $parameterResolver->forRoute($this->route)
            );
        } catch (UrlGenerationException $e) {
            return [];
        }

        return [
            $this->route->getActionMethod() => [
                'method' => $this->httpVerb ??$this->getHttpVerbForRoute($this->route),
                'action' => $action,
            ],
        ];
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
