<?php
namespace Gate\Libs\ORM;

class CharField extends Field {

	public function __construct($options = array()) {

		// CharField has one extra required argument: max_length
		if (!isset($options['max_length'])) {
			Throw new Exception("Missing required argument \"max_length\" of CharField.");
		}

		parent::__construct($options);
	}

}
