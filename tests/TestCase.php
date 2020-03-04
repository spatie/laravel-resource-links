<?php

namespace Spatie\ResourceLinks\Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Spatie\ResourceLinks\LinkTypes\ControllerLinkType;
use Spatie\ResourceLinks\ResourceLinksServiceProvider;
use Spatie\ResourceLinks\Serializers\ExtendedLinkSerializer;
use Spatie\ResourceLinks\Tests\Fakes\FakeRouter;

abstract class TestCase extends BaseTestCase
{
    /** @var \Spatie\ResourceLinks\Tests\Fakes\FakeRouter */
    protected $fakeRouter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpEnvironment();
        $this->setUpDatabase();

        ControllerLinkType::clearCache(); // Remove cache

        $this->fakeRouter = FakeRouter::setup();
    }

    protected function getPackageProviders($app)
    {
        return [ResourceLinksServiceProvider::class];
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

        config()->set('resource-links.serializer', ExtendedLinkSerializer::class);

        Model::unguard();
    }
}
