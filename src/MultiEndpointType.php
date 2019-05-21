<?php

namespace Spatie\LaravelEndpointResources;

use Illuminate\Database\Eloquent\Model;

interface MultiEndpointType
{
    public function getEndpoints(Model $model = null): array;

    public function getCollectionEndpoints(): array;
}
