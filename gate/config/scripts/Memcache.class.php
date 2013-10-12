<?php
namespace Gate\Config\Scripts;

class Memcache extends \Phplib\Config {

	protected function __construct() {
		$this->pools = array(
				array('host' => '192.168.128.13', 'port' => 11211),
				array('host' => '192.168.128.13', 'port' => 11212),
		);
	}   
}
