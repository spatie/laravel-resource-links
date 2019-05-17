<?php

namespace Spatie\LaravelEndpointResources\Tests\Fakes;

use Illuminate\Http\Request;

final class TestController
{
    public function index() {}

    public function show(TestModel $testModel) {}

    public function edit(TestModel $testModel, string $action) {}

    public function update(Request $request, TestModel $testModel) {}
}
