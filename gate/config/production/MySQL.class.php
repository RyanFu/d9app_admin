<?php
namespace Gate\Config\Production;

class MySQL extends \Phplib\Config {

	protected function __construct() {
		$this->appmarket = $this->_appmarket();
		$this->crab = $this->_crab();
	}

	/**
	 * dolphin database.
	 */
	private function _crab() {
		$config = array();
		$config['MASTER']    = array('HOST' => 'localhost', 'PORT' => '3306', 'USER' => 'appmarket', 'PASS' => '016a526c', 'DB' => 'appmarket');
		$config['SLAVES'][0] = array('HOST' => 'localhost',	'PORT' => '3306', 'USER' => 'appmarket', 'PASS' => '016a526c', 'DB' => 'appmarket');
		$config['SLAVES'][1] = array('HOST' => 'localhost',	'PORT' => '3306', 'USER' => 'appmarket', 'PASS' => '016a526c', 'DB' => 'appmarket');
		return $config;
	}

	private function _appmarket() {
		$config = array();
		$config['SLAVES'][] = array('HOST' => 'localhost',	'PORT' => '3306', 'USER' => 'appmarket', 'PASS' => '016a526c', 'DB' => 'appmarket');
		return $config;
	}

}
