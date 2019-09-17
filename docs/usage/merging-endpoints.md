---
title: Merging endpoints
weight: 5
---

When creating a single resource like `UserResource::make($user)` you not only want the endpoints tied to that resource but also the collection endpoints for that resource. In this case next to the `show`, `edit`, `update` and `delete` endpoints you also want the `index`, `create` and `store` endpoints in your resource.

This can be done by merging the collection endpoints with the single resource endpoints like so:

``` php
class UserResource extends JsonResource
{
    use HasEndpoints;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'endpoints' => $this->endpoints(UsersController::class)->mergeCollectionEndpoints(),
        ];
    }
}

```

The `UserResource` in a response will now look like this:

```json
{
   "data":[
      {
         "id":1,
         "name": "Ruben Van Assche",
         "endpoints": {
            "show": {
               "method": "GET",
               "action": "https://laravel.app/users/1"
            },
            "edit": {
               "method": "GET",
               "action": "https://laravel.app/users/1/edit"
            },
            "update": {
               "method": "PUT",
               "action": "https://laravel.app/users/1"
            },
            "delete": {
               "method": "DELETE",
               "action": "https://laravel.app/users/1"
            },
            "index": {
                "method": "GET",
                "action": "https://laravel.app/users"
             },
             "create": {
                "method": "GET",
                "action": "https://laravel.app/users/create"
             },
             "store": {
                "method": "POST",
                "action": "https://laravel.app/users"
             }
         }
      }
   ],
}
```

### Automatically merge collection endpoints

Calling `mergeCollectionEndpoints` on every resource can be a bit tedious. That's why when you include the `Spatie\LaravelResourceEndpoints\HasMeta` we'll not only add the [meta](https://docs.spatie.be/laravel-resource-endpoints/v1/usage/meta-helper/) helper but also automatic endpoint merging when you would make a single resource.

Let's have a look, now when creating a single resource like so:

```php
UserResource::make($user);
```

You would get all the endpoints: `show`, `edit`, `update`, `delete`, `index`, `create` and `store`. This will only work when making a single resource, collection resources will have their collection endpoints in the meta section.
