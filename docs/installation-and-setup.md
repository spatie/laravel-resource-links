---
title: Installation and setup
weight: 3
---

## Basic installation

You can install this package via composer:

```bash
composer require spatie/laravel-resource-endpoints
```

The package will automatically register a service provider.

Publishing the config file is optional:

```bash
php artisan vendor:publish --provider="Spatie\ResourceLinks\ResourceEndpointsServiceProvider" --tag="config"
```

This is the default content of the config file:

```php
return [

    /*
    |--------------------------------------------------------------------------
    | Formatter
    |--------------------------------------------------------------------------
    |
    | The formatter will be used for the conversion of endpoints to their array
    | representation, when no formatter is explicitly defined for an endpoint
    | resource this formatter will be used.
    |
    */

    'formatter' => Spatie\ResourceLinks\Formatters\DefaultFormatter::class,
];
```
