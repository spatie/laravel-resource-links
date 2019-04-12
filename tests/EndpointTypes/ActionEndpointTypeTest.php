<?php

namespace Spatie\LaravelEndpointResources\Tests\EndpointTypes;

use Spatie\LaravelEndpointResources\EndpointTypes\ActionEndpointType;
use Spatie\LaravelEndpointResources\Tests\Dummy\DummyController;
use Spatie\LaravelEndpointResources\Tests\Dummy\DummyModel;
use Spatie\LaravelEndpointResources\Tests\Dummy\PhonyModel;
use Spatie\LaravelEndpointResources\Tests\TestCase;

final class ActionEndpointTypeTest extends TestCase
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
    public function it_can_create_an_action_endpoint_type()
    {
        $this->dummyRoutes->route('GET', '', [DummyController::class, 'index']);

        $endpointType = new ActionEndpointType('GET', [DummyController::class, 'index']);

        $endpoints = $endpointType->getEndpoints();

        $this->assertEquals([
            'index' => [
                'method' => 'GET',
                'action' => action([DummyController::class, 'index']),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_can_create_an_action_endpoint_type_with_parameters()
    {
        $this->dummyRoutes->route('GET', '{dummyModel}', [DummyController::class, 'show']);

        $endpointType = new ActionEndpointType('GET', [DummyController::class, 'show']);

        $endpoints = $endpointType->getEndpoints($this->dummy);

        $this->assertEquals([
            'show' => [
                'method' => 'GET',
                'action' => action([DummyController::class, 'show'], $this->dummy),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_will_not_overwrite_a_model_given_as_parameter()
    {
        $this->dummyRoutes->route('GET', '{dummyModel}', [DummyController::class, 'show']);

        $otherDummyModel = DummyModel::create([
            'id' => 2,
            'name' => 'Dumbi',
        ]);

        $endpointType = new ActionEndpointType('GET', [DummyController::class, 'show'], [$otherDummyModel]);

        $endpoints = $endpointType->getEndpoints($this->dummy);

        $this->assertEquals([
            'show' => [
                'method' => 'GET',
                'action' => action([DummyController::class, 'show'], $otherDummyModel),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_can_combine_model_and_parameters_for_binding_to_routes()
    {
        $this->dummyRoutes->route('GET', '{dummyModel}/{phonyModel}', [DummyController::class, 'attach']);

        $phonyModel = PhonyModel::create([
            'id' => 2,
            'name' => 'Phono',
        ]);

        $endpointType = new ActionEndpointType('GET', [DummyController::class, 'attach'], [$phonyModel]);

        $endpoints = $endpointType->getEndpoints($this->dummy);

        $this->assertEquals([
            'attach' => [
                'method' => 'GET',
                'action' => action(
                    [DummyController::class, 'attach'],
                    [$phonyModel, $this->dummy]
                ),
            ],
        ], $endpoints);
    }
}
