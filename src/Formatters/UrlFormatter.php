<?php

namespace Spatie\ResourceLinks\Formatters;

class UrlFormatter implements Formatter
{
    public function format(Endpoint $endpoint): array
    {
        return [
            $endpoint->name => $endpoint->action,
        ];
    }
}
