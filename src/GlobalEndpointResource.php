<?php

namespace Spatie\LaravelEndpointResources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Spatie\LaravelEndpointResources\EndpointTypes\ActionEndpointType;
use Spatie\LaravelEndpointResources\EndpointTypes\ControllerEndpointType;
use Spatie\LaravelEndpointResources\EndpointTypes\EndpointType;

final class GlobalEndpointResource extends JsonResource
{
    use StoresEndpointTypes;

    /** @var \Illuminate\Support\Collection */
    private $endPointTypes;

    public function __construct()
    {
        parent::__construct(null);

        $this->endPointTypes = new Collection();
    }

    public function toArray($request)
    {
        return $this->endPointTypes->mapWithKeys(function (EndPointType $endpointType) {
            return $endpointType instanceof ControllerEndpointType
                ? $endpointType->getGlobalEndpoints()
                : $endpointType->getEndpoints(null);
        });
    }
}
