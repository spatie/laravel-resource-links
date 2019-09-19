<?php

namespace Spatie\ResourceLinks\Serializers;

class DefaultSerializer implements Serializer
{
    public function format(LinkContainer $linkContainer): array
    {
        return [
            "{$linkContainer->prefix}{$linkContainer->name}" => [
                'method' => $linkContainer->method,
                'action' => $linkContainer->action,
            ],
        ];
    }
}
