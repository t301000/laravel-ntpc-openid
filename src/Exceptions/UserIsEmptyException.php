<?php

namespace T301000\LaravelNtpcOpenid\Exceptions;

class UserIsEmptyException extends \Exception
{
	/**
     * @param string  $message
     */
    public function __construct($message = 'User 資料是空的，請確認有執行 validate() 確認資料正確性。')
    {
        parent::__construct($message);
    }
}