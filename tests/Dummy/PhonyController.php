<?php

namespace Spatie\LaravelEndpointResources\Tests\Dummy;

use Illuminate\Http\Request;

final class PhonyController
{
    public $endPointMethods = ['endpoint'];
    public $globalEndPointMethods = ['globalEndpoint'];

    public function endpoint(DummyModel $model)
    {
        return '';
    }

    public function nonEndpoint(DummyModel $model)
    {
        return '';
    }

    public function globalEndpoint()
    {
        return '';
    }

    public function nonGlobalEndpoint()
    {
        return '';
    }
}
