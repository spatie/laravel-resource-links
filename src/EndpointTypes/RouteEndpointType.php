<?php

namespace Spatie\LaravelEndpointResources\EndpointTypes;

use Spatie\LaravelEndpointResources\Exceptions\EndpointGenerationException;
use Spatie\LaravelEndpointResources\Formatters\LayeredFormatter;
use Spatie\LaravelEndpointResources\Formatters\Endpoint;
use Spatie\LaravelEndpointResources\Formatters\DefaultFormatter;
use Spatie\LaravelEndpointResources\Formatters\Formatter;
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

        try {
            $action = action("\\{$this->route->getActionName()}", $parameterResolver->forRoute($this->route));
        } catch (UrlGenerationException $exception) {
            throw EndpointGenerationException::make(
                $this->route,
                $model,
                $this->parameters
            );
        }

        $endpoint = Endpoint::make(
            $this->name ?? $this->route->getActionMethod(),
            $this->httpVerb ?? $this->getHttpVerbForRoute($this->route),
            $action,
            $this->prefix
        );

        return $this->resolveFormatter()->format($endpoint);
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

    private function resolveFormatter(): Formatter
    {
        $formatter = is_null($this->formatter)
            ? config('laravel-endpoint-resources.formatter')
            : $this->formatter;

        return new $formatter;
    }
}
