<?php

namespace Spatie\ResourceLinks\LinkTypes;

use Spatie\ResourceLinks\Serializers\LinkContainer;
use Spatie\ResourceLinks\Serializers\Serializer;
use Spatie\ResourceLinks\ParameterResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use Spatie\ResourceLinks\UrlResolver;

class RouteLinkType extends LinkType
{
    /** @var \Illuminate\Routing\Route */
    protected $route;

    /** @var string|null */
    protected $httpVerb;

    /** @var string|null */
    protected $name;

    public static function make(Route $route): RouteLinkType
    {
        return new self($route);
    }

    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    public function httpVerb(?string $httpVerb): RouteLinkType
    {
        $this->httpVerb = $httpVerb;

        return $this;
    }

    public function name(?string $name): RouteLinkType
    {
        $this->name = $name;

        return $this;
    }

    public function getLinks(Model $model = null): array
    {
        $parameterResolver = new ParameterResolver($model, $this->parameters);

        $urlResolver = new UrlResolver(app('url'));

        $linkContainer = LinkContainer::make(
            $this->name ?? $this->route->getActionMethod(),
            $this->httpVerb ?? $this->getHttpVerbForRoute($this->route),
            $urlResolver->resolve($this->route, $parameterResolver->forRoute($this->route)),
            $this->prefix
        );

        return $this->resolveSerializer()->format($linkContainer);
    }

    private function getHttpVerbForRoute(Route $route): string
    {
        $httpVerbs = $route->methods;

        if ($httpVerbs === ['GET', 'HEAD']) {
            return 'GET';
        }

        return $httpVerbs[0];
    }

    private function resolveSerializer(): Serializer
    {
        $serializer = is_null($this->serializer)
            ? config('resource-links.serializer')
            : $this->serializer;

        return new $serializer;
    }
}
