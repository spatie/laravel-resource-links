<?php

namespace Spatie\ResourceLinks\Serializers;

use Spatie\ResourceLinks\LinkContainer;

interface Serializer
{
    public function format(LinkContainer $linkContainer): array;
}
