<?php

namespace Spatie\ResourceLinks;

class LinkContainer
{
    /** @var string */
    public $name;

    /** @var string */
    public $method;

    /** @var string */
    public $action;

    /** @var null|string */
    public $prefix;

    public static function make(string $name, string $method, string $action, ?string $prefix)
    {
        return new self($name, $method, $action, $prefix);
    }

    public function __construct(string $name, string $method, string $action, ?string $prefix)
    {
        $this->name = $name;
        $this->method = $method;
        $this->action = $action;
        $this->prefix = $prefix;
    }
}
