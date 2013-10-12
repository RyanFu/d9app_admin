<?php
namespace Gate\Libs;

abstract class Redis extends \Phplib\Redis\Redis {
	const USECACHE = TRUE;

	public static  function setCache($id, $value) {
		if (empty($id) || empty($value)) {
			return FALSE;
		}
		$class = get_called_class();
		$expired = $class::EXPIRED;
		$value = base64_encode(serialize($value));
		return parent::setEx($id, $expired, $value);
    }

    public static function getCache($id) {
		if (empty($id)) {
			return FALSE;
		}
        $data = parent::get($id);
		return unserialize(base64_decode($data));
    }

	public static function deleteCache($id) {
		if (empty($id)) {
			return FALSE;
		}
		return parent::del($id);
	}

	public static function incr($id) {
		if (empty($id)) {
			return FALSE;
		}
		return parent::incr($id);
	}

	public static function decr($id) {
		if (empty($id)) {
			return FALSE;
		}
		return parent::decr($id);
	}

    public static function __callStatic($method_name, $arguments) {
		$class = get_called_class();
        if ($class::USECACHE !== TRUE || empty($method_name)) {
            return FALSE;
        }
        $method_name = ltrim($method_name, '_');
        if (method_exists($class, $method_name)) {
			return call_user_func_array(array($class, $method_name), $arguments);
        }
        return FALSE;
    }

}
