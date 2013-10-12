<?php
namespace Phplib\DB;

/**
 * 
 * beginTransaction() 
 * do imprortant stuff 
 * call method 
 *     beginTransaction() 
 *        basic stuff 1 
 *        basic stuff 2 
 *     commit() 
 * do most important stuff 
 * commit() 
 *
 * Won't work and is dangerous since you could close your transaction too early with the nested 
 * commit().
 */
class DatabaseTrans extends Database {

	/**
	 * Singleton.
	 */
	public static function getConn($database) {
		static $singletons = array();
		!isset($singletons[$database]) && $singletons[$database] = new DatabaseTrans($database);
		return $singletons[$database];
	}


	protected $transactionCounter = 0;

	public function beginTransaction() {
		if (!$this->transactionCounter++) {
			$connection = $this->getConnection(self::MASTER);
			return $connection->beginTransaction();
		}
		return $this->transactionCounter >= 0;
	}

	public function commit() {
		if (!--$this->transactionCounter) {
			$connection = $this->getConnection(self::MASTER);
			return $connection->commit();
		}
		return $this->transactionCounter >= 0;
	}

	public function rollback() {
		if ($this->transactionCounter >= 0) {
			$this->transactionCounter = 0;
			$connection = $this->getConnection(self::MASTER);
			return $connection->rollback();
		}
		$this->transactionCounter = 0;
		return FALSE;
	}

	/**
	 * @override
	 *
	 */
    protected function catchError($sth, $sql = '', $params = '') {
        list($sql_state, $error_code, $error_message) = $sth->errorInfo();

        if ($sql_state == '00000') {
            return TRUE;
    	}

		$msg = "state:{$sql_state},error_code:{$error_code},error_message:{$error_message}";
		throw new \Exception($msg);
	}
}
