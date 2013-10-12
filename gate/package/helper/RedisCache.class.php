<?php
namespace Gate\Package\Helper;

class RedisCache extends \Gate\Libs\Redis {

    protected static $prefix = 'Gate:Cache';
    const EXPIRED = 1800;
	const USECACHE = TRUE;

}

