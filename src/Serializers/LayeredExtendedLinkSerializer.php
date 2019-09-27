<?php

namespace Spatie\ResourceLinks\Serializers;

use Spatie\ResourceLinks\LinkContainer;

class LayeredExtendedLinkSerializer implements Serializer
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
