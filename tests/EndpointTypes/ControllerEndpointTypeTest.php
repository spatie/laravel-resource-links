<?php

namespace Spatie\LaravelResourceEndpoints\Tests\EndpointTypes;

use Spatie\LaravelResourceEndpoints\EndpointTypes\ControllerEndpointType;
use Spatie\LaravelResourceEndpoints\Formatters\LayeredFormatter;
use Spatie\LaravelResourceEndpoints\Tests\Fakes\SecondTestModel;
use Spatie\LaravelResourceEndpoints\Tests\Fakes\TestController;
use Spatie\LaravelResourceEndpoints\Tests\Fakes\TestModel;
use Spatie\LaravelResourceEndpoints\Tests\Fakes\TestControllerWithSpecifiedEndpoints;
use Spatie\LaravelResourceEndpoints\Tests\TestCase;

class ControllerEndpointTypeTest extends TestCase
{
    /** @var \Spatie\LaravelResourceEndpoints\Tests\Fakes\TestModel */
    private $testModel;

    /** @var \Spatie\LaravelResourceEndpoints\Tests\Fakes\SecondTestModel */
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

        $endpointType = ControllerEndpointType::make(TestController::class);

        $endpoints = $endpointType->getEndpoints($testModel);

        $this->assertEquals([
            'show' => [
                'method' => 'GET',
                'action' => action($showAction, $testModel),
            ],
            'update' => [
                'method' => 'PATCH',
                'action' => action($updateAction, $testModel),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_can_specify_which_methods_to_use()
    {
        $indexAction = [TestController::class, 'index'];
        $showAction = [TestController::class, 'show'];

        $this->fakeRouter->get('', $indexAction);
        $this->fakeRouter->get('{testModel}', $showAction);

        $endpoints = ControllerEndpointType::make(TestController::class)
            ->methods(['index', 'show'])
            ->getEndpoints($this->testModel);

        $this->assertEquals([
            'show' => [
                'method' => 'GET',
                'action' => action($showAction, $this->testModel),
            ],
            'index' => [
                'method' => 'GET',
                'action' => action($indexAction),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_can_alias_endpoints()
    {
        $indexAction = [TestController::class, 'index'];

        $this->fakeRouter->get('', $indexAction);

        $endpoints = ControllerEndpointType::make(TestController::class)
            ->methods(['index'])
            ->names([
                'index' => 'home',
            ])
            ->getEndpoints($this->testModel);

        $this->assertEquals([
            'home' => [
                'method' => 'GET',
                'action' => action($indexAction),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_can_prefix_endpoints()
    {
        $indexAction = [TestController::class, 'index'];

        $this->fakeRouter->get('', $indexAction);

        $endpoints = ControllerEndpointType::make(TestController::class)
            ->methods(['index'])
            ->prefix('this-')
            ->getEndpoints($this->testModel);

        $this->assertEquals([
            'this-index' => [
                'method' => 'GET',
                'action' => action($indexAction),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_will_merge_layered_formatted_endpoints()
    {
        $this->fakeRouter->get('/users/{testModel}', [TestController::class, 'show']);
        $this->fakeRouter->put('/users/{testModel}', [TestController::class, 'update']);

        $endpoints = ControllerEndpointType::make(TestController::class)
            ->methods(['show', 'update'])
            ->prefix('filter')
            ->formatter(LayeredFormatter::class)
            ->getEndpoints($this->testModel);

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
        ], $endpoints);
    }

    /** @test */
    public function a_controller_endpoint_type_can_have_an_empty_endpoints_array()
    {
        $endpoints = ControllerEndpointType::make(TestController::class)
            ->getEndpoints($this->testModel);

        $this->assertEquals([], $endpoints);
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

        $this->assertEquals($expected,
            ControllerEndpointType::make(TestController::class)
                ->methods(['copy'])
                ->parameters($this->secondTestModel)
                ->getEndpoints($this->testModel)
        );

        $this->assertEquals($expected,
            ControllerEndpointType::make(TestController::class)
                ->methods(['copy'])
                ->parameters($this->testModel)
                ->getEndpoints($this->secondTestModel)
        );

        $this->assertEquals($expected,
            ControllerEndpointType::make(TestController::class)
                ->methods(['copy'])
                ->parameters($this->secondTestModel, $this->testModel)
                ->getEndpoints()
        );

        $this->assertEquals($expected,
            ControllerEndpointType::make(TestController::class)
                ->methods(['copy'])
                ->parameters([$this->secondTestModel, $this->testModel])
                ->getEndpoints()
        );

        $this->assertEquals($expected,
            ControllerEndpointType::make(TestController::class)
                ->methods(['copy'])
                ->parameters($this->testModel)
                ->parameters($this->secondTestModel)
                ->getEndpoints()
        );
    }
}
