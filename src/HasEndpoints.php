<?php

namespace Spatie\LaravelEndpointResources;

use Closure;
use Illuminate\Support\Arr;

/** @mixin \Illuminate\Http\Resources\Json\JsonResource */
trait HasEndpoints
{
    public function endpoints(string $controller = null, $parameters = null): EndpointResource
    {
        $endPointResource = new EndpointResource($this->resource, EndpointResourceType::ITEM);

        if ($controller !== null) {
            $endPointResource->addController($controller, Arr::wrap($parameters));
        }

        return $endPointResource;
    }

    public static function collectionEndpoints(string $controller = null, $parameters = null): EndpointResource
    {
        $endPointResource = new EndpointResource(null, EndpointResourceType::COLLECTION);

        if ($controller !== null) {
            $endPointResource->addController($controller, Arr::wrap($parameters));
        }

        return $endPointResource;
    }

    public static function collection($resource)
    {
        $meta = self::meta();

        if (! count($meta)) {
            parent::collection($resource);
        }

        return parent::collection($resource)->additional([
            'meta' => $meta,
        ]);
    }

    public static function make(...$parameters)
    {
        $meta = self::meta();

        if (! count($meta)) {
            parent::make(...$parameters);
        }

        return parent::make(...$parameters)->additional([
            'meta' => $meta,
        ]);
    }

    public static function meta()
    {
        return [];
    }
}
