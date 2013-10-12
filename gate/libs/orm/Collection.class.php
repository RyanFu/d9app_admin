<?php
namespace Gate\Libs\ORM;

class Collection implements \Countable, \Iterator, \ArrayAccess {

	private $query = NULL;
	private $objects = array();
	private $loaded = FALSE;
	private $pos = 0;

	public function __construct(QuerySet $query) {
		$this->query = $query;
	}

	public function load() {
		if (0 == count($this->query->getSelect())) {
			// retrieve all data from database
			$data = Database::queryGetItems($this->query);
			$class = $this->query->getModel();
			foreach ($data AS $row) {
				$this->objects[] = new $class($row);
			}
		}
		else {
			// retrieve particular column from database
			$data = Database::queryGetCols($this->query, $this->query->getSelect());
			$this->objects = $data;
		}

		$this->loaded = TRUE;
	}

	public function getValues() {
		!$this->loaded && $this->load();
		return $this->objects;
	}

	/**
	 * Returns an ORM object (Model) using the class
	 * that this Collection represents
	 * 
	 * @param integer $key
	 * @return Model
	 */
	public function getItem($key) {
		!$this->loaded && $this->load();
		return $this->objects[$key];
	}
	
	/**
	 * COUNTABLE
	 */

	/**
	 * Pass $load as TRUE to load objects. Otherwise this method only issues
	 * a COUNT(1) query if this collection is not loaded.
	 */
	public function count($load = FALSE) {
		if (!$this->loaded && $load) {
			$this->load();
		}

		if ($this->loaded) {
			return count($this->objects);
		}

		return Database::queryGetLength($this->query);
	}

	/**
	 * ITERATOR
	 */

	public function rewind() {
		$this->pos = 0;
	}

	public function valid() {
		return $this->pos < $this->count(TRUE);
	}

	public function key() {
		return $this->pos;
	}

	public function current() {
		return $this->getItem($this->pos);
	}

	public function next() {
		$this->pos++;
	}

	/**
	 * ARRAYACCESS
	 */

	public function offsetSet($offset, $value) {
		throw new Exception("Cannot add to Collection.");
	}

	public function offsetExists($offset) {
		return ($offset < count($this) && $offset >= 0 ? true : false);
	}

	public function offsetUnset($offset) {
		$this->getItem($offset)->delete();
		unset($this->keys[$offset]);
	}

	public function offsetGet($offset) {
		return $this->getItem($offset);
	}
	
}
