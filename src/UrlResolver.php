<?php

namespace Spatie\ResourceLinks;

use Illuminate\Support\Str;
use Illuminate\Routing\Route;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Routing\RouteUrlGenerator;
use Illuminate\Routing\Exceptions\UrlGenerationException;

class UrlResolver extends RouteUrlGenerator
{
    public function __construct(UrlGenerator $urlGenerator)
    {
        parent::__construct($urlGenerator, $urlGenerator->getRequest());

        $this->defaults($urlGenerator->getDefaultParameters());
    }

    public function resolve(Route $route, array $parameters): string
    {
        $parameters = $this->url->formatParameters($parameters);

        try {
            return $this->to($route, $parameters, true);
        } catch (UrlGenerationException $exception) {
            // Create a URI with missing parameters between brackets
            $domain = $this->getRouteDomain($route, $parameters);

            return $this->addQueryString($this->url->format(
                $this->replaceRootParameters($route, $domain, $parameters),
                $this->replaceRouteParameters($route->uri(), $parameters),
                $route
            ), $parameters);
        }
    }

    protected function replaceRouteParameters($path, array &$parameters)
    {
        // We should try to find a solution to not including this function,
        // this had to be added to support Laravel 6:
        // https://github.com/laravel/framework/issues/29736

        $path = $this->replaceNamedParameters($path, $parameters);

        $path = preg_replace_callback('/\{.*?\}/', function ($match) use (&$parameters) {
            return (empty($parameters) && ! Str::endsWith($match[0], '?}'))
                ? $match[0]
                : array_shift($parameters);
        }, $path);

        return trim(preg_replace('/\{.*?\?\}/', '', $path), '/');
    }
}
