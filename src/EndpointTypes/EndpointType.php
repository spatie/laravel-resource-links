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

    public function hasParameters() : bool
    {
        return count($this->parameters) > 0;
    }
}
