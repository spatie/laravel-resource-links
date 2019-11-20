<?php

namespace Spatie\ResourceLinks\LinkTypes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

abstract class LinkType
{
    /** @var array */
    protected $parameters = [];

    /** @var string|null */
    protected $prefix;

    /** @var string|null */
    protected $serializer;

    /** @var string|null */
    protected $query;

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

    public function query(?string $query)
    {
        $this->query = $query;

        return $this;
    }
}
