<?php

namespace Spatie\LaravelEndpointResources\Tests\EndpointTypes;

use Spatie\LaravelEndpointResources\EndpointTypes\ActionEndpointType;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestController;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestModel;
use Spatie\LaravelEndpointResources\Tests\Fakes\SecondTestModel;
use Spatie\LaravelEndpointResources\Tests\TestCase;

final class ActionEndpointTypeTest extends TestCase
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
    public function it_will_deduce_the_route_http_verb()
    {
        $this->fakeRouter->route('GET', '', [TestController::class, 'index']);

        $endpointType = new ActionEndpointType([TestController::class, 'index']);

        $endpoints = $endpointType->getEndpoints();

        $this->assertEquals([
            'index' => [
                'method' => 'GET',
                'action' => action([TestController::class, 'index']),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_can_create_an_action_endpoint_type()
    {
        $this->fakeRouter->route('GET', '', [TestController::class, 'index']);

        $endpointType = new ActionEndpointType([TestController::class, 'index']);

        $endpoints = $endpointType->getEndpoints();

        $this->assertEquals([
            'index' => [
                'method' => 'GET',
                'action' => action([TestController::class, 'index']),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_can_create_an_action_endpoint_type_with_parameters()
    {
        $this->fakeRouter->route('GET', '{testModel}', [TestController::class, 'show']);

        $endpointType = new ActionEndpointType([TestController::class, 'show']);

        $endpoints = $endpointType->getEndpoints($this->testModel);

        $this->assertEquals([
            'show' => [
                'method' => 'GET',
                'action' => action([TestController::class, 'show'], $this->testModel),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_will_overwrite_a_model_given_as_resource()
    {
        $this->fakeRouter->route('GET', '{testModel}', [TestController::class, 'show']);

        $otherTestModel = TestModel::create([
            'id' => 2,
            'name' => 'Dumbi',
        ]);

        $endpointType = new ActionEndpointType([TestController::class, 'show'], [$otherTestModel]);

        $endpoints = $endpointType->getEndpoints($this->testModel);

        $this->assertEquals([
            'show' => [
                'method' => 'GET',
                'action' => action([TestController::class, 'show'], $otherTestModel),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_can_combine_model_and_parameters_for_binding_to_routes()
    {
        $this->fakeRouter->route('GET', '{testModel}/{secondTestModel}', [TestController::class, 'attach']);

        $secondTestModel = SecondTestModel::create([
            'id' => 2,
            'name' => 'Phono',
        ]);

        $endpointType = new ActionEndpointType([TestController::class, 'attach'], [$secondTestModel], 'GET');

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
}
