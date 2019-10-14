<?php declare (strict_types = 1);
#coding: utf-8
# +-------------------------------------------------------------------
# | Database相关异常处理类
# +-------------------------------------------------------------------
# | Copyright (c) 2017-2019 Sower rights reserved.
# +-------------------------------------------------------------------
# +-------------------------------------------------------------------
namespace sower\exception;
use sower\Exception;
class DbException extends Exception
{
    /**
     * DbException constructor.
     * @access public
     * @param  string    $message
     * @param  array     $config
     * @param  string    $sql
     * @param  int       $code
     */
    public function __construct(string $message, array $config = [], string $sql = '', int $code = 10500)
    {
        $this->message = $message;
        $this->code    = $code;

        $this->setData('Database Status', [
            'Error Code'    => $code,
            'Error Message' => $message,
            'Error SQL'     => $sql,
        ]);

        unset($config['username'], $config['password']);
        $this->setData('Database Config', $config);
    }

}
