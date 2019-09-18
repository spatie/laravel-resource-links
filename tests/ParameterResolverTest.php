<?php

namespace Spatie\ResourceLinks\Tests;

use Spatie\ResourceLinks\ParameterResolver;
use Spatie\ResourceLinks\Tests\Fakes\TestController;
use Spatie\ResourceLinks\Tests\Fakes\TestControllerWithSpecifiedEndpoints;
use Spatie\ResourceLinks\Tests\Fakes\TestModel;
use Spatie\ResourceLinks\Tests\Fakes\SecondTestModel;

class ParameterResolverTest extends TestCase
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

        $route = $this->fakeRouter->get('{secondTestModel}/{testModel}', [TestController::class, 'copy']);

        $parameterResolver = new ParameterResolver($testModel, [$secondTestModel]);

        $this->assertEquals([
            'secondTestModel' => $secondTestModel,
            'testModel' => $testModel,
        ], $parameterResolver->forRoute($route));
    }

    /** @test */
    public function it_will_try_to_find_the_model_in_the_default_parameters_if_none_was_given()
    {
        $testModel = TestModel::create([
            'name' => 'TestModel',
        ]);

        $route = $this->fakeRouter->get('{testModel}', [TestController::class, 'show']);

        $parameterResolver = new ParameterResolver(null, [$testModel]);

        $this->assertEquals([
            'testModel' => $testModel,
        ], $parameterResolver->forRoute($route));
    }

    /** @test */
    public function it_plays_nice_with_parameters_other_than_models()
    {
        $testModel = TestModel::create([
            'name' => 'TestModel',
        ]);

        $route = $this->fakeRouter->get('{testModel}/{action}', [TestController::class, 'edit']);

        $parameterResolver = new ParameterResolver($testModel, ['action' => 'doSomething']);

        $this->assertEquals([
            'testModel' => $testModel,
            'action' => 'doSomething',
        ], $parameterResolver->forRoute($route));
    }

    /** @test */
    public function when_a_parameter_cannot_be_deduced_it_will_be_ignored()
    {
        $testModel = TestModel::create([
            'name' => 'TestModel',
        ]);

        $route = $this->fakeRouter->get('{testModel}/{action}', [TestController::class, 'update']);

        $parameterResolver = new ParameterResolver($testModel);

        $this->assertEquals([
            'testModel' => $testModel,
        ], $parameterResolver->forRoute($route));
    }

    /** @test */
    public function it_will_use_the_names_provided_to_bind_models_to_parameters()
    {
        $testModel = TestModel::create([
            'name' => 'TestModel',
        ]);

        $otherTestModel = TestModel::create([
            'name' => 'otherTestModel',
        ]);

        $route = $this->fakeRouter->get('{testModel}/{otherTestModel}', [TestController::class, 'sync']);

        $parameterResolver = new ParameterResolver(null, ['testModel' => $testModel, 'otherTestModel' => $otherTestModel]);

        $this->assertEquals([
            'testModel' => $testModel,
            'otherTestModel' => $otherTestModel,
        ], $parameterResolver->forRoute($route));
    }

    /** @test */
    public function when_a_model_cannot_be_found_by_its_parameter_name_the_resource_will_be_taken_if_same_type()
    {
        $testModel = TestModel::create([
            'name' => 'TestModel',
        ]);

        $otherTestModel = TestModel::create([
            'name' => 'otherTestModel',
        ]);

        $route = $this->fakeRouter->get('{testModel}/{otherTestModel}', [TestController::class, 'sync']);

        $parameterResolver = new ParameterResolver(null, ['testModel' => $testModel]);

        $this->assertEquals([
            'testModel' => $testModel,
            'otherTestModel' => $testModel,
        ], $parameterResolver->forRoute($route));
    }

    /** @test */
    public function it_cannot_deduce_parameters_with_name_and_without_value()
    {
        $route = $this->fakeRouter->get('{withoutType}', [TestController::class, 'read']);

        $parameterResolver = new ParameterResolver(null, [42]);

        $this->assertEquals([], $parameterResolver->forRoute($route));
    }

    /** @test */
    public function it_can_deduce_parameters_with_name_and_value()
    {
        $route = $this->fakeRouter->get('{withoutType}', [TestController::class, 'read']);

        $parameterResolver = new ParameterResolver(null, [
            'withoutType' => 42,
        ]);

        $this->assertEquals([
            'withoutType' => 42,
        ], $parameterResolver->forRoute($route));
    }

    /** @test */
    public function it_will_add_primitive_parameters_at_the_end_if_they_cannot_be()
    {
        $testModel = TestModel::create([
            'name' => 'TestModel',
        ]);

        $secondTestModel = SecondTestModel::create([
            'name' => 'otherTestModel',
        ]);

        $route = $this->fakeRouter->get('{testModel}/{secondTestModel}/{aString}/{aNumber}/{aBool}',
            function (
                TestModel $testModel,
                SecondTestModel $secondTestModel,
                string $aString,
                int $aNumber,
                bool $aBool
            ) {

            });

        $parameterResolver = new ParameterResolver($testModel, [
            'secondTestModel' => $secondTestModel,
            'test',
            false,
            42,
        ]);

        $this->assertEquals([
            'testModel' => $testModel,
            'secondTestModel' => $secondTestModel,
            'aString' => 'test',
            'aNumber' => 42,
            'aBool' => false,
        ], $parameterResolver->forRoute($route));
    }
}
