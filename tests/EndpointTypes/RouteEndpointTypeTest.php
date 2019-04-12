<?php

namespace Spatie\LaravelEndpointResources\Tests\EndpointTypes;

use Spatie\LaravelEndpointResources\EndpointTypes\RouteEndpointType;
use Spatie\LaravelEndpointResources\Tests\Dummy\DummyController;
use Spatie\LaravelEndpointResources\Tests\Dummy\DummyModel;
use Spatie\LaravelEndpointResources\Tests\Dummy\PhonyModel;
use Spatie\LaravelEndpointResources\Tests\TestCase;

final class RouteEndpointTypeTest extends TestCase
{
    /** @var \Spatie\LaravelEndpointResources\Tests\Dummy\DummyModel */
    private $dummy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dummy = DummyModel::create([
            'id' => 1,
            'name' => 'Dumbo',
        ]);
    }
    
    /** @test */
    public function it_can_create_an_route_endpoint_type()
    {
        $route = $this->dummyRoutes->route('GET', '', [DummyController::class, 'index']);

        $endpointType = new RouteEndpointType($route);

        $endpoints = $endpointType->getEndpoints();

        $this->assertEquals([
            'index' => [
                'method' => 'GET',
                'action' => action([DummyController::class, 'index'])
            ]
        ], $endpoints);
    }

    /** @test */
    public function it_can_create_an_route_endpoint_type_with_parameters()
    {
        $route = $this->dummyRoutes->route('GET', '{dummyModel}', [DummyController::class, 'show']);

        $endpointType = new RouteEndpointType($route);

        $endpoints = $endpointType->getEndpoints($this->dummy);

        $this->assertEquals([
            'show' => [
                'method' => 'GET',
                'action' => action([DummyController::class, 'show'], $this->dummy)
            ]
        ], $endpoints);
    }
    
    /** @test */
    public function it_will_try_to_resolve_parameters_for_the_model()
    {
        $route = $this->dummyRoutes->route('GET', '{dummyModel}', [DummyController::class, 'show']);

        $endpointType = new RouteEndpointType($route, [$this->dummy]);

        $endpoints = $endpointType->getEndpoints();

        $this->assertEquals([
            'show' => [
                'method' => 'GET',
                'action' => action([DummyController::class, 'show'], $this->dummy)
            ]
        ], $endpoints);
    }

    /** @test */
    public function it_will_choose_the_correct_method_for_routing()
    {
        $route = $this->dummyRoutes->route(['GET', 'HEAD'], '', [DummyController::class, 'index']);

        $endpointType = new RouteEndpointType($route);

        $endpoints = $endpointType->getEndpoints();

        $this->assertEquals([
            'index' => [
                'method' => 'GET',
                'action' => action([DummyController::class, 'index'])
            ]
        ], $endpoints);
    }

    /** @test */
    public function it_can_combine_the_resource_model_and_parameters_for_binding_to_routes()
    {
        $route = $this->dummyRoutes->route('GET', '{dummyModel}/{phonyModel}', [DummyController::class, 'attach']);

        $phonyModel = PhonyModel::create([
            'id' => 2,
            'name' => 'Phono'
        ]);

        $endpointType = new RouteEndpointType($route, [$phonyModel]);

        $endpoints = $endpointType->getEndpoints($this->dummy);

        $this->assertEquals([
            'attach' => [
                'method' => 'GET',
                'action' => action(
                    [DummyController::class, 'attach'],
                    [$this->dummy, $phonyModel]
                ),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_works_nicely_with_route_defaults()
    {
        $route = $this->dummyRoutes->route('GET', '{dummyModel}/{phonyModel}', [DummyController::class, 'attach']);

        $phonyModel = PhonyModel::create([
            'id' => 2,
            'name' => 'Phono'
        ]);

        app('url')->defaults(['phonyModel' => $phonyModel->id]);

        $endpointType = new RouteEndpointType($route);

        $endpoints = $endpointType->getEndpoints($this->dummy);

        $this->assertEquals([
            'attach' => [
                'method' => 'GET',
                'action' => action(
                    [DummyController::class, 'attach'],
                    [$this->dummy]
                ),
            ],
        ], $endpoints);
    }
}
