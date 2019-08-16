<?php

namespace Spatie\LaravelResourceEndpoints\Tests;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\LaravelResourceEndpoints\EndpointResource;
use Spatie\LaravelResourceEndpoints\EndpointResourceType;
use Spatie\LaravelResourceEndpoints\EndpointsGroup;
use Spatie\LaravelResourceEndpoints\Formatters\LayeredFormatter;
use Spatie\LaravelResourceEndpoints\HasEndpoints;
use Spatie\LaravelResourceEndpoints\HasMeta;
use Spatie\LaravelResourceEndpoints\Tests\Fakes\TestController;
use Spatie\LaravelResourceEndpoints\Tests\Fakes\TestControllerWithSpecifiedEndpoints;
use Spatie\LaravelResourceEndpoints\Tests\Fakes\TestInvokableCollectionController;
use Spatie\LaravelResourceEndpoints\Tests\Fakes\TestInvokableController;
use Spatie\LaravelResourceEndpoints\Tests\Fakes\TestModel;

class HasEndpointsTest extends TestCase
{
    /** @var \Spatie\LaravelResourceEndpoints\Tests\Fakes\TestModel */
    private $testModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testModel = TestModel::create([
            'id' => 1,
            'name' => 'testModel',
        ]);

        $this->fakeRouter->get('/index', [TestController::class, 'index']);
        $this->fakeRouter->get('/show/{id}', [TestController::class, 'show']);
    }

    /** @test */
    public function it_will_generate_endpoints_when_making_a_resource()
    {
        $testResource = new class(null) extends JsonResource
        {
            use HasEndpoints, HasMeta;

            public function toArray($request)
            {
                return [
                    'endpoints' => $this->endpoints(TestController::class),
                ];
            }

            public static function meta()
            {
                return [
                    'endpoints' => self::collectionEndpoints(TestController::class),
                ];
            }
        };

        $this->fakeRouter->get('/resource', function () use ($testResource) {
            return $testResource::make(TestModel::first());
        });

        $this->get('/resource')->assertExactJson([
            'data' => [
                'endpoints' => [
                    'show' => [
                        'method' => 'GET',
                        'action' => action([TestController::class, 'show'], $this->testModel),
                    ],
                ],
            ],
            'meta' => [
                'endpoints' => [
                    'index' => [
                        'method' => 'GET',
                        'action' => action([TestController::class, 'index']),
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function it_will_generate_endpoints_when_collecting_a_resource()
    {
        $testResource = new class(null) extends JsonResource
        {
            use HasEndpoints, HasMeta;

            public function toArray($request)
            {
                return [
                    'endpoints' => $this->endpoints(TestController::class),
                ];
            }

            public static function meta()
            {
                return [
                    'endpoints' => self::collectionEndpoints(TestController::class),
                ];
            }
        };

        $this->fakeRouter->get('/resource', function () use ($testResource) {
            return $testResource::collection(TestModel::all());
        });

        $this->get('/resource')->assertExactJson([
            'data' => [
                0 => [
                    'endpoints' => [
                        'show' => [
                            'method' => 'GET',
                            'action' => action([TestController::class, 'show'], $this->testModel),
                        ],
                    ],
                ],
            ],
            'meta' => [
                'endpoints' => [
                    'index' => [
                        'method' => 'GET',
                        'action' => action([TestController::class, 'index']),
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function it_can_merge_collection_endpoints_with_item_endpoints()
    {
        $testResource = new class(null) extends JsonResource
        {
            use HasEndpoints, HasMeta;

            public function toArray($request)
            {
                return [
                    'endpoints' => $this->endpoints(TestController::class)->mergeCollectionEndpoints(),
                ];
            }
        };

        $this->fakeRouter->get('/resource', function () use ($testResource) {
            return $testResource::make(TestModel::first());
        });

        $this->get('/resource')->assertExactJson([
            'data' => [
                'endpoints' => [
                    'show' => [
                        'method' => 'GET',
                        'action' => action([TestController::class, 'show'], $this->testModel),
                    ],
                    'index' => [
                        'method' => 'GET',
                        'action' => action([TestController::class, 'index']),
                    ],
                ],
            ],
            'meta' => [],
        ]);
    }

    /** @test */
    public function it_can_use_invokable_controllers_when_creating_endpoints()
    {
        $this->fakeRouter->invokableGet('/show/{testModel}', TestInvokableController::class);

        $testResource = new class(null) extends JsonResource
        {
            use HasEndpoints, HasMeta;

            public function toArray($request)
            {
                return [
                    'endpoints' => $this->endpoints(TestInvokableController::class),
                ];
            }
        };

        $this->fakeRouter->get('/resource', function () use ($testResource) {
            return $testResource::make(TestModel::first());
        });

        $this->get('/resource')->assertExactJson([
            'data' => [
                'endpoints' => [
                    'invoke' => [
                        'method' => 'GET',
                        'action' => action(TestInvokableController::class, $this->testModel),
                    ],
                ],
            ],
            'meta' => []
        ]);
    }

    /** @test */
    public function it_will_generate_endpoints_when_making_a_resource_using_endpoint_groups()
    {
        $this->fakeRouter->invokableGet('/invoke/{testModel}', TestInvokableController::class);

        $testResource = new class(null) extends JsonResource
        {
            use HasEndpoints, HasMeta;

            public function toArray($request)
            {
                return [
                    'endpoints' => $this->endpoints(function (EndpointsGroup $endpoints) {
                        $endpoints->controller(TestController::class);
                        $endpoints->controller(TestInvokableController::class)->name('invoke');
                    }),
                ];
            }

            public static function meta()
            {
                return [
                    'endpoints' => self::collectionEndpoints(function (EndpointsGroup $endpoints) {
                        $endpoints->controller(TestController::class);
                        $endpoints->action([TestController::class, 'index'])->name('action');
                    }),
                ];
            }
        };

        $this->fakeRouter->get('/resource', function () use ($testResource) {
            return $testResource::make(TestModel::first());
        });

        $this->get('/resource')->assertExactJson([
            'data' => [
                'endpoints' => [
                    'show' => [
                        'method' => 'GET',
                        'action' => action([TestController::class, 'show'], $this->testModel),
                    ],
                    'invoke' => [
                        'method' => 'GET',
                        'action' => action([TestInvokableController::class], $this->testModel),
                    ],
                ],
            ],
            'meta' => [
                'endpoints' => [
                    'index' => [
                        'method' => 'GET',
                        'action' => action([TestController::class, 'index']),
                    ],
                    'action' => [
                        'method' => 'GET',
                        'action' => action([TestController::class, 'index']),
                    ],
                ],
            ],
        ]);
    }
}
