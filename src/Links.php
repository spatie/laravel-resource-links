<?php

namespace Spatie\ResourceLinks;

use Illuminate\Support\Collection;
use Spatie\ResourceLinks\LinkTypes\ActionLinkType;
use Spatie\ResourceLinks\LinkTypes\ControllerLinkType;
use Spatie\ResourceLinks\LinkTypes\InvokableControllerLinkType;

class Links
{
    /** @var \Illuminate\Support\Collection */
    private $linkTypes;

    public function __construct()
    {
        $this->linkTypes = new Collection();
    }

    public function controller(string $controller): ControllerLinkType
    {
        $controllerLinkType = ControllerLinkType::make($controller);

        $this->linkTypes[] = $controllerLinkType;

        return $controllerLinkType;
    }

    public function invokableController(string $controller): ActionLinkType
    {
        $actionLinkType = ActionLinkType::make($controller);

        $this->linkTypes[] = $actionLinkType;

        return $actionLinkType;
    }

    public function action(array $action): ActionLinkType
    {
        $actionLinkType = ActionLinkType::make($action);

        $this->linkTypes[] = $actionLinkType;

        return $actionLinkType;
    }

    public function links(Links $links)
    {
        $this->linkTypes = $this->linkTypes->merge(
            $links->getLinkTypes()
        );
    }

    public function getLinkTypes(): Collection
    {
        return $this->linkTypes;
    }
}
