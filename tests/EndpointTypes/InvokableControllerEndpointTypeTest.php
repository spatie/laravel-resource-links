<?php

namespace Spatie\LaravelEndpointResources\Tests\EndpointTypes;

use Spatie\LaravelEndpointResources\EndpointTypes\ControllerEndpointType;
use Spatie\LaravelEndpointResources\EndpointTypes\InvokableControllerEndpointType;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestController;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestInvokableController;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestModel;
use Spatie\LaravelEndpointResources\Tests\TestCase;

class InvokableControllerEndpointTypeTest extends TestCase
{
    /** @var \Spatie\LaravelEndpointResources\Tests\Fakes\TestModel */
    private $testModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testModel = TestModel::create([
            'id' => 1,
            'name' => 'TestModel',
        ]);
    }

    /** @test */
    public function it_will_generate_endpoints_for_invokable_controllers()
    {
        $action = TestInvokableController::class;

        $this->fakeRouter->invokableGet('{testModel}', $action);

        $endpointType = InvokableControllerEndpointType::make(TestInvokableController::class);

        $endpoints = $endpointType->getEndpoints($this->testModel);

        $this->assertEquals([
            'sync' => [
                'method' => 'GET',
                'action' => action($action, $this->testModel),
            ],
        ], $endpoints);
    }
}
