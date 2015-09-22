<?php

namespace T301000\LaravelNtpcOpenid\Exceptions;

class UserIsEmptyException extends \Exception
{
	/**
     * @param string  $message
     */
    public function __construct($message = 'User 資料是空的，請確認 OpenID 認證流程已完成。')
    {
        parent::__construct($message);
    }
}