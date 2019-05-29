<?php

namespace Spatie\LaravelEndpointResources\Tests\EndpointTypes;

use Illuminate\Support\Arr;
use Spatie\LaravelEndpointResources\EndpointTypes\RouteEndpointType;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestController;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestControllerWithSpecifiedEndpoints;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestModel;
use Spatie\LaravelEndpointResources\Tests\Fakes\SecondTestModel;
use Spatie\LaravelEndpointResources\Tests\TestCase;

class RouteEndpointTypeTest extends TestCase
{
    /** @var \Spatie\LaravelEndpointResources\Tests\Fakes\TestModel */
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
    public function it_can_create_an_route_endpoint_type()
    {
        $action = [TestController::class, 'index'];

        $route = $this->fakeRouter->get('', $action);

        $endpoints =  RouteEndpointType::make($route)->getEndpoints();

        $this->assertEquals([
            'index' => [
                'method' => 'GET',
                'action' => action($action)
            ]
        ], $endpoints);
    }

    /** @test */
    public function it_can_create_an_route_endpoint_type_with_parameters()
    {
        $action = [TestController::class, 'show'];

        $route = $this->fakeRouter->get('{testModel}', $action);

        $endpoints = RouteEndpointType::make($route)->getEndpoints($this->testModel);

        $this->assertEquals([
            'show' => [
                'method' => 'GET',
                'action' => action($action, $this->testModel)
            ]
        ], $endpoints);
    }
    
    /** @test */
    public function it_will_try_to_resolve_parameters_for_the_model()
    {
        $action = [TestController::class, 'show'];

        $route = $this->fakeRouter->get('{testModel}', $action);

        $endpoints = RouteEndpointType::make($route)
            ->parameters([$this->testModel])
            ->getEndpoints();

        $this->assertEquals([
            'show' => [
                'method' => 'GET',
                'action' => action($action, $this->testModel)
            ]
        ], $endpoints);
    }

    /** @test */
    public function it_will_choose_the_correct_method_for_routing()
    {
        $action = [TestController::class, 'index'];

        $route = $this->fakeRouter->route(['GET', 'HEAD'], '', $action);

        $endpoints = RouteEndpointType::make($route)->getEndpoints();

        $this->assertEquals([
            'index' => [
                'method' => 'GET',
                'action' => action($action)
            ]
        ], $endpoints);
    }

    /** @test */
    public function it_can_combine_the_resource_model_and_parameters_for_binding_to_routes()
    {
        $action = [TestControllerWithSpecifiedEndpoints::class, 'endpointWithTwoParameters'];

        $route = $this->fakeRouter->get('{testModel}/{secondTestModel}', $action);

        $secondTestModel = SecondTestModel::create([
            'id' => 2,
            'name' => 'secondTestModel'
        ]);

        $endpoints = RouteEndpointType::make($route)
            ->parameters([$secondTestModel])
            ->getEndpoints($this->testModel);

        $this->assertEquals([
            'endpointWithTwoParameters' => [
                'method' => 'GET',
                'action' => action(
                    $action,
                    [$this->testModel, $secondTestModel]
                ),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_works_nicely_with_route_defaults()
    {
        $action = [TestControllerWithSpecifiedEndpoints::class, 'endpointWithTwoParameters'];

        $route = $this->fakeRouter->get('{testModel}/{secondTestModel}', $action);

        $secondTestModel = SecondTestModel::create([
            'id' => 2,
            'name' => 'secondTestModel'
        ]);

        app('url')->defaults(['secondTestModel' => $secondTestModel->id]);

        $endpoints = RouteEndpointType::make($route)->getEndpoints($this->testModel);

        $this->assertEquals([
            'endpointWithTwoParameters' => [
                'method' => 'GET',
                'action' => action(
                    $action,
                    [$this->testModel]
                ),
            ],
        ], $endpoints);
    }
}
