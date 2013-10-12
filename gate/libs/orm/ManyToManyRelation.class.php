<?php
namespace Gate\Libs\ORM;

class ManyToManyRelation extends Relation {

	private $through;

	public function __construct($to_class, $through) {
		parent::__construct($to_class);

		if (!class_exists($through)) {
			throw new Exception("Could not create ManyToManyField relationship: class {$through} does not exist.");
		}

		$this->through = $through;
	}

	public function getThroughClass() {
		return $this->through;
	}
}
