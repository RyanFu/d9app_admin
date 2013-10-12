<?php
namespace Gate\Libs\ORM;

class Field {

	/**
	 * Returns a callback type expression for creating complex queries
	 * where a certain field needs to be tested against a subquery, e.g.
	 * 
	 * Field::id__in(Field::__callback($somequery, "other_id"))
	 * 
	 * would create a statement where the ID field of one table is checked
	 * if it exists in the other_id field of another query
	 * 
	 * This is used for creating complex callbacks in ManyToManyField
	 * retrieval
	 * 
	 * @param QuerySet $query
	 * @param string $field
	 * @return CallbackExpression
	 */
	public static function __callback(QuerySet $query, $field) {
		return new CallbackExpression($query, $field);
	}
}
