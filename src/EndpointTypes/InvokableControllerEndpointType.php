<?php

namespace Spatie\LaravelEndpointResources\EndpointTypes;

use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelEndpointResources\MultiEndpointType;

class InvokableControllerEndpointType extends EndpointType implements MultiEndpointType
{
    /** @var string */
    protected $controller;

    /** @var array */
    protected $defaultParameters;

    public function __construct(string $controller, array $defaultParameters = [])
    {
        $this->controller = $controller;
        $this->defaultParameters = $defaultParameters;
    }

    public function getEndpoints(Model $model = null): array
    {
        $endpointType = new ActionEndpointType([$this->controller], $this->defaultParameters);

        $controller = new $this->controller();

        $endPointName =  property_exists($controller, 'endPointMethod')
            ? $controller->endPointMethod
            : 'invoke';

        $endpointType->setName($endPointName);

        return $endpointType->getEndpoints($model);
    }

    public function getCollectionEndpoints(): array
    {
        $endpointType = new ActionEndpointType([$this->controller], $this->defaultParameters);

        $controller = new $this->controller();

        $endPointName =  property_exists($controller, 'collectionEndPointMethod')
            ? $controller->collectionEndPointMethod
            : 'invoke';

        $endpointType->setName($endPointName);

        return $endpointType->getEndpoints();
    }
}
