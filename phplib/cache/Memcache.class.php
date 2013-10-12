<?php
namespace Phplib\Cache;

class Memcache {

	private static $singleton = NULL;

	/**
	 * Singleton.
	 */
    public static function instance() {
        $class = get_called_class();
		is_null(self::$singleton) && self::$singleton = new $class();
		return self::$singleton;
	}


	protected $config = NULL;
	private $engine = NULL;


	/**
	 * Connection pools.
	 *
	 * @var array
	 */
	private $pools = array();
	
    protected static $useMemcached = TRUE;
    //protected static $useMemcached = FALSE;


	/**
	 * Constructor.
	 */
    protected function __construct() {
		is_null($this->config) && $this->config = \Phplib\Config::load('Memcache');

        if (class_exists('\\Memcached') && self::$useMemcached) {
            $this->engine = 'Memcached';
            $class = "\\{$this->engine}";
            $this->pools = new $class();
            $this->pools->setOption(\Memcached::OPT_NO_BLOCK, TRUE);
            $this->pools->setOption(\Memcached::OPT_DISTRIBUTION, \Memcached::DISTRIBUTION_CONSISTENT);
            $this->pools->setOption(\Memcached::OPT_LIBKETAMA_COMPATIBLE, TRUE);
            //$this->pools->setOption(\Memcached::OPT_SEND_TIMEOUT, 1000);
            //$this->pools->setOption(\Memcached::OPT_RECV_TIMEOUT, 1000);
            $config = array();
            foreach ($this->config->pools as $m) {
                if (!isset($m['weight']) || !is_numeric($m['weight'])) {
                    $m['weight'] = 10;
                }
                $config[] = array($m['host'], $m['port'], $m['weight']);
            }
            $result = $this->pools->addServers($config);
            if ($result != TRUE) {
				$logHandle = new \Phplib\Tools\Liblog('error_memcache', 'normal');
                $logHandle->w_log('FAILED TO ADD SERVERS');
            }
            return TRUE;
        }

		if (class_exists('\\Memcache')) {
			$this->engine = 'Memcache';
			$class = "\\{$this->engine}";
			$this->pools = new $class();
		}
		if (is_null($this->engine)) {
			throw new \Exception("Neither \"Memcached\" nor \"Memcache\" class can be found.");
			die();
		}

		foreach ($this->config->pools AS $server) {
			$this->pools->addServer($server['host'], $server['port']);
		}
    }

	protected static $allowedMethods = array(
		'set' => 1, 'setMulti' => 2, 'delete' => 1, 'increment' => 1, 'decrement' => 1,
		'get' => 1, 'getMulti' => 2,
	);

	protected function checkKey($method, $arg0) {
		if (self::$allowedMethods[$method] === 1) {
			$key = $arg0;
			if (empty($key) || is_array($key)) {
				throw new \Exception("Invalid key, key must be a string:" . json_encode($key));
			}
		}
		elseif ($method == 'setMulti') {
			if (!is_array($arg0)) {
				throw new \Exception("Argument 0 must be an array. " . json_encode($arg0));
			}
			$keys = array_keys($arg0);
			$key = implode('', $keys);
		}
		elseif ($method == 'getMulti') {
			if (!is_array($arg0)) {
				throw new \Exception("Argument 0 must be an array. " . json_encode($arg0));
			}
			$key = implode('', $arg0);
		}
        if (strpos($key, ' ') !== FALSE || strpos($key, "\n") !== FALSE || strpos($key, "\r") !== FALSE || strpos($key, "\t") !== FALSE) {
        //if (strpos($key, ' ') !== FALSE) {
			throw new \Exception("Key contains invalid characters:" . $key);
        }
	}

    public function __call($method, $arguments) {
		//echo "$name \n";
		try {
			if (empty(self::$allowedMethods[$method])) {
				throw new \Exception("method: $method is not allowed.");
			}
			$this->checkKey($method, $arguments[0]);
			return $this->$method($arguments);
		}
        catch (\Exception $e) {
			$logHandle = new \Phplib\Tools\Liblog('error_memcache', 'normal');
            $logHandle->w_log("Memcached_exception: " . $e->getMessage());
            $logHandle->w_log(print_r(array('method' => $method, 'arguments' => $arguments), 1));
			return FALSE;
        }    
	}

    public function getconfig() {
        print_r($this->config);
    }

	/**
	 * Delete an item.
	 *
	 * @param string $key The key to be deleted.
	 * @param int $time The amount of time the server will wait to delete
	 * the item.
	 *
	 * @return Returns TRUE on success or FALSE on failure.
	 */
	protected function delete($args) {
		$key = $args[0];
		if (!isset($args[1])) {
			$args[1] = 0;
		}
		$time = $args[1];
		$result = TRUE;
		$result = $result && $this->pools->delete($key, $time);
		return $result;
	}

	/**
	 * Store an item.
	 *
	 * @param string $key The key under which to store the value.
	 * @param mixed $value The value to store.
	 * @param int $expiration The expiration time, defaults to 0.
	 *
	 * @return Returns TRUE on success or FALSE on failure.
	 */
	protected function set($args) {
		$key = $args[0];
		$value = $args[1];
		if (!isset($args[2])) {
			$args[2] = 0;
		}
		$expiration = $args[2];
		$result = TRUE;
        //$start = microtime(TRUE);
		switch ($this->engine) {
			case 'Memcache':
				$result = $result && $this->pools->set($key, $value, MEMCACHE_COMPRESSED, $expiration);
				break;

			case 'Memcached':
			default:
				$result = $result && $this->pools->set($key, $value, $expiration);
				break;
		}
        /*$spent = intval((microtime(TRUE) - $start) * 1000);
        if ($spent > MEMCACHE_TIMEOUT_FOR_WEB && !empty($_SERVER['REQUEST_URI'])) {
            $function = __function__;
            $logString = "[:]\t[{$function}]\t[{$spent}]\t[{$_SERVER['REQUEST_URI']}]\t[{$key}]";  
            $logHandle = new \Phplib\Tools\Liblog('MEMCACHE_OVERTIME_LOG', 'normal');
            $logHandle->w_log($logString);
        }
		*/
		return $result;
	}

	/**
	 * Retrieve an item.
	 *
	 * @param string $key The key of the item to retrieve.
	 *
	 * @return Returns the value stored in the cache or FALSE otherwise.
	 */
	protected function get($args) {
		$key = $args[0];
		$result = FALSE;
		$result = $this->pools->get($key);

		return $result;
	}

	/**
	 * Increment numeric item's value.
	 */
	protected function increment($args) {
		$key = $args[0];
		if (!isset($args[1])) {
			$args[1] = 1;
		}
		$offset = $args[1];
		$result = FALSE;
		$result = $this->pools->increment($key, $offset);
		return $result;
	}

	/**
	 * Decrement numeric item's value.
	 */
	protected function decrement($args) {
		$key = $args[0];
		if (!isset($args[1])) {
			$args[1] = 1;
		}
		$offset = $args[1];
		$result = FALSE;
		$result = $this->pools->decrement($key, $offset);
		return $result;
	}

	/**
	 * Retrieve multiple items.
	 */
    protected function getMulti($args) {
		$keys = $args[0];
        if (empty($keys) || !is_array($keys)) {
            return FALSE;
        }
		switch ($this->engine) {
			case 'Memcached':
                if (count($keys) > 10) {
                    return $this->pools->getMulti($keys);
                }
			case 'Memcache':
				$values = array();
				foreach ($keys as $key) {
					$val = $this->pools->get($key);
					if ($val !== FALSE) {
						$values[$key] = $val;
					}
				}
				return $values;
        }
    }

	/**
	 * Store multiple items.
	 */
    protected function setMulti($args) {
		$items = $args[0];
		if (!isset($args[1])) {
			$args[1] = 0;
		}
		$expiration = $args[1];
		switch ($this->engine) {
			case 'Memcached':
				$result = TRUE;
				$result = $result && $this->pools->setMulti($items, $expiration);
				return $result;

			case 'Memcache':
				$result = TRUE;
				foreach ($items AS $k => $v) {
					$result = $result && $this->pools->set($k, $v, MEMCACHE_COMPRESSED, $expiration);
				}
		}	
		return $result;
    }


}
