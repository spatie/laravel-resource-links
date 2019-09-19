<?php

namespace Spatie\ResourceLinks\Serializers;

class LayeredSerializer implements Serializer
{
    public function format(LinkContainer $linkContainer): array
    {
        $format = [
            $linkContainer->name => [
                'method' => $linkContainer->method,
                'action' => $linkContainer->action,
            ],
        ];

        return is_null($linkContainer->prefix)
            ? $format
            : [$linkContainer->prefix => $format];
    }
}
