<?php declare (strict_types = 1);
#coding: utf-8
# +-------------------------------------------------------------------
# | HTTP异常
# +-------------------------------------------------------------------
# | Copyright (c) 2017-2019 Sower rights reserved.
# +-------------------------------------------------------------------
# +-------------------------------------------------------------------
namespace sower\exception;
class HttpException extends \RuntimeException
{
    private $statusCode;
    private $headers;

    public function __construct(int $statusCode, string $message = null, \Exception $previous = null, array $headers = [], $code = 0)
    {
        $this->statusCode = $statusCode;
        $this->headers    = $headers;

        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}
