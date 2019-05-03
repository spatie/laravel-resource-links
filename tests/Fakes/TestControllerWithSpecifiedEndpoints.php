<?php

namespace Spatie\LaravelEndpointResources\Tests\Fakes;

use Illuminate\Http\Request;

final class TestControllerWithSpecifiedEndpoints
{
    public $endPointMethods = ['endpoint'];
    public $globalEndPointMethods = ['globalEndpoint'];

    public function endpoint(TestModel $testModel)
    {
        return '';
    }

    public function nonEndpoint(TestModel $testModel)
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
