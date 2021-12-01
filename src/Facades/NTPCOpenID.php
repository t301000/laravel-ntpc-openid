<?php

namespace T301000\LaravelNtpcOpenid\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Route;

class NTPCOpenID extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ntpcopenid';
    }

    /**
     * 註冊 相關路由
     *
     * @return void
     */
    public static function routes()
    {
        Route::group(
            [
                'middleware' => ['guest'],
                'prefix' => config('ntpcopenid.prefix'),
                'namespace' => '\T301000\LaravelNtpcOpenid\Controllers'
            ],
            function() {
                Route::post('start', 'NTPCOpenIDController@startOpenID')->name('ntpcopenid.login.start'); // 啟動 OpenID 認證流程
                Route::get('login', 'NTPCOpenIDController@process')->name('ntpcopenid.login.back'); // OpenID 導回 by GET
                Route::post('login', 'NTPCOpenIDController@process'); // OpenID 導回 by POST
                Route::get('check', 'NTPCOpenIDController@loginCheck')->name('ntpcopenid.login.check'); // 檢查是否允許登入
            }
        );
        
    }
}
