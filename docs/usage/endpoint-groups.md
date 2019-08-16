---
title: Endpoint groups
weight: 6
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

When working with invokable controllers, you can alias `__invoke`:

```php
$endpoints
    ->controller(UsersController::class)
    ->name('publish');
```

It is also possible to add action endpoints:

```php
$endpoints
    ->action([UsersController::class, 'create'])
    ->prefix('users')
    ->parameters(User::first())
    ->name('build')
```

You can change the Http verb(POST, GET, ...) of the action
 
```php
$endpoints
    ->action([UsersController::class, 'create'])
    ->httpVerb('POST');
```

And off course it is possible to use endpoint groups with collection endpoints:

``` php
class UserResource extends JsonResource
{
    use HasEndpoints;

    ...
    
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
