<?php
namespace Gate\Libs;

class Memcache extends \Phplib\Cache\Memcache {

	/**
	 * Constructor.
	 */
    protected function __construct() {
		$this->config = \Phplib\Config::load('Memcache');
        parent::__construct();
    }

}
