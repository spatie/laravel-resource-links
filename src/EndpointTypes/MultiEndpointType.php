<?php

namespace Spatie\LaravelEndpointResources\EndpointTypes;

use Illuminate\Database\Eloquent\Model;

interface MultiEndpointType
{
    public function getEndpoints(Model $model = null): array;

    public function getCollectionEndpoints(): array;
}
