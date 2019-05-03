<?php

namespace T301000\LaravelNtpcOpenid\Exceptions;


class RuleErrorException extends \Exception
{
    /**
     * @param string  $message
     */
    public function __construct($message = '登入規則設定錯誤或格式錯誤。')
    {
        parent::__construct($message);
    }
}