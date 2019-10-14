<?php declare (strict_types = 1);
#coding: utf-8
# +-------------------------------------------------------------------
# | 数据验证异常
# +-------------------------------------------------------------------
# | Copyright (c) 2017-2019 Sower rights reserved.
# +-------------------------------------------------------------------
# +-------------------------------------------------------------------
namespace sower\exception;
class ValidateException extends \RuntimeException
{
    protected $error;

    public function __construct($error)
    {
        $this->error   = $error;
        $this->message = is_array($error) ? implode("\n\r", $error) : $error;
    }

    /**
     * 获取验证错误信息
     * @access public
     * @return array|string
     */
    public function getError()
    {
        return $this->error;
    }
}
