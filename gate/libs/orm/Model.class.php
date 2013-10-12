<?php
namespace Gate\Libs\ORM;

abstract class Model extends Object {

	public function __construct($init = null, $new = FALSE) {
		if (is_numeric($init)) {
			// has pre-defined id for this object
			parent::__construct($init);
		}
		else if (is_array($init)) {
			$class = get_called_class();

			if (isset($init[$class::_PRIMARY_KEY_])) {
				// initialize an existing object with given values
				parent::__construct($init[$class::_PRIMARY_KEY_]);
				unset($init[$class::_PRIMARY_KEY_]);
				foreach ($init as $field => $value) {
					$this->setField($field, $value, $new);
				}
				$this->loaded = TRUE;
			}
			else {
				// initialize an new object with given values
				parent::__construct();
				foreach ($init as $field => $value) {
					$this->__set($field, $value);
				}
			}
		}
		else {
			parent::__construct();
		}
	}

	/**
	 * Return this object as a string. Simply return class name.
	 */
	public function __toString() {
		return get_called_class();
	}

	/**
	 * Set the value of a particular field, e.g.
	 * $obj->some_field = 'value'
	 *
	 * @param string $field
	 * @param mixed $value
	 * @return object
	 */
	public function __set($field, $value) {
		$class = get_called_class();
		$relation = $class::getRelation($field);

		if ($relation instanceof ForeignKey) {
			$to_class = $relation->getClass();
			if ($value instanceof $to_class || is_numeric($value)) {
				// allow to assign integer value as foreign key or a proper
				// object of the class which the foreign key represents
				return parent::__set($field, $value);
			}
			else if (!$value instanceof $to_class) {
				$error = sprintf("Cannot assign a \"%s\" object to ForeignKey field of type \"%s\" of a \"%s\" Object.", get_class($value), $to_class, $class);
				throw new \Exception($error);
			}
			else {
				$error = sprintf("Invalid field assignment value: value of \"%s\" field must be of type \"%s\" or an integer.", $field, $to_class);
				throw new \Exception($error);
			}
		}

		if ($relation instanceof ManyToManyRelation) {
			$to_class = $relation->getClass();

			// in case the value is a single entry, we enforce the value
			// to be an array contains the single entry
			!is_array($value) && $value = array($value);

			$values = array();
			foreach ($value AS $one) {
				if ($one instanceof $to_class || is_numeric($one)) {
					// allow to assign integer value as foreign key or a proper
					// object of the class which the foreign key represents
					$values[] = $one;

					// TODO: do we need to require the array only contains
					// integer or object?
				}
				else if (!$value instanceof $to_class) {
					$error = sprintf("Cannot assign a \"%s\" object to ForeignKey field of type \"%s\" of a \"%s\" Object.", get_class($value), $to_class, $class);
					throw new \Exception($error);
				}
				else {
					$error = sprintf("Invalid field assignment value: value of \"%s\" field must be of type \"%s\" or an integer.", $field, $to_class);
					throw new \Exception($error);
				}
			}
			return parent::__set($field, $values);
		}

		// not an external field
		return parent::__set($field, $value);
	}

	/**
	 * Retrieve the value of a particular field, e.g.
	 * echo $obj->some_field
	 *
	 * @param string $field
	 * @return mixed
	 */
	public function __get($field) {
		$class = get_called_class();
		$relation = $class::getRelation($field);

		if ($relation instanceof OneToManyRelation) {
			$to_class = $relation->getClass();

			// verify whether $to_class has the matching Foreign Key
			$to_field = NULL;
			$to_class_relations = $to_class::getRelations();
			foreach ($to_class_relations AS $maybe_field => $maybe_relation) {
				if ($maybe_relation instanceof ForeignKey && $maybe_relation->getClass() == $class) {
					$to_field = $maybe_relation->getField();
					break;
				}
			}

			if (is_null($to_field)) {
				throw new \Exception("Could not find matching \"{$class}\" field \"{$field}\" reference in \"{$to_class}\".");
			}

			return $to_class::objects()->filter("{$to_field}__exact", $this->id);
		}

		if ($relation instanceof ManyToManyRelation) {
			$to_class = $relation->getClass();
			$through = $relation->getThroughClass();

			// verify whether $through has the matching Foreign Key
			$to_field = NULL;
			$through_field = NULL;
			$through_relations = $through::getRelations();

			foreach ($through_relations AS $maybe_field => $maybe_relation) {
				if ($maybe_relation instanceof ForeignKey && $maybe_relation->getClass() == $class && is_null($through_field)) {
					$through_field = $maybe_relation->getField();
				}

				if ($maybe_relation instanceof ForeignKey && $maybe_relation->getClass() == $to_class && is_null($to_field)) {
					$to_field = $maybe_relation->getField();
				}
			}

			if (is_null($through_field)) {
				throw new \Exception("Could not find matching \"{$class}\" field \"{$field}\" reference in \"{$through}\".");
			}

			if (is_null($to_field)) {
				throw new \Exception("Could not find matching \"{$to_class}\" field reference in \"{$through}\".");
			}

			$target_matching_field = NULL;
			foreach ($to_class::getRelations() AS $to_field => $to_relation) {
				if ($to_relation instanceof ManyToManyRelation && $to_relation->getClass() == $class && $to_relation->getThroughClass() == $through) {
					$target_matching_field = $to_field;
				}
			}
			if (is_null($target_matching_field)) {
				throw new \Exception("Could not find matching \"{$to_class}\" field reference in \"{$to_class}\".");
			}

			$class_pk = $class::_PRIMARY_KEY_;
			return $to_class::objects()->filter("{$target_matching_field}__{$class_pk}__exact", $this->id);
		}

		if ($relation instanceof ForeignKey) {
            $to_class = $relation->getClass();
            $to_field = $relation->getField();
            return $to_class::objects()->get($this->$to_field);
		}

		// not an external field
		return parent::__get($field);
	}

    /**
     * Model level save() method. Compare to Object level, this method
     * handles converting the abstract fields to real fields.
     */
    public function save($force_insert = FALSE) {
		if (count($this->modified_fields)) {
            $modified_fields = array();
            $class = get_called_class();
			foreach ($this->modified_fields AS $field) {
                $relation = $class::getRelation($field);
                if ($relation instanceof ForeignKey) {
                    $real_field = $relation->getField();
                    $this->$real_field = $this->fields[$field];
                    unset($this->fields[$field]);
                    $modified_fields[] = $real_field;
                }
                else {
                    $modified_fields[] = $field;
                }
			}
            $this->modified_fields = $modified_fields;
		}

        parent::save($force_insert);
    }

	/**
	 * Returns a QueryManager object for creating queries.
	 *
	 * @return QueryManager
	 */
	public static function objects() {
		return new QueryManager(get_called_class());
	}

	/**
	 * Get all relations schema. Relations are defined by class constants.
	 * i.e.
	 * OneToManyField:  const blogs = 'Blog:'
	 * ManyToManyRelation: const groups = 'Group:UserGroup'
	 * ForeignKey:      const user = 'User'
	 */
	public static function getRelations() {
		static $relations = array();

		if (!empty($relations)) {
			return $relations;
		}

		$class = get_called_class();
		$refl = new \ReflectionClass($class);
		foreach ($refl->getConstants() AS $name => $value) {
			// skip all constants start with "_", i.e. _DATABASE_
			if (0 === strpos($name, '_')) {
				continue;
			}

			if (FALSE !== strpos($value, ':')) {
				$classes = explode(':', $value);
				if (!empty($classes[0]) && !empty($classes[1])) {
					// ManyToManyRelation, i.e. "to_class:through_class"
					$relations[$name] = new ManyToManyRelation($classes[0], $classes[1]);
				}
				else {
					// OneToManyField, i.e. "to_class:"
					$relations[$name] = new OneToManyRelation($classes[0]);
				}
			}
            else {
                $pattern = '/([a-zA-Z_\\\\]+)\(([a-zA-Z_]+)\)/';
                preg_match($pattern, $value, $matches);
                $to_class = $matches[1];
                $to_field = $matches[2];
                if (class_exists("{$to_class}")) {
				    $relations[$name] = new ForeignKey($to_class, $to_field);
                }
            }
		}

		return $relations;
	}

	/**
	 * Get relation by a particular name.
	 */
	public static function getRelation($name) {
		$class = get_called_class();
		$relations = $class::getRelations();
		if (isset($relations[$name])) {
			return $relations[$name];
		}
		return FALSE;
	}
}
