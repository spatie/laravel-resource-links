<?php

namespace Spatie\LaravelEndpointResources;

use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Routing\Route;
use Illuminate\Routing\RouteUrlGenerator;
use Illuminate\Routing\UrlGenerator;

class UrlResolver extends RouteUrlGenerator
{
    public function __construct(UrlGenerator $urlGenerator)
    {
        parent::__construct($urlGenerator, $urlGenerator->getRequest());

        // The URL defaults are not given through automatically
        $this->defaults($urlGenerator->getDefaultParameters());
    }

    public function resolve(Route $route, array $parameters): string
    {
        try {
            return $this->to(
                $route,
                $this->url->formatParameters($parameters),
                true
            );
        } catch (UrlGenerationException $exception) {
            // Create an uri with missing parameters between brackets
            $domain = $this->getRouteDomain($route, $parameters);

            return $this->addQueryString($this->url->format(
                $root = $this->replaceRootParameters($route, $domain, $parameters),
                $this->replaceRouteParameters($route->uri(), $parameters),
                $route
            ), $parameters);
        }
    }
}
