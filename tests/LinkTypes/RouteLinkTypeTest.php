<?php

namespace Spatie\ResourceLinks\Tests\LinkTypes;

use Spatie\ResourceLinks\LinkTypes\RouteLinkType;
use Spatie\ResourceLinks\Serializers\LinkSerializer;
use Spatie\ResourceLinks\Tests\Fakes\SecondTestModel;
use Spatie\ResourceLinks\Tests\Fakes\TestController;
use Spatie\ResourceLinks\Tests\Fakes\TestModel;
use Spatie\ResourceLinks\Tests\TestCase;

class RouteLinkTypeTest extends TestCase
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
    public function it_can_create_a_route_link_type()
    {
        $action = [TestController::class, 'index'];

        $route = $this->fakeRouter->get('', $action);

        $links = RouteLinkType::make($route)->getLinks();

        $this->assertEquals([
            'index' => [
                'method' => 'GET',
                'action' => action($action),
            ],
        ], $links);
    }

    /** @test */
    public function it_can_create_an_route_link_type_with_parameters()
    {
        $action = [TestController::class, 'show'];

        $route = $this->fakeRouter->get('{testModel}', $action);

        $links = RouteLinkType::make($route)->getLinks($this->testModel);

        $this->assertEquals([
            'show' => [
                'method' => 'GET',
                'action' => action($action, $this->testModel),
            ],
        ], $links);
    }

    /** @test */
    public function it_will_try_to_resolve_parameters_for_the_model()
    {
        $action = [TestController::class, 'show'];

        $route = $this->fakeRouter->get('{testModel}', $action);

        $links = RouteLinkType::make($route)
            ->parameters([$this->testModel])
            ->getLinks();

        $this->assertEquals([
            'show' => [
                'method' => 'GET',
                'action' => action($action, $this->testModel),
            ],
        ], $links);
    }

    /** @test */
    public function it_will_choose_the_correct_method_for_routing()
    {
        $action = [TestController::class, 'index'];

        $route = $this->fakeRouter->route(['GET', 'HEAD'], '', $action);

        $links = RouteLinkType::make($route)->getLinks();

        $this->assertEquals([
            'index' => [
                'method' => 'GET',
                'action' => action($action),
            ],
        ], $links);
    }

    /** @test */
    public function it_can_combine_the_resource_model_and_parameters_for_binding_to_routes()
    {
        $action = [TestController::class, 'copy'];

        $route = $this->fakeRouter->get('{testModel}/{secondTestModel}', $action);

        $secondTestModel = SecondTestModel::create([
            'id' => 2,
            'name' => 'secondTestModel',
        ]);

        $links = RouteLinkType::make($route)
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
    public function it_works_nicely_with_route_defaults()
    {
        $action = [TestController::class, 'copy'];

        $route = $this->fakeRouter->get('{testModel}/{secondTestModel}', $action);

        $secondTestModel = SecondTestModel::create([
            'id' => 2,
            'name' => 'secondTestModel',
        ]);

        app('url')->defaults(['secondTestModel' => $secondTestModel->id]);

        $links = RouteLinkType::make($route)->getLinks($this->testModel);

        $this->assertEquals([
            'copy' => [
                'method' => 'GET',
                'action' => action(
                    $action,
                    [$this->testModel]
                ),
            ],
        ], $links);
    }

    /** @test */
    public function it_can_prefix_links()
    {
        $action = [TestController::class, 'index'];

        $route = $this->fakeRouter->get('', $action);

        $links = RouteLinkType::make($route)
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
    public function it_can_change_the_serializer()
    {
        $action = [TestController::class, 'index'];

        $route = $this->fakeRouter->get('', $action);

        $links = RouteLinkType::make($route)
            ->serializer(LinkSerializer::class)
            ->getLinks();

        $this->assertEquals([
            'index' => action($action),
        ], $links);
    }

    /** @test */
    public function it_uses_the_global_serializer_when_no_serializer_was_explicitly_defined()
    {
        $action = [TestController::class, 'index'];

        $route = $this->fakeRouter->get('', $action);

        app(config()->set('resource-links.serializer', LinkSerializer::class));

        $links = RouteLinkType::make($route)->getLinks();

        $this->assertEquals(['index' => action($action)], $links);
    }

    /** @test */
    public function a_not_constructable_route_has_its_missing_parameters_in_brackets()
    {
        $action = [TestController::class, 'copy'];

        $route = $this->fakeRouter->get('/{secondTestModel}/{testModel}', $action);

        $links = RouteLinkType::make($route)
            ->getLinks($this->testModel);

        $this->assertEquals([
            'copy' => [
                'method' => 'GET',
                'action' => 'http://localhost/{secondTestModel}/1',
            ],
        ], $links);
    }

    /** @test */
    public function a_route_without_provided_parameters_can_still_be_constructed()
    {
        $action = [TestController::class, 'copy'];

        $route = $this->fakeRouter->get('/{secondTestModel}/{testModel}', $action);

        $links = RouteLinkType::make($route)->getLinks();

        $this->assertEquals([
            'copy' => [
                'method' => 'GET',
                'action' => 'http://localhost/{secondTestModel}/{testModel}',
            ],
        ], $links);
    }

    /** @test */
    public function it_plays_nice_with_routes_combining_parameters_with_and_without_type()
    {
        $action = [TestController::class, 'edit'];

        $route = $this->fakeRouter->get('{testModel}/{action}', $action);

        $links = RouteLinkType::make($route)
            ->parameters([
                'action' => 'dump-and-die',
            ])
            ->getLinks($this->testModel);

        $this->assertEquals([
            'edit' => [
                'method' => 'GET',
                'action' => action($action, [
                    'testModel' => $this->testModel,
                    'action' => 'dump-and-die',
                ]),
            ],
        ], $links);
    }

    /** @test */
    public function it_can_use_a_query_string()
    {
        $action = [TestController::class, 'index'];

        $route = $this->fakeRouter->get('', $action);

        $links = RouteLinkType::make($route)->query('filter=disabled')->getLinks();

        $this->assertEquals([
            'index' => [
                'method' => 'GET',
                'action' => action($action).'?filter=disabled',
            ],
        ], $links);
    }
}
