<?php

namespace Spatie\ResourceLinks\Tests;

use Spatie\ResourceLinks\Links;
use Spatie\ResourceLinks\LinkTypes\ActionLinkType;
use Spatie\ResourceLinks\LinkTypes\ControllerLinkType;
use Spatie\ResourceLinks\LinkTypes\InvokableControllerLinkType;
use Spatie\ResourceLinks\Tests\Fakes\TestController;
use Spatie\ResourceLinks\Tests\Fakes\TestInvokableCollectionController;
use Spatie\ResourceLinks\Tests\Fakes\TestInvokableController;

class LinksTest extends TestCase
{
    /** @test */
    public function it_can_add_an_action()
    {
        $links = new Links();

        $links->action([TestInvokableController::class]);

        $this->assertTrue(
            $links->getLinkTypes()
                ->contains(ActionLinkType::make([TestInvokableController::class]))
        );
    }
    
    /** @test */
    public function it_can_add_a_controller()
    {
        $links = new Links();

        $links->controller(TestController::class);

        $this->assertTrue(
            $links->getLinkTypes()
                ->contains(ControllerLinkType::make(TestController::class))
        );
    }

    /** @test */
    public function it_can_add_an_invokable_controller()
    {
        $links = new Links();

        $links->controller(TestInvokableController::class);

        $this->assertTrue(
            $links->getLinkTypes()
                ->contains(ActionLinkType::make([TestInvokableController::class]))
        );
    }
    
    /** @test */
    public function it_can_add_a_links_group()
    {
        $links = new Links();
        $links->controller(TestInvokableController::class);

        $secondLinks = new Links();
        $secondLinks->action([TestController::class, 'index']);

        $links->links($secondLinks);

        $this->assertTrue(
            $links->getLinkTypes()
                ->contains(ActionLinkType::make([TestInvokableController::class]))
        );
        $this->assertTrue(
            $links->getLinkTypes()
                ->contains(ActionLinkType::make([TestController::class, 'index']))
        );
    }
}
