<?php

namespace Spatie\LaravelEndpointResources\Tests;

use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\LaravelEndpointResources\HasEndpoints;
use Spatie\LaravelEndpointResources\Tests\Fakes\TestControllerWithSpecifiedEndpoints;
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

        $this->fakeRouter->route('GET', '/local/{id}', [TestControllerWithSpecifiedEndpoints::class, 'endpoint']);
        $this->fakeRouter->route('GET', '/global', [TestControllerWithSpecifiedEndpoints::class, 'collectionEndpoint']);
    }

    /** @test */
    public function it_will_generate_endpoints_when_making_a_resource()
    {
        $testResource = new class(null) extends JsonResource {
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

        $this->fakeRouter->route('GET', '/index', function () use ($testResource) {
            return $testResource::make(TestModel::first());
        });

        $this->get('/index')->assertJson([
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
        $testResource = new class(null) extends JsonResource {
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

        $this->fakeRouter->route('GET', '/index', function () use ($testResource) {
            return $testResource::collection(TestModel::all());
        });

        $this->get('/index')->assertJson([
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
    public function it_can_merge_global_endpoints_with_local_endpoints()
    {
        $testResource = new class(null) extends JsonResource {
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

        $this->fakeRouter->route('GET', '/index', function () use ($testResource) {
            return $testResource::make(TestModel::first());
        });

        $this->get('/index')->assertJson([
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
        ]);
    }
}
