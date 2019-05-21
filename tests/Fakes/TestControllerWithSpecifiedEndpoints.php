<?php

namespace Spatie\LaravelEndpointResources\Tests\Fakes;

class TestControllerWithSpecifiedEndpoints
{
    public $endPointMethods = ['endpoint'];
    public $collectionEndPointMethods = ['collectionEndpoint'];

    public function endpoint(TestModel $testModel)
    {
    }

    public function nonEndpoint(TestModel $testModel)
    {
    }

    public function collectionEndpoint()
    {
    }

    public function nonCollectionEndpoint()
    {
    }

    public function endpointWithTwoParameters(SecondTestModel $secondTestModel, TestModel $testModel)
    {
    }

    public function endpointWithTwoIdenticalParameters(TestModel $testModel, TestModel $otherTestModel)
    {
    }

    public function endpointWithoutTypes($withoutType)
    {
    }
}
