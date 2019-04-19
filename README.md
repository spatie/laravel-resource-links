# Laravel Endpoint Resources

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-endpoint-resources.svg?style=flat-square)](https://packagist.org/packages/spatie/:package_name)
[![Build Status](https://img.shields.io/travis/spatie/laravel-endpoint-resources/master.svg?style=flat-square)](https://travis-ci.org/spatie/:package_name)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-endpoint-resources.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/:package_name)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-endpoint-resources.svg?style=flat-square)](https://packagist.org/packages/spatie/:package_name)

Add endpoints to your Laravel api resources without a hassle

## Installation

You can install the package via composer:

```bash
composer require spatie/laravel-endpoint-resources
```

## Usage

In your resources, add the `HasEndpoints` trait and a new attribute where the endpoints will be stored

``` php
class UserResource extends JsonResource
{
    use HasEndpoints;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'endpoints' => $this->endpoints(UsersController::class),
        ];
    }
}

```


Now every UserResource has an additional EndpointResource which will in responses look like

``` json
"endpoints":{  
    "show":{  
       "method":"GET",
       "action":"htts://app.laravel/admin/users/2"
    },
    "update":{  
       "method":"PUT",
       "action":"htts://app.laravel/admin/users/2"
    },
    "delete":{  
       "method":"DELETE",
       "action":"htts://app.laravel/admin/users/2"
    }
```

By default we'll only construct endpoints from the `show`, `update` and `delete` methods of your controller.  This can be easily changed by adding the `$endpointMethods` property on your controller:

``` php
class UsersController
{
    public $endPointMethods = ['show', 'edit', 'update', 'delete'];
    
    ...
}
```

### Global Endpoints
We haven't talked about endpoints like `index`, `create` and `store`, we could include these endpoints by setting the `$endPointMethods` property on the controller. But then every resource in an collection will always have three identical routes. A more efficient solution would be to store the routes on an higher level, so we don't generate these endpoints for every resource. 

Since resource collections not only have a data section but also a links and meta section. It is wisely to put endpoints like `index`, `create` and `store` which don't depend on the model here. 

You can put the global endpoints inthe meta section of a resource collection like so:

``` php
class UserResource extends JsonResource
{
    use HasEndpoints;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'endpoints' => $this->endpoints(UsersController::class),
        ];
    }
    
    public static function collection($resource)
    {
        return parent::collection($resource)
            ->additional(self::getGlobalEndpoints(UsersController::class));
    }
}
```

Now when we create an UserResource collection, the meta section will look like this:


``` json
   "meta":{  
      "endpoints":{  
         "index":{  
            "method":"GET",
            "action":"htts://app.laravel/admin/users"
         },
         "store":{  
            "method":"POST",
            "action":"htts://app.laravel/admin/users"
         }
      }
   }
```


By default the global endpoints will only be constructed from the `index` and `store` methods in your controller. This behaviour can be changed by setting `$globalEndpointMethods` property on your controller:

``` php
class UsersController
{
    public $globalEndpointMethods = ['index', 'create', 'store'];
    
    ...
}
```

### Route parameters

An endpoint resource will try to deduce the parameters for a route as best as possible when generating the url to that route. We'll firstly look at the model given to the resource and after that we search in the parameters of the current route for parameters that can be used to construct the route.

It is not always possible to automtically deduce all the parameters for a route, that's why you can specify your own set of parameters:

```php
class UserResource extends JsonResource
{
    use HasEndpoints;

    public function toArray($request)
    {
        return [
            'endpoints' => $this->endpoints(UsersController::class, [
            		'user' => Auth::user(),
            ]),
            ...
        ];
    }
}
```

The parameters for a global endpoints can also be set: 
   
``` php
class UserResource extends JsonResource
{
	...
    
    public static function collection($resource)
    {
        return parent::collection($resource)
            ->additional(self::getGlobalEndpoints(
            	UsersController::class,
            	[
            		'user' => Auth::user()
        		]
			));
    }
}
```

Endpoint resources will stop automatically deducing parameters when you manually give a set of parameters.

### Other endpoints

Sometimes you want to add endpoints not belonging to the controller, then

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Postcardware

You're free to use this package, but if it makes it to your production environment we highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

We publish all received postcards [on our company website](https://spatie.be/en/opensource/postcards).

## Credits

- [Ruben Van Assche](https://github.com/rubenvanassche)
- [All Contributors](../../contributors)

## Support us

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

Does your business depend on our contributions? Reach out and support us on [Patreon](https://www.patreon.com/spatie). 
All pledges will be dedicated to allocating workforce on maintenance and new awesome stuff.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
