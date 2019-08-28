---
title: Endpoint parameters
weight: 4
---

An endpoint resource will try to deduce the parameters for a route as best as possible when generating the endpoint to that route. Without extra configuration the parameters of the current request and the current model given to the resource are used to construct the endpoints.

But it is also possible to specify your own parameters:

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

Or for collection endpoints:

   
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

And for actions:

``` php
class OtherResource extends JsonResource
{
    use HasEndpoints;

    public function toArray($request)
    {
        $user = Auth::user();

        return [
            'endpoints' => $this->endpoints()->addAction([UsersController::class, 'show'], [
                'user' => Auth::user(),
            ]),
        ];
    }
}
```

When you manually specify the parameters, then we will not check the request for missing parameters. So you should add all the missing parameters by yourself.

### Parameter resolving rules

An endpoint has zero or more signature parameters(i.e. the parameters of your function) that should be filled in when creating a url to a route.

We use a set of rules when trying to deduce a correct value for the signature parameter. When a value of a rule fits the signature parameter of a route it will be used to create the url.

The rule are executed in following order:

1. If the type of the signature parameter is the same as the model given to the resource, use the model as value
2. Search in the provided parameters(provided by the request or by you explicitly) if a parameter exists with the same name as the signature parameter use it's value
3. If a primitive(string, bool, int, ...) signature parameter is expected we'll look in the provided parameters if we can find a value with the same type
4. Lastly we search through the provided parameters if we can find a parameter which value has the same type as the signature parameter


#### Endpoints that cannot be deduced

Sometimes it is not possible to fully deduce all the endpoints for a resource. In this case, we will try to construct an endpoint as close as possible to the route. We do this by putting the parameters we cannot deduce between brackets.

Let's look at an example: say you want to link an `App\User` to an `App\Post`. The `link` method in your controller expects two parameters `$user` and `$post` with matching types. When the `App\User` is given to the resource but `App\Post` is missing the URL of the endpoint will then look like `/user/link/1/{post}` for the `App\User` with id 1.

This becomes handy to debug which parameters are missing in the resource and should be manually specified for creating endpoints. You can also replace these parameters between brackets on the frontend of your application for a more dynamic endpoint!
