<?php
namespace Gate\Libs\ORM;

abstract class Object {

	const _DATABASE_ = '';
	const _TABLE_ = '';
	const _PRIMARY_KEY_ = '';

	protected $id;
	protected $fields;
	protected $loaded;
	protected $modified_fields;

	public static function getConn() {
		static $conn = NULL;

		if (is_null($conn)) {
			$class = get_called_class();
			$conn = \Phplib\DB\Database::getConn($class::_DATABASE_);
		}

		return $conn;
	}

	/**
	 * Public constructor.
	 * Supported 3 types of argument:
	 * 1) int init an object with the primary key (id)
	 * 2) array init an object with pre-defined values
	 * 3) NULL init an empty object
	 */
	public function __construct($id = NULL) {
		$this->id = is_null($id) ? 0 : $id;
		$this->loaded = FALSE;
		$this->fields = array();
		$this->modified_fields = array();
	}

	public function __get($field) {
		return $this->getField($field);
	}

	public function __set($field, $value) {
		$this->setField($field, $value);
	}

	public function getAllFields() {
		$this->loaded || $this->load();
		return $this->fields;
	}

	protected function getField($field) {
		$class = get_called_class();

		// "_PRIMARY_KEY_" or "id" are reserved
		if ($field == $class::_PRIMARY_KEY_ || $field == 'id') {
			return $this->id;
		}

		$this->loaded || $this->load();

		if (isset($this->fields[$field]) || $this->fields[$field] === NULL) {
			return $this->fields[$field];
		}

		throw new \Exception("Property \"{$field}\" does not exist in \"{$class}\".");
	}

	protected function setField($field, $value, $modify = TRUE) {
		$class = get_called_class();
		if ($field == $class::_PRIMARY_KEY_) {
			throw new \Exception("Cannot change ID field.");
		}

		$this->fields[$field] = $value;
		if ($modify && !in_array($field, $this->modified_fields)) {
			$this->modified_fields[] = $field;
		}
	}

	/**
	 * Load the object from database;
	 */
	public function load($from_master = FALSE) {
		if (!$this->id) {
			throw new \Exception("Cannot load object without an ID.");
		}

		if ($this->loaded) return;

		$class = get_called_class();
		foreach (Database::objectLoad($this) AS $field => $value) {
			// only load fields that haven't been modified yet
			if (!in_array($field, $this->modified_fields) && $field != $class::_PRIMARY_KEY_) {
				$this->$field = $value;
			}
		}

		$this->loaded = TRUE;
	}

	/**
	 * Save the object to database.
	 * Depends on whether primary key is set this method may issue an INSERT
	 * or an UPDATE.
	 */
	public function save($force_insert = FALSE) {
		if (count($this->modified_fields)) {
			$fields = array();
			foreach ($this->modified_fields AS $field) {
				$value = $this->fields[$field];
				if ($value instanceof Object) {
					// value is an object, also need to save any changes to
					// that object
					$value->save();
					$value = $value->id;
					$this->fields[$field] = $value;
				}
				$fields[$field] = $value;
			}

			// in force_insert mode, primary key is also passed down to be
			// stored
			if ($force_insert && isset($this->id)) {
				$class = get_called_class();
				$fields[$class::_PRIMARY_KEY_] = $this->id;
			}

			$this->id = Database::objectSave($this, $fields, $force_insert);
			$this->modified_fields = array();
		}
		return $this;
	}

	/**
	 * Replace the object to database.
	 */

	public function forcesave() {
		if (count($this->modified_fields)) {
			$fields = array();
			foreach ($this->modified_fields AS $field) {
				$value = $this->fields[$field];
				if ($value instanceof Object) {
					// value is an object, also need to save any changes to
					// that object
					$value->save();
					$value = $value->id;
					$this->fields[$field] = $value;
				}
				$fields[$field] = $value;
			}

			// in force_insert mode, primary key is also passed down to be
			// stored
			if ($force_insert && isset($this->id)) {
				$class = get_called_class();
				$fields[$class::_PRIMARY_KEY_] = $this->id;
			}

			$this->id = Database::objectReplace($this, $fields);
			$this->modified_fields = array();
		}
		return $this;
	}
	/**
	 * Delete the object from database.
	 */
	public function delete() {
		if (!$this->id) {
			throw new \Exception("Cannot delete object without an ID.");
		}

		Database::objectDelete($obj);
		$class = get_called_class();
		return new $class();
	}
}
