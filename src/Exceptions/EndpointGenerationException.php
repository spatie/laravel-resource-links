<?php

namespace Spatie\LaravelEndpointResources\Exceptions;

use Exception;
use Illuminate\Routing\Route;
use Illuminate\Database\Eloquent\Model;

class EndpointGenerationException extends Exception
{
    public static function make(Route $route, ?Model $model, array $parameters)
    {
        return new self($route, $model, $parameters);
    }

    public function __construct(Route $route, ?Model $model, array $parameters)
    {
        $message = "Endpoint resource couldn't generate endpoint for \n";
        $message .= "Constructing route: {$route->uri()} \n";
        $message .= "For action: {$route->getActionName()} \n";

        if ($model) {
            $message .= $model->exists
                ? "With model: {$model->getMorphClass()} with id: {$model->getKey()} \n"
                : "With non existing model of type: {$model->getMorphClass()} \n";
        }

        if (! empty($parameters)) {
            $message .= "Following parameters were provided: ";
            $message .= implode(', ', $parameters) . '\n';
        }

        parent::__construct($message);
    }
}
