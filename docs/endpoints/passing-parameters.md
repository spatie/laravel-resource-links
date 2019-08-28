---
title: Passing parameters
weight: 5
---

You can pass parameters in different ways to an endpoint type. By value's:

```php
$endpoints
    ->controller(UsersController::class)
    ->parameters(User::first(), Post::first());
    
```

Or by array: 

```php
$endpoints
    ->controller(UsersController::class)
    ->parameters([User::first(), Post::first()]);
```

You can even pass an associative array where the keys will be used for [parameter deducing](https://docs.spatie.be/laravel-resource-endpoints/v1/usage/endpoint-parameters/#parameter-resolving-rules):

```php
$endpoints
    ->controller(UsersController::class)
    ->parameters(['user' => User::first(), 'post' => Post::first()]);
```

Lastly, the parameters function can be called as many times as you want:

```php
$endpoints
    ->controller(UsersController::class)
    ->parameters(User::first())
    ->parameters(Post::first());
```
