<?php


namespace Spatie\LaravelResourceEndpoints\Formatters;

interface Formatter
{
    public function format(Endpoint $endpoint): array;
}
