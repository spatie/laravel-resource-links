<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Serializer
    |--------------------------------------------------------------------------
    |
    | The serializer will be used for the conversion of links to their array
    | representation, when no serializer is explicitly defined for an link
    | resource this serializer will be used.
    |
    */

    'serializer' => Spatie\ResourceLinks\Serializers\LinkSerializer::class,
];
