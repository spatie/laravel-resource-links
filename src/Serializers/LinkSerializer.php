<?php

namespace Spatie\ResourceLinks\Serializers;

class LinkSerializer implements Serializer
{
    public function format(LinkContainer $linkContainer): array
    {
        return [
            $linkContainer->name => $linkContainer->action,
        ];
    }
}
