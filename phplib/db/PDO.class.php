<?php
namespace Phplib\DB;

class PDO extends \PDO {
	public function __construct($host, $db, $user, $pass, $port = 3306) {
		$dsn ="mysql:dbname={$db};host={$host};port={$port};";

		$options = array(
			PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => TRUE,
		);

		parent::__construct($dsn, $user, $pass, $options);
	}
}

