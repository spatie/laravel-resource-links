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

    public static function initialize(Model $model = null, string $endpointResourceType = null): EndpointResource
    {
        return new self($model, $endpointResourceType);
    }

    public function __construct(Model $model = null, string $endpointResourceType = null)
    {
        parent::__construct($model);

        $this->endpointResourceType = $endpointResourceType ?? EndpointResourceType::ITEM;
        $this->endpointsGroup = new EndpointsGroup();
    }

    public function endpoint($controller, $parameters = null, $httpVerb = null): EndpointResource
    {
        if ($controller instanceof Closure) {
            $endpointsGroup = new EndpointsGroup();

            $controller($endpointsGroup);

            return $this->addEndpointsGroup($endpointsGroup);
        }

        if (is_array($controller)) {
            return $this->addAction($controller, Arr::wrap($parameters), $httpVerb);
        }

        if (is_string($controller)) {
            return method_exists($controller, '__invoke')
                ? $this->addInvokableController($controller, Arr::wrap($parameters))
                : $this->addController($controller, Arr::wrap($parameters));
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


    private function addController(string $controller, $parameters = null): EndpointResource
    {
        $this->endpointsGroup->controller($controller)
            ->parameters(Arr::wrap($parameters));

        return $this;
    }

    private function addAction(array $action, $parameters = null, string $httpVerb = null): EndpointResource
    {
        $this->endpointsGroup->action($action)
            ->httpVerb($httpVerb)
            ->parameters(Arr::wrap($parameters));

        return $this;
    }

    private function addInvokableController(string $controller, $parameters = null): EndpointResource
    {
        $this->endpointsGroup->invokableController($controller)
            ->parameters(Arr::wrap($parameters));

        return $this;
    }

    private function addEndpointsGroup(EndpointsGroup $endpointsGroup): EndpointResource
    {
        $this->endpointsGroup->endpointsGroup($endpointsGroup);

        return $this;
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

        if (config('laravel-resource-endpoints.automatically-merge-endpoints') === false) {
            return;
        }

        if (is_null($this->resource) || $this->resource->exists === false) {
            $this->mergeCollectionEndpoints();
        }
    }
}
