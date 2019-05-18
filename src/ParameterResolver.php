<?php

namespace Spatie\LaravelEndpointResources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Routing\Route;
use ReflectionParameter;

class ParameterResolver
{
    /** @var \Illuminate\Database\Eloquent\Model|null */
    protected $model;

    /** @var array */
    protected $defaultParameters;

    public function __construct(?Model $model, array $defaultParameters = [])
    {
        $this->model = $model;

        $this->defaultParameters = $defaultParameters;
    }

    public function forRoute(Route $route): array
    {
        $providedParameters = $this->getProvidedParameters();

        return collect($route->signatureParameters())
            ->mapWithKeys(function (ReflectionParameter $signatureParameter) use ($providedParameters) {
                return [
                    $signatureParameter->getName() => $this->resolveParameter(
                        $signatureParameter,
                        $providedParameters
                    ),
                ];
            })
            ->reject(function ($parameter) {
                return $parameter === null;
            })->all();
    }

    protected function getProvidedParameters(): array
    {
        return optional($this->model)->exists
            ? array_merge($this->defaultParameters, [$this->model])
            : $this->defaultParameters;
    }

    protected function resolveParameter(ReflectionParameter $signatureParameter, array $providedParameters)
    {
        if (array_key_exists($signatureParameter->getName(), $providedParameters)) {
            return $providedParameters[$signatureParameter->getName()];
        }

        foreach ($providedParameters as $providedParameter) {
            if (! is_object($providedParameter) || $signatureParameter->getType() === null) {
                continue;
            }

            if ($signatureParameter->getType()->getName() === get_class($providedParameter)) {
                return $providedParameter;
            }
        }

        return null;
    }
}
