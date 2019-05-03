<?php


namespace Spatie\LaravelEndpointResources;

use Illuminate\Support\Arr;
use Spatie\LaravelEndpointResources\EndpointTypes\ControllerEndpointType;

trait HasEndpoints
{
    public function endpoints(string $controller = null, $parameters = null): EndpointResource
    {
        $endPointResource = new EndpointResource($this->resource);

        if ($controller !== null) {
            $endPointResource->addController($controller, Arr::wrap($parameters));
        }

        return $endPointResource;
    }

    public static function globalEndpoints(string $controller = null, $parameters = null): GlobalEndpointResource
    {
        $globalEndpointResource = new GlobalEndpointResource();

        if ($controller !== null) {
            $globalEndpointResource->addController($controller, Arr::wrap($parameters));
        }

        return $globalEndpointResource;
    }

    public static function meta()
    {
        return [];
    }

    public static function collection($resource)
    {
        return parent::collection($resource)->additional([
            'meta' => self::meta(),
        ]);
    }
}
