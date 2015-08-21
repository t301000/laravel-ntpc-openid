<?php

namespace T301000\LaravelNtpcOpenid;

class NtpcOpenid extends \LightOpenID
{
	
	/**
	 * 建構函式，呼叫父類別的建構函式，傳入host參數
	 */
	public function __construct()
	{
		parent::__construct(\Request::getHttpHost());
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
	 * 取得　User OpenID 資料，以陣列回傳，附加 openid 帳號
	 * 
	 * @return array
	 */
	public function getUserData()
	{
		$data = $this->getAttributes();

		$data['openid'] = collect( array_values( explode( '/', $this->identity ) ) )->last();

		// 如果有 授權資訊，則將 原 json 格式 轉換為 陣列
		if (isset($data['pref/timezone'])) {
			$data['pref/timezone'] = json_decode($data['pref/timezone'], true)[0];
		}

		return $data;
	}

}