---
title: Endpoint groups
weight: 1
---

Sometimes a more fine-grained control is needed to construct endpoints. Let's say you want to prefix a set of endpoints, change the name of an endpoint, or specify which endpoints to include. That's where endpoint groups come into place. You can now create a resource with controller endpoint as such:

``` php
class UserResource extends JsonResource
{
    use HasEndpoints;

    public function toArray($request)
    {
        return [
            'endpoints' => $this->endpoints(function (EndpointsGroup $endpoints) {
                $endpoints->controller(UsersController::class);
            }),
        ];
    }
}
```

Off course it is possible to use endpoint groups with collection endpoints:

``` php
class UserResource extends JsonResource
{
    use HasEndpoints;

    //

    public static function collection($resource)
    {
        return parent::collection($resource)->additional([
            'meta' => [
                'endpoints' => self::collectionEndpoints(function (EndpointsGroup $endpoints) {
                    $endpoints->controller(UsersController::class);
                })
             ],
         ]);
    }
}
```

In the following sections we'll explain which endpoint types you can create in an endpoint group.
