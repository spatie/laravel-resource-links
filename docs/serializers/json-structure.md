---
title: Json structure
weight: 1
---

Want a different representation for links? For example, something like this:

```json
"links": {
    "show": "https://laravel.app/users/1",
    "edit": "https://laravel.app/users/1/edit",
}
```

This can be done with serializers! You can create your own serializers by implementing the `Spatie\ResourceLinks\Serializers\Serializer` interface.

The package includes 3 serializers:

- DefaultSerializer: the serializer from the all the previous examples
- LayeredSerializer: this serializer will put prefixed links in their own prefixed array
- UrlSerializer: a simple serializer which has an link name as key and link URL as value

You can set the serializer used in the `resource-links.php` config file. Or if you are using link groups, it is possible to set a serializer specifically for each link:

```php
$links
    ->controller(UsersController::class)
    ->serializer(Spatie\LaravelResourceLinks\Serializers\ UrlSerializer::class);
```
