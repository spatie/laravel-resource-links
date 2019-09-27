<?php

namespace Spatie\ResourceLinks;

use Closure;
use Illuminate\Support\Arr;
use Spatie\ResourceLinks\LinkTypes\ControllerLinkType;
use Spatie\ResourceLinks\LinkTypes\LinkType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;

class LinkResource extends JsonResource
{
    /** @var string */
    private $linkResourceType;

    /** @var \Spatie\ResourceLinks\Links */
    private $linksGroup;

    public static function create(Model $model = null, string $linkResourceType = null): LinkResource
    {
        return new self($model, $linkResourceType);
    }

    public function __construct(Model $model = null, string $linkResourceType = null)
    {
        parent::__construct($model);

        $this->linkResourceType = $linkResourceType ?? LinkResourceType::ITEM;
        $this->linksGroup = new Links();
    }

    public function link($link, $parameters = null, $httpVerb = null): LinkResource
    {
        if ($link instanceof Closure) {
            $link($this->linksGroup);

            return $this;
        }

        if (is_array($link)) {
            $this->linksGroup
                ->action($link)
                ->httpVerb($httpVerb)
                ->parameters(Arr::wrap($parameters));

            return $this;
        }

        if (is_string($link) && method_exists($link, '__invoke')) {
            $this->linksGroup
                ->action([$link])
                ->parameters(Arr::wrap($parameters));

            return $this;
        }

        if (is_string($link)) {
            $this->linksGroup
                ->controller($link)
                ->parameters(Arr::wrap($parameters));

            return $this;
        }

        return $this;
    }

    public function action(array $action, $parameters = null, $httpVerb = null): LinkResource
    {
        return $this->link($action, $parameters, $httpVerb);
    }

    public function controller(string $controller, $parameters = null): LinkResource
    {
        return $this->link($controller, $parameters);
    }

    public function links(Closure $closure): LinkResource
    {
        return $this->link($closure);
    }

    public function withCollectionLinks(): LinkResource
    {
        $this->linkResourceType = LinkResourceType::ALL;

        return $this;
    }

    public function toArray($request)
    {
        return $this->linksGroup
            ->getLinkTypes()
            ->mapWithKeys(function (LinkType $linkType) use ($request) {
                $linkType->parameters($request->route()->parameters());

                if ($linkType instanceof ControllerLinkType) {
                    return $this->resolveLinksFromControllerLinkType($linkType);
                }

                return $linkType->getLinks($this->resource);
            });
    }

    private function resolveLinksFromControllerLinkType(ControllerLinkType $controllerLinkType): array
    {
        if ($this->linkResourceType === LinkResourceType::ITEM) {
            return $controllerLinkType->getLinks($this->resource);
        }

        if ($this->linkResourceType === LinkResourceType::COLLECTION) {
            return $controllerLinkType->getCollectionLinks();
        }

        if ($this->linkResourceType === LinkResourceType::ALL) {
            return array_merge(
                $controllerLinkType->getLinks($this->resource),
                $controllerLinkType->getCollectionLinks()
            );
        }

        return [];
    }
}
