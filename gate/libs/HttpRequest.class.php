<?php
namespace Gate\Libs;

class HttpRequest {

	private $request_data = NULL;

	public static function getRequest() {
		static $singleton = NULL;
		is_null($singleton) && $singleton = new HttpRequest();
		return $singleton;
	}

	public function __construct() {
		// initialize HTTP data
		$this->request_data['protocol']  = $_SERVER['SERVER_PROTOCOL'];
		$this->request_data['domain']    = $_SERVER['SERVER_NAME'];
		$this->request_data['uri']       = $_SERVER['REQUEST_URI'];
		$this->request_data['path']      = $this->getRequestPath();
		$this->request_data['path_args'] = explode('/', $this->path);
		$this->request_data['method']    = $this->getRequestMethod();
		$this->request_data['GET']       = $_GET;
		$this->request_data['POST']      = $_POST;
		$this->request_data['COOKIE']    = Utilities::zaddslashes($_COOKIE);
		$this->request_data['REQUEST']   = Utilities::zaddslashes($_REQUEST);
		$this->request_data['headers']   = Utilities::parseRequestHeaders();
		$this->request_data['base_url']  = $this->detectBaseUrl();
		$this->request_data['ip']        = $_SERVER['REMOTE_ADDR'];
		$this->request_data['time']      = $_SERVER['REQUEST_TIME'];
		$this->request_data['session']   = \Gate\Libs\Session::singleton()->load($_COOKIE);
	}

	public function __get($name) {
		if (!isset($this->request_data[$name])) {
			return NULL;
		}
		return $this->request_data[$name];
	}


	/**
	 * Returns the requested URL path.
	 * E.g., for http://io.meilishuo.com/a/b it returns "a/b".
	 */
	private function getRequestPath() {
		// only parse $path once in a request lifetime
		static $path;

		if (isset($path)) {
			return $path;
		}

		if (isset($_SERVER['REQUEST_URI'])) {
			// extract the path from REQUEST_URI
			$request_path = strtok($_SERVER['REQUEST_URI'], '?');
			$base_path_len = strlen(rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/'));

			// unescape and strip $base_path prefix, leaving $path without a leading slash
			$path = substr(urldecode($request_path), $base_path_len + 1);

			// $request_path is "/" on root page and $path is FALSE in this case
			if ($path === FALSE) {
				$path = '';
			}

			// if the path equals the script filename, either because 'index.php' was
			// explicitly provided in the URL, or because the server added it to
			// $_SERVER['REQUEST_URI'] even when it wasn't provided in the URL (some
			// versions of Microsoft IIS do this), the front page should be served
			if ($path == basename($_SERVER['PHP_SELF'])) {
				$path = '';
			}
		}

		return $path;
	}

	private function getRequestMethod() {
		static $method;

		if (isset($method)) {
			return $method;
		}

		$method = strtolower($_SERVER['REQUEST_METHOD']); 
		// make sure $method is valid and supported
		in_array($method, array('get', 'post', 'delete')) || $method = 'get';

		return $method;
	}

	private function detectBaseUrl() {
		$protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
		$host = $_SERVER['SERVER_NAME'];
		$port = ($_SERVER['SERVER_PORT'] == 80 ? '' : ':' . $_SERVER['SERVER_PORT']);
		$uri = preg_replace("/\?.*/", '', $_SERVER['REQUEST_URI']);

		return "$protocol$host$port";
	}

    public function __toString() {
        return serialize($this->request_data);
        //return json_encode($this->request_data);
    }

}
