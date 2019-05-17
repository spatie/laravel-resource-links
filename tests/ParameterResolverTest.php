<?php

namespace Spatie\LaravelEndpointResources\Tests;

use Spatie\LaravelEndpointResources\ParameterResolver;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestController;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestControllerWithSpecifiedEndpoints;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestModel;
use Spatie\LaravelEndpointResources\Tests\Fakes\SecondTestModel;

final class ParameterResolverTest extends TestCase
{
    /** @test */
    public function it_will_resolve_required_parameters_for_a_route()
    {
        $testModel = TestModel::create([
            'name' => 'TestModel',
        ]);

        $secondTestModel = SecondTestModel::create([
            'name' => 'secondTestModel',
        ]);

        $route = $this->fakeRouter->route('GET', '{secondTestModel}/{testModel}', [TestControllerWithSpecifiedEndpoints::class, 'endpointWithTwoParameters']);

        $parameterResolver = new ParameterResolver($testModel, [$secondTestModel]);

        $this->assertEquals([
            'secondTestModel' => $secondTestModel,
            'testModel' => $testModel,
        ], $parameterResolver->forRoute($route));
    }

    /** @test */
    public function it_will_insert_the_correct_model()
    {
        $testModel = TestModel::create([
            'name' => 'TestModel',
        ]);

        $otherTestModel = TestModel::create([
            'name' => 'otherTestModel',
        ]);

        $route = $this->fakeRouter->route('GET', '{testModel}', [TestController::class, 'show']);

        $parameterResolver = new ParameterResolver($otherTestModel, ['testModel' => $testModel]);

        $this->assertEquals([
            'testModel' => $testModel,
        ], $parameterResolver->forRoute($route));
    }

    /** @test */
    public function it_can_deduce_the_correct_model_if_none_is_given()
    {
        $testModel = TestModel::create([
            'name' => 'TestModel',
        ]);

        $route = $this->fakeRouter->route('GET', '{testModel}', [TestController::class, 'show']);

        $parameterResolver = new ParameterResolver(null, [$testModel]);

        $this->assertEquals([
            'testModel' => $testModel,
        ], $parameterResolver->forRoute($route));
    }

    /** @test */
    public function it_plays_nice_with_other_parameters_than_models()
    {
        $testModel = TestModel::create([
            'name' => 'TestModel',
        ]);

        $route = $this->fakeRouter->route('GET', '{testModel}/{action}', [TestController::class, 'edit']);

        $parameterResolver = new ParameterResolver($testModel, ['action' => 'doSomething']);

        $this->assertEquals([
            'testModel' => $testModel,
            'action' => 'doSomething',
        ], $parameterResolver->forRoute($route));
    }

    /** @test */
    public function it_also_plays_nice_with_parameters_needed_from_the_container()
    {
        $testModel = TestModel::create([
            'name' => 'TestModel',
        ]);

        $route = $this->fakeRouter->route('GET', '{testModel}/{action}', [TestController::class, 'update']);

        $parameterResolver = new ParameterResolver($testModel);

        $this->assertEquals([
            'testModel' => $testModel,
        ], $parameterResolver->forRoute($route));
    }
    
    /** @test */
    public function it_can_have_multiple_parameters_of_the_same_type()
    {
        $testModel = TestModel::create([
            'name' => 'TestModel',
        ]);

        $otherTestModel = TestModel::create([
            'name' => 'otherTestModel',
        ]);

        $route = $this->fakeRouter->route('GET', '{testModel}/{otherTestModel}', [TestControllerWithSpecifiedEndpoints::class, 'endpointWithTwoIdenticalParameters']);

        $parameterResolver = new ParameterResolver(null, ['testModel' => $testModel, 'otherTestModel' => $otherTestModel]);

        $this->assertEquals([
            'testModel' => $testModel,
            'otherTestModel' => $otherTestModel,
        ], $parameterResolver->forRoute($route));
    }
    
    /** @test */
    public function when_the_parameter_name_is_not_specified_the_resource_will_be_taken()
    {
        $testModel = TestModel::create([
            'name' => 'TestModel',
        ]);

        $otherTestModel = TestModel::create([
            'name' => 'otherTestModel',
        ]);

        $route = $this->fakeRouter->route('GET', '{testModel}/{otherTestModel}', [TestControllerWithSpecifiedEndpoints::class, 'endpointWithTwoIdenticalParameters']);

        $parameterResolver = new ParameterResolver($otherTestModel, ['testModel' => $testModel]);

        $this->assertEquals([
            'testModel' => $testModel,
            'otherTestModel' => $testModel,
        ], $parameterResolver->forRoute($route));
    }
    
    /** @test */
    public function it_cannot_deduce_parameters_without_type_and_name()
    {
        $route = $this->fakeRouter->route('GET', '{withoutType}', [TestControllerWithSpecifiedEndpoints::class, 'endpointWithoutTypes']);

        $parameterResolver = new ParameterResolver(null);

        $this->assertEquals([], $parameterResolver->forRoute($route));
    }

    /** @test */
    public function it_can_deduce_parameters_with_type_and_name()
    {
        $route = $this->fakeRouter->route('GET', '{withoutType}', [TestControllerWithSpecifiedEndpoints::class, 'endpointWithoutTypes']);

        $parameterResolver = new ParameterResolver(null, [
            'withoutType' => 42
        ]);

        $this->assertEquals([
            'withoutType' => 42
        ], $parameterResolver->forRoute($route));
    }
}
