<?php

namespace Spatie\ResourceLinks\EndpointTypes;

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

    public function parameters(...$parameters)
    {
        foreach ($parameters as $parameter){
            $this->parameters = array_merge($this->parameters, Arr::wrap($parameter));
        }

        return $this;
    }

    public function prefix(?string $prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    public function formatter(?string $formatter)
    {
        $this->formatter = $formatter;

        return $this;
    }
}
