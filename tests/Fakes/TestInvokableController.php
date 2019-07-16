<?php

namespace Spatie\LaravelResourceEndpoints\Tests\Fakes;

class TestInvokableController
{
    public function __invoke(TestModel $testModel)
    {
    }
}
