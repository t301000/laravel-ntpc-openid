## Laravel 5 OpenID 整合套件 for 新北市

## Installation

```
composer require t301000/laravel-ntpc-openid
```

OR

Update composer.json
```
{
    "require": {
        ...
        "t301000/laravel-ntpc-openid": "dev-master"
    },
}
```

Require this package with composer:

```
composer update
```

### Laravel 5.1:

Update config/app.php
```php
'providers' => [
    ...
    T301000\LaravelNtpcOpenid\NtpcOpenidServiceProvider::class,
];
```

