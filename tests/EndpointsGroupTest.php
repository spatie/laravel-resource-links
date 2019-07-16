<?php

namespace Spatie\LaravelResourceEndpoints\Tests;

use Spatie\LaravelResourceEndpoints\EndpointsGroup;
use Spatie\LaravelResourceEndpoints\EndpointTypes\ActionEndpointType;
use Spatie\LaravelResourceEndpoints\EndpointTypes\ControllerEndpointType;
use Spatie\LaravelResourceEndpoints\EndpointTypes\InvokableControllerEndpointType;
use Spatie\LaravelResourceEndpoints\Tests\Fakes\TestController;
use Spatie\LaravelResourceEndpoints\Tests\Fakes\TestInvokableCollectionController;
use Spatie\LaravelResourceEndpoints\Tests\Fakes\TestInvokableController;

class EndpointsGroupTest extends TestCase
{
    /** @test */
    public function it_can_add_an_action()
    {
        $endpointsGroup = new EndpointsGroup();

        $endpointsGroup->action([TestInvokableController::class]);

        $this->assertTrue(
            $endpointsGroup->getEndpointTypes()
                ->contains(ActionEndpointType::make([TestInvokableController::class]))
        );
    }
    
    /** @test */
    public function it_can_add_a_controller()
    {
        $endpointsGroup = new EndpointsGroup();

        $endpointsGroup->controller(TestController::class);

        $this->assertTrue(
            $endpointsGroup->getEndpointTypes()
                ->contains(ControllerEndpointType::make(TestController::class))
        );
    }

    /** @test */
    public function it_can_add_an_invokable_controller()
    {
        $endpointsGroup = new EndpointsGroup();

        $endpointsGroup->invokableController(TestInvokableController::class);

        $this->assertTrue(
            $endpointsGroup->getEndpointTypes()
                ->contains(InvokableControllerEndpointType::make(TestInvokableController::class))
        );
    }

    /** @test */
    public function it_can_add_an_invokable_controller_as_controller()
    {
        $endpointsGroup = new EndpointsGroup();

        $endpointsGroup->controller(TestInvokableController::class);

        $this->assertTrue(
            $endpointsGroup->getEndpointTypes()
                ->contains(InvokableControllerEndpointType::make(TestInvokableController::class))
        );
    }
    
    /** @test */
    public function it_can_add_an_endpoints_group()
    {
        $endpointsGroup = new EndpointsGroup();
        $endpointsGroup->controller(TestInvokableController::class);

        $secondEndpointsGroup = new EndpointsGroup();
        $secondEndpointsGroup->action([TestController::class, 'index']);

        $endpointsGroup->endpointsGroup($secondEndpointsGroup);

        $this->assertTrue(
            $endpointsGroup->getEndpointTypes()
                ->contains(InvokableControllerEndpointType::make(TestInvokableController::class))
        );
        $this->assertTrue(
            $endpointsGroup->getEndpointTypes()
                ->contains(ActionEndpointType::make([TestController::class, 'index']))
        );
    }
}
