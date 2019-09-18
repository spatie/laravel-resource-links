<?php

namespace Spatie\ResourceLinks\Tests;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Spatie\ResourceLinks\LinkResource;
use Spatie\ResourceLinks\LinkResourceType;
use Spatie\ResourceLinks\Links;
use Spatie\ResourceLinks\Formatters\LayeredFormatter;
use Spatie\ResourceLinks\HasEndpoints;
use Spatie\ResourceLinks\HasMeta;
use Spatie\ResourceLinks\Tests\Fakes\TestController;
use Spatie\ResourceLinks\Tests\Fakes\TestControllerWithSpecifiedEndpoints;
use Spatie\ResourceLinks\Tests\Fakes\TestInvokableCollectionController;
use Spatie\ResourceLinks\Tests\Fakes\TestInvokableController;
use Spatie\ResourceLinks\Tests\Fakes\TestModel;

class HasEndpointsTest extends TestCase
{
    /** @var \Spatie\ResourceLinks\Tests\Fakes\TestModel */
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
                    'endpoints' => $this->endpoints(function (Links $endpoints) {
                        $endpoints->controller(TestController::class);
                        $endpoints->invokableController(TestInvokableController::class)->name('invoke');
                    }),
                ];
            }

            public static function meta()
            {
                return [
                    'endpoints' => self::collectionEndpoints(function (Links $endpoints) {
                        $endpoints->controller(TestController::class);
                        $endpoints->action([TestController::class, 'index'])->name('action');
                    }),
                ];
            }
        };

        $this->fakeRouter->get('/resource', function () use ($testResource) {
            return $testResource::collection(TestModel::all());
        });

        $this->get('/resource')->assertExactJson([
            'data' => [
                [
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

    /** @test */
    public function collections_are_generated_correctly()
    {
        $otherTestModel = TestModel::create([
            'id' => 2,
            'name' => 'testModel',
        ]);

        $testResource = new class(null) extends JsonResource
        {
            use HasEndpoints, HasMeta;

            public function toArray($request)
            {
                return [
                    'id' => $this->id,
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

        $this->fakeRouter->get('/collection/{testModel}', function () use ($testResource) {
            return $testResource::collection(TestModel::all());
        });

        $this->get("/collection/{$this->testModel->id}")->assertExactJson([
            'data' => [
                [
                    'id' => 1,
                    'endpoints' => [
                        'show' => [
                            'method' => 'GET',
                            'action' => action([TestController::class, 'show'], $this->testModel),
                        ],
                    ],
                ],
                [
                    'id' => 2,
                    'endpoints' => [
                        'show' => [
                            'method' => 'GET',
                            'action' => action([TestController::class, 'show'], $otherTestModel),
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
}
