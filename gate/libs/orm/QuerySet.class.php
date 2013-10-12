<?php
namespace Gate\Libs\ORM;

/**
 * The base class for creating database queries on the table
 * of a particular Model class
 */
class QuerySet implements \IteratorAggregate, \ArrayAccess, \Countable {
	
	public static $operators = array(
		"EXACT",
		"IEXACT",
		"CONTAINS",
		"ICONTAINS",
		"IN",
		"GT",
		"GTE",
		"LT",
		"LTE",
		"STARTSWITH",
		"ISTARTSWITH",
		"ENDSWITH",
		"IENDSWITH",
		"RANGE",
		"YEAR",
		"MONTH",
		"DAY",
		"WEEK_DAY",
		"ISNULL",
		"SEARCH",
		"REGEX",
		"IREGEX",
		"TIMES",
		"DIVIDED_BY",
		"PLUS",
		"MINUS"
	);

	/**
	 * Store the query arguments for this query
	 *
	 * @var array
	 */
	protected $args = array();

	/**
	 * Store the parent query that is being filtered
	 * or store the class name of the Model for which this QuerySet
	 * references
	 *
	 * @var string|QuerySet
	 */
	protected $parent = NULL;

	/**
	 * Store the SQL query orders that should be used
	 * default is ascending, descending is defined by - in front of field name
	 * i.e. orderBy('make') or orderBy('-make')
	 *
	 * @var array
	 */
	protected $order_by = array();

	/**
	 * Store the SQL query limit using the ArrayAccess interface
	 * such as
	 *
	 * $test = SomeClass::get();
	 * $test["5:10"]
	 *
	 * @see http://docs.djangoproject.com/en/1.2/topics/db/queries/#limiting-querysets
	 *
	 * @var string
	 */
	protected $limit = NULL;

	/**
	 * Stores the Collection object for this QuerySet and caches it
	 * 
	 * @var Collection
	 */
	private $collection = NULL;

	/**
	 * If this variable is set, this query only returns data of
	 * particular columns.
	 *
	 * @var array
	 */
	protected $select = array();

    /**
     * Stores the columns to get from database for this QuerySet.
     *
     * @var array
     */

	/**
	 * Create a new query using provided parent (either class name for root
	 * QuerySet object, or another QuerySet object) and arguments
	 *
	 * @param QuerySet $parent
	 * @param array $args
	 */
	public function __construct($parent, $args = NULL) {

		if (!($parent instanceof QuerySet) && !class_exists($parent)) {
			throw new \Exception("Invalid QuerySet parent. Must either be valid Model class name or QuerySet object.");
		}
		$this->parent = $parent;

		// perfect valid to have a query that returns all items
		if (count($args)) {
			$this->parseArguments($args);
		}
	}

	/**
	 * Returns a debugging reference string of all the objects
	 * returned by this QuerySet
	 * 
	 * @return type string
	 */
	public function __toString() {
		$objs = array();
		foreach($this AS $obj)
			$objs[] = "<" . get_class($obj) . ": {$obj}>";
		return htmlentities("[" . implode(", ", $objs) . "]");
	}
	
	/**
	 * Returns the first Object by given particular query arguments.
	 * NOTICE: This method triggers the query running IMMEDIATELY.
	 * 
	 * @param mixed $args
	 * @return Object
	 */
	public function get() {
        call_user_func_array(array($this, 'filter'), func_get_args());
		return $this[0];
	}

	public function getCol($col) {
		$values = array();
		foreach ($this AS $item) {
			$values = $item[$col];
		}
		return $values;
	}

	/**
	 * Filter this query
	 *
	 * @return QueryFilter
	 */
	public function filter() {
		if ($this->limit || $this->order_by) {
			throw new \Exception("Cannot further filter query after limiting or ordering.");
		}

		// wrap all passed-in arguments in an array
		$args = func_get_args();
		if (count($args)) {
			$this->parseArguments($args);
		}

		// return the object itself for chaining calls, i.e.
		// $users = Model::objects()
		//   ->filter('name__contains', 'jobs')
		//   ->filter('age__gt', 27);
		return $this;
	}

	/**
	 * Set what field to order by for this query, using
	 * the - symbol to define ASC/DESC
	 * i.e. orderBy("make") or orderBy("-make")
	 * Also accepts multiple ordering schemes, i.e.
	 * orderBy("username", "-password")
	 * represents
	 * ORDER BY username ASC, password DESC
	 *
	 * @param mixed $field
	 */
	public function orderBy($fields) {
		// reset the collection
		$this->collection = NULL;
		$this->order_by = func_get_args();
		return $this;
	}
	
	/**
	 * Reverses the orders of a QuerySet
	 * 
	 * @return QuerySet 
	 */
	public function reverse() {
		// reset the collection
		$this->collection = NULL;

		$new = array();
		foreach($this->order_by AS $order) {
			if (substr($order, 0, 1) == "-") {
				$new[] = substr($order, 1);
			}
			else {
				$new[] = "-{$order}";
			}
		}
		$this->order_by = $new;

		return $this;
	}

    public function select($cols) {
		if (!is_array($cols)) {
			$cols = func_get_args();
		}

		foreach ($cols AS $col) {
			$this->select[] = $col;
		}

		$this->select = array_flip(array_flip($this->select));
		return $this;
    }
	
	public function getSelect() {
		return $this->select;
	}

	/**
	 * Returns what Model class this QuerySet pertains to
	 * by recursively finding the top-most QuerySet object
	 * 
	 * @return string
	 */
	public function getModel() {
		if ($this->parent instanceof QuerySet) {
			return $this->parent->getModel();
		}
		return $this->parent;
	}

	/**
	 * A shortcut function for retrieving Model table name.
	 */
	public function getTable() {
		$class = $this->getModel();
		return $class::_TABLE_;
	}

	/**
	 * Limit this query set using offset and limit parameters
	 *
	 * @see http://docs.djangoproject.com/en/1.2/topics/db/queries/#limiting-querysets
	 *
	 * @param int $offset
	 * @param int $limit
	 * @return QuerySet
	 */
	public function limit($offset = NULL, $limit = NULL) {
		$offset = (int) $offset;
		$limit = (int) $limit;

		if (!$limit)
			$limit = "18446744073709551610";

		$this->limit = "{$offset}:{$limit}";
		return $this;
	}

	/**
	 * Returns the number of rows that _should_ be returned
	 * given the current limit of this query. For example:
	 *
	 * $query[":10"]
	 *
	 * Should theoretically return 10 results. However if the
	 * actual number of rows in the database is less, there are
	 * less than 10 results. This will help determine what is real.
	 *
	 * @return integer
	 */
	public function getLimitCount() {
		if (strpos($this->limit, ":") !== false) {
			list($offset, $limit) = split(":", $this->limit);
			if ($offset)
				return $limit - $offset;
			else
				return $limit;
		}
	}

	/**
	 * Update all the objects returned by a query in one shot, basically
	 * they same as looping through each returned object and updating
	 * one or more fields, except it performs a direct database update
	 * 
	 * e.g. ->update(array("field" => "new value"))
	 * 
	 * @param array $args
	 * @return QuerySet
	 */
	public function update($args) {
		return Database::queryUpdate($this, $args);
	}

	public function incr($args) {
		return Database::queryIncr($this, $args);
	}

	public function decr($args) {
		return Database::queryDecr($this, $args);
	}
	
	/**
	 * Deletes all of the returned objects of a query
	 * Same as update() method, where except deleting returned objects 1 by 1,
	 * it performs a direct DELETE statement on the database given the
	 * built query
	 * 
	 * @return QuerySet
	 */
	public function delete() {
		return Database::queryDelete($this);
	}

	/**
	 * Creates a new object given $args, an array of predefined default
	 * values
	 * 
	 * @param array $args
	 * @return Object
	 */
	public function create($args) {
		$class = $this->getModel();
		$obj = new $class($args, TRUE);
		$obj->save(TRUE); // force insert
		return $obj;
	}

	/**
	 * Replace a new object given $args, an array of predefined default
	 * values
	 * 
	 * @param array $args
	 * @return Object
	 */
	public function forcesave($args) {
		$class = $this->getModel();
		$obj = new $class($args, TRUE);
		$obj->forcesave(TRUE); // force insert
		return $obj;
	}

	/**
	 * Create a full or partial MySQL statement using this Query
	 * object
	 * 
	 * Returns an array of each part of the full MySQL statement:
	 * 
	 * array(
	 *	table
	 *	WHERE query/clause
	 *	ORDER BY clause
	 *	LIMIT clause
	 * );
	 *
	 * @return array
	 */
	public function createStatement() {
		// TODO: lock this function out from being called by anything
		// other than the DBHandler? Leaving for now for debugging

		$parent = $this->parent;
		$class = $this->getModel();
		
		$sql_table = '';
		$sql_query = '';
		$sql_order = '';
		$sql_limit = '';

		$sql_table = $parent::_TABLE_;
		$sql_query = $this->createWhere();

		/**
		 * ORDER BY
		 */
		if (count($this->order_by)) {

			$sql_order .= "ORDER BY ";

			$orders = array();
			foreach($this->order_by AS $order) {
				if ($order[0] == "-") {
					$field = substr($order, 1);
					$order = "DESC";
				} else {
					$field = $order;
					$order = "ASC";
				}

				$table = strtolower($class::_TABLE_);
				$orders[] = "{$table}.{$field} {$order}";
			}
			$sql_order .= implode(", ", $orders);
		}

		/**
		 * LIMIT
		 */
		if ($this->limit) {
			list($offset, $limit) = explode(':', $this->limit);

			if ($offset) {
				$sql_limit = " LIMIT {$limit} OFFSET {$offset}";
			}
			else {
				$sql_limit .= " LIMIT {$limit}";
			}
		}

		/**
		 * BUILD THE QUERY
		 */
		//TODO??

		return array(trim($sql_table), trim($sql_query), trim($sql_order), trim($sql_limit));
	}
	
	/**
	 * Create only the portion of the MySQL statement after the 'WHERE' clause
	 * 
	 * Returns an array of the WHERE clause and any joins necessary to properly
	 * execute it
	 * 
	 * @return array
	 */
	public function createWhere() {
		$where = '';

		if (count($this->args)) {

			if (!($this->parent instanceof QuerySet)) {
				$where .= ' WHERE ';
			}

			$tests = array();

			if (count($this->args) > 1) $where .= '(';

			foreach($this->args AS $arg) {
				$stmt = $this->formatArgument($arg);
				$tests[] = $stmt;
			}

			$where .= implode(' AND ', $tests);

			if (count($this->args) > 1) $where .= ')';

		}
		return $where;
	}

	/**
	 * Create a new Expression for QuerySet objects
	 *
	 * @param string $func
	 * @param string|Expression $value
	 * @return Expression
	 */
	public static function createExpression($func, $value) {
		$num_split = substr_count($func, "__");
		if ($num_split == 1) {
			// standard query using fields from an object's own table
			// e.g. username__contains("a")
			list($field, $operator) = explode("__", $func);
			$subfield = NULL;
		}
		else {
			// can't have an expression that goes deeper than this
			throw new \Exception(sprintf("Invalid Expression: %s", $func));
		}

		/**
		 * if the provided value is an expression itself, this is a complex
		 * qurey that requires use of an F expression node (for self-referencing
		 * fields), e.g:
		 *
		 * Field::price__gt(Field::cost__times(2))
		 *
		 * Should produce a query like:
		 *
		 * price > cost * 2
		 *
		 * where 'Field::cost__times(2)' is the subexpression
		 */
		if (in_array(strtoupper($operator), array("TIMES", "DIVIDED_BY", "PLUS", "MINUS"))) {

			switch(strtoupper($operator)) {
				case "TIMES":
					$operator = "*";
					break;
				case "DIVIDED_BY":
					$operator = "/";
					break;
				case "PLUS":
					$operator = "+";
					break;
				case "MINUS":
					$operator = "-";
					break;
			}

			if ($value instanceof F) {
				$f = $value->add($field, $operator);
			} else {
				$f = new F($field);
				$f = $f->add($value, $operator);
			}
			return $f;

		} else {
			return new Expression($field, $operator, $value, $subfield);
		}
	}

	/**
	 * Format a query expression (i.e. Field::fieldname__contains('value'))
	 * into a MySQL compatible WHERE test
	 * 
	 * Returns the SQL statement
	 *
	 * @param Expression $arg
	 * @return string $stmt
	 */
	public function formatArgument(Expression $arg) {
		$model_class = $this->getModel();

		$field = $arg->getField();
		$value = $arg->getValue();
		$operator = $arg->getOperator();

		$table = $model_class::_TABLE_;

		$stmt = '';

		// properly escape quotes
		if (!is_array($value) && !is_object($value))
			$value = Database::quoteSmart($value);

		/**
		 * @see http://docs.djangoproject.com/en/1.2/ref/models/querysets/#field-lookups
		 */
		switch(strtoupper($operator)) {
			case "EXACT":
				if ($value == NULL and 0 !== $value)
					$stmt = "{$field} IS NULL";
                elseif (is_numeric($value) && $value == 0)
					$stmt = "{$field} = 0";
				else
					$stmt = "{$field} = '{$value}'";
				break;
			case "IEXACT":
				$stmt = "{$field} ILIKE '{$value}'";
				break;
			case "CONTAINS":
				$stmt = "{$field} LIKE '%{$value}%'";
				break;
			case "ICONTAINS":
				$stmt = "{$field} ILIKE '%{$value}%'";
				break;
			case "IN":
				if (is_array($value)) {
					$ids = array();
					foreach($value AS $v)
						$ids[] = "'" . Database::quoteSmart($v) . "'";
					$stmt = "{$field} IN (" . implode (", ", $ids) . ")";
				} else {
					throw new \Exception("Invalid value for {$field} IN " . var_export($value, true));
				}
				break;
			case "GT":
				$stmt = "{$field} > '{$value}'";
				break;
			case "GTE":
				$stmt = "{$field} >= '{$value}'";
				break;
			case "LT":
				$stmt = "{$field} < '{$value}'";
				break;
			case "LTE":
				$stmt = "{$field} <= '{$value}'";
				break;
			case "STARTSWITH":
				$stmt = "{$field} LIKE '{$value}%'";
				break;
			case "ISTARTSWITH":
				$stmt = "{$field} ILIKE '{$value}%'";
				break;
			case "ENDSWITH":
				$stmt = "{$field} LIKE '%{$value}'";
				break;
			case "IENDSWITH":
				$stmt = "{$field} ILIKE '%{$value}'";
				break;
			case "RANGE":
				if (is_array($value) && count($value) == 2)
					$stmt = "{$field} BETWEEN '{$value[0]}' AND '{$value[1]}'";
				else
					throw new \Exception("Invalid value for {$field} RANGE " . var_export($value, true));
				break;
			case "YEAR":
				$stmt = "EXTRACT('year' FROM {$field}) = '{$value}'";
				break;
			case "MONTH":
				$stmt = "EXTRACT('month' FROM {$field}) = '{$value}'";
				break;
			case "DAY":
				$stmt = "EXTRACT ('day' FROM {$field}) = '{$value}'";
				break;
			case "WEEK_DAY":
				$stmt = "EXTRACT('dayofweek' FROM {$field}) = '{$value}'";
				break;
			case "ISNULL":
				if ($value === true)
					$stmt = "{$field} IS NULL";
				else
					$stmt = "{$field} IS NOT NULL";
				break;
			case "SEARCH":
				$stmt = "MATCH({$table}, {$field}) AGAINST ('{$value}' IN BOOLEAN MODE)";
				break;
			case "REGEX":
				$stmt = "{$field} REGEXP BINARY '{$value}'";
				break;
			case "IREGEX":
				$stmt = "{$field} REGEXP '{$value}'";
				break;
		}
		
		$arg->isNot() && $stmt = "NOT ({$stmt})";
		
		return $stmt;
	}

	/**
	 * Parse arguments.
	 * Possible argument types:
	 *   Model::objects()->filter(12); 
	 *   Model::objects()->filter('id__gt', 3);
     *   Model::objects()->filter('user__username__contains', 'jobs');
	 *   Model::objects()->filter(array('id__lt' => 54, 'name__contains' => 'jobs'));
	 *   Q or F object
	 */
	private function parseArguments($args) {
		$model_class = $this->getModel();

		if (1 == count($args) && is_int($args[0])) {
			// filter by primary key, i.e.
			// Model::objects()->filter(1);
			$this->args[] = self::createExpression($model_class::_PRIMARY_KEY_ . '__exact', $args[0]);
			return;
		}

		if (2 == count($args) && is_string($args[0])) {
			// Model::objects()->filter('id__gt', 3);

            // check NOT
			$not = FALSE;
            $expr = $args[0];
			if (strpos($expr, '!') === 0) {
				$not = TRUE;
				$expr = substr($expr, 1);
			}

            $num_split = substr_count($expr, "__");
            if (2 == $num_split) {
                // remote field query using foreign key, for example
                // filter('user__username__contains', 'a');
                list($field, $remote_field, $operator) = explode("__", $expr);
                $relation = $model_class::getRelation($field);

                if ($relation instanceof ForeignKey) {
					$to_class = $relation->getClass();
					$foreign_key_field = $relation->getField();

					if ($remote_field == $to_class::_PRIMARY_KEY_) {
						// if the specified $remote_field is $to_class' PK then
						// there is no need to load $to_class primary key
						$expr = "{$foreign_key_field}__{$operator}";
						$value = $args[1];
					}
					else {
						$expr = "{$foreign_key_field}__in";
						$value = $to_class::objects()->filter("{$remote_field}__{$operator}", $args[1])->select($to_class::_PRIMARY_KEY_);
					}
                }
                else if ($relation instanceof OneToManyRelation) {
                    $to_class = $relation->getClass();
                    // find the matching ForeignKey in $to_class
                    $foreign_key_field = NULL;
                    foreach ($to_class::getRelations() AS $r) {
                        if ($r instanceof ForeignKey && $r->getClass() == $model_class) {
                            $foreign_key_field = $r->getField(); 
                        }
                    }
                    if (is_null($foreign_key_field)) {
                        throw new \Exception("Cannot find matching ForeignKey field in class \"{$to_class}\".");
                    }

                    // in this case main query filters on primary key of main
                    // table. sub query returns id set from remote table 
                    $expr = $model_class::_PRIMARY_KEY_ . '__in';
                    $value = $to_class::objects()->filter("{$remote_field}__{$operator}", $args[1])->select($foreign_key_field);
                }
                else if ($relation instanceof ManyToManyRelation) {
                    $to_class = $relation->getClass();
                    $through = $relation->getThroughClass();
                    $self_key_field = NULL;
                    $to_key_field = NULL;
                    foreach ($through::getRelations() AS $r) {
                        if ($r instanceof ForeignKey && $r->getClass() == $model_class) {
                            $self_key_field = $r->getField();
                        }
                        if ($r instanceof ForeignKey && $r->getClass() == $to_class) {
                            $to_key_field = $r->getField();
                        }
                    }

                    if (is_null($self_key_field)) {
                        throw new \Exception ("Cannot find matching reference to class \"{$model_class}\" in class \"{$through}\".");
                    }
                    if (is_null($to_key_field)) {
                        throw new \Exception ("Cannot find matching reference to class \"{$to_class}\" in class \"{$through}\".");
                    }

					if ($remote_field == $to_class::_PRIMARY_KEY_) {
						// if the specified $remote_field is $to_class' PK then
						// there is no need to load $to_class primary key
						$value = $through::objects()->filter("{$to_key_field}__{$operator}", $args[1])->select($self_key_field);
					}
					else {
						// query on the supporting table (named by xiaoshui)
						$value = $to_class::objects()->filter("{$remote_field}__{$operator}", $args[1])->select($to_class::_PRIMARY_KEY_);
						// query on the relation table
						$value = $through::objects()->filter("{$to_key_field}__in", $value)->select($self_key_field);
					}

                    $expr = $model_class::_PRIMARY_KEY_ . '__in';
                }
            }
            else if (1 == $num_split) {
                // standard query using fields from a model's own table
                // ('username__contains', 'a');
                $value = $args[1];
            }
            else if (0 == $num_split) {
                // standard query using fields from a model's own table
                // by default use 'exact' expression
                // ('user_id', 15)
                $expr .= '__exact';
                $value = $args[1];

            }
            else {
                throw new \Exception("Unknown argument type of Model::objects()->filter() \"{$args[0]}\".");
            }

			$arg = self::createExpression($expr, $value);
			$not && $arg->setNot();
			$this->args[] = $arg;
			return;
		}

		if (1 == count($args) && ($args[0] instanceof Expression) || ($args[0] instanceof ExpressionNode)) {
			// Q or F object
			$this->args[] = $args[0];
		}

		if (1 == count($args) && is_array($args[0])) {
			// Model::objects()->filter(array('id__lt' => 54, 'name__contains' => 'jobs'));
			foreach ($args[0] AS $expr => $value) {
				$this->parseArguments(array($expr, $value));
			}
		}

		else {
			throw new \Exception("Invalid QuerySet argument: " . var_export($args, true));
		}
	}
	

	/**
	 * ITERATORAGGREGATE
	 */

	/**
	 * Returns a Collection object that is iteratable by a foreach loop
	 * 
	 * @return Collection
	 */
	public function getIterator() {
		return $this->getCollection();
	}

	public function getCollection() {
		if ($this->collection) {
			return $this->collection;
		} else {
			$this->collection = new Collection($this);
			return $this->getCollection();
		}
	}
	
	/**
	 * ARRAYACCESS
	 */
	
	public function offsetSet($offset, $value) {
		throw new \Exception("Cannot arbitrarily set the object at a current " .
						"position in a QuerySet");
		return false;
	}

	public function offsetExists($offset) {
		return ($offset < count($this) && $offset >= 0 ? true : false);
	}

	public function offsetUnset($offset) {
		return $this->collection[$offset]->delete();
	}

	/**
	 * Handles array access when using array shortform, i.e.
	 * $somequery["5:10"], that means offset=5, limit=10
	 *
	 * @param string $limit
	 * @return QuerySet
	 */
	public function offsetGet($limit) {
		if (strpos($limit, ':') !== FALSE) {
			list($offset, $limit) = explode(':', $limit);
			$this->limit($offset, $limit);
			return $this;
		} else {
			return $this->getIterator()->offsetGet($limit);
		}
	}

	/**
	 * COUNTABLE
	 */

	/**
	 * Returns the number of results this QuerySet contains
	 * using it's Collection object, useable by count() and count()
	 * PHP functions
	 *
	 * @return integer
	 */
	public function count() {
		return count($this->getIterator());
	}
	
}
