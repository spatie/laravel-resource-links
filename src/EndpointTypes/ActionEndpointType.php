<?php

namespace Spatie\LaravelEndpointResources\EndpointTypes;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;

final class ActionEndpointType extends EndpointType
{
    /** @var array */
    private $action;

    /** @var array */
    private $parameters;

    /** @var string|null */
    private $httpVerb;

    public function __construct(array $action, array $parameters = null, string $httpVerb = null)
    {
        $this->action = $action;
        $this->parameters = $parameters ?? [];
        $this->httpVerb = $httpVerb;
    }

    public function getEndpoints(Model $model = null): array
    {
        $formattedAction = $this->formatAction();

        $route = resolve(Router::class)
            ->getRoutes()
            ->getByAction($formattedAction);

        if ($route === null) {
            throw new Exception("Route {$formattedAction} does not exists!");
        }

        $parameters = $this->getParameters($model);

        $endpointType = new RouteEndpointType($route, $parameters, $this->httpVerb);

        return $endpointType->getEndpoints($model);
    }

    private function formatAction(): string
    {
        return trim('\\' . implode('@', $this->action), '\\');
    }

    private function getParameters(?Model $model)
    {
        if (! optional($model)->exists) {
            return $this->parameters;
        }

        return array_merge($this->parameters, [$model]);
    }
}
