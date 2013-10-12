<?php
namespace Gate\Libs\ORM;

/**
 * ExpressionNodes act as a linked-list where each
 * node can have a child node that is another ExpressionNode.
 * This link of nodes can be used to create complex queries.
 */
abstract class ExpressionNode {
	/**
	 * Attached ExpressionNode objects with operators
	 *
	 * @var ExpressionNode
	 */
	protected $child;

	/**
	 * Defines what field of the database should be
	 * referenced
	 *
	 * @var string
	 */
	protected $field;

	/**
	 * The modified for this field reference
	 *
	 * @var char
	 */
	protected $modifier;

	/**
	 * Construct a new ExpressionNode
	 *
	 * @param mixed $field
	 */
	public function __construct($field) {
		$this->field = $field;
	}

	/**
	 * Store child element of linked list
	 *
	 * @param ExpressionNode $child
	 */
	protected function setChild($child) {
		$this->child = $child;
	}

	/**
	 * Store the modifier (either &, | for Q, or
	 * +, -, *, / for F)
	 *
	 * @param char $modifier
	 */
	protected function setModifier($modifier) {
		$this->modifier .= $modifier;
	}

	/**
	 * Create a linked list of F objects
	 *
	 * @param F|int $f
	 * @param char $modifier
	 * @return F
	 */
	public function add($s, $modifier) {
		$s->setModifier($modifier);
		$s->setChild($this);
		return $s;
	}

}

/**
 * Create a set of Q nodes using 'AND'
 */
function _AND_($args) {
	$q = false;
	$args = func_get_args(); 

	if (is_array($args[0]) && sizeof($args)) {
		// trying to pass as array of args
		$args = $args[0];
		if (!sizeof($args))
			throw new Exception("Cannot create _AND_ expression without " .
							"arguments.");
	}

	foreach($args as $arg)
		if (!$q)
			$q = new Q($arg);
		else
			$q = $q->add(new Q($arg), "&");

	return $q;
}

/**
 * Create a set of Q nodes using 'OR'
 */
function _OR_($args) {
	$q = false;
	$args = func_get_args(); 

	if (is_array($args[0]) && sizeof($args)) {
		// trying to pass as array of args
		$args = $args[0];
		if (!sizeof($args))
			throw new Exception("Cannot create _OR_ expression without arguments.");
	}

	foreach($args as $arg)
		if (!$q)
			$q = new Q($arg);
		else
			$q = $q->add(new Q($arg), "|");

	return $q;
}
