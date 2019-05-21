<?php

namespace Spatie\LaravelEndpointResources\Tests\Fakes;

class TestInvokableController
{
    public $endPointMethod = 'sync';

    public function __invoke(TestModel $testModel){}
}
