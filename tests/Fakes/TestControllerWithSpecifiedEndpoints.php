<?php

namespace Spatie\LaravelEndpointResources\Tests\Fakes;

final class TestControllerWithSpecifiedEndpoints
{
    public $endPointMethods = ['endpoint'];
    public $globalEndPointMethods = ['globalEndpoint'];

    public function endpoint(TestModel $testModel) {}

    public function nonEndpoint(TestModel $testModel) {}

    public function globalEndpoint() {}

    public function nonGlobalEndpoint() {}

    public function endpointWithTwoParameters(SecondTestModel $secondTestModel, TestModel $testModel) {}

    public function endpointWithTwoIdenticalParameters(TestModel $testModel, TestModel $otherTestModel) {}

    public function endpointWithoutTypes($withoutType) {}
}
