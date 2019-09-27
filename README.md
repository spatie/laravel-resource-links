# Laravel Resource Links

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-resource-links.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-resource-links)
[![Build Status](https://travis-ci.org/spatie/laravel-resource-links.svg?branch=master)](https://travis-ci.org/spatie/laravel-resource-links)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-resource-links.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-resource-links)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-resource-links.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-resource-links)

Let's say you have a `UsersController` with `index`, `show`, `create`, `edit`, `store`, `update` and `delete` methods and an `UserResource`. Wouldn't it be nice if you had the URL's to these methods immediately in your `UserResource` without having to construct them from scratch?

This package will add these links to your resource based upon a controller or actions you define. Let's look at an example of a resource.

``` php
class UserResource extends JsonResource
{
    use Spatie\ResourceLinks\HasLinks;
    use Spatie\ResourceLinks\HasMeta;

    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'links' => $this->links(UsersController::class),
        ];
    }

    public static function meta()
    {
        return [
            'links' => self::collectionLinks(UsersController::class),
        ];
    }
}
```

Now when creating an `UserResource` collection, you will have all the links from the `UserController` available:

```json
{
   "data":[
      {
         "id":1,
         "name": "Ruben Van Assche",
         "links": {
            "show": "https://laravel.app/users/1",
            "edit": "https://laravel.app/users/1/edit",
            "update": "https://laravel.app/users/1",
            "delete": "https://laravel.app/users/1"
         }
      }
   ],
   "meta": {
      "links": {
         "index": "https://laravel.app/users",
         "create": "https://laravel.app/users/create",
         "store":  "https://laravel.app/users"
      }
   }
}
```

## Why include links in your resources?

Let's say you're building a single-page application or an application built with [Inertia](https://inertiajs.com), then you have a PHP application running at the backend and a Javascript application at the front. These applications communicate with each other via an api but what if the frontend wants to route a user to another page?

Since routes are defined in the backend, the frontend has no idea where it has to route the user to. We could just write the url's in the javascript code but what if a route is changed? So why not pass these routes from the backend to the frontend? You could just manually write down all these routes, or let this package do that job for you.

## Setting up resource links

We have a dedicated [docs](https://docs.spatie.be/laravel-resource-links/v1/usage/resource-setup/) site for this package.

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](https://github.com/spatie/laravel-resource-links/blob/master/CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/laravel-resource-links/blob/master/CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Ruben Van Assche](https://github.com/rubenvanassche)
- [All Contributors](https://github.com/spatie/laravel-resource-links/contributors)

## Support us

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

Does your business depend on our contributions? Reach out and support us on [Patreon](https://www.patreon.com/spatie).
All pledges will be dedicated to allocating workforce on maintenance and new awesome stuff.

## License

The MIT License (MIT). Please see [License File](https://github.com/spatie/laravel-resource-links/blob/master/LICENSE.md) for more information.
