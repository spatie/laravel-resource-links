<?php

namespace Spatie\LaravelResourceEndpoints\EndpointTypes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class InvokableControllerEndpointType extends EndpointType
{
    /** @var string */
    private $controller;

    /** @var string|null */
    private $name;

    public static function make(string $controller): InvokableControllerEndpointType
    {
        return new InvokableControllerEndpointType($controller);
    }

    public function __construct(string $controller)
    {
        $this->controller = $controller;
    }

    public function name(?string $name): InvokableControllerEndpointType
    {
        $this->name = $name;

        return $this;
    }

    public function getEndpoints(Model $model = null): array
    {
        return $this->resolveEndpointType()->getEndpoints($model);
    }

    private function resolveEndpointType(): ActionEndpointType
    {
        return ActionEndpointType::make([$this->controller])
            ->name($this->name ?? 'invoke')
            ->parameters($this->parameters)
            ->prefix($this->prefix)
            ->formatter($this->formatter);
    }
}
