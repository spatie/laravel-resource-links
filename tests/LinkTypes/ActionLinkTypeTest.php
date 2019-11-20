<?php

namespace Spatie\ResourceLinks\Tests\LinkTypes;

use Exception;
use Spatie\ResourceLinks\LinkTypes\ActionLinkType;
use Spatie\ResourceLinks\Serializers\LayeredExtendedLinkSerializer;
use Spatie\ResourceLinks\Tests\Fakes\SecondTestModel;
use Spatie\ResourceLinks\Tests\Fakes\TestController;
use Spatie\ResourceLinks\Tests\Fakes\TestInvokableController;
use Spatie\ResourceLinks\Tests\Fakes\TestModel;
use Spatie\ResourceLinks\Tests\TestCase;

class ActionLinkTypeTest extends TestCase
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
    public function it_will_deduce_the_route_http_verb()
    {
        $action = [TestController::class, 'index'];

        $this->fakeRouter->get('', $action);

        $links = ActionLinkType::make($action)->getLinks();

        $this->assertEquals([
            'index' => [
                'method' => 'GET',
                'action' => action($action),
            ],
        ], $links);
    }

    /** @test */
    public function it_can_create_an_action_links_type()
    {
        $action = [TestController::class, 'index'];

        $this->fakeRouter->get('', $action);

        $links = ActionLinkType::make($action)->getLinks();

        $this->assertEquals([
            'index' => [
                'method' => 'GET',
                'action' => action($action),
            ],
        ], $links);
    }

    /** @test */
    public function it_can_create_an_action_link_type_with_a_model_as_resource()
    {
        $action = [TestController::class, 'show'];

        $this->fakeRouter->get('{testModel}', $action);

        $links = ActionLinkType::make($action)->getLinks($this->testModel);

        $this->assertEquals([
            'show' => [
                'method' => 'GET',
                'action' => action($action, $this->testModel),
            ],
        ], $links);
    }

    /** @test */
    public function it_will_not_overwrite_a_model_given_as_resource()
    {
        $action = [TestController::class, 'show'];

        $this->fakeRouter->get('{testModel}', $action);

        $otherTestModel = TestModel::create([
            'id' => 2,
            'name' => 'OtherTestModel',
        ]);

        $links = ActionLinkType::make($action)
            ->parameters([$otherTestModel])
            ->getLinks($this->testModel);

        $this->assertEquals([
            'show' => [
                'method' => 'GET',
                'action' => action($action, $this->testModel),
            ],
        ], $links);
    }

    /** @test */
    public function it_can_combine_model_and_parameters_for_binding_to_routes()
    {
        $action = [TestController::class, 'copy'];

        $this->fakeRouter->get('{testModel}/{secondTestModel}', $action);

        $secondTestModel = SecondTestModel::create([
            'id' => 2,
            'name' => 'secondTestModel',
        ]);

        $links = ActionLinkType::make($action)
            ->httpVerb('GET')
            ->parameters([$secondTestModel])
            ->getLinks($this->testModel);

        $this->assertEquals([
            'copy' => [
                'method' => 'GET',
                'action' => action(
                    $action,
                    [$this->testModel, $secondTestModel]
                ),
            ],
        ], $links);
    }

    /** @test */
    public function it_will_throw_an_exception_when_a_route_was_not_found()
    {
        $this->expectException(Exception::class);

        $action = [TestController::class, 'index'];

        ActionLinkType::make($action)->getLinks();
    }

    /** @test */
    public function it_can_prefix_an_action_link()
    {
        $action = [TestController::class, 'index'];

        $this->fakeRouter->get('', $action);

        $links = ActionLinkType::make($action)
            ->prefix('this-')
            ->getLinks();

        $this->assertEquals([
            'this-index' => [
                'method' => 'GET',
                'action' => action($action),
            ],
        ], $links);
    }

    /** @test */
    public function it_can_change_the_serializer_to_layered()
    {
        $action = [TestController::class, 'index'];

        $this->fakeRouter->get('', $action);

        $links = ActionLinkType::make($action)
            ->serializer(LayeredExtendedLinkSerializer::class)
            ->getLinks();

        $layeredLinks = ActionLinkType::make($action)
            ->serializer(LayeredExtendedLinkSerializer::class)
            ->prefix('tests')
            ->getLinks();

        $this->assertEquals([
            'index' => [
                'method' => 'GET',
                'action' => action($action),
            ],
        ], $links);

        $this->assertEquals([
            'tests' => [
                'index' => [
                    'method' => 'GET',
                    'action' => action($action),
                ],
            ],
        ], $layeredLinks);
    }

    /** @test */
    public function it_will_generate_links_for_invokable_controllers()
    {
        $action = TestInvokableController::class;

        $this->fakeRouter->invokableGet('{testModel}', $action);

        $linkType = ActionLinkType::make(TestInvokableController::class);

        $links = $linkType->getLinks($this->testModel);

        $this->assertEquals([
            'invoke' => [
                'method' => 'GET',
                'action' => action($action, $this->testModel),
            ],
        ], $links);
    }

    /** @test */
    public function it_will_also_accept_an_invokable_controller_in_an_array()
    {
        $action = TestInvokableController::class;

        $this->fakeRouter->invokableGet('{testModel}', $action);

        $linkType = ActionLinkType::make([TestInvokableController::class]);

        $links = $linkType->getLinks($this->testModel);

        $this->assertEquals([
            'invoke' => [
                'method' => 'GET',
                'action' => action($action, $this->testModel),
            ],
        ], $links);
    }

    /** @test */
    public function it_can_add_a_query_string()
    {
        $action = [TestController::class, 'index'];

        $this->fakeRouter->get('', $action);

        $links = ActionLinkType::make($action)->query('filter=disabled')->getLinks();

        $this->assertEquals([
            'index' => [
                'method' => 'GET',
                'action' => action($action).'?filter=disabled',
            ],
        ], $links);
    }
}
