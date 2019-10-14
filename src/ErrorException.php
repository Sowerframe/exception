<?php declare (strict_types = 1);
#coding: utf-8
# +-------------------------------------------------------------------
# | 异常其他除开从 sower\Exception 继承的功能和PHP系统\ErrorException功能基本一样
# +-------------------------------------------------------------------
# | Copyright (c) 2017-2019 Sower rights reserved.
# +-------------------------------------------------------------------
# +-------------------------------------------------------------------
namespace sower\exception;
use sower\Exception;
class ErrorException extends Exception
{
    /**
     * 用于保存错误级别
     * @var integer
     */
    protected $severity;

    /**
     * 错误异常构造函数
     * @access public
     * @param  integer $severity 错误级别
     * @param  string  $message  错误详细信息
     * @param  string  $file     出错文件路径
     * @param  integer $line     出错行号
     */
    public function __construct(int $severity, string $message, string $file, int $line)
    {
        $this->severity = $severity;
        $this->message  = $message;
        $this->file     = $file;
        $this->line     = $line;
        $this->code     = 0;
    }

    /**
     * 获取错误级别
     * @access public
     * @return integer 错误级别
     */
    final public function getSeverity()
    {
        return $this->severity;
    }
}
