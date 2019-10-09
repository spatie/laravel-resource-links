<?php

namespace Spatie\ResourceLinks\Tests\Fakes;

use Illuminate\Http\Request;
use Spatie\ResourceLinks\Tests\Fakes\TestModel as Item;
use Spatie\ResourceLinks\Tests\Fakes\FakeResourceCollection as ItemResourceCollection;

class FakeController
{
    public function index()
    {
        $TestModel = new Item;
        $queries = [];
        $TestModel = $TestModel->paginate(2)->appends($queries);
        return new ItemResourceCollection($TestModel);
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
