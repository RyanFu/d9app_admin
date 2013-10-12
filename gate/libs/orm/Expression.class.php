<?php
namespace Gate\Libs\ORM;

/**
 * Each Expression defines how a QuerySet is built and how a database
 * query is execution. Expressions can be used in conjunction with
 * ExpressionNode objects to create complex queries.
 */
class Expression {

	/**
	 * Defines whether this expression should be reversed with a NOT expression
	 * 
	 * @var boolean
	 */
	protected $not = FALSE;

	/**
	 * What table field the operator is testing against
	 *
	 * @var string
	 */
	protected $field = NULL;

	/**
	 * Defines what SQL comparison operator should be used
	 * (=, LIKE, IN, etc)
	 *
	 * @var string
	 */
	protected $operator = NULL;

	/**
	 * What value the field and operator are testing
	 *
	 * @var mixed
	 */
	protected $value = NULL;

	/**
	 * Create a new Expression object
	 * 
	 * @see QuerySet::createExpression()
	 *
	 * @param string $field
	 * @param string $operator
	 * @param mixed $value
	 */
	public function __construct($field, $operator, $value) {
		
		// requires a value that is not NULL
		if (!$value && 0 !== $value) {
			throw new \Exception("Cannot create Expression without test value.");
			return NULL;
		}
		else {
			$this->field = $field;
			// defualt operator: "exact", for field = 'value' queries
			$this->operator = (!$operator ? "exact" : $operator);
			$this->value = $value;
		}
	}
	
	/**
	 * Returns this Expressions' operator
	 *
	 * @return string
	 */
	public function getOperator() {
		return $this->operator;
	}

	/**
	 * Returns this Expressions' field
	 * 
	 * @return string
	 */
	public function getField() {
		return $this->field;
	}

	/**
	 * Returns this Expressions' value
	 *
	 * @return mixed
	 */
	public function getValue() {
		if ($this->value instanceof QuerySet) {
			return $this->value->getCollection()->getValues();
		}
		return $this->value;
	}

	/**
	 * Returns whether or not this Expression should lead with
	 * 'NOT';
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
	public function setNot() {
		$this->not = ($this->not ? FALSE : TRUE);
		return $this;
	}

}
