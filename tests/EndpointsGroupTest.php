<?php

namespace Spatie\LaravelEndpointResources\Tests;

use Spatie\LaravelEndpointResources\EndpointsGroup;
use Spatie\LaravelEndpointResources\EndpointTypes\ActionEndpointType;
use Spatie\LaravelEndpointResources\EndpointTypes\ControllerEndpointType;
use Spatie\LaravelEndpointResources\EndpointTypes\InvokableControllerEndpointType;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestController;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestInvokableCollectionController;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestInvokableController;

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
