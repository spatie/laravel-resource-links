<?php

namespace Spatie\ResourceLinks\Tests;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Spatie\ResourceLinks\Links;
use Spatie\ResourceLinks\HasMeta;
use Spatie\ResourceLinks\HasLinks;
use Spatie\ResourceLinks\Tests\Fakes\TestModel;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\ResourceLinks\Tests\Fakes\TestController;
use Spatie\ResourceLinks\Tests\Fakes\TestInvokableController;
use Spatie\ResourceLinks\Tests\Fakes\TestResource;

class HasLinksTest extends TestCase
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
    public function it_will_generate_links_when_making_a_resource()
    {
        $testResource = new class(null) extends JsonResource
        {
            use HasLinks, HasMeta;

            public function toArray($request)
            {
                return [
                    'links' => $this->links(TestController::class),
                ];
            }
        };

        $this->fakeRouter->get('/resource', function () use ($testResource) {
            return $testResource::make(TestModel::first());
        });

        $this->get('/resource')->assertExactJson([
            'data' => [
                'links' => [
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
    public function it_will_generate_links_when_collecting_a_resource()
    {
        $testResource = new class(null) extends JsonResource
        {
            use HasLinks, HasMeta;

            public function toArray($request)
            {
                return [
                    'links' => $this->links(TestController::class),
                ];
            }

            public static function meta()
            {
                return [
                    'links' => self::collectionLinks(TestController::class),
                ];
            }
        };

        $this->fakeRouter->get('/resource', function () use ($testResource) {
            return $testResource::collection(TestModel::all());
        });

        $this->get('/resource')->assertExactJson([
            'data' => [
                0 => [
                    'links' => [
                        'show' => [
                            'method' => 'GET',
                            'action' => action([TestController::class, 'show'], $this->testModel),
                        ],
                    ],
                ],
            ],
            'meta' => [
                'links' => [
                    'index' => [
                        'method' => 'GET',
                        'action' => action([TestController::class, 'index']),
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function it_can_use_invokable_controllers_when_creating_links()
    {
        $this->fakeRouter->invokableGet('/show/{testModel}', TestInvokableController::class);

        $testResource = new class(null) extends JsonResource
        {
            use HasLinks, HasMeta;

            public function toArray($request)
            {
                return [
                    'links' => $this->links(TestInvokableController::class),
                ];
            }
        };

        $this->fakeRouter->get('/resource', function () use ($testResource) {
            return $testResource::make(TestModel::first());
        });

        $this->get('/resource')->assertExactJson([
            'data' => [
                'links' => [
                    'invoke' => [
                        'method' => 'GET',
                        'action' => action(TestInvokableController::class, $this->testModel),
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function it_will_generate_links_when_making_a_resource_using_links()
    {
        $this->fakeRouter->invokableGet('/invoke/{testModel}', TestInvokableController::class);

        $testResource = new class(null) extends JsonResource
        {
            use HasLinks, HasMeta;

            public function toArray($request)
            {
                return [
                    'links' => $this->links(function (Links $links) {
                        $links->controller(TestController::class);
                        $links->controller(TestInvokableController::class)->name('invoke');
                    }),
                ];
            }

            public static function meta()
            {
                return [
                    'links' => self::collectionLinks(function (Links $links) {
                        $links->controller(TestController::class);
                        $links->action([TestController::class, 'index'])->name('action');
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
                    'links' => [
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
                'links' => [
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
            use HasLinks, HasMeta;

            public function toArray($request)
            {
                return [
                    'id' => $this->id,
                    'links' => $this->links(TestController::class),
                ];
            }

            public static function meta()
            {
                return [
                    'links' => self::collectionLinks(TestController::class),
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
                    'links' => [
                        'show' => [
                            'method' => 'GET',
                            'action' => action([TestController::class, 'show'], $this->testModel),
                        ],
                    ],
                ],
                [
                    'id' => 2,
                    'links' => [
                        'show' => [
                            'method' => 'GET',
                            'action' => action([TestController::class, 'show'], $otherTestModel),
                        ],
                    ],
                ],
            ],
            'meta' => [
                'links' => [
                    'index' => [
                        'method' => 'GET',
                        'action' => action([TestController::class, 'index']),
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function it_can_use_collections()
    {
        $testResourceCollection = new class(TestModel::all()) extends ResourceCollection
        {
            use HasMeta, HasLinks;

            public $collects = TestResource::class;

            public static function meta()
            {
                return [
                    'links' => self::collectionLinks(TestController::class),
                ];
            }
        };

        $this->fakeRouter->get('/resource', function () use ($testResourceCollection) {
            return $testResourceCollection;
        });

        $this->get('/resource')->assertExactJson([
            'data' => [
                0 => [
                    'links' => [
                        'show' => [
                            'method' => 'GET',
                            'action' => action([TestController::class, 'show'], $this->testModel),
                        ],
                    ],
                ],
            ],
            'meta' => [
                'links' => [
                    'index' => [
                        'method' => 'GET',
                        'action' => action([TestController::class, 'index']),
                    ],
                ],
            ],
        ]);
    }
}
