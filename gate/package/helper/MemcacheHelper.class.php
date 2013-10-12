<?php
namespace Gate\Package\Helper;

class MemcacheHelper extends \Gate\Libs\Memcache{

	const PREFIX = 'Gate:Cache';
	//超时时间(s)
	const TIMEOUT = 1800;
	//静态memcache
	static $instance;

}
