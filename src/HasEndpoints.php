<?php

namespace Spatie\LaravelEndpointResources;

use Closure;
use Illuminate\Support\Arr;

/** @mixin \Illuminate\Http\Resources\Json\JsonResource */
trait HasEndpoints
{
    /**
     * @param string|Closure|null $controller
     * @param null $parameters
     *
     * @return \Spatie\LaravelEndpointResources\EndpointResource
     */
    public function endpoints($controller = null, $parameters = null): EndpointResource
    {
        return self::initializeEndpointResource(
            new EndpointResource($this->resource, EndpointResourceType::ITEM),
            $controller,
            $parameters
        );
    }

    /**
     * @param string|Closure|null $controller
     * @param null $parameters
     *
     * @return \Spatie\LaravelEndpointResources\EndpointResource
     */
    public static function collectionEndpoints($controller = null, $parameters = null): EndpointResource
    {
        return self::initializeEndpointResource(
            new EndpointResource(null, EndpointResourceType::COLLECTION),
            $controller,
            $parameters
        );
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

    /**
     * @param \Spatie\LaravelEndpointResources\EndpointResource|null $endpointResource
     * @param string|Closure|null $controller
     * @param null $parameters
     *
     * @return \Spatie\LaravelEndpointResources\EndpointResource
     */
    private static function initializeEndpointResource(
        EndpointResource $endpointResource,
        $controller = null,
        $parameters = null
    ): EndpointResource {
        if ($controller instanceof Closure) {
            $endpointsGroup = new EndpointsGroup();

            $controller($endpointsGroup);

            return $endpointResource->addEndpointsGroup($endpointsGroup);
        }

        if ($controller !== null) {
            return $endpointResource->addController($controller, Arr::wrap($parameters));
        }

        return $endpointResource;
    }
}
