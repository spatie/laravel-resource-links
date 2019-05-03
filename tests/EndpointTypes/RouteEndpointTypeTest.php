<?php

namespace Spatie\LaravelEndpointResources\Tests\EndpointTypes;

use Illuminate\Support\Arr;
use Spatie\LaravelEndpointResources\EndpointTypes\RouteEndpointType;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestController;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestModel;
use Spatie\LaravelEndpointResources\Tests\Fakes\SecondTestModel;
use Spatie\LaravelEndpointResources\Tests\TestCase;

final class RouteEndpointTypeTest extends TestCase
{
    /** @var \Spatie\LaravelEndpointResources\Tests\Fakes\TestModel */
    private $testModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testModel = TestModel::create([
            'id' => 1,
            'name' => 'Dumbo',
        ]);
    }
    
    /** @test */
    public function it_can_create_an_route_endpoint_type()
    {
        $route = $this->fakeRouter->route('GET', '', [TestController::class, 'index']);

        $endpointType = new RouteEndpointType($route);

        $endpoints = $endpointType->getEndpoints();

        $this->assertEquals([
            'index' => [
                'method' => 'GET',
                'action' => action([TestController::class, 'index'])
            ]
        ], $endpoints);
    }

    /** @test */
    public function it_can_create_an_route_endpoint_type_with_parameters()
    {
        $route = $this->fakeRouter->route('GET', '{testModel}', [TestController::class, 'show']);

        $endpointType = new RouteEndpointType($route);

        $endpoints = $endpointType->getEndpoints($this->testModel);

        $this->assertEquals([
            'show' => [
                'method' => 'GET',
                'action' => action([TestController::class, 'show'], $this->testModel)
            ]
        ], $endpoints);
    }
    
    /** @test */
    public function it_will_try_to_resolve_parameters_for_the_model()
    {
        $route = $this->fakeRouter->route('GET', '{testModel}', [TestController::class, 'show']);

        $endpointType = new RouteEndpointType($route, [$this->testModel]);

        $endpoints = $endpointType->getEndpoints();

        $this->assertEquals([
            'show' => [
                'method' => 'GET',
                'action' => action([TestController::class, 'show'], $this->testModel)
            ]
        ], $endpoints);
    }

    /** @test */
    public function it_will_choose_the_correct_method_for_routing()
    {
        $route = $this->fakeRouter->route(['GET', 'HEAD'], '', [TestController::class, 'index']);

        $endpointType = new RouteEndpointType($route);

        $endpoints = $endpointType->getEndpoints();

        $this->assertEquals([
            'index' => [
                'method' => 'GET',
                'action' => action([TestController::class, 'index'])
            ]
        ], $endpoints);
    }

    /** @test */
    public function it_can_combine_the_resource_model_and_parameters_for_binding_to_routes()
    {
        $route = $this->fakeRouter->route('GET', '{testModel}/{secondTestModel}', [TestController::class, 'attach']);

        $secondTestModel = SecondTestModel::create([
            'id' => 2,
            'name' => 'Phono'
        ]);

        $endpointType = new RouteEndpointType($route, [$secondTestModel]);

        $endpoints = $endpointType->getEndpoints($this->testModel);

        $this->assertEquals([
            'attach' => [
                'method' => 'GET',
                'action' => action(
                    [TestController::class, 'attach'],
                    [$this->testModel, $secondTestModel]
                ),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_works_nicely_with_route_defaults()
    {
        $route = $this->fakeRouter->route('GET', '{testModel}/{secondTestModel}', [TestController::class, 'attach']);

        $secondTestModel = SecondTestModel::create([
            'id' => 2,
            'name' => 'Phono'
        ]);

        app('url')->defaults(['secondTestModel' => $secondTestModel->id]);

        $endpointType = new RouteEndpointType($route);

        $endpoints = $endpointType->getEndpoints($this->testModel);

        $this->assertEquals([
            'attach' => [
                'method' => 'GET',
                'action' => action(
                    [TestController::class, 'attach'],
                    [$this->testModel]
                ),
            ],
        ], $endpoints);
    }
}
