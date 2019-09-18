<?php

namespace Spatie\ResourceLinks;

use Illuminate\Support\Collection;
use Spatie\ResourceLinks\EndpointTypes\ActionEndpointType;
use Spatie\ResourceLinks\EndpointTypes\ControllerEndpointType;
use Spatie\ResourceLinks\EndpointTypes\InvokableControllerEndpointType;

class Links
{
    /** @var \Illuminate\Support\Collection */
    private $endpointTypes;

    public function __construct()
    {
        $this->endpointTypes = new Collection();
    }

    public function controller(string $controller): ControllerEndpointType
    {
        $controllerEndpointType = ControllerEndpointType::make($controller);

        $this->endpointTypes[] = $controllerEndpointType;

        return $controllerEndpointType;
    }

    public function invokableController(string $controller): InvokableControllerEndpointType
    {
        $invokableControllerEndpointType = InvokableControllerEndpointType::make($controller);

        $this->endpointTypes[] = $invokableControllerEndpointType;

        return $invokableControllerEndpointType;
    }

    public function action(array $action): ActionEndpointType
    {
        $actionEndpointType = ActionEndpointType::make($action);

        $this->endpointTypes[] = $actionEndpointType;

        return $actionEndpointType;
    }

    public function endpointsGroup(Links $endpointsGroup)
    {
        $this->endpointTypes = $this->endpointTypes->merge(
            $endpointsGroup->getEndpointTypes()
        );
    }

    public function getEndpointTypes(): Collection
    {
        return $this->endpointTypes;
    }
}
