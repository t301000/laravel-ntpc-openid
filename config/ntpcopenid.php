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
    'canLoginRules' => []
];