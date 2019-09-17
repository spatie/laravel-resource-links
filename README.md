# Laravel Resource Endpoints

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-resource-endpoints.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-resource-endpoints)
[![Build Status](https://travis-ci.org/spatie/laravel-resource-endpoints.svg?branch=master)](https://travis-ci.org/spatie/laravel-resource-endpoints)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-resource-endpoints.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-resource-endpoints)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-resource-endpoints.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-resource-endpoints)

Let's say you have a `UsersController` with the usual `index`, `show`, `create`, `edit`, `store`, `update`, and `delete` methods. Wouldn't it be nice if you had the URLs to these methods readily available in your `UserResource` without having to construct them from scratch?

This package will add these endpoints to your resource based upon a controller or actions you define. Let's look at an example of a resource.

``` php
use Spatie\ResourceEndpoints\HasEndpoints;
use Spatie\ResourceEndpoints\HasMeta;

class UserResource extends JsonResource
{
    use HasEndpoints;
    use HasMeta;

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'endpoints' => $this->endpoints(UsersController::class),
        ];
    }

    public static function meta()
    {
        return [
            'endpoints' => self::collectionEndpoints(UsersController::class),
        ];
    }
}
```

Now when creating an `UserResource` collection, you will have all the endpoints from the `UserController` available:

```json
{
   "data": [
      {
         "id": 1,
         "name": "Ruben Van Assche",
         "endpoints": {
            "show": {
               "method": "GET",
               "action": "https://laravel.app/users/1"
            },
            "edit": {
               "method": "GET",
               "action": "https://laravel.app/users/1/edit"
            },
            "update": {
               "method": "PUT",
               "action": "https://laravel.app/users/1"
            },
            "delete": {
               "method": "DELETE",
               "action": "https://laravel.app/users/1"
            }
         }
      }
   ],
   "meta": {
      "endpoints": {
         "index": {
            "method": "GET",
            "action": "https://laravel.app/users"
         },
         "create": {
            "method": "GET",
            "action": "https://laravel.app/users/create"
         },
         "store": {
            "method": "POST",
            "action": "https://laravel.app/users"
         }
      }
   }
}
```

## Why include endpoints in your resources?

When building a SPA or an application with [Inertia](https://inertiajs.com), you'll have PHP running on the server and Javascript on the client. These applications communicate with each other via an API or by passing JSON. The client doesn't have access to the `action` and `route` helpers like Blade does.

You could hard code URLs in the JavaScript app, but that makes it difficult to refactor. This package streamlines the process of passing URLs to the client in your Laravel resources.

## Setting up resource endpoints

We have a dedicated [docs](https://docs.spatie.be/laravel-resource-endpoints/v1/usage/resource-setup/) site for this package.

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](https://github.com/spatie/laravel-resource-endpoints/blob/master/CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/laravel-resource-endpoints/blob/master/CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Ruben Van Assche](https://github.com/rubenvanassche)
- [All Contributors](https://github.com/spatie/laravel-resource-endpoints/contributors)

## Support us

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

Does your business depend on our contributions? Reach out and support us on [Patreon](https://www.patreon.com/spatie).
All pledges will be dedicated to allocating workforce on maintenance and new awesome stuff.

## License

The MIT License (MIT). Please see [License File](https://github.com/spatie/laravel-resource-endpoints/blob/master/LICENSE.md) for more information.
