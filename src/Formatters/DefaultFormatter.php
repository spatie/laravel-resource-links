<?php

namespace Spatie\LaravelEndpointResources\Formatters;

class DefaultFormatter implements Formatter
{
    public function format(Endpoint $endpoint): array
    {
        $format = [
            $endpoint->name => [
                'method' => $endpoint->method,
                'action' => $endpoint->action,
            ],
        ];

        if($endpoint->prefix !== null){
            return [
                $endpoint->prefix => $format
            ];
        }

        return $format;
    }
}
