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

    public function __construct(Route $route, Model $model, array $parameters)
    {
        $message = "Endpoint resource couldn't generate endpoint for ";
        $message .= "Constructing route: {$route->uri()} ";
        $message .= "For action: {$route->getActionName()} ";

        if($model){
            $message .= "With model: {$model->getMorphClass()} with id: {$model->getKey()}";
        }

        parent::__construct($message);
    }
}
