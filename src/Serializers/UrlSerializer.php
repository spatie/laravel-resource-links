<?php

namespace Spatie\ResourceLinks\Serializers;

class UrlSerializer implements Serializer
{
    public function format(Endpoint $endpoint): array
    {
        return [
            $endpoint->name => $endpoint->action,
        ];
    }
}
