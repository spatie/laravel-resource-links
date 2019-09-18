<?php

namespace Spatie\ResourceLinks\Tests\Fakes;

class TestInvokableController
{
    public function __invoke(TestModel $testModel)
    {
    }
}
