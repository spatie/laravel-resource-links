<?php

namespace Spatie\LaravelEndpointResources\EndpointTypes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

abstract class EndpointType
{
    /** @var array */
    protected $parameters = [];

    /** @var string|null */
    protected $prefix;

    /** @var string|null */
    protected $formatter;

    abstract public function getEndpoints(Model $model = null): array;

    public function parameters($parameters)
    {
        $this->parameters = Arr::wrap($parameters);

        return $this;
    }

    public function prefix(?string $prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function formatter(string $formatter)
    {
        $this->formatter = $formatter;

        return $this;
    }

    public function hasParameters(): bool
    {
        return count($this->parameters) > 0;
    }
}
