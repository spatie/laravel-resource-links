---
title: Action endpoints
weight: 5
---

Sometimes you want to add endpoints not belonging to a specific controller. Then it is possible to add an action as an endpoint. They look just like a standard Laravel action:

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

You can also manually set the parameters for the action:

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
 
The HTTP verb for the action will be resolved from the route in Laravel. Should you have an action with two verbs, then you can always specify the verb for a particular action:

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
