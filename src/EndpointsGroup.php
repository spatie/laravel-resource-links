<?php

namespace Spatie\LaravelResourceEndpoints;

use Illuminate\Support\Collection;
use Spatie\LaravelResourceEndpoints\EndpointTypes\ActionEndpointType;
use Spatie\LaravelResourceEndpoints\EndpointTypes\ControllerEndpointType;
use Spatie\LaravelResourceEndpoints\EndpointTypes\InvokableControllerEndpointType;

class EndpointsGroup
{
    /** @var \Illuminate\Support\Collection */
    private $endpointTypes;

    public function __construct()
    {
        $this->endpointTypes = new Collection();
    }

    public function controller(string $controller): ControllerEndpointType
    {
        if (method_exists($controller, '__invoke')) {
            return $this->invokableController($controller);
        }

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

    public function endpointsGroup(EndpointsGroup $endpointsGroup)
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
