<?php

namespace Spatie\ResourceLinks\LinkTypes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class InvokableControllerLinkType extends LinkType
{
    /** @var string */
    private $controller;

    /** @var string|null */
    private $name;

    public static function make(string $controller): InvokableControllerLinkType
    {
        return new InvokableControllerLinkType($controller);
    }

    public function __construct(string $controller)
    {
        $this->controller = $controller;
    }

    public function name(?string $name): InvokableControllerLinkType
    {
        $this->name = $name;

        return $this;
    }

    public function getLinks(Model $model = null): array
    {
        return $this->resolveLinkType()->getLinks($model);
    }

    private function resolveLinkType(): ActionLinkType
    {
        return ActionLinkType::make([$this->controller])
            ->name($this->name ?? 'invoke')
            ->parameters($this->parameters)
            ->prefix($this->prefix)
            ->serializer($this->serializer);
    }
}
