<?php

namespace Spatie\LaravelResourceEndpoints;

/** @mixin \Illuminate\Http\Resources\Json\JsonResource */
trait HasMeta
{
    /** @var bool  */
    private $mergeCollectionEndpoints = false;

    public static function collection($resource)
    {
        $meta = self::meta();

        if (! count($meta)) {
            parent::collection($resource);
        }

        return parent::collection($resource)->additional([
            'meta' => $meta,
        ]);
    }

    public static function make(...$parameters)
    {
        return parent::make(...$parameters)->mergeCollectionEndpoints();

//        $meta = self::meta();
//
//        if (! count($meta)) {
//            parent::make(...$parameters);
//        }
//
//        return parent::make(...$parameters)->additional([
//            'meta' => $meta,
//        ]);
    }

    public function mergeCollectionEndpoints()
    {
        $this->mergeCollectionEndpoints = true;

        return $this;
    }

    public static function meta()
    {
        return [];
    }
}
