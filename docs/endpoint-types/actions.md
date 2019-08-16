---
title: Endpoint groups
weight: 4
---

Next to controller, you can also add actions to an endpoints group:

``` php
class UserResource extends JsonResource
{
    use HasEndpoints;

    public function toArray($request)
    {
        return [
            'endpoints' => $this->endpoints(function (EndpointsGroup $endpoints) {
                $endpoints->action([UsersController::class, 'create']);
            }),
        ];
    }
}
```

Is possible to specify the parameters for the endpoints:

```php
$endpoints
    ->action([UsersController::class, 'create'])
    ->parameters(User::first());
```

Or prefix the endpoint:

```php
$endpoints
    ->action([UsersController::class, 'create'])
    ->prefix('admin');
```

The name of the action can also be changed:

```php
$endpoints
    ->action([UsersController::class, 'create'])
    ->name('build');
```

Changing the Http verb(POST, GET, ...) of the action can be done as such:
 
```php
$endpoints
    ->action([UsersController::class, 'create'])
    ->httpVerb('POST');
```
