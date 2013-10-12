<?php
namespace Gate\Libs\ORM;

class ForeignKey extends Relation {
    private $to_field;

    public function __construct($class, $field) {
        parent::__construct($class);
        $this->to_field = $field;
    }

    public function getField() {
        return $this->to_field;
    }

	public function getObject($id) {
		return new $this->to_class((int) $id);
	}

	public function getDatabase() {
		$to_class = $this->to_class;
		return $to_class::_DATABASE_;
	}

	public function getTable() {
		$to_class = $this->to_class;
		return $to_class::_TABLE_;
	}

	public function getPrimaryKey() {
		$to_class = $this->to_class;
		return $to_class::_PRIMARY_KEY_;
	}
}
