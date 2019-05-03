<?php

namespace Spatie\LaravelEndpointResources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Spatie\LaravelEndpointResources\EndpointTypes\ActionEndpointType;
use Spatie\LaravelEndpointResources\EndpointTypes\ControllerEndpointType;

/** @mixin \Illuminate\Http\Resources\Json\JsonResource */
trait StoresEndpointTypes
{
    public function addController(string $controller, $parameters = null): JsonResource
    {
        $this->endPointTypes->push(new ControllerEndpointType(
            $controller,
            $this->resolveProvidedParameters($parameters)
        ));

        return $this;
    }

    public function addAction(array $action, $parameters = null, string $httpVerb = null): JsonResource
    {
        $this->endPointTypes->push(new ActionEndpointType(
            $action,
            $this->resolveProvidedParameters($parameters),
            $httpVerb
        ));

        return $this;
    }

    private function resolveProvidedParameters($parameters = null)
    {
        $parameters = Arr::wrap($parameters);

        return count($parameters) === 0
            ? request()->route()->parameters()
            : $parameters;
    }
}
