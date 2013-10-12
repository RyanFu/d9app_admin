<?php
namespace Phplib\DB;

abstract class DBModel {

    public static function getConn() {
        $class = get_called_class();
		$database = $class::_DATABASE_;

        return Database::getConn($database);

    }

}
