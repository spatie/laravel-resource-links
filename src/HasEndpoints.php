<?php

namespace Spatie\LaravelResourceEndpoints;

use Closure;

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
        $resource = EndpointResource::create($this->resource, EndpointResourceType::ITEM)->endpoint($controller, $parameters);

        if (property_exists($this, 'mergeCollectionEndpoints') && $this->mergeCollectionEndpoints === true) {
            $resource->mergeCollectionEndpoints();
        }

        return $resource;
    }

    /**
     * @param string|Closure|null|array $controller
     * @param null $parameters
     *
     * @return \Spatie\LaravelResourceEndpoints\EndpointResource
     */
    public static function collectionEndpoints($controller = null, $parameters = null): EndpointResource
    {
        return EndpointResource::create(null, EndpointResourceType::COLLECTION)->endpoint($controller, $parameters);
    }
}
