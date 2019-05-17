# Laravel Endpoint Resources

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-endpoint-resources.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-endpoint-resources)
[![Build Status](https://img.shields.io/travis/spatie/laravel-endpoint-resources/master.svg?style=flat-square)](https://travis-ci.org/spatie/laravel-endpoint-resources)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-endpoint-resources.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-endpoint-resources)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-endpoint-resources.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-endpoint-resources)

Add endpoints to your Laravel API Resources without a hassle!

Let's say you have a UsersController with `index`, `show`, `create`, `edit`, `store`, `update` and `delete` methods and an UserResource. Wouldn't it be nice if you had the URL's to these methods immediately in your UserResource without having to construct them from scratch?

Laravel endpoint resources will add these endpoints to your resource based upon a controller or actions you define. Let's look at an example of a resource.

## Example

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
            ->additional([
                'meta' => [
                    'endpoints' => self::globalEndpoints(UsersController::class)
                 ],
             ]);
    }
}
```

Now when creating an UserResource collection, you will have all the endpoints from the UserController available:

```json
{  
   "data":[  
      {  
         "id":1,
         "name":"Ruben Van Assche",
         "endpoints":{  
            "show":{  
               "method":"GET",
               "action":"https://app.laravel/users/1"
            },
            "edit":{  
               "method":"GET",
               "action":"https://app.laravel/users/1/edit"
            },
            "update":{  
               "method":"PUT",
               "action":"https://app.laravel/users/1"
            },
            "delete":{  
               "method":"DELETE",
               "action":"https://app.laravel/users/1"
            }
         }
      }  
   ],
   "meta":{  
      "endpoints":{  
         "index":{  
            "method":"GET",
            "action":"https://app.laravel/users"
         },
         "create":{  
            "method":"GET",
            "action":"https://app.laravel/users/create"
         },
         "store":{  
            "method":"POST",
            "action":"https://app.laravel/users"
         }
      }
   }
}
```


## Installation

You can install the package via composer:

```bash
composer require spatie/laravel-endpoint-resources
```

## Usage

In your resources, add the `HasEndpoints` trait and a new attribute where the endpoints will be stored:

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


Now every UserResource has an additional EndpointResource which in responses will look like:

``` json
"endpoints":{  
    "show":{  
       "method":"GET",
       "action":"https://app.laravel/admin/users/1"
    },
    "edit":{  
       "method":"GET",
       "action":"https://app.laravel/admin/users/1/edit"
    },
    "update":{  
       "method":"PUT",
       "action":"https://app.laravel/admin/users/1"
    },
    "delete":{  
       "method":"DELETE",
       "action":"https://app.laravel/admin/users/1"
    }
```

By default, we'll only construct endpoints from the `show`, `edit`, `update` and `delete` methods of your controller.  This can be easily changed by adding the `$endpointMethods` property on your controller:

``` php
class UsersController
{
    public $endPointMethods = ['show', 'update', 'delete', 'send'];
    
    ...
}
```

### Global Endpoints
What about endpoints like `index`, `create` and `store`? We could include these endpoints by setting the `$endPointMethods` property on the controller. But then every item in a resource collection would always have three identical endpoints. A more efficient solution is to store the endpoints on a higher level, so we don't generate these endpoints for every item in the resource collection. 

Since resource collections not only can have a data section but also a meta section. It is wisely to put endpoints like `index`, `create` and `store` which don't depend on the model here. 

You can put the global endpoints in the meta section of a resource collection like so:

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
        return parent::collection($resource)->additional([
            'meta' => [
                'endpoints' => self::globalEndpoints(UsersController::class)
             ],
         ]);
    }
}
```

Now when we create an UserResource collection, the meta section will look like this:


``` json
   "meta":{  
      "endpoints":{  
         "index":{  
            "method":"GET",
            "action":"https://app.laravel/admin/users"
         },
          "create":{  
            "method":"POST",
            "action":"https://app.laravel/admin/users/create"
         }
         "store":{  
            "method":"POST",
            "action":"https://app.laravel/admin/users"
         }
      }
   }
```


By default, the global endpoints will only be constructed from the `index`, `create`  and `store` methods in your controller. This behavior can be changed by setting `$globalEndpointMethods` property on your controller:

``` php
class UsersController
{
    public $globalEndpointMethods = ['store', 'nuke'];
    
    ...
}
```

#### A small helper

If you don't want to overwrite the `collection` function every time you want to add a global endpoint to a resource collection, then you're in luck. We've added a little helper which puts endpoints immediately in the meta section of a resource collection:

``` php
class UserResource extends JsonResource
{
    ...
    
    public static function meta()
    {
        return [
            'endpoints' => self::globalEndpoints(UsersController::class)
        ];
    }
}
```

This meta function will always be added when you use the `HasEndpoints` trait.

#### Global endpoints and a single resource

When creating a single resource `UserResource::make($user)` you sometimes only want to have an object with a data section and without a meta section. So you can, for example, convert the resource to an array.

In this case, global endpoints will be excluded, and only the endpoints specific to the resource will be included. You can merge global endpoints with the other endpoints like so:

``` php
class UserResource extends JsonResource
{
    use HasEndpoints;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'endpoints' => $this->endpoints(UsersController::class)->mergeGlobalEndpoints(),
        ];
    }
}

```

The UserResource in a response will now look like this:

```json
{  
   "data":[  
      {  
         "id":1,
         "name":"Ruben Van Assche",
         "endpoints":{  
            "show":{  
               "method":"GET",
               "action":"https://app.laravel/users/1"
            },
            "edit":{  
               "method":"GET",
               "action":"https://app.laravel/users/1/edit"
            },
            "update":{  
               "method":"PUT",
               "action":"https://app.laravel/users/1"
            },
            "delete":{  
               "method":"DELETE",
               "action":"https://app.laravel/users/1"
            },
            "index":{  
                "method":"GET",
                "action":"https://app.laravel/users"
             },
             "create":{  
                "method":"GET",
                "action":"https://app.laravel/users/create"
             },
             "store":{  
                "method":"POST",
                "action":"https://app.laravel/users"
             }
         }
      }  
   ],
}
```


### Route parameters

An endpoint resource will try to deduce the parameters for a route as best as possible when generating the endpoint to that route. The first thing we do is looking at the model given to your resource, and we try to create the endpoints from that.

If no model is given or more parameters are required, then we'll start searching the parameters of the current route for parameters that can be used to construct the endpoint.

It is not always possible to automatically deduce all the parameters for a route, that's why it is possible to specify the parameters for an endpoint:

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


You can also set the parameters for a global endpoints:
   
``` php
class UserResource extends JsonResource
{
    use HasEndpoints;

    ...
    
    public static function collection($resource)
    {
        return parent::collection($resource)->additional([
            'meta' => [
                'endpoints' => self::globalEndpoints(UsersController::class, [
                    'user' => Auth::user()
                ])
             ],
         ]);
    }
}
```

An endpoint will only be added when all parameters required are present. Endpoint resources will stop automatically deducing parameters when you manually give a set of parameters.

### Action endpoints

Sometimes you want to add endpoints not belonging to a specific controller. Then it is possible to add an action as an endpoint. This looks just like a standard Laravel action:

``` php
class OtherResource extends JsonResource
{
    use HasEndpoints;

    public function toArray($request)
    {
        return [
            'endpoints' => $this->endpoints()->addAction([UsersController::class, 'create']),
        ];
    }
}
```

We'll try to deduce the parameters for constructing the endpoint. But you can also manually set the parameters:


``` php
class OtherResource extends JsonResource
{
    use HasEndpoints;

    public function toArray($request)
    {
        $user = Auth::user();

        return [
            'endpoints' => $this->endpoints()
                ->addAction([UsersController::class, 'show'], [$user]),
        ];
    }
}
```
 
The HTTP verb for the action will be deduced from the route in Laravel. Should you have an action with two verbs, then you can always specify the verb for a particular action:

``` php
class OtherResource extends JsonResource
{
    use HasEndpoints;

    public function toArray($request)
    {
        $user = Auth::user();

        return [
            'endpoints' => $this->endpoints()
                ->addAction([UsersController::class, 'update'], $user, 'PUT'),
        ];
    }
}
```

Of course, it is also possible to use this with global endpoints:

``` php
class UserResource extends JsonResource
{
    use HasEndpoints;

    ...
    
    public static function collection($resource)
    {
        return parent::collection($resource)->additional([
            'meta' => [
                'endpoints' => self::globalEndpoints(UsersController::class)
                    ->addAction([UsersController::class, 'update'], $user, 'PUT'),
             ],
         ]);
    }
}
```

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
