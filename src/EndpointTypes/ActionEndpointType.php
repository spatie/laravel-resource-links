<?php

namespace Spatie\LaravelResourceEndpoints\EndpointTypes;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;

class ActionEndpointType extends EndpointType
{
    /** @var array */
    private $action;

    /** @var string|null */
    private $httpVerb;

    /** @var string|null */
    private $name;

    public static function make(array $action): ActionEndpointType
    {
        return new self($action);
    }

    public function __construct(array $action)
    {
        $this->action = $action;
    }

    public function httpVerb(?string $httpVerb): ActionEndpointType
    {
        $this->httpVerb = $httpVerb;

        return $this;
    }

    public function name(?string $name): ActionEndpointType
    {
        $this->name = $name;

        return $this;
    }

    public function getEndpoints(Model $model = null): array
    {
        $formattedAction = $this->formatAction();

        $route = app(Router::class)
            ->getRoutes()
            ->getByAction($formattedAction);

        if ($route === null) {
            throw new Exception("Route `{$formattedAction}` does not exist!");
        }

        return RouteEndpointType::make($route)
            ->name($this->name)
            ->httpVerb($this->httpVerb)
            ->prefix($this->prefix)
            ->parameters($this->getParameters($model))
            ->formatter($this->formatter)
            ->getEndpoints($model);
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
