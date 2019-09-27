<?php

namespace Spatie\ResourceLinks\LinkTypes;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Routing\Router;
use Illuminate\Database\Eloquent\Model;

class ActionLinkType extends LinkType
{
    /** @var array|string */
    private $action;

    /** @var string|null */
    private $httpVerb;

    /** @var string|null */
    private $name;

    public static function make($action): ActionLinkType
    {
        return new self($action);
    }

    public function __construct($action)
    {
        $this->action = $action;
    }

    public function httpVerb(?string $httpVerb): ActionLinkType
    {
        $this->httpVerb = $httpVerb;

        return $this;
    }

    public function name(?string $name): ActionLinkType
    {
        $this->name = $name;

        return $this;
    }

    public function getLinks(Model $model = null): array
    {
        $formattedAction = $this->formatAction();

        $route = app(Router::class)
            ->getRoutes()
            ->getByAction($formattedAction);

        if ($route === null) {
            throw new Exception("Route `{$formattedAction}` does not exist!");
        }

        return RouteLinkType::make($route)
            ->name($this->resolveName())
            ->httpVerb($this->httpVerb)
            ->prefix($this->prefix)
            ->parameters($this->getParameters($model))
            ->serializer($this->serializer)
            ->getLinks($model);
    }

    private function formatAction(): string
    {
        return is_array($this->action)
            ? trim('\\'.implode('@', $this->action), '\\')
            : $this->action;
    }

    private function resolveName(): ?string
    {
        if ($this->name !== null) {
            return $this->name;
        }

        if ($this->isInvokableController()) {
            return 'invoke';
        }

        return null;
    }

    private function isInvokableController(): bool
    {
        if (is_array($this->action) && count($this->action) > 1) {
            return false;
        }

        $action = is_array($this->action) ? $this->action[0] : $this->action;

        return ! Str::contains($action, '@');
    }

    private function getParameters(?Model $model)
    {
        if (! optional($model)->exists) {
            return $this->parameters;
        }

        return array_merge($this->parameters, [$model]);
    }
}
