<?php
namespace Gate\Libs\ORM;

/**
 * TODO: Fix more complicated statements such as
 * F('make') + F('model') * 3
 */
class F extends ExpressionNode {

	/**
	 * Add new object as linked list element along with it's
	 * math operator
	 *
	 * @param F $f
	 * @param char $modifier
	 * @return F
	 */
	public function add($f, $modifier) {
		if (!($f instanceof F))
			$f = new F($f);

		if ($this->child instanceof F) {
			$this->child->setChild($f);
			$this->child->setModifier($modifier);
			return $this;
		} else {
			return parent::add($f, $modifier);
		}
	}

	/**
	 * Create the partial SQL query that this node represents
	 *
	 * @return array Returns both the created "where" statement and any joins required
	 */
	public function create_where($query) {			
		$joins = array();
		$stmt = "";

		if ($this->child) {
			list($arg, $this_joins) = $this->child->create_where($query);
			$joins = array_merge($joins, $this_joins);
			$stmt .= strtolower($query->get_class()) . ".{$arg}";
		}

		if ($this->modifier)
			$stmt .= " {$this->modifier} ";

		/**
		 * If this field of this F object is another F object,
		 * then use that fields' create_where() method to build
		 * this statement through recursion
		 */
		$stmt .= $this->field;
		
		return array($stmt, $joins);
	}

}
