<?php

namespace Spatie\ResourceLinks\Tests;

use Spatie\ResourceLinks\Tests\Fakes\TestModel;
use Spatie\ResourceLinks\Tests\Fakes\FakeController;

class HasMetasTest extends TestCase
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

        $this->fakeRouter->get('/index', [FakeController::class, 'index']);
        $this->fakeRouter->get('/show/{id}', [FakeController::class, 'show']);
    }

    /** @test */
    public function it_will_generate_links_and_metas_when_collecting_a_resource()
    {
        $this->fakeRouter->get('TestModel', 'Spatie\ResourceLinks\Tests\Fakes\FakeController@index');

        $this->get('/TestModel')->assertExactJson([
            'data' => [
                0 => [
                    'links' => [
                        'show' => [
                            'method' => 'GET',
                            'action' => action([FakeController::class, 'show'], $this->testModel),
                        ],
                    ],
                ],
            ],
            'meta' => [
                'links' => [
                    'index' => [
                        'method' => 'GET',
                        'action' => action([FakeController::class, 'index']),
                    ],
                ],
            ],
        ]);
    }
}
