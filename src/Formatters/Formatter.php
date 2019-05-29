<?php


namespace Spatie\LaravelEndpointResources\Formatters;


interface Formatter
{
    public function format(Endpoint $endpoint): array;
}
