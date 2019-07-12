<?php

namespace Spatie\LaravelEndpointResources\Tests\Fakes;

class TestInvokableController
{
    public function __invoke(TestModel $testModel)
    {
    }
}
