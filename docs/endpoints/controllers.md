---
title: Controllers
weight: 2
---

A controller can be added to an endpoint group as such:

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

It is possible to specify the parameters for the endpoints:

```php
$endpoints
    ->controller(UsersController::class)
    ->parameters(User::first());
```

Or prefix all the endpoints of the controller:

```php
$endpoints
    ->controller(UsersController::class)
    ->prefix('admin');
```

This will produce the following JSON:

``` json
"endpoints":{  
    "admin.show":{  
       "method":"GET",
       "action":"https://app.laravel/admin/users/1"
    },
    "admin.edit":{  
       "method":"GET",
       "action":"https://app.laravel/admin/users/1/edit"
    },
    
    ...
}
```

You can also choose the methods of the controller to include as endpoints:

```php
$endpoints
    ->controller(UsersController::class)
    ->methods(['create', 'index', 'show']);
```

Or even alias the name of methods:

```php
$endpoints
    ->controller(UsersController::class)
    ->names(['index' => 'list']);
```

This will produce the following JSON:

``` json
"endpoints":{  
    "list":{  
       "method":"GET",
       "action":"https://app.laravel/admin/users"
    },
    
    ...
}
```
