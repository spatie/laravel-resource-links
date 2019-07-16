<?php

namespace Spatie\LaravelResourceEndpoints\Tests\Fakes;

use Illuminate\Http\Request;

class TestController
{
    public function index()
    {
    }

    public function show(TestModel $testModel)
    {
    }

    public function edit(TestModel $testModel, string $action)
    {
    }

    public function update(Request $request, TestModel $testModel)
    {
    }

    public function copy(SecondTestModel $secondTestModel, TestModel $testModel)
    {

    }

    public function sync(TestModel $testModel, TestModel $otherTestModel)
    {

    }

    public function read($withoutType)
    {

    }
}
