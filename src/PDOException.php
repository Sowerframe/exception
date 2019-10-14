<?php declare (strict_types = 1);
#coding: utf-8
# +-------------------------------------------------------------------
# | PDO异常处理类 重新封装了系统的\PDOException类
# +-------------------------------------------------------------------
# | Copyright (c) 2017-2019 Sower rights reserved.
# +-------------------------------------------------------------------
# +-------------------------------------------------------------------
namespace sower\exception;
class PDOException extends DbException
{
    public function __construct(\PDOException $exception, array $config = [], string $sql = '', int $code = 10501)
    {
        $error = $exception->errorInfo;

        $this->setData('PDO Error Info', [
            'SQLSTATE'             => $error[0],
            'Driver Error Code'    => $error[1] ?? 0,
            'Driver Error Message' => $error[2] ?? '',
        ]);

        parent::__construct($exception->getMessage(), $config, $sql, $code);
    }
}
