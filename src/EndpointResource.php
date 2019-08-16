<?php

namespace Spatie\LaravelResourceEndpoints;

use Closure;
use Illuminate\Support\Arr;
use Spatie\LaravelResourceEndpoints\EndpointTypes\ControllerEndpointType;
use Spatie\LaravelResourceEndpoints\EndpointTypes\EndpointType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

class EndpointResource extends JsonResource
{
    /** @var string */
    private $endpointResourceType;

    /** @var \Spatie\LaravelResourceEndpoints\EndpointsGroup */
    private $endpointsGroup;

    public static function create(Model $model = null, string $endpointResourceType = null): EndpointResource
    {
        return new self($model, $endpointResourceType);
    }

    public function __construct(Model $model = null, string $endpointResourceType = null)
    {
        parent::__construct($model);

        $this->endpointResourceType = $endpointResourceType ?? EndpointResourceType::ITEM;
        $this->endpointsGroup = new EndpointsGroup();
    }

    public function endpoint($endpoint, $parameters = null, $httpVerb = null): EndpointResource
    {
        if ($endpoint instanceof Closure) {
            $endpoint($this->endpointsGroup);

            return $this;
        }

        if (is_array($endpoint)) {
            $this->endpointsGroup
                ->action($endpoint)
                ->httpVerb($httpVerb)
                ->parameters(Arr::wrap($parameters));

            return $this;
        }

        if (is_string($endpoint)) {
            $this->endpointsGroup
                ->controller($endpoint)
                ->parameters(Arr::wrap($parameters));

            return $this;
        }

        return $this;
    }

    public function mergeCollectionEndpoints(): EndpointResource
    {
        $this->endpointResourceType = EndpointResourceType::MULTI;

        return $this;
    }

    public function toArray($request)
    {
        $this->ensureCollectionEndpointsAreAutomaticallyMerged();

        return $this->endpointsGroup
            ->getEndpointTypes()
            ->map(function (EndpointType $endpointType) use ($request) {
                return $endpointType->hasParameters() === false
                    ? $endpointType->parameters($request->route()->parameters())
                    : $endpointType;
            })
            ->mapWithKeys(function (EndPointType $endpointType) {
                if ($endpointType instanceof ControllerEndpointType) {
                    return $this->resolveEndpointsFromControllerEndpointType($endpointType);
                }

                return $endpointType->getEndpoints($this->resource);
            });
    }

    private function resolveEndpointsFromControllerEndpointType(ControllerEndpointType $endpointType): array
    {
        if ($this->endpointResourceType === EndpointResourceType::ITEM) {
            return $endpointType->getEndpoints($this->resource);
        }

        if ($this->endpointResourceType === EndpointResourceType::COLLECTION) {
            return $endpointType->getCollectionEndpoints();
        }

        if ($this->endpointResourceType === EndpointResourceType::MULTI) {
            return array_merge(
                $endpointType->getEndpoints($this->resource),
                $endpointType->getCollectionEndpoints()
            );
        }

        return [];
    }

    private function ensureCollectionEndpointsAreAutomaticallyMerged()
    {
        if ($this->endpointResourceType !== EndpointResourceType::ITEM) {
            return;
        }

        if (config('laravel-resource-endpoints.automatically_merge_endpoints') === false) {
            return;
        }

        if (is_null($this->resource) || $this->resource->exists === false) {
            $this->mergeCollectionEndpoints();
        }
    }
}
