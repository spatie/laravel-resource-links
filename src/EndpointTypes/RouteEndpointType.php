<?php

namespace Spatie\ResourceLinks\EndpointTypes;

use Illuminate\Routing\RouteUrlGenerator;
use Spatie\ResourceLinks\Exceptions\EndpointGenerationException;
use Spatie\ResourceLinks\Serializers\LayeredSerializer;
use Spatie\ResourceLinks\Serializers\Endpoint;
use Spatie\ResourceLinks\Serializers\DefaultSerializer;
use Spatie\ResourceLinks\Serializers\Serializer;
use Spatie\ResourceLinks\ParameterResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Routing\Route;
use Spatie\ResourceLinks\UrlResolver;

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

        $urlResolver = new UrlResolver(app('url'));

        $endpoint = Endpoint::make(
            $this->name ?? $this->route->getActionMethod(),
            $this->httpVerb ?? $this->getHttpVerbForRoute($this->route),
            $urlResolver->resolve($this->route, $parameterResolver->forRoute($this->route)),
            $this->prefix
        );

        return $this->resolveFormatter()->format($endpoint);
    }

    private function getHttpVerbForRoute(Route $route): string
    {
        $httpVerbs = $route->methods;

        if ($httpVerbs === ['GET', 'HEAD']) {
            return 'GET';
        }

        return $httpVerbs[0];
    }

    private function resolveFormatter(): Serializer
    {
        $formatter = is_null($this->formatter)
            ? config('resource-links.formatter')
            : $this->formatter;

        return new $formatter;
    }
}
