---
title: Endpoint parameters
weight: 4
---

An endpoint resource will try to deduce the parameters for a route as best as possible when generating the endpoint to that route. There are a few steps in the resolving of parameters in a route.

First, we check if a model was given to the resource you created. Then we'll check the parameters of the current request of your application if they can be used to create the route.

Let's say you want to replace the parameters deduced from the request. You can do this by specifying them like this:

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

When you manually specify the parameters, then we will not check the request for missing parameters. So you should add all the missing parameters by yourself.

#### Endpoints that cannot be deduced

Sometimes it is not possible to fully deduce all the endpoints for a resource. In this case, we will try to construct an endpoint as close as possible to the route. We do this by putting the parameters we cannot deduce between brackets.

Let's look at an example: Say you want to link an `App\User` to an `App\Post`. The `link` method in your controller expects two parameters `$user` and `$post` with matching types. When the `App\User` is given to the resource but `App\Post` is missing the URL of the endpoint will then look like `/user/link/1/{post}` for the `App\User` with id 1.

This becomes handy to debug which parameters are missing in the resource and should be manually specified for creating endpoints. You can also replace these parameters between brackets on the frontend of your application for a more dynamic endpoint!
