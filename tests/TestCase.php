<?php

namespace Spatie\LaravelResourceEndpoints\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Spatie\LaravelResourceEndpoints\EndpointResourcesServiceProvider;
use Spatie\LaravelResourceEndpoints\Tests\Fakes\TestModel;
use Spatie\LaravelResourceEndpoints\Tests\Fakes\FakeRouter;
use Spatie\LaravelResourceEndpoints\Tests\Fakes\SecondTestModel;

abstract class TestCase extends BaseTestCase
{
    /** @var \Spatie\LaravelResourceEndpoints\Tests\Fakes\FakeRouter */
    protected $fakeRouter;

    protected function setUp() : void
    {
        parent::setUp();

        $this->setUpEnvironment();
        $this->setUpDatabase();

        $this->fakeRouter = FakeRouter::setup();
    }

    protected function getPackageProviders($app)
    {
        return [EndpointResourcesServiceProvider::class];
    }

    private function setUpDatabase()
    {
        Schema::create('test_models', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
        });

        Schema::create('second_test_models', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
        });
    }

    private function setUpEnvironment(): void
    {
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        config()->set('app.key', 'kuFyUdCwrgWJjLWURIbkemJlFLGatcmo');

        Model::unguard();
    }
}
