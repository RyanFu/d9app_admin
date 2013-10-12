<?php
namespace Phplib\DB;

abstract class DBModelTrans {
    protected static $instances = array();

    public static function getConn() {
        $class = get_called_class();
		$database = $class::_DATABASE_;

        if (is_null($instances[$class])) {
            $instances[$database] = DatabaseTrans::getConn($database);
        }

        return $instances[$database];
    }

}
