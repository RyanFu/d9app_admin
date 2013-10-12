<?php
namespace Gate\Libs\ORM;

class CallbackExpression extends Expression {

	public function __construct(QuerySet $query, $field) {
		$this->value = $query;
		$this->field = $field;
		$this->operator = "IN";
	}

}
