<?php

namespace Spatie\LaravelEndpointResources\EndpointTypes;

use Illuminate\Database\Eloquent\Model;

class InvokableControllerEndpointType extends ControllerEndpointType
{
    /** @var string */
    private $controller;

    /** @var string|null */
    private $name;

    public static function make(string $controller): ControllerEndpointType
    {
        return new InvokableControllerEndpointType($controller);
    }

    public function __construct(string $controller)
    {
        parent::__construct($controller);

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

    public function getCollectionEndpoints(): array
    {
        return $this->resolveEndpointType()->getEndpoints();
    }

    private function resolveEndpointType(): ActionEndpointType
    {
        return ActionEndpointType::make([$this->controller])
            ->name($this->resolveEndpointName())
            ->parameters($this->parameters)
            ->prefix($this->prefix)
            ->formatter($this->formatter);
    }

    private function resolveEndpointName(): string
    {
        if ($this->name !== null) {
            return $this->name;
        }

        $controller = new $this->controller;

        if (property_exists($controller, 'endpointName')) {
            return $controller->endpointName;
        }

        return 'invoke';
    }
}
