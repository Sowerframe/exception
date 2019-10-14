<?php declare (strict_types = 1);
#coding: utf-8
# +-------------------------------------------------------------------
# | 类不存在异常
# +-------------------------------------------------------------------
# | Copyright (c) 2017-2019 Sower rights reserved.
# +-------------------------------------------------------------------
# +-------------------------------------------------------------------
namespace sower\exception;
use Psr\Container\NotFoundExceptionInterface;
class ClassNotFoundException extends \RuntimeException implements NotFoundExceptionInterface
{
    protected $class;
    public function __construct(string $message, string $class = '')
    {
        $this->message = $message;
        $this->class   = $class;
    }

    /**
     * 获取类名
     * @access public
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
}
