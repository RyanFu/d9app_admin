<?php
namespace Gate\Libs\ORM;

/**
 * Q nodes are used to create complex queries using chained
 * AND/OR operations
 */
class Q extends ExpressionNode {

	protected $not = false;

	/**
	 * Create new Q object with given argument/test
	 *
	 * @param array $arg
	 */
	public function __construct($arg) {
		parent::__construct($arg);
	}

	/**
	 * Use this Q object and it's child Q object to create
	 * a chained statement
	 *
	 * @return array Returns both the created "where" statement and any joins required
	 */
	public function create_where($query) {
		$stmt = "";
		$joins = array();

		// prepend child statement
		if ($this->child) {
			list($arg, $this_joins) = $this->child->create_where($query);
			$stmt .= $arg;
			$joins = array_merge($joins, $this_joins);
		}

		// use 'AND' or 'OR' modifier if applicable (when chained)
		switch($this->modifier) {
			case "&":
				$stmt .= " AND ";
				break;
			case "|":
				$stmt .= " OR ";
				break;
		}

		if ($this->field instanceof Q) {
			list($arg, $this_joins) = $this->field->create_where($query);
			$arg = "({$arg})";
		} else {
			list($arg, $this_joins) = QuerySet::format_argument($this->field, $query);
		}
		$joins = array_merge($joins, $this_joins);
		$stmt .= $arg;

		return array($stmt, $joins);
	}

	/**
	 * Returns whether or not this Expression should lead with
	 * 'NOT'
	 *
	 * @return boolean
	 */
	public function isNot() {
		return $this->not;
	}

	/**
	 * Toggles the $not property of this Q depending on it's
	 * existing value
	 *
	 * @see Q::$not
	 */
	public function set_not() {
		if (!$this->not) $this->not = true;
		else $this->not = false;
		return $this;
	}

}
