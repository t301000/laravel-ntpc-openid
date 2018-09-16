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
                
                // 取得 user data 陣列
                $data = $openid->getUserData('*');
                
                // 將取得的資料存入 session
                session([config('ntpcopenid.sessionKey') => $data]);
                // session()->flash(config('ntpcopenid.sessionKey'), $data);

                // 多重身份時選擇身份，否則進行登入檢查
                return count($data['pref/timezone']) > 1 ? $this->selectRole($data) : $this->loginCheck();
                break;

            default: // 其他，如直接輸入網址瀏覽
                return redirect(config('ntpcopenid.redirectToUrls.other'));
                break;
        }
    }

    /**
     * 檢查是否可以登入，依結果執行導向
     */
    public function loginCheck()
    {
        // 選擇之身份 index
        // 只有一個身份時，直接呼叫本方法而非 query string 傳入，會是 null
        $idx = request('idx');
        if (!is_null($idx)) {
            // 更新 session 資料，授權資訊只保留選擇之身份
            $user = session(config('ntpcopenid.sessionKey'));
            $user['pref/timezone'] = array($user['pref/timezone'][(int) $idx]);
            session([config('ntpcopenid.sessionKey') => $user]);
        }
        
        // 此時物件中的 user 屬性是選擇身份後的 session 資料
        $openid = app('ntpcopenid');

        return $openid->canLogin() ? $this->redirectToLoginAllow() : $this->redirectToLoginDeny();
    }

    /**
     * 多重身份時，選擇登入身份
     */
    protected function selectRole(array $user)
    {
        $data = [
            'name' => $user['namePerson'],
            'authInfos' => $user['pref/timezone'],
        ];
        return view('ntpcopenid::select-role')->with('user', $data);
    }

    /**
     * 可以登入，結果導向至對應 url
     */
    protected function redirectToLoginAllow()
    {
        $toUrl = config('ntpcopenid.redirectToUrls.login_allow');

        // 將暫存之 session 改為一次性 session
        session()->flash(config('ntpcopenid.sessionKey'), session(config('ntpcopenid.sessionKey')));

        return redirect($toUrl);
    }

    /**
     * 拒絕登入，結果導向至對應 url
     */
    protected function redirectToLoginDeny()
    {
        $toUrl = config('ntpcopenid.redirectToUrls.login_deny');

        // 清除暫存之 session
        session()->forget(config('ntpcopenid.sessionKey'));
        
        return redirect($toUrl);
    }
}
