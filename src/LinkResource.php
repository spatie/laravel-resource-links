<?php

namespace Spatie\ResourceLinks;

use Closure;
use Illuminate\Support\Arr;
use Spatie\ResourceLinks\EndpointTypes\ControllerEndpointType;
use Spatie\ResourceLinks\EndpointTypes\EndpointType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

class LinkResource extends JsonResource
{
    /** @var string */
    private $endpointResourceType;

    /** @var \Spatie\ResourceLinks\Links */
    private $endpointsGroup;

    public static function create(Model $model = null, string $endpointResourceType = null): LinkResource
    {
        return new self($model, $endpointResourceType);
    }

    public function __construct(Model $model = null, string $endpointResourceType = null)
    {
        parent::__construct($model);

        $this->endpointResourceType = $endpointResourceType ?? LinkResourceType::ITEM;
        $this->endpointsGroup = new Links();
    }

    public function endpoint($endpoint, $parameters = null, $httpVerb = null): LinkResource
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

        if (is_string($endpoint) && method_exists($endpoint, '__invoke')) {
            $this->endpointsGroup
                ->invokableController($endpoint)
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

    public function mergeCollectionEndpoints(): LinkResource
    {
        $this->endpointResourceType = LinkResourceType::MULTI;

        return $this;
    }

    public function toArray($request)
    {
        return $this->endpointsGroup
            ->getEndpointTypes()
            ->mapWithKeys(function (EndPointType $endpointType) use ($request) {
                $endpointType->parameters($request->route()->parameters());

                if ($endpointType instanceof ControllerEndpointType) {
                    return $this->resolveEndpointsFromControllerEndpointType($endpointType);
                }

                return $endpointType->getEndpoints($this->resource);
            });
    }

    private function resolveEndpointsFromControllerEndpointType(ControllerEndpointType $endpointType): array
    {
        if ($this->endpointResourceType === LinkResourceType::ITEM) {
            return $endpointType->getEndpoints($this->resource);
        }

        if ($this->endpointResourceType === LinkResourceType::COLLECTION) {
            return $endpointType->getCollectionEndpoints();
        }

        if ($this->endpointResourceType === LinkResourceType::MULTI) {
            return array_merge(
                $endpointType->getEndpoints($this->resource),
                $endpointType->getCollectionEndpoints()
            );
        }

        return [];
    }
}
