<?php

namespace Spatie\ResourceLinks\Tests\Fakes;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Spatie\ResourceLinks\HasLinks;
use Spatie\ResourceLinks\HasMeta;

class FakeResourceCollection extends ResourceCollection
{
    use HasLinks, HasMeta;

    public function toArray($request)
    {
      return FakeResource::collection($this->collection);
    }
};
