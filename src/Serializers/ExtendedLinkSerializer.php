<?php

namespace Spatie\ResourceLinks\Serializers;

use Spatie\ResourceLinks\LinkContainer;

class ExtendedLinkSerializer implements Serializer
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
