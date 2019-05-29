<?php

namespace Spatie\LaravelEndpointResources;

use Illuminate\Support\Collection;
use Spatie\LaravelEndpointResources\EndpointTypes\ActionEndpointType;
use Spatie\LaravelEndpointResources\EndpointTypes\ControllerEndpointType;
use Spatie\LaravelEndpointResources\EndpointTypes\InvokableControllerEndpointType;

class EndpointsCollection
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

    public function endpointsCollection(EndpointsCollection $endpointsCollection)
    {
        $this->endpointTypes = $this->endpointTypes->merge(
            $endpointsCollection->getEndpointTypes()
        );
    }

    public function getEndpointTypes(): Collection
    {
        return $this->endpointTypes;
    }
}
