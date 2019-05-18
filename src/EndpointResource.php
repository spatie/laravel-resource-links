<?php

namespace Spatie\LaravelEndpointResources;

use Illuminate\Support\Arr;
use Spatie\LaravelEndpointResources\EndpointTypes\ActionEndpointType;
use Spatie\LaravelEndpointResources\EndpointTypes\ControllerEndpointType;
use Spatie\LaravelEndpointResources\EndpointTypes\EndpointType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class EndpointResource extends JsonResource
{
    /** @var string */
    protected $endpointResourceType;

    /** @var \Illuminate\Support\Collection */
    protected $endPointTypes;

    /** @var \Illuminate\Database\Eloquent\Model */
    protected $model;

    public function __construct(Model $model = null, string $endpointResourceType = null)
    {
        parent::__construct($model);

        $this->endPointTypes = new Collection();
        $this->model = $model;
        $this->endpointResourceType = $endpointResourceType ?? EndpointResourceType::LOCAL;
    }

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

    public function mergeCollectionEndpoints() : JsonResource
    {
        $this->endpointResourceType = EndpointResourceType::MULTI;

        return $this;
    }

    public function toArray($request)
    {
        return $this->endPointTypes->mapWithKeys(function (EndPointType $endpointType) {
            if ($endpointType instanceof ControllerEndpointType) {
                return $this->resolveControllerEndpoints($endpointType);
            }

            return $endpointType->getEndpoints($this->model);
        });
    }

    protected function resolveProvidedParameters($parameters = null): array
    {
        $parameters = Arr::wrap($parameters);

        return count($parameters) === 0
            ? request()->route()->parameters()
            : $parameters;
    }

    protected function resolveControllerEndpoints(ControllerEndpointType $endpointType): array
    {
        if ($this->endpointResourceType === EndpointResourceType::LOCAL) {
            return $endpointType->getEndpoints($this->model);
        }

        if ($this->endpointResourceType === EndpointResourceType::GLOBAL) {
            return $endpointType->getCollectionEndpoints();
        }

        if ($this->endpointResourceType === EndpointResourceType::MULTI) {
            return array_merge(
                $endpointType->getEndpoints($this->model),
                $endpointType->getCollectionEndpoints()
            );
        }

        return [];
    }
}
