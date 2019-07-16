<?php

namespace Spatie\LaravelResourceEndpoints\Tests\EndpointTypes;

use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\URL;
use Spatie\LaravelResourceEndpoints\EndpointTypes\RouteEndpointType;
use Spatie\LaravelResourceEndpoints\Exceptions\EndpointGenerationException;
use Spatie\LaravelResourceEndpoints\Formatters\LayeredFormatter;
use Spatie\LaravelResourceEndpoints\Formatters\UrlFormatter;
use Spatie\LaravelResourceEndpoints\Tests\Fakes\TestController;
use Spatie\LaravelResourceEndpoints\Tests\Fakes\TestControllerWithSpecifiedEndpoints;
use Spatie\LaravelResourceEndpoints\Tests\Fakes\TestModel;
use Spatie\LaravelResourceEndpoints\Tests\Fakes\SecondTestModel;
use Spatie\LaravelResourceEndpoints\Tests\TestCase;

class RouteEndpointTypeTest extends TestCase
{
    /** @var \Spatie\LaravelResourceEndpoints\Tests\Fakes\TestModel */
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
    public function it_can_create_a_route_endpoint_type()
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
        $action = [TestController::class, 'copy'];

        $route = $this->fakeRouter->get('{testModel}/{secondTestModel}', $action);

        $secondTestModel = SecondTestModel::create([
            'id' => 2,
            'name' => 'secondTestModel'
        ]);

        $endpoints = RouteEndpointType::make($route)
            ->parameters([$secondTestModel])
            ->getEndpoints($this->testModel);

        $this->assertEquals([
            'copy' => [
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
        $action = [TestController::class, 'copy'];

        $route = $this->fakeRouter->get('{testModel}/{secondTestModel}', $action);

        $secondTestModel = SecondTestModel::create([
            'id' => 2,
            'name' => 'secondTestModel'
        ]);

        app('url')->defaults(['secondTestModel' => $secondTestModel->id]);

        $endpoints = RouteEndpointType::make($route)->getEndpoints($this->testModel);

        $this->assertEquals([
            'copy' => [
                'method' => 'GET',
                'action' => action(
                    $action,
                    [$this->testModel]
                ),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_can_prefix_endpoints()
    {
        $action = [TestController::class, 'index'];

        $route = $this->fakeRouter->get('', $action);

        $endpoints =  RouteEndpointType::make($route)
            ->prefix('this-')
            ->getEndpoints();

        $this->assertEquals([
            'this-index' => [
                'method' => 'GET',
                'action' => action($action)
            ]
        ], $endpoints);
    }

    /** @test */
    public function it_can_change_the_formatter()
    {
        $action = [TestController::class, 'index'];

        $route = $this->fakeRouter->get('', $action);

        $endpoints =  RouteEndpointType::make($route)
            ->formatter(UrlFormatter::class)
            ->getEndpoints();

        $this->assertEquals([
            'index' => action($action)
        ], $endpoints);
    }

    /** @test */
    public function it_uses_the_global_formatter_when_no_formatter_was_explicitly_defined()
    {
        $action = [TestController::class, 'index'];

        $route = $this->fakeRouter->get('', $action);

        app(config()->set('laravel-endpoint-resources.formatter', UrlFormatter::class));

        $endpoints =  RouteEndpointType::make($route)->getEndpoints();

        $this->assertEquals(['index' => action($action)], $endpoints);
    }

    /** @test */
    public function a_not_constructable_route_has_its_missing_parameters_in_brackets()
    {
        $action = [TestController::class, 'copy'];

        $route = $this->fakeRouter->get('/{secondTestModel}/{testModel}', $action);

        $secondTestModel = SecondTestModel::create([
            'id' => 2,
            'name' => 'secondTestModel'
        ]);

        $endpoints =  RouteEndpointType::make($route)
//            ->parameters([$secondTestModel])
            ->getEndpoints($this->testModel);

        $this->assertEquals([
            'copy' => [
                'method' => 'GET',
                'action' => ''
            ]
        ], $endpoints);
    }
}
