---
title: Introduction
weight: 1
---

**This package is under heavy development, things will change, and documentation may not be up to date!**

Let's say you have a `UsersController` with `index`, `show`, `create`, `edit`, `store`, `update` and `delete` methods and an `UserResource`. Wouldn't it be nice if you had the URL's to these methods immediately in your `UserResource` without having to construct them from scratch?

This package will add these endpoints to your resource based upon a controller or actions you define. Let's look at an example of a resource.

``` php
class UserResource extends JsonResource
{
    use Spatie\LaravelResourceEndpoints\HasEndpoints;

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'endpoints' => $this->endpoints(UsersController::class),
        ];
    }
    
    public static function collection($resource)
    {
        return parent::collection($resource)
            ->additional([
                'meta' => [
                    'endpoints' => self::collectionEndpoints(UsersController::class)
                 ],
             ]);
    }
}
```

Now when creating an `UserResource` collection, you will have all the endpoints from the `UserController` available:

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
            }
         }
      }  
   ],
   "meta":{  
      "endpoints":{  
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
}
```

## Why include endpoints in your resources?

Let's say you're having a single-page application or an application built with [Inertia](https://inertiajs.com), then you have a PHP application running at the backend and a Javascript application at the front. These applications communicate with each other via an api but what if the frontend wants to route a user to another page? 

Since routes are defined in the backend, the frontend has no idea where it has to route the user to. We could just write the url's in the javascript code but what if a route is changed? So why not pass these routes from the backend to the frontend? You can manually write down all these routes, or just use this package.
