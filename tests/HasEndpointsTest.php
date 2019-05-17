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
            'name' => 'Dumbi',
        ]);

        $this->fakeRouter->route('GET', '/local/{id}', [TestControllerWithSpecifiedEndpoints::class, 'endpoint']);
        $this->fakeRouter->route('GET', '/global/{id}', [TestControllerWithSpecifiedEndpoints::class, 'globalEndpoint']);
    }

    /** @test */
    public function it_will_generate_local_endpoints()
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
                    'endpoints' => $this->endpoints(TestControllerWithSpecifiedEndpoints::class, $this->resource),
                ];
            }
        };

        $this->assertEquals([
            'endpoint' => [
                'method' => 'GET',
                'action' => action([TestControllerWithSpecifiedEndpoints::class, 'endpoint'], $this->testModel),
            ],
        ], $this->getEndpoints($testResource));
    }

    /** @test */
    public function it_will_generate_global_endpoints()
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
                return [];
            }

            public static function meta()
            {
                return [
                    'endpoints' => self::globalEndpoints(TestControllerWithSpecifiedEndpoints::class, [])
                ];
            }
        };

        dd($testResource::collection(TestModel::all())->toResponse(request()));
    }

    private function getEndpoints(JsonResource $resource): array
    {
        $data = $resource::make($this->testModel)
            ->toResponse(request())
            ->getData(true);

        return $data['data']['endpoints'];
    }
}
