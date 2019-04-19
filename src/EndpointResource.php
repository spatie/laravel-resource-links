<?php

namespace Spatie\LaravelEndpointResources;

use Spatie\LaravelEndpointResources\EndpointTypes\ActionEndpointType;
use Spatie\LaravelEndpointResources\EndpointTypes\ControllerEndpointType;
use Spatie\LaravelEndpointResources\EndpointTypes\EndpointType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

final class EndpointResource extends JsonResource
{
    /** @var \Illuminate\Support\Collection */
    private $endPointTypes;

    /** @var \Illuminate\Database\Eloquent\Model */
    private $model;

    public function __construct(?Model $model)
    {
        parent::__construct($model);

        $this->endPointTypes = new Collection();
        $this->model = $model;
    }

    public function toArray($request)
    {
        return $this->endPointTypes->mapWithKeys(function (EndPointType $endpointType) {
            return $endpointType->getEndpoints($this->model);
        });
    }

    public function addController(string $controller, array $parameters = null): EndpointResource
    {
        $providedParameters = $parameters ?? request()->route()->parameters();

        $this->endPointTypes->push(new ControllerEndpointType(
            $controller,
            $providedParameters
        ));

        return $this;
    }

    public function addAction(string $httpVerb, array $action, array $parameters = null): EndpointResource
    {
        $this->endPointTypes->push(new ActionEndpointType(
            $httpVerb,
            $action,
            $parameters
        ));

        return $this;
    }
}
