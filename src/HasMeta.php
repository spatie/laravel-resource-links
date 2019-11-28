<?php

namespace Spatie\ResourceLinks;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Spatie\ResourceLinks\Tests\HasLinksTest;

/** @mixin \Illuminate\Http\Resources\Json\JsonResource */
trait HasMeta
{
    public static function collection($resource)
    {
        $meta = self::meta();

        if (! count($meta)) {
            return parent::collection($resource);
        }

        return parent::collection($resource)->additional([
            'meta' => $meta,
        ]);
    }

    public static function make(...$parameters)
    {
        return parent::make(...$parameters)->withCollectionLinks();
    }

    public function toResponse($request)
    {
        if (is_subclass_of($this, ResourceCollection::class)) {
            $this->additional([
                'meta' => $meta = self::meta(),
            ]);
        }

        return parent::toResponse($request);
    }

    public static function meta()
    {
        return [];
    }
}
