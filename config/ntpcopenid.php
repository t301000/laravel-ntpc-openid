<?php

/**
 * required 各欄位意義：
 * 'namePerson/friendly', //暱稱
 * 'contact/email', //公務信箱
 * 'namePerson', //姓名
 * 'birthDate', //出生年月日，如： 1973-01-16
 * 'person/gender', //性別
 * 'contact/postalCode/home', //識別碼
 * 'contact/country/home', //單位（學校名），如：育林國中
 * 'pref/language', //年級班級座號 6 碼
 * 'pref/timezone' // 授權資訊[學校別、身分別、職稱別、職務別]，例如：
 * [{"id":"014569","name":"新北市立育林國民中學","role":"教師","title":"專任教師","groups":["科任教師"]}]
 *
 *
 * canLoginRules 陣列可設定登入規則
 * 每條規則均為陣列
 * 未設定規則代表不設限
 * 可用欄位 => unitCode 單位代碼, role 身份, title 職務, group 職稱, openID OpedID 帳號
 * 除 unitCode 為字串之外，其餘可為字串或陣列
 * 
 * 規則設定範例：
 * ['unitCode' => '014569'],
 * ['unitCode' => '014569', 'role' => '教師'],
 * ['unitCode' => '014569', 'role' => ['教師', '學生']],
 * ['role' => '教師'],
 * ['unitCode' => '014569', 'title' => ['主任', '組長']],
 * ['group' => '資訊組長'],
 * ['openID' => ['somebody']],
 * 
 */

return [
    'identity' => 'https://openid.ntpc.edu.tw/',
	'required' => [
        'namePerson/friendly',
        'contact/email',
        'namePerson',
        'birthDate',
        'person/gender',
        'contact/postalCode/home',
        'contact/country/home',
        'pref/language',
        'pref/timezone'
    ],
    'canLoginRules' => [],
    'prefix' => 'ntpcopenid', // 相關路由之前綴字串
    'sessionKey' => 'ntpcopenid', // 存放取回之 OpenID 資料之 session key
    'redirectToUrls' => [
        'user_cancel' => '/', // user 取消認證 時導向至哪裡
        'validate_fail' => '/', // user 驗證未過 時導向至哪裡
        'login_allow' => '/', // user 允許登入後 導向至哪裡
        'login_deny' => '/', // user 被拒絕登入 時導向至哪裡
        'other' => '/', // 其他狀況未經正常程序，如直接輸入網址瀏覽，導向至哪裡
    ],

    /* 以下只有開發時測試用 */
    'skipRealOpenID' => false, // 略過真正的 OpenID 流程，測試時使用
    'fakeUserIndex' => 0, // 使用哪一筆假資料，index 由 0 開始
    'fakeUsers' => [ // 測試用假資料，每筆假資料為陣列，可自行增加
        // index = 0
        [
            "namePerson/friendly" => "林小明",
            "contact/email" => "t001@apps.ntpc.edu.tw",
            "namePerson" => "林小明",
            "birthDate" => "2000-08-19",
            "person/gender" => "M",
            "contact/postalCode/home" => "5EE2EFC0E0722558C2B47AA5461F60FE69F811651068678F6F7F264BAF1234DA",
            "contact/country/home" => "育林國中",
            "pref/language" => "000000",
            "pref/timezone" => [
                [
                    "id" => "014569",
                    "name" => "新北市立育林國民中學",
                    "role" => "家長",
                    "title" => "其他",
                    "groups" => ["其他"]
                ],
                [
                    "id" => "014569",
                    "name" => "新北市立育林國民中學",
                    "role" => "教師",
                    "title" => "教師兼組長",
                    "groups" => ["資訊組長"]
                ],
            ],
            "openid" => "t001"
        ],
        // index = 1
        [
            "namePerson/friendly" => "黃小保",
            "contact/email" => "s001@apps.ntpc.edu.tw",
            "namePerson" => "黃小寶",
            "birthDate" => "2003-10-09",
            "person/gender" => "M",
            "contact/postalCode/home" => "5FF2GGC0E0722558C2B47GG5461F60FE69F811651068678F6F7F264BAF5678GG",
            "contact/country/home" => "育林國中",
            "pref/language" => "070215",
            "pref/timezone" => [
                [
                    "id" => "014569",
                    "name" => "新北市立育林國民中學",
                    "role" => "學生",
                    "title" => "其他",
                    "groups" => ["其他"]
                ],
            ],
            "openid" => "s001"
        ],
        // index = 2
        [
            "namePerson/friendly" => "許小誠",
            "contact/email" => "s002@apps.ntpc.edu.tw",
            "namePerson" => "許小誠",
            "birthDate" => "2003-12-09",
            "person/gender" => "M",
            "contact/postalCode/home" => "5FF2GGC0E0722558C2B47GG5461F60GG69F811651012345F6F7F264BAF5678GG",
            "contact/country/home" => "育林國中",
            "pref/language" => "081005",
            "pref/timezone" => [
                [
                    "id" => "014569",
                    "name" => "新北市立育林國民中學",
                    "role" => "學生",
                    "title" => "其他",
                    "groups" => ["其他"]
                ],
            ],
            "openid" => "s002"
        ],
    ],
];
