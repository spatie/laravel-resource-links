---
title: Merging endpoints
weight: 3
---

When creating a single resource like `UserResource::make($user)` you sometimes not only want the endpoints tied to that resource but also the collection endpoints for that resource. In this case, you want not only the `show`, `edit`, `update` and `delete` endpoints but also the `index`, `create` and `store` endpoints for a single resource.

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
         "name":"Ruben Van Assche",
         "endpoints":{  
            "show":{  
               "method":"GET",
               "action":"https://app.laravel/users/1"
            },
            "edit":{  
               "method":"GET",
               "action":"https://app.laravel/users/1/edit"
            },
            "update":{  
               "method":"PUT",
               "action":"https://app.laravel/users/1"
            },
            "delete":{  
               "method":"DELETE",
               "action":"https://app.laravel/users/1"
            },
            "index":{  
                "method":"GET",
                "action":"https://app.laravel/users"
             },
             "create":{  
                "method":"GET",
                "action":"https://app.laravel/users/create"
             },
             "store":{  
                "method":"POST",
                "action":"https://app.laravel/users"
             }
         }
      }  
   ],
}
```

#### Automatically merge collection endpoints

Sometimes you just want to automatically merge collection endpoints into a single endpoint resource when the model given to that resource does not exist or is null. This because you want to provide, for example, an endpoint to create a new model.

Calling `->mergeCollectionEndpoints()` on every resource can be a bit tedious. So when setting `automatically_merge_endpoints` in `config\laravel-resource-endpoints.php` to `true`, each single endpoint resource will merge it's collection endpoints when a non-existing or null model is given to the resource.

