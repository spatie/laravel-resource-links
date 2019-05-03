<?php

namespace Spatie\LaravelEndpointResources;

use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\LaravelEndpointResources\EndpointTypes\ActionEndpointType;
use Spatie\LaravelEndpointResources\EndpointTypes\ControllerEndpointType;

trait StoresEndpointTypes
{
    public function addController(string $controller, array $parameters = null): JsonResource
    {
        $providedParameters = $parameters ?? request()->route()->parameters();

        $this->endPointTypes->push(new ControllerEndpointType(
            $controller,
            $providedParameters
        ));

        return $this;
    }

    public function addAction(array $action, array $parameters = null, string $httpVerb = null): JsonResource
    {
        $providedParameters = $parameters ?? request()->route()->parameters();

        $this->endPointTypes->push(new ActionEndpointType(
            $action,
            $providedParameters,
            $httpVerb
        ));

        return $this;
    }
}
