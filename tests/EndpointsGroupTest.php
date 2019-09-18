<?php

namespace Spatie\ResourceLinks\Tests;

use Spatie\ResourceLinks\Links;
use Spatie\ResourceLinks\EndpointTypes\ActionEndpointType;
use Spatie\ResourceLinks\EndpointTypes\ControllerEndpointType;
use Spatie\ResourceLinks\EndpointTypes\InvokableControllerEndpointType;
use Spatie\ResourceLinks\Tests\Fakes\TestController;
use Spatie\ResourceLinks\Tests\Fakes\TestInvokableCollectionController;
use Spatie\ResourceLinks\Tests\Fakes\TestInvokableController;

class EndpointsGroupTest extends TestCase
{
    /** @test */
    public function it_can_add_an_action()
    {
        $endpointsGroup = new Links();

        $endpointsGroup->action([TestInvokableController::class]);

        $this->assertTrue(
            $endpointsGroup->getEndpointTypes()
                ->contains(ActionEndpointType::make([TestInvokableController::class]))
        );
    }
    
    /** @test */
    public function it_can_add_a_controller()
    {
        $endpointsGroup = new Links();

        $endpointsGroup->controller(TestController::class);

        $this->assertTrue(
            $endpointsGroup->getEndpointTypes()
                ->contains(ControllerEndpointType::make(TestController::class))
        );
    }

    /** @test */
    public function it_can_add_an_invokable_controller()
    {
        $endpointsGroup = new Links();

        $endpointsGroup->invokableController(TestInvokableController::class);

        $this->assertTrue(
            $endpointsGroup->getEndpointTypes()
                ->contains(InvokableControllerEndpointType::make(TestInvokableController::class))
        );
    }
    
    /** @test */
    public function it_can_add_an_endpoints_group()
    {
        $endpointsGroup = new Links();
        $endpointsGroup->invokableController(TestInvokableController::class);

        $secondEndpointsGroup = new Links();
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
