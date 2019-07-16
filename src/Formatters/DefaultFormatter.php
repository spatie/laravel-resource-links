<?php

namespace Spatie\LaravelResourceEndpoints\Formatters;

class DefaultFormatter implements Formatter
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
