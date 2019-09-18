<?php


namespace Spatie\ResourceLinks\Formatters;

interface Formatter
{
    public function format(Endpoint $endpoint): array;
}
