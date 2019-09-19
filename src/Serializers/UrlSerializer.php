<?php

namespace Spatie\ResourceLinks\Serializers;

class UrlSerializer implements Serializer
{
    public function format(LinkContainer $linkContainer): array
    {
        return [
            $linkContainer->name => $linkContainer->action,
        ];
    }
}
