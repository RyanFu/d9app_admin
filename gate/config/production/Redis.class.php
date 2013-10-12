<?php
namespace Gate\Config\Production;

class Redis extends \Phplib\Config {

	public function __construct() {
		$this->servers = array(
			array('host' => '192.168.11.10', 'port' => '6379'),
			array('host' => '192.168.11.10', 'port' => '6479'),
		);

		$this->writeHost = 'http://192.168.11.10/write';
		$this->xwriteHost = 'http://192.168.11.10/xwrite';
		$this->readHosts = array(
            'http://192.168.11.10:8080/read',
            'http://192.168.11.10:8080/read',
            'http://192.168.11.10:8080/read'
        );
	}
}
