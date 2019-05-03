<?php

namespace Spatie\LaravelEndpointResources\Tests\EndpointTypes;

use Spatie\LaravelEndpointResources\EndpointTypes\ControllerEndpointType;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestController;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestModel;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestControllerWithSpecifiedEndpoints;
use Spatie\LaravelEndpointResources\Tests\TestCase;

final class ControllerEndpointTypeTest extends TestCase
{
    /** @var \Spatie\LaravelEndpointResources\Tests\Fakes\TestModel */
    private $dummy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dummy = TestModel::create([
            'id' => 1,
            'name' => 'Dumbo',
        ]);
    }

    /** @test */
    public function it_will_only_give_endpoints_which_can_be_constructed()
    {
        $this->fakeRouter->route('GET', '', [TestController::class, 'index']);
        $this->fakeRouter->route('GET', '{testModel}', [TestController::class, 'show']);

        $endpointType = new ControllerEndpointType(TestController::class);

        $endpoints = $endpointType->getEndpoints();

        $this->assertEquals([
            'index' => [
                'method' => 'GET',
                'action' => action([TestController::class, 'index']),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_will_create_all_possible_routes_when_a_model_is_available()
    {
        $this->fakeRouter->route('GET', '', [TestController::class, 'index']);
        $this->fakeRouter->route('GET', '{testModel}', [TestController::class, 'show']);

        $testModel = TestModel::create([
            'name' => 'Dumbo',
        ]);

        $endpointType = new ControllerEndpointType(TestController::class);

        $endpoints = $endpointType->getEndpoints($testModel);

        $this->assertEquals([
            'index' => [
                'method' => 'GET',
                'action' => action([TestController::class, 'index']),
            ],
            'show' => [
                'method' => 'GET',
                'action' => action([TestController::class, 'show'], $testModel),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_will_only_create_routes_based_upon_the_end_point_methods_property()
    {
        $this->fakeRouter->route('GET', '/a/{testModel}', [TestControllerWithSpecifiedEndpoints::class, 'endpoint']);
        $this->fakeRouter->route('GET', '/b/{testModel}', [TestControllerWithSpecifiedEndpoints::class, 'nonEndpoint']);

        $testModel = TestModel::create([
            'name' => 'Dumbo',
        ]);

        $endpointType = new ControllerEndpointType(TestControllerWithSpecifiedEndpoints::class);

        $endpoints = $endpointType->getEndpoints($testModel);

        $this->assertEquals([
            'endpoint' => [
                'method' => 'GET',
                'action' => action([TestControllerWithSpecifiedEndpoints::class, 'endpoint'], $testModel),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_will_only_create_routes_based_upon_the_global_end_point_methods_property()
    {
        $this->fakeRouter->route('GET', '/a/', [TestControllerWithSpecifiedEndpoints::class, 'globalEndpoint']);
        $this->fakeRouter->route('GET', '/b/', [TestControllerWithSpecifiedEndpoints::class, 'nonGlobalEndpoint']);

        $endpointType = new ControllerEndpointType(TestControllerWithSpecifiedEndpoints::class);

        $endpoints = $endpointType->getGlobalEndpoints();

        $this->assertEquals([
            'globalEndpoint' => [
                'method' => 'GET',
                'action' => action([TestControllerWithSpecifiedEndpoints::class, 'globalEndpoint']),
            ],
        ], $endpoints);
    }
}
