<?php declare (strict_types = 1);
#coding: utf-8
# +-------------------------------------------------------------------
# | 路由未定义异常
# +-------------------------------------------------------------------
# | Copyright (c) 2017-2019 Sower rights reserved.
# +-------------------------------------------------------------------
# +-------------------------------------------------------------------
namespace sower\exception;
class RouteNotFoundException extends HttpException
{

    public function __construct()
    {
        parent::__construct(404, '页面错误');
    }

}
