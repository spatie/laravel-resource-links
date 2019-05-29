<?php

namespace Spatie\LaravelEndpointResources\Formatters;

class FlatFormatter implements Formatter
{
    public function format(Endpoint $endpoint): array
    {
        return [
            "{$endpoint->prefix}{$endpoint->name}" => [
                'method' => $endpoint->method,
                'action' => $endpoint->action,
            ],
        ];
    }
}
