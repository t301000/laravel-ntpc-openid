## Laravel 5 OpenID 整合套件 for 新北市

## 安裝

### 方法 1：

```
composer require t301000/laravel-ntpc-openid
```

### 方法 2：

在 composer.json 加入：
```
{
    "require": {
        ...
        "t301000/laravel-ntpc-openid": "dev-master"
    },
}
```

然後：

```
composer update
```

## 設定

### Laravel 5.1:

在 config/app.php 中加入 service provider
```php
'providers' => [
    ...
    T301000\LaravelNtpcOpenid\NtpcOpenidServiceProvider::class,
];
```

發布設定檔，設定檔會發布在 config/ntpcopenid.php，預設會取回所有資料欄位，請自行依需求修改
```
php artisan vendor:publish --provider="T301000\LaravelNtpcOpenid\NtpcOpenidServiceProvider"
```

## 說明文件

請見 [wiki](https://github.com/t301000/laravel-ntpc-openid/wiki)