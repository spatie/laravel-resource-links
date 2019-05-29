<?php

namespace Spatie\LaravelEndpointResources\Tests\EndpointTypes;

use Spatie\LaravelEndpointResources\EndpointTypes\ActionEndpointType;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestController;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestControllerWithSpecifiedEndpoints;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestModel;
use Spatie\LaravelEndpointResources\Tests\Fakes\SecondTestModel;
use Spatie\LaravelEndpointResources\Tests\TestCase;

class ActionEndpointTypeTest extends TestCase
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
    public function it_will_deduce_the_route_http_verb()
    {
        $action = [TestController::class, 'index'];

        $this->fakeRouter->get('', $action);

        $endpointType = ActionEndpointType::make($action);

        $endpoints = $endpointType->getEndpoints();

        $this->assertEquals([
            'index' => [
                'method' => 'GET',
                'action' => action($action),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_can_create_an_action_endpoint_type()
    {
        $action = [TestController::class, 'index'];

        $this->fakeRouter->get('', $action);

        $endpointType = ActionEndpointType::make($action);

        $endpoints = $endpointType->getEndpoints();

        $this->assertEquals([
            'index' => [
                'method' => 'GET',
                'action' => action($action),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_can_create_an_action_endpoint_type_with_parameters()
    {
        $action = [TestController::class, 'show'];

        $this->fakeRouter->get('{testModel}', $action);

        $endpointType = ActionEndpointType::make($action);

        $endpoints = $endpointType->getEndpoints($this->testModel);

        $this->assertEquals([
            'show' => [
                'method' => 'GET',
                'action' => action($action, $this->testModel),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_will_overwrite_a_model_given_as_resource()
    {
        $action = [TestController::class, 'show'];

        $this->fakeRouter->get('{testModel}', $action);

        $otherTestModel = TestModel::create([
            'id' => 2,
            'name' => 'OtherTestModel',
        ]);

        $endpointType = ActionEndpointType::make($action)->parameters([$otherTestModel]);

        $endpoints = $endpointType->getEndpoints($this->testModel);

        $this->assertEquals([
            'show' => [
                'method' => 'GET',
                'action' => action($action, $otherTestModel),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_can_combine_model_and_parameters_for_binding_to_routes()
    {
        $action = [TestControllerWithSpecifiedEndpoints::class, 'endpointWithTwoParameters'];

        $this->fakeRouter->get('{testModel}/{secondTestModel}', $action);

        $secondTestModel = SecondTestModel::create([
            'id' => 2,
            'name' => 'secondTestModel',
        ]);

        $endpointType = ActionEndpointType::make($action)
            ->httpVerb('GET')
            ->parameters([$secondTestModel]);

        $endpoints = $endpointType->getEndpoints($this->testModel);

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
}
