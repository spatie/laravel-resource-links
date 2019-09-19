---
title: Invokable controllers
weight: 3
---

An invokable controller can be added as such:

``` php
class UserResource extends JsonResource
{
    use HasLinks;

    public function toArray($request)
    {
        return [
            'links' => $this->links(function (Links $links) {
                $links->invokableController(DownloadUserController::class);
            }),
        ];
    }
}
```

By default the `__invoke()` method of this controller will be the only link that will be created named `invoke. This will produce following JSON:

``` json
"links": {
    "invoke": {
       "method": "GET",
       "action": "https://laravel.app/admin/users/1/download"
    },
}
```

You can alias `invoke` to another name:

```php
$links
    ->invokableController(DownloadUserController::class)
    ->name('download');
```

Now your JSON will look like this:

``` json
"links": {
    "download": {
       "method": "GET",
       "action": "https://laravel.app/admin/users/1/download"
    },
}
```

Just like a regular controller it is possible to specify the parameters for the links:

```php
$links
    ->invokableController(DownloadUserController::class)
    ->parameters(User::first());
```

Or prefix the link:

```php
$links
    ->invokableController(DownloadUserController::class)
    ->prefix('admin');
```
