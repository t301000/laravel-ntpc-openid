<?php

namespace T301000\LaravelNtpcOpenid\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use T301000\LaravelNtpcOpenid\NtpcOpenid;

class NTPCOpenIDController extends Controller
{
    /**
     * 啟動 OpenID 認證流程
     */
    public function startOpenID()
    {
        $openid = app('ntpcopenid');
        return redirect($openid->authUrl());
    }

    /**
     * OpenID 導回後之處理
     */
    public function process()
    {
        $openid = app('ntpcopenid');
        switch ($openid->mode) {
            case 'cancel': // 取消授權
                return redirect(config('ntpcopenid.redirectToUrls.user_cancel'));
                break;

            case 'id_res': // 同意授權
                if (!$openid->validate()) {
                    // 驗證未過              
                    return redirect(config('ntpcopenid.redirectToUrls.validate_fail'));
                }
                
                // 驗證通過，檢查是否允許登入
                if($openid->canLogin()) {
                    // 允許登入
                    // 取得 user data 陣列
                    $data = $openid->getUserData('*');
                    
                    // 將取得的資料存入 session
                    session([config('ntpcopenid.sessionKey') => $data]);

                    return redirect(config('ntpcopenid.redirectToUrls.login_allow'));
                    break;

                }
                // 不允許登入
                return redirect(config('ntpcopenid.redirectToUrls.login_deny'));
                break;

            default: // 其他，如直接輸入網址瀏覽
                return redirect(config('ntpcopenid.redirectToUrls.other'));
                break;
        }
    }
}
