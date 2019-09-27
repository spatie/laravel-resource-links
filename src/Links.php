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

    /**
     * @param string $controller
     *
     * @return \Spatie\ResourceLinks\LinkTypes\ControllerLinkType|\Spatie\ResourceLinks\LinkTypes\ActionLinkType
     */
    public function controller(string $controller)
    {
        $linkType = method_exists($controller, '__invoke')
            ? ActionLinkType::make([$controller])
            : ControllerLinkType::make($controller);

        $this->linkTypes[] = $linkType;

        return $linkType;
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
