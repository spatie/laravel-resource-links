<?php

namespace Spatie\ResourceLinks\Serializers;

use Spatie\ResourceLinks\LinkContainer;

class LinkSerializer implements Serializer
{
    public function format(LinkContainer $linkContainer): array
    {
        return [
            $linkContainer->name => $linkContainer->action,
        ];
    }
}
