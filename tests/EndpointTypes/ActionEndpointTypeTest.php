<?php

namespace Spatie\LaravelEndpointResources\Tests\EndpointTypes;

use Exception;
use Spatie\LaravelEndpointResources\EndpointTypes\ActionEndpointType;
use Spatie\LaravelEndpointResources\Formatters\LayeredFormatter;
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

        $endpoints = ActionEndpointType::make($action)->getEndpoints();

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

        $endpoints = ActionEndpointType::make($action)->getEndpoints();

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

        $endpoints = ActionEndpointType::make($action)->getEndpoints($this->testModel);

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

        $endpoints = ActionEndpointType::make($action)
            ->parameters([$otherTestModel])
            ->getEndpoints($this->testModel);

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

        $endpoints = ActionEndpointType::make($action)
            ->httpVerb('GET')
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
    public function it_will_throw_an_exception_when_a_route_was_not_found()
    {
        $this->expectException(Exception::class);

        $action = [TestController::class, 'index'];

        ActionEndpointType::make($action)->getEndpoints();
    }

    /** @test */
    public function it_can_prefix_an_action_endpoint()
    {
        $action = [TestController::class, 'index'];

        $this->fakeRouter->get('', $action);

        $endpoints = ActionEndpointType::make($action)
            ->prefix('this-')
            ->getEndpoints();

        $this->assertEquals([
            'this-index' => [
                'method' => 'GET',
                'action' => action($action),
            ],
        ], $endpoints);
    }

    /** @test */
    public function it_can_change_the_formatter_to_layered()
    {
        $action = [TestController::class, 'index'];

        $this->fakeRouter->get('', $action);

        $endpoints = ActionEndpointType::make($action)
            ->formatter(LayeredFormatter::class)
            ->getEndpoints();

        $layeredEndpoints = ActionEndpointType::make($action)
            ->formatter(LayeredFormatter::class)
            ->prefix('tests')
            ->getEndpoints();

        $this->assertEquals([
            'index' => [
                'method' => 'GET',
                'action' => action($action),
            ],
        ], $endpoints);

        $this->assertEquals([
            'tests' => [
                'index' => [
                    'method' => 'GET',
                    'action' => action($action),
                ],
            ],
        ], $layeredEndpoints);
    }
}
