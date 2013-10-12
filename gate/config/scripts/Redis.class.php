<?php
namespace Gate\Config\Scripts;

class Redis extends \Phplib\Config {

	public function __construct() {
		$this->servers = array(
			array('host' => '192.168.128.139', 'port' => '6379'),
			array('host' => '192.168.128.139', 'port' => '6479'),
		);

		$this->writeHost = 'http://192.168.128.139/write';
		$this->xwriteHost = 'http://192.168.128.139/xwrite';
		$this->readHosts = array(
            'http://192.168.128.139:8080/read',
            'http://192.168.128.139:8080/read',
            'http://192.168.128.139:8080/read'
        );
	}
}
