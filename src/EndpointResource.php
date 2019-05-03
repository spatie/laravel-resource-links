<?php

namespace Spatie\LaravelEndpointResources;

use Spatie\LaravelEndpointResources\EndpointTypes\ActionEndpointType;
use Spatie\LaravelEndpointResources\EndpointTypes\ControllerEndpointType;
use Spatie\LaravelEndpointResources\EndpointTypes\EndpointType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

// TODO: - rename Dummy en Phony to something suitable
// TODO: - reduce the amount of routes in DummyController
// Todo: - Fix ActionEndpointType tests
// Todo: - Make it possible to add a non array as parameters for endpoints
// Todo: - Invokable controllers support -> this is hard

final class EndpointResource extends JsonResource
{
    use StoresEndpointTypes;

    /** @var \Illuminate\Support\Collection */
    private $endPointTypes;

    /** @var \Illuminate\Database\Eloquent\Model */
    private $model;

    public function __construct(Model $model = null)
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
}
