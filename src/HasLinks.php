<?php

namespace Spatie\ResourceLinks;

use Closure;

/** @mixin \Illuminate\Http\Resources\Json\JsonResource */
trait HasLinks
{
    /**
     * @param string|\Closure|null|array $controller
     * @param null $parameters
     *
     * @return \Spatie\ResourceLinks\LinkResource
     */
    public function links($controller = null, $parameters = null): LinkResource
    {
        $resource = LinkResource::create($this->resource, LinkResourceType::ITEM)->link($controller, $parameters);

        if (property_exists($this, 'withCollectionLinks') && $this->withCollectionLinks === true) {
            $resource->withCollectionLinks();
        }

        return $resource;
    }

    /**
     * @param string|\Closure|null|array $controller
     * @param null $parameters
     *
     * @return \Spatie\ResourceLinks\LinkResource
     */
    public static function collectionLinks($controller = null, $parameters = null): LinkResource
    {
        return LinkResource::create(null, LinkResourceType::COLLECTION)->link($controller, $parameters);
    }
}
