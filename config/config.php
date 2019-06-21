<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Formatter
    |--------------------------------------------------------------------------
    |
    | The formatter will be used for the conversion of endpoints to their array
    | representation, when no formatter is explicitly defined for an endpoint
    | resource this formatter will be used.
    |
    */

    'formatter' => Spatie\LaravelEndpointResources\Formatters\DefaultFormatter::class,

    /*
    |--------------------------------------------------------------------------
    | Automatically merge endpoints
    |--------------------------------------------------------------------------
    |
    | When a single endpoint resource is being generated without an existing
    | model, this option make it possible to automatically merge the
    | collection endpoints into the resource.
    |
    */

    'automatically-merge-endpoints' => 'false',
];
