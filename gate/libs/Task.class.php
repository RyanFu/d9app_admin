<?php
namespace Gate\Libs;

use \Phplib\DB\SQLMonitor;
use \Phplib\Redis\RedisMonitor;

class Task {

	protected $args = array();

	public function __construct($args = array()) {
		$this->args = $args;
		$sql_monitor = SQLMonitor::getMonitor();
		$sql_monitor->shutDownMonitor();
		$redis_monitor = RedisMonitor::getMonitor();
		$redis_monitor->shutDownMonitor();
	}
}
