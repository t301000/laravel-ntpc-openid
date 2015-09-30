<?php

namespace T301000\LaravelNtpcOpenid\Exceptions;


class SingleRuleMustBeArray extends \Exception
{
    /**
     * @param string  $message
     */
    public function __construct($message = '每條登入規則必須是陣列。')
    {
        parent::__construct($message);
    }
}