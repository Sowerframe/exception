<?php declare (strict_types = 1);
#coding: utf-8
# +-------------------------------------------------------------------
# | 缓存异常
# +-------------------------------------------------------------------
# | Copyright (c) 2017-2019 Sower rights reserved.
# +-------------------------------------------------------------------
# +-------------------------------------------------------------------
namespace sower\exception;
use Psr\Cache\CacheException as Psrcache;
use Psr\SimpleCache\CacheException as Psrsimple;
use sower\Exception;
class CacheException extends Exception implements Psrcache, Psrsimple
{
}
