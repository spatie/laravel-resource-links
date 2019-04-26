<?php

namespace Spatie\LaravelEndpointResources\Tests\EndpointTypes;

use Spatie\LaravelEndpointResources\EndpointTypes\ControllerEndpointType;
use Spatie\LaravelEndpointResources\Tests\Dummy\DummyController;
use Spatie\LaravelEndpointResources\Tests\Dummy\DummyModel;
use Spatie\LaravelEndpointResources\Tests\Dummy\PhonyController;
use Spatie\LaravelEndpointResources\Tests\TestCase;

final class ControllerEndpointTypeTest extends TestCase
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
    public function it_will_only_give_endpoints_which_can_be_constructed()
    {
        $this->fakeRouter->route('GET', '', [DummyController::class, 'index']);
        $this->fakeRouter->route('GET', '{dummyModel}', [DummyController::class, 'show']);

        $endpointType = new ControllerEndpointType(DummyController::class);

        $endpoints = $endpointType->getEndpoints();

        $this->assertEquals([
            'index' => [
                'method' => 'GET',
                'action' => action([DummyController::class, 'index']),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_will_create_all_possible_routes_when_a_model_is_available()
    {
        $this->fakeRouter->route('GET', '', [DummyController::class, 'index']);
        $this->fakeRouter->route('GET', '{dummyModel}', [DummyController::class, 'show']);

        $dummyModel = DummyModel::create([
            'name' => 'Dumbo',
        ]);

        $endpointType = new ControllerEndpointType(DummyController::class);

        $endpoints = $endpointType->getEndpoints($dummyModel);

        $this->assertEquals([
            'index' => [
                'method' => 'GET',
                'action' => action([DummyController::class, 'index']),
            ],
            'show' => [
                'method' => 'GET',
                'action' => action([DummyController::class, 'show'], $dummyModel),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_will_only_create_routes_based_upon_the_end_point_methods_property()
    {
        $this->fakeRouter->route('GET', '/a/{dummyModel}', [PhonyController::class, 'endpoint']);
        $this->fakeRouter->route('GET', '/b/{dummyModel}', [PhonyController::class, 'nonEndpoint']);

        $dummyModel = DummyModel::create([
            'name' => 'Dumbo',
        ]);

        $endpointType = new ControllerEndpointType(PhonyController::class);

        $endpoints = $endpointType->getEndpoints($dummyModel);

        $this->assertEquals([
            'endpoint' => [
                'method' => 'GET',
                'action' => action([PhonyController::class, 'endpoint'], $dummyModel),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_will_only_create_routes_based_upon_the_global_end_point_methods_property()
    {
        $this->fakeRouter->route('GET', '/a/', [PhonyController::class, 'globalEndpoint']);
        $this->fakeRouter->route('GET', '/b/', [PhonyController::class, 'nonGlobalEndpoint']);

        $endpointType = new ControllerEndpointType(PhonyController::class);

        $endpoints = $endpointType->getGlobalEndpoints();

        $this->assertEquals([
            'globalEndpoint' => [
                'method' => 'GET',
                'action' => action([PhonyController::class, 'globalEndpoint']),
            ],
        ], $endpoints);
    }
}
