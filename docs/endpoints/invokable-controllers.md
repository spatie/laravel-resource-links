---
title: Invokable controllers
weight: 3
---

An invokable controller can be added as such: 

``` php
class UserResource extends JsonResource
{
    use HasEndpoints;

    public function toArray($request)
    {
        return [
            'endpoints' => $this->endpoints(function (EndpointsGroup $endpoints) {
                $endpoints->invokableController(DownloadUserController::class);
            }),
        ];
    }
}
```

By default the `__invoke()` method of this controller will be the only endpoint that will be created named `invoke. This will produce following JSON:

``` json
"endpoints":{  
    "invoke":{  
       "method":"GET",
       "action":"https://app.laravel/admin/users/1/download"
    },
}

You can alias `invoke` to another name:

```php
$endpoints
    ->invokableController(UsersController::class)
    ->name('download');
```

Now your JSON will look like this:

``` json
"endpoints":{  
    "download":{  
       "method":"GET",
       "action":"https://app.laravel/admin/users/1/download"
    },
}
```

Just like a regular controller it is possible to specify the parameters for the endpoints:

```php
$endpoints
    ->invokableController(DownloadUserController::class)
    ->parameters(User::first());
```

Or prefix the endpoint:

```php
$endpoints
    ->invokableController(DownloadUserController::class)
    ->prefix('admin');
```
