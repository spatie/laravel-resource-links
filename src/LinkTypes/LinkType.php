<?php

namespace Spatie\ResourceLinks\LinkTypes;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;

abstract class LinkType
{
    /** @var array */
    protected $parameters = [];

    /** @var string|null */
    protected $prefix;

    /** @var string|null */
    protected $serializer;

    abstract public function getLinks(Model $model = null): array;

    public function parameters(...$parameters)
    {
        foreach ($parameters as $parameter) {
            $this->parameters = array_merge($this->parameters, Arr::wrap($parameter));
        }

        return $this;
    }

    public function prefix(?string $prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function serializer(?string $serializer)
    {
        $this->serializer = $serializer;

        return $this;
    }
}
