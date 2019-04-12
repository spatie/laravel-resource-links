<?php

namespace Spatie\LaravelEndpointResources\EndpointTypes;

use Exception;
use Illuminate\Database\Eloquent\Model;

final class ActionEndpointType extends EndpointType
{
    /** @var string */
    private $httpVerb;

    /** @var array */
    private $action;

    /** @var array */
    private $parameters;

    public function __construct(string $httpVerb, array $action, array $parameters = null)
    {
        $this->httpVerb = $httpVerb;
        $this->action = $action;
        $this->parameters = $parameters ?? [];
    }

    public function getEndpoints(Model $model = null): array
    {
        if (! in_array($this->httpVerb, ['GET', 'PUT', 'PATCH', 'POST', 'HEAD', 'DELETE'])) {
            throw new Exception("HttpVerb {$this->httpVerb} does not exist!");
        }

        $this->addModelToParameters($model);

        return [
            $this->action[1] => [
                'method' => $this->httpVerb,
                'action' => action($this->action, $this->parameters),
            ],
        ];
    }

    private function addModelToParameters(?Model $model)
    {
        if (! optional($model)->exists) {
            return;
        }

        foreach ($this->parameters as $parameter) {
            if (get_class($parameter) === get_class($model)) {
                return;
            }
        }

        $this->parameters[] = $model;
    }
}
