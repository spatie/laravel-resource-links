<?php

namespace Spatie\LaravelEndpointResources\Tests;

use Spatie\LaravelEndpointResources\ParameterResolver;
use Spatie\LaravelEndpointResources\Tests\Dummy\DummyController;
use Spatie\LaravelEndpointResources\Tests\Dummy\DummyModel;
use Spatie\LaravelEndpointResources\Tests\Dummy\PhonyModel;

final class ParameterResolverTest extends TestCase
{
    /** @test */
    public function it_will_resolve_required_parameters_for_a_route()
    {
        $dummyModel = DummyModel::create([
            'name' => 'dumbo',
        ]);

        $phonyModel = PhonyModel::create([
            'name' => 'phono',
        ]);

        $route = $this->dummyRoutes->route('GET', '{phonyModel}/{dummyModel}', [DummyController::class, 'attach']);

        $parameterResolver = new ParameterResolver($dummyModel, [$phonyModel]);

        $this->assertEquals([
            'phonyModel' => $phonyModel,
            'dummyModel' => $dummyModel,
        ], $parameterResolver->forRoute($route));
    }

    /** @test */
    public function it_will_insert_the_correct_model()
    {
        $dummyModel = DummyModel::create([
            'name' => 'dumbo',
        ]);

        $otherDummyModel = DummyModel::create([
            'name' => 'dumbo',
        ]);

        $route = $this->dummyRoutes->route('GET', '{dummyModel}', [DummyController::class, 'show']);

        $parameterResolver = new ParameterResolver($otherDummyModel, ['dummyModel' => $dummyModel]);

        $this->assertEquals([
            'dummyModel' => $dummyModel,
        ], $parameterResolver->forRoute($route));
    }

    /** @test */
    public function it_can_deduce_the_correct_model_if_none_is_given()
    {
        $dummyModel = DummyModel::create([
            'name' => 'dumbo',
        ]);

        $route = $this->dummyRoutes->route('GET', '{dummyModel}', [DummyController::class, 'show']);

        $parameterResolver = new ParameterResolver(null, [$dummyModel]);

        $this->assertEquals([
            'dummyModel' => $dummyModel,
        ], $parameterResolver->forRoute($route));
    }

    /** @test */
    public function it_plays_nice_with_other_parameters_than_models()
    {
        $dummyModel = DummyModel::create([
            'name' => 'dumbo',
        ]);

        $route = $this->dummyRoutes->route('GET', '{dummyModel}/{action}', [DummyController::class, 'execute']);

        $parameterResolver = new ParameterResolver($dummyModel, ['action' => 'doSomething']);

        $this->assertEquals([
            'dummyModel' => $dummyModel,
            'action' => 'doSomething',
        ], $parameterResolver->forRoute($route));
    }

    /** @test */
    public function it_also_plays_nice_with_parameters_needed_from_the_container()
    {
        $dummyModel = DummyModel::create([
            'name' => 'dumbo',
        ]);

        $route = $this->dummyRoutes->route('GET', '{dummyModel}/{action}', [DummyController::class, 'update']);

        $parameterResolver = new ParameterResolver($dummyModel);

        $this->assertEquals([
            'dummyModel' => $dummyModel,
        ], $parameterResolver->forRoute($route));
    }
    
    /** @test */
    public function it_can_have_multiple_parameters_of_the_same_type()
    {
        $dummyModel = DummyModel::create([
            'name' => 'dumbo',
        ]);

        $otherDummyModel = DummyModel::create([
            'name' => 'otherDumbo',
        ]);

        $route = $this->dummyRoutes->route('GET', '{dummyModel}/{otherDummyModel}', [DummyController::class, 'switch']);

        $parameterResolver = new ParameterResolver(null, ['dummyModel' => $dummyModel, 'otherDummyModel' => $otherDummyModel]);

        $this->assertEquals([
            'dummyModel' => $dummyModel,
            'otherDummyModel' => $otherDummyModel,
        ], $parameterResolver->forRoute($route));
    }
    
    /** @test */
    public function when_the_parameter_name_is_not_specified_the_resource_will_be_taken()
    {
        $dummyModel = DummyModel::create([
            'name' => 'dumbo',
        ]);

        $otherDummyModel = DummyModel::create([
            'name' => 'otherDumbo',
        ]);

        $route = $this->dummyRoutes->route('GET', '{dummyModel}/{otherDummyModel}', [DummyController::class, 'switch']);

        $parameterResolver = new ParameterResolver($otherDummyModel, ['dummyModel' => $dummyModel]);

        $this->assertEquals([
            'dummyModel' => $dummyModel,
            'otherDummyModel' => $otherDummyModel,
        ], $parameterResolver->forRoute($route));
    }
    
    /** @test */
    public function it_can_check_if_routes_can_be_constructed()
    {
        $dummyModel = DummyModel::create([
            'name' => 'dumbo',
        ]);

        $phonyModel = PhonyModel::create([
            'name' => 'phono',
        ]);

        $route = $this->dummyRoutes->route('GET', '{dummyModel}', [DummyController::class, 'attach']);

        $parameterResolver = new ParameterResolver($dummyModel, [$phonyModel]);

        $this->assertTrue($parameterResolver->canRouteBeConstructed($route));
    }

    /** @test */
    public function it_will_fail_when_a_route_cannot_be_constructed()
    {
        $dummyModel = DummyModel::create([
            'name' => 'dumbo',
        ]);

        $route = $this->dummyRoutes->route('GET', '{dummyModel}/{phonyModel}', [DummyController::class, 'attach']);

        $parameterResolver = new ParameterResolver($dummyModel);

        $this->assertFalse($parameterResolver->canRouteBeConstructed($route));
    }

    /** @test */
    public function it_will_take_nullable_parameters_into_account_when_checking_if_a_route_can_be_constructed()
    {
        $route = $this->dummyRoutes->route('GET', '{dummyModel?}', [DummyController::class, 'attach']);

        $parameterResolver = new ParameterResolver(null);

        $this->assertTrue($parameterResolver->canRouteBeConstructed($route));
    }
}
