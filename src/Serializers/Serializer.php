<?php


namespace Spatie\ResourceLinks\Serializers;

interface Serializer
{
    public function format(LinkContainer $linkContainer): array;
}
