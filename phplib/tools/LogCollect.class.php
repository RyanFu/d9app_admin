<?php
namespace Phplib\Tools;

USE Phplib\Thrift;
USE Phplib\Thrift\Transport\TFramedTransport;
USE Phplib\Thrift\Protocol\TBinaryProtocol;
USE Phplib\Thrift\Protocol\TBinaryProtocolAccelerated;
USE Phplib\Thrift\Packages\Scribe\ScribeClient;
USE Phplib\Thrift\Transport\TSocket;
USE Phplib\Thrift\Packages\Scribe\LogEntry;

require_once($GLOBALS['THRIFT_ROOT_LIB'] . '/packages/scribe/scribe_types.php');

require_once($GLOBALS['THRIFT_ROOT_LIB'] . '/Thrift.php');
require_once($GLOBALS['THRIFT_ROOT_LIB'] . '/transport/TFramedTransport.php');
require_once($GLOBALS['THRIFT_ROOT_LIB'] . '/transport/TSocket.php');
require_once($GLOBALS['THRIFT_ROOT_LIB'] . '/protocol/TBinaryProtocol.php');
require_once(PLATFORM_SERVICE_PATH_LIB . '/Scribe.php');

class LogCollect {
	private $nodes;
	static $instance = NULL;

	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
        if (empty($GLOBALS['LOG_SERVER_CONFIG'])) {
            return FALSE;
        }
        $this->nodes = $GLOBALS['LOG_SERVER_CONFIG'];
        $this->nodesCount = count($this->nodes);
	}


	public function getScribeClient() {
		$node = $this->nodes[rand(0, $this->nodesCount - 1)];
		$socket = new TSocket($node['host'], $node['port'], TRUE);
		$socket->setSendTimeout(100);
		$socket->setRecvTimeout(100);
		$this->transport = $transport = new TFramedTransport($socket);
		$protocol = new TBinaryProtocolAccelerated($transport);
		$client = new ScribeClient($protocol, $protocol);
		return $client;
	}

	public function sendLog($filename, $log_str) {
		if (empty($this->nodes)) {
			self::MlscacheLog('ERROR', 'LogCollect nodes is empty. Please configure first');
			return FALSE;
		}
		try {
			$client = $this->getScribeClient();
			$this->transport->open();
			!empty($_SERVER['SERVER_ADDR']) ? $n_ip = $_SERVER['SERVER_ADDR'] : $n_ip = '127.0.0.1';
			//$from_ip = gethostbyname($_SERVER['HOSTNAME']);
			$from_ip = gethostbyname(gethostname());
			$log_str .= "\t[$from_ip]\t[$n_ip]";
			$msg1['category'] = $filename;
			$msg1['message'] = $log_str;
			$entry1 = new LogEntry($msg1);
			$message = array($entry1);
			$client->Log($message);
			$this->transport->close();
		} catch(\Exception $e) {
			self::MlscacheLog('ERROR', 'LogCollect_scribe log timeout. Error message: ' . $e->getMessage());
		}
	}

    public static function MlscacheLog($level, $str) {
       list($usec, $sec) = explode(' ',microtime());
       $milliSec = (int)((float)$usec * 1000000);
       $intSec = intval($sec);
       $ret = file_put_contents('/home/work/webdata/logs/mlscache.' . date('YmdH',$intSec) . '.log',
           sprintf("%s %s:%d:%d %s\n", $level, date('Y-m-d H:i:s', $intSec), $milliSec, 0, $str), FILE_APPEND);
    }
}
