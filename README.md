# Laravel Resource Endpoints

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-resource-endpoints.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-resource-endpoints)
[![Build Status](https://travis-ci.org/spatie/laravel-resource-endpoints.svg?branch=master)](https://travis-ci.org/spatie/laravel-resource-endpoints)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-resource-endpoints.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-resource-endpoints)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-resource-endpoints.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-resource-endpoints)

**This package is under heavy development, things will change and documentation may not be up to date!**

Let's say you have a `UsersController` with `index`, `show`, `create`, `edit`, `store`, `update` and `delete` methods and an `UserResource`. Wouldn't it be nice if you had the URL's to these methods immediately in your `UserResource` without having to construct them from scratch?

Laravel endpoint resources will add these endpoints to your resource based upon a controller or actions you define. Let's look at an example of a resource.

``` php
class UserResource extends JsonResource
{
    use Spatie\LaravelResourceEndpoints\HasEndpoints;

    public function toArray($request): array
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
                    'endpoints' => self::collectionEndpoints(UsersController::class)
                 ],
             ]);
    }
}
```

Now when creating an `UserResource` collection, you will have all the endpoints from the `UserController` available:

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
composer require spatie/laravel-resource-endpoints
```

## Usage

In your resources, add the `Spatie\LaravelResourceEndpoints\HasEndpoints` trait and a new attribute where the endpoints will be stored:

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


Now every `UserResource` has an additional `EndpointResource` in which the responses will look like:

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
}
```

By default, we'll only construct endpoints from the `show`, `edit`, `update` and `delete` methods of your controller.  You can modify this behaviour by adding the `$endpointMethods` property on your controller:

``` php
class UsersController
{
    public $endPointMethods = ['show', 'update', 'delete', 'send'];
    
    ...
}
```

### Collection endpoints

What about endpoints like `index`, `create` and `store`? These endpoints are not generally not tied to a single item, so it's not a good idea to store them at that level. Instead it's better to put the links to those collection endpoints on the collection level of a resource.

You can put the collection endpoints in the meta section of a resource collection like so:

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
                'endpoints' => self::collectionEndpoints(UsersController::class)
             ],
         ]);
    }
}
```

Now when we create an `UserResource` collection, the meta section will look like this:

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
         },
         "store":{  
            "method":"POST",
            "action":"https://app.laravel/admin/users"
         }
      }
   }
```


By default, the collection endpoints will only be constructed from the `index`, `create`  and `store` methods in your controller. This behavior can be changed by setting `$collectionEndpointMethods` property on your controller:

``` php
class UsersController
{
    public $collectionEndpointMethods = ['store', 'nuke'];
    
    ...
}
```

#### A small helper

We've added a little helper which puts endpoints immediately in the meta section of a resource collection:

``` php
class UserResource extends JsonResource
{
    ...
    
    public static function meta()
    {
        return [
            'endpoints' => self::collectionEndpoints(UsersController::class)
        ];
    }
}
```

This meta function will always be added when you use the `HasEndpoints` trait.

#### Collection endpoints and a single resource

When creating a single resource `UserResource::make($user)` you sometimes only want to have an object with a data section and without a meta section. So you can, for example, convert the resource to an array.

In this case, collection endpoints will be excluded, and only the endpoints specific to the resource will be included. You can merge collection endpoints with the other endpoints like so:

``` php
class UserResource extends JsonResource
{
    use HasEndpoints;

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'endpoints' => $this->endpoints(UsersController::class)->mergeCollectionEndpoints(),
        ];
    }
}

```

The `UserResource` in a response will now look like this:

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

#### Automatically merge collection endpoints

Sometimes you yust want to automatically merge collection endpoints into a single endpoint resource when the model given to that resource does not exist or is null. Calling `->mergeCollectionEndpoints()` on every resource can be a bit tedious. So when setting `automatically-merge-endpoints` in `config\laravel-resource-endpoints.php` to true, each single endpoint resource will merge it's collection endpoints when a non-existing or null model is given to the resource.


### Route parameters

An endpoint resource will try to deduce the parameters for a route as best as possible when generating the endpoint to that route. The first thing we do is looking at the model given to your resource, and the packages tries to create the endpoints from that.

If no model is given or more parameters are required, then the package will start searching the parameters of the current route for parameters that can be used to construct the endpoint.

You can also manually specify the route parameters.

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


You can also set the parameters for a collection endpoints:
   
``` php
class UserResource extends JsonResource
{
    use HasEndpoints;

    ...
    
    public static function collection($resource)
    {
        return parent::collection($resource)->additional([
            'meta' => [
                'endpoints' => self::collectionEndpoints(UsersController::class, [
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

Of course, it is also possible to use this with collection endpoints:

``` php
class UserResource extends JsonResource
{
    use HasEndpoints;

    ...
    
    public static function collection($resource)
    {
        return parent::collection($resource)->additional([
            'meta' => [
                'endpoints' => self::collectionEndpoints(UsersController::class)
                    ->addAction([UsersController::class, 'update'], $user, 'PUT'),
             ],
         ]);
    }
}
```

### Endpoint groups

Sometimes a more fine grained control is needed to construct endpoints. Let's say you want to prefix a set of endpoints, change the name of an endpoint or just specify which endpoints to include. That's where endpoint groups come into place. You can now create a resource witg controller endpoints and an action endpoint as such:

``` php
class UserResource extends JsonResource
{
    use HasEndpoints;

    public function toArray($request)
    {
        return [
            'endpoints' => $this->endpoints(function (EndpointsGroup $endpoints) {
                $endpoints->controller(UsersController::class);
                $endpoints->action([UsersController::class, 'create']);
            }),
        ];
    }
}
```

You can now specify the parameters for the endpoints:

```php
$endpoints
	->controller(UsersController::class)
	->parameters(User::first());
```

Or prefix all the endpoints of the controller:

```php
$endpoints
	->controller(UsersController::class)
	->prefix('admin');
```

This will produce the following json:

``` json
"endpoints":{  
    "admin.show":{  
       "method":"GET",
       "action":"https://app.laravel/admin/users/1"
    },
    "admin.edit":{  
       "method":"GET",
       "action":"https://app.laravel/admin/users/1/edit"
    },
    
    ...
}
```

You can also choose the methods of the controller to include as endpoints:

```php
$endpoints
	->controller(UsersController::class)
	->methods(['create', 'index', 'show']);
```

Or even alias the name of methods:

```php
$endpoints
	->controller(UsersController::class)
	->names(['index' => 'list']);
```

This will produce the following json:

``` json
"endpoints":{  
    "list":{  
       "method":"GET",
       "action":"https://app.laravel/admin/users"
    },
    
    ...
}
```

When working with invokable controllers you can alias `__invoke`:

```php
$endpoints
	->controller(UsersController::class)
	->name('publish');
```

Action endpoints can also be adjusted to their specific needs

```php
$endpoints
	->action([UsersController::class, 'create'])
	->prefix('users')
	->parameters(User::first())
	->name('build')
```

It is even possible to change the http verb(POST, GET, ...)
 
```php
$endpoints
	->action([UsersController::class, 'create'])
	->httpVerb('POST');
```

And off course it is possible to use endpoint groups with collection endpoints:

``` php
class UserResource extends JsonResource
{
    use HasEndpoints;

    ...
    
    public static function collection($resource)
    {
        return parent::collection($resource)->additional([
            'meta' => [
                'endpoints' => self::collectionEndpoints(function (EndpointsGroup $endpoints) {
	                $endpoints->controller(UsersController::class);
	            })
             ],
         ]);
    }
}
```

### Formatters

Want a different representation for endpoints? For example something like this:

```json
"endpoints":{  
	"show":"https://app.laravel/users/1",
	"edit":"https://app.laravel/users/1/edit",
}
```

You can do this with formatters! You can create your own formatters by implementing the `Spatie\LaravelResourceEndpoints\Formatters\Formatter` interface.

The package includes 3 formatters:

- DefaultFormatter: the formatter from the examples above
- LayeredFormatter: this formatter will put prefixed endpoints in their own prefixed array
- UrlFormatter: a simple formatter which has an endpoint name as key and endpoint url as value

You can set the formatter used in the `laravel-resource-endpoints.php` config file. Or if you are using endpoint groups it is possible to set an formatter specifically for each endpoint:

```php
$endpoints
	->controller(UsersController::class)
	->formatter(Spatie\LaravelResourceEndpoints\Formatters\ UrlFormatter::class);
``` 


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
