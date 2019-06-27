<?php

namespace Spatie\LaravelEndpointResources;

use Illuminate\Support\Arr;
use Spatie\LaravelEndpointResources\EndpointTypes\ActionEndpointType;
use Spatie\LaravelEndpointResources\EndpointTypes\ControllerEndpointType;
use Spatie\LaravelEndpointResources\EndpointTypes\EndpointType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Spatie\LaravelEndpointResources\EndpointTypes\InvokableControllerEndpointType;
use Spatie\LaravelEndpointResources\EndpointTypes\MultiEndpointType;

class EndpointResource extends JsonResource
{
    /** @var string */
    private $endpointResourceType;

    /** @var \Spatie\LaravelEndpointResources\EndpointsGroup */
    private $endpointsGroup;

    public function __construct(Model $model = null, string $endpointResourceType = null)
    {
        parent::__construct($model);

        $this->endpointResourceType = $endpointResourceType ?? EndpointResourceType::ITEM;
        $this->endpointsGroup = new EndpointsGroup();
    }

    public function addController(string $controller, $parameters = null): EndpointResource
    {
        if (method_exists($controller, '__invoke')) {
            return $this->addInvokableController($controller, $parameters);
        }

        $this->endpointsGroup->controller($controller)
            ->parameters(Arr::wrap($parameters));

        return $this;
    }

    public function addAction(array $action, $parameters = null, string $httpVerb = null): EndpointResource
    {
        $this->endpointsGroup->action($action)
            ->httpVerb($httpVerb)
            ->parameters(Arr::wrap($parameters));

        return $this;
    }

    public function addInvokableController(string $controller, $parameters = null): EndpointResource
    {
        $this->endpointsGroup->invokableController($controller)
            ->parameters(Arr::wrap($parameters));

        return $this;
    }

    public function addEndpointsGroup(EndpointsGroup $endpointsGroup): EndpointResource
    {
        $this->endpointsGroup->endpointsGroup($endpointsGroup);

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
                if ($endpointType instanceof MultiEndpointType) {
                    return $this->resolveEndpointsFromMultiEndpointType($endpointType);
                }

                return $endpointType->getEndpoints($this->resource);
            });
    }

    private function resolveEndpointsFromMultiEndpointType(MultiEndpointType $multiEndpointType): array
    {
        if ($this->endpointResourceType === EndpointResourceType::ITEM) {
            return $multiEndpointType->getEndpoints($this->resource);
        }

        if ($this->endpointResourceType === EndpointResourceType::COLLECTION) {
            return $multiEndpointType->getCollectionEndpoints();
        }

        if ($this->endpointResourceType === EndpointResourceType::MULTI) {
            return array_merge(
                $multiEndpointType->getEndpoints($this->resource),
                $multiEndpointType->getCollectionEndpoints()
            );
        }

        return [];
    }

    private function ensureCollectionEndpointsAreAutomaticallyMerged()
    {
        if ($this->endpointResourceType !== EndpointResourceType::ITEM) {
            return;
        }

        if(config('laravel-endpoint-resources.automatically-merge-endpoints') === false){
            return;
        }

        if (is_null($this->resource) || $this->resource->exists === false) {
            $this->mergeCollectionEndpoints();
        }
    }
}
