<?php

namespace Spatie\LaravelEndpointResources\Tests\Fakes;

use Illuminate\Http\Request;

final class TestController
{
    public $endPointMethods = ['index', 'show', 'attach', 'switch', 'execute', 'update'];
    public $globalEndPointMethods = ['index', 'show', 'attach', 'switch', 'execute', 'update'];

    public function index()
    {
        return 'DummyModel list';
    }

    public function show(TestModel $testModel)
    {
        return $testModel->name;
    }

    public function attach(SecondTestModel $secondTestModel, TestModel $testModel)
    {
        return "{$secondTestModel->name} {$testModel->name}";
    }

    public function switch(TestModel $testModel, TestModel $otherTestModel)
    {
        return "{$testModel->name} {$otherTestModel->name}";
    }

    public function execute(TestModel $testModel, string $action)
    {
        return "{$testModel->name} execute {$action}";
    }

    public function update(Request $request, TestModel $testModel)
    {
        return $testModel->name;
    }
}
