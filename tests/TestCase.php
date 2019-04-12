<?php

namespace Spatie\LaravelEndpointResources\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Spatie\LaravelEndpointResources\Tests\Dummy\DummyModel;
use Spatie\LaravelEndpointResources\Tests\Dummy\DummyRoutes;
use Spatie\LaravelEndpointResources\Tests\Dummy\PhonyModel;

abstract class TestCase extends BaseTestCase
{
    /** @var \Spatie\LaravelEndpointResources\Tests\Dummy\DummyRoutes */
    protected $dummyRoutes;

    protected function setUp() : void
    {
        parent::setUp();

        $this->setUpEnvironment();
        $this->setUpDatabase();

        $this->dummyRoutes = DummyRoutes::setup();
    }

    private function setUpDatabase(){
        Schema::create('dummy_models', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name');
        });

        Schema::create('phony_models', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name');
        });
    }

    private function setUpEnvironment(): void
    {
        $this->app['config']->set('database.default', 'sqlite');
        $this->app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        Model::unguard();
    }
}
