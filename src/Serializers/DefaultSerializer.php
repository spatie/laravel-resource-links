<?php

namespace Spatie\ResourceLinks\Serializers;

class DefaultSerializer implements Serializer
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
