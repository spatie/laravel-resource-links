<?php

namespace Spatie\LaravelResourceEndpoints;

use Closure;
use Illuminate\Support\Arr;

/** @mixin \Illuminate\Http\Resources\Json\JsonResource */
trait HasEndpoints
{
    /**
     * @param string|Closure|null|array $controller
     * @param null $parameters
     *
     * @return \Spatie\LaravelResourceEndpoints\EndpointResource
     */
    public function endpoints($controller = null, $parameters = null): EndpointResource
    {
        return EndpointResource::initialize($this->resource, EndpointResourceType::ITEM)->endpoint($controller, $parameters);
    }

    /**
     * @param string|Closure|null|array $controller
     * @param null $parameters
     *
     * @return \Spatie\LaravelResourceEndpoints\EndpointResource
     */
    public static function collectionEndpoints($controller = null, $parameters = null): EndpointResource
    {
        return EndpointResource::initialize(null, EndpointResourceType::COLLECTION)->endpoint($controller, $parameters);
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
