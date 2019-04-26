<?php


namespace Spatie\LaravelEndpointResources;

use Spatie\LaravelEndpointResources\EndpointTypes\ControllerEndpointType;

trait HasEndpoints
{
    public function endpoints(string $controller = null, array $parameters = null): EndpointResource
    {
        $endPointResource = new EndpointResource($this->resource);

        if ($controller !== null) {
            $endPointResource->addController($controller, $parameters);
        }

        return $endPointResource;
    }

    public static function globalEndpoints(string $controller = null, array $parameters = null): GlobalEndpointResource
    {
        $globalEndpointResource = new GlobalEndpointResource();

        if ($controller !== null) {
            $globalEndpointResource->addController($controller, $parameters);
        }

        return $globalEndpointResource;
    }

    public static function getGlobalEndpoints(
        string $controller = null,
        array $parameters = null
    ): array {
        $parameters = $parameters ?? request()->route()->parameters();

        $endpointType = new ControllerEndpointType($controller, $parameters);

        return [
            'meta' => [
                'endpoints' => $endpointType->getGlobalEndpoints(),
            ],
        ];
    }
}
