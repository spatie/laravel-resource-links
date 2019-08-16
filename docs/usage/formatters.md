---
title: Formatters
weight: 7
---

Want a different representation for endpoints? For example, something like this:

```json
"endpoints":{  
    "show":"https://app.laravel/users/1",
    "edit":"https://app.laravel/users/1/edit",
}
```

This can be done with formatters! You can create your own formatters by implementing the `Spatie\LaravelResourceEndpoints\Formatters\Formatter` interface.

The package includes 3 formatters:

- DefaultFormatter: the formatter from the examples above
- LayeredFormatter: this formatter will put prefixed endpoints in their own prefixed array
- UrlFormatter: a simple formatter which has an endpoint name as key and endpoint URL as value

You can set the formatter used in the `laravel-resource-endpoints.php` config file. Or if you are using endpoint groups, it is possible to set a formatter specifically for each endpoint:

```php
$endpoints
    ->controller(UsersController::class)
    ->formatter(Spatie\LaravelResourceEndpoints\Formatters\ UrlFormatter::class);
``` 
