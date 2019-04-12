<?php

namespace Spatie\LaravelEndpointResources\Tests\Dummy;

use Illuminate\Http\Request;

final class DummyController
{
    public $endPointMethods = ['index', 'show', 'attach', 'switch', 'execute', 'update'];
    public $globalEndPointMethods = ['index', 'show', 'attach', 'switch', 'execute', 'update'];

    public function index()
    {
        return 'DummyModel list';
    }

    public function show(DummyModel $dummyModel)
    {
        return $dummyModel->name;
    }

    public function attach(PhonyModel $phonyModel, DummyModel $dummyModel)
    {
        return "{$phonyModel->name} {$dummyModel->name}";
    }

    public function switch(DummyModel $dummyModel, DummyModel $otherDummyModel)
    {
        return "{$dummyModel->name} {$otherDummyModel->name}";
    }

    public function execute(DummyModel $dummyModel, string $action)
    {
        return "{$dummyModel->name} execute {$action}";
    }

    public function update(Request $request, DummyModel $dummyModel)
    {
        return $dummyModel->name;
    }
}
