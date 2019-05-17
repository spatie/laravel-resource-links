<?php


namespace Spatie\LaravelEndpointResources;

use Illuminate\Support\Arr;

trait HasEndpoints
{
    /** @var bool */
    protected $mergeGlobalEndpoints = false;

    public function endpoints(string $controller = null, $parameters = null): EndpointResource
    {
        $endPointResourceType = $this->mergeGlobalEndpoints
            ? EndpointResourceType::MULTI
            : EndpointResourceType::LOCAL;

        $endPointResource = new EndpointResource($this->resource, $endPointResourceType);

        if ($controller !== null) {
            $endPointResource->addController($controller, Arr::wrap($parameters));
        }

        return $endPointResource;
    }

    public static function globalEndpoints(string $controller = null, $parameters = null): EndpointResource
    {
        $endPointResource = new EndpointResource(null, EndpointResourceType::GLOBAL);

        if ($controller !== null) {
            $endPointResource->addController($controller, Arr::wrap($parameters));
        }

        return $endPointResource;
    }

    public static function meta()
    {
        return [];
    }

    public function mergeGlobalEndpoints()
    {
        $this->mergeGlobalEndpoints = true;

        return $this;
    }

    public static function collection($resource)
    {
        return parent::collection($resource)->additional([
            'meta' => self::meta(),
        ]);
    }

    public static function make(...$parameters)
    {
        return parent::make(...$parameters)->mergeGlobalEndpoints();
    }
}
