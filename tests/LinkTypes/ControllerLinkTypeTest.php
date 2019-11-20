<?php

namespace Spatie\ResourceLinks\Tests\LinkTypes;

use Exception;
use Spatie\ResourceLinks\LinkTypes\ControllerLinkType;
use Spatie\ResourceLinks\Serializers\LayeredExtendedLinkSerializer;
use Spatie\ResourceLinks\Tests\Fakes\SecondTestModel;
use Spatie\ResourceLinks\Tests\Fakes\TestController;
use Spatie\ResourceLinks\Tests\Fakes\TestModel;
use Spatie\ResourceLinks\Tests\TestCase;

class ControllerLinkTypeTest extends TestCase
{
    /** @var \Spatie\ResourceLinks\Tests\Fakes\TestModel */
    private $testModel;

    /** @var \Spatie\ResourceLinks\Tests\Fakes\SecondTestModel */
    private $secondTestModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testModel = TestModel::create([
            'id' => 1,
            'name' => 'TestModel',
        ]);

        $this->secondTestModel = SecondTestModel::create([
            'id' => 1,
            'name' => 'SecondTestModel',
        ]);
    }

    /** @test */
    public function it_will_create_all_possible_routes_when_a_model_is_available()
    {
        $showAction = [TestController::class, 'show'];
        $updateAction = [TestController::class, 'update'];

        $this->fakeRouter->get('{testModel}', $showAction);
        $this->fakeRouter->route('PATCH', '{testModel}', $updateAction);

        $testModel = TestModel::create([
            'name' => 'TestModel',
        ]);

        $linkType = ControllerLinkType::make(TestController::class);

        $links = $linkType->getLinks($testModel);

        $this->assertEquals([
            'show' => [
                'method' => 'GET',
                'action' => action($showAction, $testModel),
            ],
            'update' => [
                'method' => 'PATCH',
                'action' => action($updateAction, $testModel),
            ],
        ], $links);
    }

    /** @test */
    public function it_can_specify_which_methods_to_use()
    {
        $indexAction = [TestController::class, 'index'];
        $showAction = [TestController::class, 'show'];

        $this->fakeRouter->get('', $indexAction);
        $this->fakeRouter->get('{testModel}', $showAction);

        $links = ControllerLinkType::make(TestController::class)
            ->methods(['index', 'show'])
            ->getLinks($this->testModel);

        $this->assertEquals([
            'show' => [
                'method' => 'GET',
                'action' => action($showAction, $this->testModel),
            ],
            'index' => [
                'method' => 'GET',
                'action' => action($indexAction),
            ],
        ], $links);
    }

    /** @test */
    public function it_can_alias_links()
    {
        $indexAction = [TestController::class, 'index'];

        $this->fakeRouter->get('', $indexAction);

        $links = ControllerLinkType::make(TestController::class)
            ->methods(['index'])
            ->names([
                'index' => 'home',
            ])
            ->getLinks($this->testModel);

        $this->assertEquals([
            'home' => [
                'method' => 'GET',
                'action' => action($indexAction),
            ],
        ], $links);
    }

    /** @test */
    public function it_can_prefix_links()
    {
        $indexAction = [TestController::class, 'index'];

        $this->fakeRouter->get('', $indexAction);

        $links = ControllerLinkType::make(TestController::class)
            ->methods(['index'])
            ->prefix('this-')
            ->getLinks($this->testModel);

        $this->assertEquals([
            'this-index' => [
                'method' => 'GET',
                'action' => action($indexAction),
            ],
        ], $links);
    }

    /** @test */
    public function it_will_merge_layered_formatted_links()
    {
        $this->fakeRouter->get('/users/{testModel}', [TestController::class, 'show']);
        $this->fakeRouter->put('/users/{testModel}', [TestController::class, 'update']);

        $links = ControllerLinkType::make(TestController::class)
            ->methods(['show', 'update'])
            ->prefix('filter')
            ->serializer(LayeredExtendedLinkSerializer::class)
            ->getLinks($this->testModel);

        $this->assertEquals([
            'filter' => [
                'show' => [
                    'method' => 'GET',
                    'action' => action([TestController::class, 'show'], $this->testModel),
                ],
                'update' => [
                    'method' => 'PUT',
                    'action' => action([TestController::class, 'update'], $this->testModel),
                ],
            ],
        ], $links);
    }

    /** @test */
    public function a_controller_link_type_can_have_an_empty_links_array()
    {
        $links = ControllerLinkType::make(TestController::class)
            ->getLinks($this->testModel);

        $this->assertEquals([], $links);
    }

    /** @test */
    public function there_are_different_ways_to_define_parameters()
    {
        $action = [TestController::class, 'copy'];

        $this->fakeRouter->get('{testModel}/{secondTestModel}', $action);

        $expected = [
            'copy' => [
                'method' => 'GET',
                'action' => action($action, [$this->testModel, $this->secondTestModel]),
            ],
        ];

        $this->assertEquals(
            $expected,
            ControllerLinkType::make(TestController::class)
                ->methods(['copy'])
                ->parameters($this->secondTestModel)
                ->getLinks($this->testModel)
        );

        $this->assertEquals(
            $expected,
            ControllerLinkType::make(TestController::class)
                ->methods(['copy'])
                ->parameters($this->testModel)
                ->getLinks($this->secondTestModel)
        );

        $this->assertEquals(
            $expected,
            ControllerLinkType::make(TestController::class)
                ->methods(['copy'])
                ->parameters($this->secondTestModel, $this->testModel)
                ->getLinks()
        );

        $this->assertEquals(
            $expected,
            ControllerLinkType::make(TestController::class)
                ->methods(['copy'])
                ->parameters([$this->secondTestModel, $this->testModel])
                ->getLinks()
        );

        $this->assertEquals(
            $expected,
            ControllerLinkType::make(TestController::class)
                ->methods(['copy'])
                ->parameters($this->testModel)
                ->parameters($this->secondTestModel)
                ->getLinks()
        );
    }

    /** @test */
    public function it_can_use_a_query_string()
    {
        $indexAction = [TestController::class, 'index'];

        $this->fakeRouter->get('', $indexAction);

        $links = ControllerLinkType::make(TestController::class)
            ->methods(['index'])
            ->query('filter=disabled')
            ->getLinks($this->testModel);

        $this->assertEquals([
            'index' => [
                'method' => 'GET',
                'action' => action($indexAction).'?filter=disabled',
            ],
        ], $links);
    }

    /** @test */
    public function it_will_throw_an_exception_if_a_specified_method_does_not_exist_on_the_controller()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Resource links tried to check non-existing method does-not-exist on controller: Spatie\ResourceLinks\Tests\Fakes\TestController');

        ControllerLinkType::make(TestController::class)
            ->methods(['does-not-exist'])
            ->getLinks($this->testModel);
    }
}
