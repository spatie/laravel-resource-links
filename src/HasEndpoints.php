<?php

namespace Spatie\ResourceLinks;

use Closure;

/** @mixin \Illuminate\Http\Resources\Json\JsonResource */
trait HasEndpoints
{
    /**
     * @param string|Closure|null|array $controller
     * @param null $parameters
     *
     * @return \Spatie\ResourceLinks\LinkResource
     */
    public function endpoints($controller = null, $parameters = null): LinkResource
    {
        $resource = LinkResource::create($this->resource, LinkResourceType::ITEM)->endpoint($controller, $parameters);

        if (property_exists($this, 'mergeCollectionEndpoints') && $this->mergeCollectionEndpoints === true) {
            $resource->mergeCollectionEndpoints();
        }

        return $resource;
    }

    /**
     * @param string|Closure|null|array $controller
     * @param null $parameters
     *
     * @return \Spatie\ResourceLinks\LinkResource
     */
    public static function collectionEndpoints($controller = null, $parameters = null): LinkResource
    {
        return LinkResource::create(null, LinkResourceType::COLLECTION)->endpoint($controller, $parameters);
    }
}
