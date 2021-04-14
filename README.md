This package is abandoned: [Laravel resource links two years later](https://rubenvanassche.com/laravel-resource-links-two-years-later/)

# Add links to Laravel API resources

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-resource-links.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-resource-links)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/spatie/laravel-resource-links/run-tests?label=tests)
![Check & fix styling](https://github.com/spatie/laravel-resource-links/workflows/Check%20&%20fix%20styling/badge.svg)
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

Now when creating a `UserResource` collection, you will have all the links from the `UserController` available:

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

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-resource-links.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-resource-links)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

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

## Credits

- [Ruben Van Assche](https://github.com/rubenvanassche)
- [All Contributors](https://github.com/spatie/laravel-resource-links/contributors)

## License

The MIT License (MIT). Please see [License File](https://github.com/spatie/laravel-resource-links/blob/master/LICENSE.md) for more information.
