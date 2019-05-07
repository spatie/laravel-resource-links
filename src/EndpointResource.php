<?php

namespace Spatie\LaravelEndpointResources;

use Spatie\LaravelEndpointResources\EndpointTypes\ControllerEndpointType;
use Spatie\LaravelEndpointResources\EndpointTypes\EndpointType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

final class EndpointResource extends JsonResource
{
    use StoresEndpointTypes;

    /** @var \Illuminate\Support\Collection */
    private $endPointTypes;

    /** @var \Illuminate\Database\Eloquent\Model */
    private $model;

    /** @var bool  */
    private $includeGlobalEndpoints;

    public function __construct(Model $model = null, bool $includeGlobalEndpoints)
    {
        parent::__construct($model);

        $this->endPointTypes = new Collection();
        $this->model = $model;
        $this->includeGlobalEndpoints = $includeGlobalEndpoints;
    }

    public function toArray($request)
    {
        return $this->endPointTypes->mapWithKeys(function (EndPointType $endpointType) {
            if($this->includeGlobalEndpoints && $endpointType instanceof ControllerEndpointType){
                return array_merge(
                    $endpointType->getEndpoints($this->model),
                    $endpointType->getGlobalEndpoints()
                );
            }

            return $endpointType->getEndpoints($this->model);
        });
    }
}
