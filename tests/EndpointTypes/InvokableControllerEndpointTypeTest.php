<?php

namespace Spatie\ResourceLinks\Tests\EndpointTypes;

use Spatie\ResourceLinks\EndpointTypes\ControllerEndpointType;
use Spatie\ResourceLinks\EndpointTypes\InvokableControllerEndpointType;
use Spatie\ResourceLinks\Tests\Fakes\TestController;
use Spatie\ResourceLinks\Tests\Fakes\TestInvokableCollectionController;
use Spatie\ResourceLinks\Tests\Fakes\TestInvokableController;
use Spatie\ResourceLinks\Tests\Fakes\TestModel;
use Spatie\ResourceLinks\Tests\TestCase;

class InvokableControllerEndpointTypeTest extends TestCase
{
    /** @var \Spatie\ResourceLinks\Tests\Fakes\TestModel */
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
            'invoke' => [
                'method' => 'GET',
                'action' => action($action, $this->testModel),
            ],
        ], $endpoints);
    }
    
    /** @test */
    public function it_can_name_an_endpoint()
    {
        $action = TestInvokableController::class;

        $this->fakeRouter->invokableGet('{testModel}', $action);

        $endpoints = InvokableControllerEndpointType::make($action)
            ->name('purge')
            ->getEndpoints($this->testModel);

        $this->assertEquals([
            'purge' => [
                'method' => 'GET',
                'action' => action(TestInvokableController::class, $this->testModel),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_can_prefix_endpoints()
    {
        $action = TestInvokableController::class;

        $this->fakeRouter->invokableGet('{testModel}', $action);

        $endpoints = InvokableControllerEndpointType::make($action)
            ->prefix('this-')
            ->getEndpoints($this->testModel);

        $this->assertEquals([
            'this-invoke' => [
                'method' => 'GET',
                'action' => action($action, $this->testModel),
            ],
        ], $endpoints);
    }
}
