<?php
namespace Gate\Libs\ORM;

abstract class QuerySub extends QuerySet {

	/**
	 * Create new subquery (as from ->filter() and ->exclude())
	 */
	public function __construct($parent, $args) {
		// subqueries require arguments
		if (!sizeof($args)) {
			throw new Exception("Cannot exclude or filter query without arguments.");
		}
		parent::__construct($parent, $args);
	}

}

