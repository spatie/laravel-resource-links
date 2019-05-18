<?php

namespace Spatie\LaravelEndpointResources\EndpointTypes;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;

class ActionEndpointType extends EndpointType
{
    /** @var array */
    protected $action;

    /** @var array */
    protected $parameters;

    /** @var string|null */
    protected $httpVerb;

    public function __construct(array $action, array $parameters = [], string $httpVerb = null)
    {
        $this->action = $action;
        $this->parameters = $parameters;
        $this->httpVerb = $httpVerb;
    }

    public function getEndpoints(Model $model = null): array
    {
        $formattedAction = $this->formatAction();

        $route = resolve(Router::class)
            ->getRoutes()
            ->getByAction($formattedAction);

        if ($route === null) {
            throw new Exception("Route `{$formattedAction}` does not exist!");
        }

        $parameters = $this->getParameters($model);

        $endpointType = new RouteEndpointType($route, $parameters, $this->httpVerb);

        return $endpointType->getEndpoints($model);
    }

    protected function formatAction(): string
    {
        return trim('\\' . implode('@', $this->action), '\\');
    }

    protected function getParameters(?Model $model)
    {
        if (! optional($model)->exists) {
            return $this->parameters;
        }

        return array_merge($this->parameters, [$model]);
    }
}
