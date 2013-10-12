<?php
namespace Gate\Libs\ORM;

abstract class Relation extends Field {

	protected $class = '';

	public function __construct($class) {

		if (!class_exists($class)) {
			throw new \Exception("Could not create table relation: class \"{$class}\" does not exist.");
		}

		$this->class = $class;
	}

	public function getClass() {
		return $this->class;
	}
}
