<?php

namespace Spatie\LaravelResourceEndpoints\Formatters;

class LayeredFormatter implements Formatter
{
    public function format(Endpoint $endpoint): array
    {
        $format = [
            $endpoint->name => [
                'method' => $endpoint->method,
                'action' => $endpoint->action,
            ],
        ];

        return is_null($endpoint->prefix)
            ? $format
            : [$endpoint->prefix => $format];
    }
}
