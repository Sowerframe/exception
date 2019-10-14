<?php declare (strict_types = 1);
#coding: utf-8
# +-------------------------------------------------------------------
# | 非法数据异常
# +-------------------------------------------------------------------
# | Copyright (c) 2017-2019 Sower rights reserved.
# +-------------------------------------------------------------------
# +-------------------------------------------------------------------
namespace sower\exception;
use Psr\Cache\InvalidArgumentException as Psrcache;
use Psr\SimpleCache\InvalidArgumentException as Psrsimple;
class InvalidArgumentException extends \InvalidArgumentException implements Psrcache, Psrsimple
{
}
