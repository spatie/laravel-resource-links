<?php

namespace Spatie\LaravelEndpointResources\Tests;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\LaravelEndpointResources\EndpointResource;
use Spatie\LaravelEndpointResources\EndpointResourceType;
use Spatie\LaravelEndpointResources\EndpointsGroup;
use Spatie\LaravelEndpointResources\HasEndpoints;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestController;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestControllerWithSpecifiedEndpoints;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestInvokableCollectionController;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestInvokableController;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestModel;

class HasEndpointsTest extends TestCase
{
    /** @var \Spatie\LaravelEndpointResources\Tests\Fakes\TestModel */
    private $testModel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testModel = TestModel::create([
            'id' => 1,
            'name' => 'testModel',
        ]);

        $this->fakeRouter->get('/local/{id}', [TestControllerWithSpecifiedEndpoints::class, 'endpoint']);
        $this->fakeRouter->get('/collection', [TestControllerWithSpecifiedEndpoints::class, 'collectionEndpoint']);
    }

    /** @test */
    public function it_will_generate_endpoints_when_making_a_resource()
    {
        $testResource = new class(null) extends JsonResource
        {
            use HasEndpoints;

            public function toArray($request)
            {
                return [
                    'endpoints' => $this->endpoints(TestControllerWithSpecifiedEndpoints::class),
                ];
            }

            public static function meta()
            {
                return [
                    'endpoints' => self::collectionEndpoints(TestControllerWithSpecifiedEndpoints::class),
                ];
            }
        };

        $this->fakeRouter->get('/index', function () use ($testResource) {
            return $testResource::make(TestModel::first());
        });

        $this->get('/index')->assertExactJson([
            'data' => [
                'endpoints' => [
                    'endpoint' => [
                        'method' => 'GET',
                        'action' => action([TestControllerWithSpecifiedEndpoints::class, 'endpoint'], $this->testModel),
                    ],
                ],
            ],
            'meta' => [
                'endpoints' => [
                    'collectionEndpoint' => [
                        'method' => 'GET',
                        'action' => action([TestControllerWithSpecifiedEndpoints::class, 'collectionEndpoint']),
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
            use HasEndpoints;

            public function __construct($resource)
            {
                parent::__construct($resource);
            }

            public function toArray($request)
            {
                return [
                    'endpoints' => $this->endpoints(TestControllerWithSpecifiedEndpoints::class),
                ];
            }

            public static function meta()
            {
                return [
                    'endpoints' => self::collectionEndpoints(TestControllerWithSpecifiedEndpoints::class),
                ];
            }
        };

        $this->fakeRouter->get('/index', function () use ($testResource) {
            return $testResource::collection(TestModel::all());
        });

        $this->get('/index')->assertExactJson([
            'data' => [
                0 => [
                    'endpoints' => [
                        'endpoint' => [
                            'method' => 'GET',
                            'action' => action([TestControllerWithSpecifiedEndpoints::class, 'endpoint'], $this->testModel),
                        ],
                    ],
                ],
            ],
            'meta' => [
                'endpoints' => [
                    'collectionEndpoint' => [
                        'method' => 'GET',
                        'action' => action([TestControllerWithSpecifiedEndpoints::class, 'collectionEndpoint']),
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function it_can_merge_collection_endpoints_with_local_endpoints()
    {
        $testResource = new class(null) extends JsonResource
        {
            use HasEndpoints;

            public function __construct($resource)
            {
                parent::__construct($resource);
            }

            public function toArray($request)
            {
                return [
                    'endpoints' => $this->endpoints(TestControllerWithSpecifiedEndpoints::class)->mergeCollectionEndpoints(),
                ];
            }
        };

        $this->fakeRouter->get('/index', function () use ($testResource) {
            return $testResource::make(TestModel::first());
        });

        $this->get('/index')->assertExactJson([
            'data' => [
                'endpoints' => [
                    'endpoint' => [
                        'method' => 'GET',
                        'action' => action([TestControllerWithSpecifiedEndpoints::class, 'endpoint'], $this->testModel),
                    ],
                    'collectionEndpoint' => [
                        'method' => 'GET',
                        'action' => action([TestControllerWithSpecifiedEndpoints::class, 'collectionEndpoint']),
                    ],
                ],
            ],
            'meta' => []
        ]);
    }

    /** @test */
    public function it_can_use_invokable_controllers_when_creating_endpoints()
    {
        $this->fakeRouter->invokableGet('/invoke/{testModel}', TestInvokableController::class);
        $this->fakeRouter->invokableGet('/invoke/collection', TestInvokableCollectionController::class);

        $testResource = new class(null) extends JsonResource
        {
            use HasEndpoints;

            public function toArray($request)
            {
                return [
                    'endpoints' => $this->endpoints(TestInvokableController::class),
                ];
            }

            public static function meta()
            {
                return [
                    'endpoints' => self::collectionEndpoints(TestInvokableCollectionController::class),
                ];
            }
        };

        $this->fakeRouter->get('/invoke', function () use ($testResource) {
            return $testResource::make(TestModel::first());
        });

        $this->get('/invoke')->assertExactJson([
            'data' => [
                'endpoints' => [
                    'sync' => [
                        'method' => 'GET',
                        'action' => action(TestInvokableController::class, $this->testModel),
                    ],
                ],
            ],
            'meta' => [
                'endpoints' => [
                    'invoke' => [
                        'method' => 'GET',
                        'action' => action(TestInvokableCollectionController::class),
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function it_will_generate_endpoints_when_making_a_resource_using_endpoint_groups()
    {
        $this->fakeRouter->invokableGet('/invoke/{testModel}', TestInvokableController::class);
        $this->fakeRouter->invokableGet('/invoke', TestInvokableCollectionController::class);

        $testResource = new class(null) extends JsonResource
        {
            use HasEndpoints;

            public function toArray($request)
            {
                return [
                    'endpoints' => $this->endpoints(function (EndpointsGroup $endpoints) {
                        $endpoints->controller(TestControllerWithSpecifiedEndpoints::class);
                        $endpoints->action([TestInvokableController::class])->name('invoke');
                    }),
                ];
            }

            public static function meta()
            {
                return [
                    'endpoints' => self::collectionEndpoints(function (EndpointsGroup $endpoints) {
                        $endpoints->controller(TestControllerWithSpecifiedEndpoints::class);
                        $endpoints->action([TestInvokableCollectionController::class])->name('invoke');
                    }),
                ];
            }
        };

        $this->fakeRouter->get('/index', function () use ($testResource) {
            return $testResource::make(TestModel::first());
        });

        $this->get('/index')->assertExactJson([
            'data' => [
                'endpoints' => [
                    'endpoint' => [
                        'method' => 'GET',
                        'action' => action([TestControllerWithSpecifiedEndpoints::class, 'endpoint'], $this->testModel),
                    ],
                    'invoke' => [
                        'method' => 'GET',
                        'action' => action([TestInvokableController::class], $this->testModel),
                    ],
                ],
            ],
            'meta' => [
                'endpoints' => [
                    'collectionEndpoint' => [
                        'method' => 'GET',
                        'action' => action([TestControllerWithSpecifiedEndpoints::class, 'collectionEndpoint']),
                    ],
                    'invoke' => [
                        'method' => 'GET',
                        'action' => action([TestInvokableCollectionController::class]),
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function it_can_use_different_http_verbs_with_the_same_uri()
    {
        $this->fakeRouter->get('/users/{testModel}', [TestController::class, 'show']);
        $this->fakeRouter->put('/users/{testModel}', [TestController::class, 'update']);

        $testResource = new class(null) extends JsonResource
        {
            use HasEndpoints;

            public function toArray($request)
            {
                return [
                    'endpoints' => $this->endpoints(function (EndpointsGroup $endpoints) {
                        $endpoints->controller(TestController::class)->methods(['show', 'update']);
                    }),
                ];
            }
        };

        $this->fakeRouter->get('/index', function () use ($testResource) {
            return $testResource::make(TestModel::first());
        });

        $this->get('/index')->assertExactJson([
            'data' => [
                'endpoints' => [
                    'show' => [
                        'method' => 'GET',
                        'action' => action([TestController::class, 'show'], $this->testModel),
                    ],
                    'update' => [
                        'method' => 'PUT',
                        'action' => action([TestController::class, 'update'], $this->testModel),
                    ],
                ],
            ],
            'meta' => [],
        ]);
    }
}
