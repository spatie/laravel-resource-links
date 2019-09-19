<?php

namespace Spatie\ResourceLinks\Tests\LinkTypes;

use Spatie\ResourceLinks\LinkTypes\ControllerLinkType;
use Spatie\ResourceLinks\LinkTypes\InvokableControllerLinkType;
use Spatie\ResourceLinks\Tests\Fakes\TestController;
use Spatie\ResourceLinks\Tests\Fakes\TestInvokableCollectionController;
use Spatie\ResourceLinks\Tests\Fakes\TestInvokableController;
use Spatie\ResourceLinks\Tests\Fakes\TestModel;
use Spatie\ResourceLinks\Tests\TestCase;

class InvokableControllerLinkTypeTest extends TestCase
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
    public function it_will_generate_links_for_invokable_controllers()
    {
        $action = TestInvokableController::class;

        $this->fakeRouter->invokableGet('{testModel}', $action);

        $linkType = InvokableControllerLinkType::make(TestInvokableController::class);

        $links = $linkType->getLinks($this->testModel);

        $this->assertEquals([
            'invoke' => [
                'method' => 'GET',
                'action' => action($action, $this->testModel),
            ],
        ], $links);
    }
    
    /** @test */
    public function it_can_name_a_link()
    {
        $action = TestInvokableController::class;

        $this->fakeRouter->invokableGet('{testModel}', $action);

        $links = InvokableControllerLinkType::make($action)
            ->name('purge')
            ->getLinks($this->testModel);

        $this->assertEquals([
            'purge' => [
                'method' => 'GET',
                'action' => action(TestInvokableController::class, $this->testModel),
            ],
        ], $links);
    }

    /** @test */
    public function it_can_prefix_links()
    {
        $action = TestInvokableController::class;

        $this->fakeRouter->invokableGet('{testModel}', $action);

        $links = InvokableControllerLinkType::make($action)
            ->prefix('this-')
            ->getLinks($this->testModel);

        $this->assertEquals([
            'this-invoke' => [
                'method' => 'GET',
                'action' => action($action, $this->testModel),
            ],
        ], $links);
    }
}
