---
title: Meta helper
weight: 3
---

## A little helper

When using collection endpoints in your resource the code for adding these collection endpoints can be quite confusing:

``` php
class UserResource extends JsonResource
{
    use Spatie\LaravelResourceEndpoints\HasEndpoints;
    
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

That's why we've added a little helper which puts endpoints immediately in the meta section of a resource collection:

``` php
class UserResource extends JsonResource
{
    use Spatie\LaravelResourceEndpoints\HasEndpoints;
    use Spatie\LaravelResourceEndpoints\HasMeta;
    
    public static function meta()
    {
        return [
            'endpoints' => self::collectionEndpoints(UsersController::class)
        ];
    }
}
```

You can use this little helper by including the `Spatie\LaravelResourceEndpoints\HasMeta` trait in your resource.