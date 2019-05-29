<?php

namespace Spatie\LaravelEndpointResources\Tests\Fakes;

class TestInvokableController
{
    public $endpointName = 'sync';

    public function __invoke(TestModel $testModel)
    {
    }
}
