<?php
namespace Gate\Config\Scripts;

class MySQL extends \Phplib\Config {

	protected function __construct() {
		$this->redmine = $this->_redmine();
		$this->crab = $this->_crab();
	}

	/**
	 * dolphin database.
	 */
	private function _crab() {
		$config = array();
		$config['MASTER']    = array('HOST' => '192.168.128.18', 'PORT' => '3306', 'USER' => 'root', 'PASS' => 'LRo4LVcvxJFSk', 'DB' => 'crab');
		$config['SLAVES'][0] = array('HOST' => '192.168.128.18',	'PORT' => '3306', 'USER' => 'dbreader', 'PASS' => 'wearefashions', 'DB' => 'crab');
		$config['SLAVES'][1] = array('HOST' => '192.168.128.18',	'PORT' => '3306', 'USER' => 'dbreader', 'PASS' => 'wearefashions', 'DB' => 'crab');
		return $config;
	}

	private function _redmine() {
		$config = array();
		$config['SLAVES'][] = array('HOST' => '192.168.146.7',	'PORT' => '3306', 'USER' => 'redmine', 'PASS' => 'KrAysmRz4fXnbfph', 'DB' => 'appmarket');
		return $config;
	}

}
