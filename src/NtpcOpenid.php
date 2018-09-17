<?php

namespace T301000\LaravelNtpcOpenid;

use T301000\LaravelNtpcOpenid\Exceptions\UserIsEmptyException;
use T301000\LaravelNtpcOpenid\Exceptions\SingleRuleMustBeArrayException;

class NtpcOpenid extends \LightOpenID
{
	/**
     * 存放取自 OpenID 的 user data
     *
     * @var array
     */
	private $user = [];

	/**
	 * 建構函式，呼叫父類別的建構函式，傳入host參數
	 */
	public function __construct()
	{
		if (isset($_SERVER['REQUEST_URI'])) {
            parent::__construct(app('request')->getHttpHost());
        }
	}

	/**
	 * 產生 openid 認證位址，附加必要 url 參數
	 * 此為覆寫父類別的 method
	 * 
	 * @param  boolean $immediate 
	 * @return string             
	 */
	public function authUrl($immediate = false)
	{
		$this->identity = config('ntpcopenid.identity');
		$this->required = config('ntpcopenid.required');
	
		return parent::authUrl();
	}

	/**
	 * 向 OpenID Provider 驗證資料是否正確
	 * 若正確則擷取資料
	 * 
	 * @return bool
	 */
	public function validate()
	{
		if(parent::validate()) {
			$this->fetchUserDataFromOpenID();
			return true;
		}

		return false;
	}

	/**
	 * 取得指定欄位之　User OpenID 資料，以陣列回傳
	 * 傳入之參數為 OpenID 欄位名稱字串如下，多個則以陣列傳入
	 * 
	 * namePerson/friendly 暱稱
	 * contact/email 公務信箱
	 * namePerson 姓名
	 * birthDate 出生年月日
	 * person/gender 性別，字母
	 * contact/postalCode/home 識別碼
	 * contact/country/home 單位名稱（校名，簡稱）
	 * pref/language 年班座號
	 * pref/timezone 授權資訊
	 * openid OpenID 帳號
	 *
	 * @param  string|array|null $fields OpenID 欄位名稱，多個則以陣列傳入
	 * @return $this|array
	 */
	public function getUserData($fields = null)
	{		
		if(empty($this->user)) {
			throw new UserIsEmptyException;
		}

		// 未傳參數則回傳 $this，可以 chain 呼叫 method
		if(func_num_args() == 0) {
			return $this;
		}

		// 取得所有取回之欄位資料，包含 OpenID 帳號
		if($fields === '*') {
			return $this->user;
		}

		// 回傳指定欄位資料
		// 將欄位名稱整理為陣列
		$fields = is_array($fields) ? $fields : array($fields);
		$data = []; // 收集回傳之資料
		foreach ($fields as $field) {
			if(array_key_exists($field, $this->user)) {
				$data[$field] = $this->user[$field];
			}
		}

		return $data;
	}

	/**
	 * 取得 OpenID user 指定欄位資料
	 * 主要的取資料方法，
	 * 除了 getUserData 之外，
	 * 其他方法最終都是透過此方法取資料
	 *
	 * @param  string $field OpenID 欄位名稱
	 * @return string|array|null
	 */
	public function getField($field)
	{
		if(empty($this->user)) {
			throw new UserIsEmptyException;
		}

		return array_key_exists($field, $this->user) ? $this->user[$field] : null;
	}

	/**
	 * 取得 授權資訊
	 *
	 * @return array|null
	 */
	public function getAuthorizedInfo()
	{
		return $this->getField('pref/timezone');
	}

	/**
	 * 取得 OpenID 帳號
	 *
	 * @return string
	 */
	public function getOpenID()
	{
		return $this->getField('openid');
	}

	/**
	 * 取得 公務信箱
	 *
	 * @return string|null
	 */
	public function getEmail()
	{
		return $this->getField('contact/email');
	}

	/**
	 * 取得 暱稱
	 *
	 * @return string|null
	 */
	public function getNickName()
	{
		return $this->getField('namePerson/friendly');
	}

	/**
	 * 取得 姓名
	 *
	 * @return string|null
	 */
	public function getName()
	{
		return $this->getField('namePerson');
	}

	/**
	 * 取得 出生年月日
	 *
	 * @return string|null
	 */
	public function getBirthday()
	{
		return $this->getField('birthDate');
	}

	/**
	 * 取得 性別
	 *
	 * @return string|null
	 */
	public function getGender()
	{
		$gender = $this->getField('person/gender');

		if(!is_null($gender)) {
			$gender = ($gender == 'M') ? '男' : '女';
		}

		return $gender;
	}

	/**
	 * 取得 識別碼
	 *
	 * @return string|null
	 */
	public function getIdCode()
	{
		return $this->getField('contact/postalCode/home');
	}

	/**
	 * 取得 單位名
	 *
	 * @return string|null
	 */
	public function getUnitName()
	{
		return $this->getField('contact/country/home');
	}

	/**
	 * 取得 單位全銜
	 *
	 * @return array|null
	 */
	public function getUnitFullNames()
	{
		return $this->collectAuthorizedInfoToArray('name', true, false);
	}

	/**
	 * 取得 年級班級座號 6 碼，回傳陣列
	 *
	 * @return array|null
	 */
	public function getClassInfo()
	{
		$data = $this->getField('pref/language');

		if(!is_null($data)) {
			return array_combine(['grade', 'classNumber', 'seatNumber'], str_split($data, 2));
		}

		return $data;
	}

	/**
	 * 取得 單位代碼，回傳陣列
	 *
	 * @return array|null
	 */
	public function getUnitCodes()
	{
		return $this->collectAuthorizedInfoToArray('id');
	}

	/**
	 * 取得 角色(身分別)，回傳陣列，key 為校代碼，value 為角色陣列
	 *
	 * @return array|null
	 */
	public function getRoles()
	{
		return $this->collectAuthorizedInfoToArray('role', true);
	}

	/**
	 * 取得 職務別，回傳陣列，key 為校代碼，value 為職務別陣列
	 *
	 * @return array|null
	 */
	public function getTitles()
	{
		return $this->collectAuthorizedInfoToArray('title', true);
	}

	/**
	 * 取得 職稱別，回傳陣列，key 為校代碼，value 為職稱別陣列
	 *
	 * @return array|null
	 */
	public function getGroups()
	{
		$authorizedInfo = $this->getAuthorizedInfo();

		if(!is_null($authorizedInfo)) {
			$groups = [];
			foreach ($authorizedInfo as $single) {
				if(array_key_exists($single['id'], $groups)) {
					$groups[$single['id']] = array_merge($groups[$single['id']], $single['groups']);
				} else {
					$groups[$single['id']] = $single['groups'];
				}
			}

			return $groups;
		}

		return null;
	}

	/**
	 * 檢查 是否可登入
     *
	 * @param  array  $user
	 *
     * @return bool
	 */
	public function canLogin(array $user = null)
	{
        if (!is_null($user)) {
            // 有傳入則使用傳入之資料
            $this->user = $user;
        } else if (!is_null(session(config('ntpcopenid.sessionKey')))) {
            // 沒有傳入但 session 有，則使用 session 之資料
            $this->user = session(config('ntpcopenid.sessionKey'));
        }

		// 取得所有登入規則
		$rules = config('ntpcopenid.canLoginRules');

		// 未設定規則，允許登入
		if(count($rules) === 0) {
			return true;
		}

		// 逐條檢查
		foreach ($rules as $rule) {
            // 檢查每條登入規則是否為陣列
			if (!is_array($rule)) {
                throw new SingleRuleMustBeArrayException;
            }

			// 每條規則逐一檢查限制欄位
			foreach ($rule as $key => $need) {
				$method = 'check' . ucfirst($key);
				if(method_exists($this, $method)) {
					// 欄位檢查結果
					$result = $this->$method($rule);

					// 第一次遇到欄位不通過時
					// 立即中斷該條規則之檢查
					// 略過剩餘欄位之檢查
					if(!$result) {
						break;
					}
				}
			}

			// 目前規則未通過，進入下一條規則之檢查
			if(!$result) {
				continue;
			}

			// 目前規則檢查通過，回傳 true 允許登入
			return true;
		}

		// 所有規則檢查都未通過，回傳 false 拒絕登入
		return false;
	}

	/**
	 * 檢查 單位代碼
	 *
	 * @param  array $rule 登入規則
	 * @return bool
	 */
	public function checkUnitCode(array $rule)
	{
		return in_array($rule['unitCode'], $this->getUnitCodes());
	}

	/**
	 * 檢查 身份
	 *
	 * @param  array $rule 登入規則
	 * @return bool
	 */
	public function checkRole(array $rule)
	{
		return $this->checkSingleRuleField($rule, 'role');
	}

	/**
	 * 檢查 職務
	 *
	 * @param  array $rule 登入規則
	 * @return bool
	 */
	public function checkTitle(array $rule)
	{
		return $this->checkSingleRuleField($rule, 'title');
	}

	/**
	 * 檢查 職稱
	 *
	 * @param  array $rule 登入規則
	 * @return bool
	 */
	public function checkGroup(array $rule)
	{
		return $this->checkSingleRuleField($rule, 'group');
	}

	/**
	 * 檢查 OpenID 帳號
	 *
	 * @param  array $rule 登入規則
	 * @return bool
	 */
	public function checkOpenID(array $rule)
	{
		$need = is_string($rule['openID']) ? array($rule['openID']) : $rule['openID'];

		return in_array($this->getOpenID(), $need);
	}


	/**
	 ***********************************
	 *
	 *	輔助方法
	 *
	 *********************************** 
	 */

	/**
	 * OpenID 導回後擷取　User 資料，附加 openid 帳號整理為陣列，賦值給 $this->user
	 * 陣列之 key 為：
	 * namePerson/friendly 暱稱
	 * contact/email 公務信箱
	 * namePerson 姓名
	 * birthDate 出生年月日
	 * person/gender 性別，字母
	 * contact/postalCode/home 識別碼
	 * contact/country/home 單位名稱（校名，簡稱）
	 * pref/language 年班座號
	 * pref/timezone 授權資訊，為陣列，數字索引，元素為陣列，key 為：
	 * 		id 單位代碼（校代碼）
	 * 		name 單位全銜
	 * 		role 身份
	 * 		title 職務
	 * 		groups 職稱，值為陣列，數字索引
	 * openid OpenID 帳號
	 *
	 * @return void
	 */
	protected function fetchUserDataFromOpenID()
	{

		$data = $this->getAttributes();

		$data['openid'] = collect( array_values( explode( '/', $this->identity ) ) )->last();

		// 如果有 授權資訊，則將 原 json 格式 轉換為 陣列
		// 陣列中每個元素代表一種身份的授權資訊，為陣列
		if (isset($data['pref/timezone'])) {
			$data['pref/timezone'] = json_decode($data['pref/timezone'], true);
		}

		$this->user = $data;
	}

	/**
	 * 收集授權資訊指定欄位，回傳陣列，數字索引，元素為字串；或以單位代碼為索引，元素為陣列
	 *
	 * @param  string $field 欄位名稱
	 * @param  bool $hasKey 是否以單位代碼為索引
	 * @param  bool $subArray 元素是否為陣列（$hasKey = true 時才有作用）
	 * @return array|null
	 */
	protected function collectAuthorizedInfoToArray($field, $hasKey = false, $subArray = true)
	{
		$authorizedInfo = $this->getAuthorizedInfo();

		// 有取回授權資訊
		if(!is_null($authorizedInfo)) {
			$data = [];

			// 回傳之陣列以單位代碼為索引
			if($hasKey) {
				if($subArray) {
					// 元素為陣列
					foreach ($authorizedInfo as $single) {
						$data[$single['id']][] = $single[$field];
					}
				}else{
					// 直接賦值
					foreach ($authorizedInfo as $single) {
						$data[$single['id']] = $single[$field];
					}
				}

				return $data;
			}

			// 回傳之陣列為數字索引，元素為字串
			foreach ($authorizedInfo as $single) {
				$data[] = $single[$field];
			}

			return array_unique($data); // 去除重複元素後回傳
		}

		// 未要求取得授權資訊
		return null;
	}

	/**
	 * 檢查某登入規則的某欄位
	 * 用來檢查規則中的 group title role
	 *
	 * @param  array $rule 登入規則
	 * @param  string $type 只接受 group title role 之一
	 * @return bool
	 */
	protected function checkSingleRuleField(array $rule, $type)
	{
		$method = 'get' . ucfirst($type) . 's';

		// 呼叫以下方法取得 user 具備的身份陣列、職務陣列、職稱陣列
		// $this->getRoles()
		// $this->getTitles()
		// $this->getGroups()
		$userHas = $this->$method();

		if(is_null($userHas)) {
			return false;
		}

		$need = is_string($rule[$type]) ? array($rule[$type]) : $rule[$type];

		// 如果有限制單位代碼
		if(array_key_exists('unitCode', $rule)) {
			return count(array_intersect($userHas[$rule['unitCode']], $need)) > 0;
		}

		return count(array_intersect(array_flatten($userHas), $need)) > 0;
	}

}