<?php

namespace Spatie\LaravelEndpointResources\EndpointTypes;

use Illuminate\Database\Eloquent\Model;

abstract class EndpointType
{
    abstract public function getEndpoints(Model $model = null): array;
}
