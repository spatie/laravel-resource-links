---
title: Resource setup
weight: 1
---

In your resources, add the `HasEndpoints` trait and a new key where the endpoints will be stored:

``` php
use Spatie\ResourceEndpoints\HasEndpoints;

class UserResource extends JsonResource
{
    use HasEndpoints;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'endpoints' => $this->endpoints(UsersController::class),
        ];
    }
}

```

Now every `UserResource` has an additional `EndpointResource` which in the responses will look like:

``` json
"endpoints": {
    "show": {
       "method": "GET",
       "action": "https://laravel.app/admin/users/1"
    },
    "edit": {
       "method": "GET",
       "action": "https://laravel.app/admin/users/1/edit"
    },
    "update": {
       "method": "PUT",
       "action": "https://laravel.app/admin/users/1"
    },
    "delete": {
       "method": "DELETE",
       "action": "https://laravel.app/admin/users/1"
    }
}
```

By default, we'll only construct endpoints from the `show`, `edit`, `update` and `delete` methods of your controller.

## Collection endpoints

What about endpoints like `index`, `create` and `store`? These endpoints are not tied to a single model instance, so it's not a good idea to store them at that level. Instead, it's better to put the links to those collection endpoints on the collection level of a resource.

You can put the collection endpoints in the meta section of a resource collection like so:

``` php
class UserResource extends JsonResource
{
    use HasEndpoints;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'endpoints' => $this->endpoints(UsersController::class),
        ];
    }

    public static function collection($resource)
    {
        return parent::collection($resource)->additional([
            'meta' => [
                'endpoints' => self::collectionEndpoints(UsersController::class)
             ],
         ]);
    }
}
```

Now when we create an `UserResource` collection, the meta section will look like this:

``` json
"meta": {
   "endpoints": {
      "index": {
         "method": "GET",
         "action": "https://laravel.app/admin/users"
      },
         "create": {
         "method": "POST",
         "action": "https://laravel.app/admin/users/create"
      },
      "store": {
         "method": "POST",
         "action": "https://laravel.app/admin/users"
      }
   },
   //
}
```

By default, the collection endpoints will only be constructed for the `index`, `create` and `store` methods in your controller.
