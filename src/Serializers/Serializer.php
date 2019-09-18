<?php


namespace Spatie\ResourceLinks\Serializers;

interface Serializer
{
    public function format(Endpoint $endpoint): array;
}
