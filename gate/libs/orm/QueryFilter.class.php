<?php
namespace Gate\Libs\ORM;

class QueryFilter extends QuerySub {

	/**
	 * Return the where portion of the statement prepended
	 * this QueryFilter object
	 *
	 * @return string
	 */
	public function createWhere() {
		list($where, $joins) = parent::createWhere();
		if (!($this->parent->parent instanceof QuerySet))
			$where = " WHERE " . $where;
		else
			$where = " AND " . $where;
	 
		return array($where, $joins);
	}

}

