<?php

namespace Spatie\LaravelEndpointResources\Formatters;

class DefaultFormatter implements Formatter
{
    public function format(string $name, string $method, string $action): array
    {
        return [
            $name => [
                'method' => $method,
                'action' => $action,
            ],
        ];
    }
}
