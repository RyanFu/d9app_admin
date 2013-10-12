<?php
namespace Gate\Libs\ORM;

abstract class DatabaseField {
	private $options;

	public function __construct($options = array()) {
		// set default values for options
		$defaults = array(
			'null' => FALSE,
			'blank' => FALSE,
			'db_column' => NULL,
			'db_index' => FALSE,
			'default' => NULL,
			'primary_key' => FALSE,
			'unique' => FALSE,
		);

		foreach ($defaults AS $opt_name => $default_value) {
			if (isset($options[$opt_name])) {
				$this->$opt_name = $opt_values[$opt_name];
			}
			else {
				$this->$opt_name = $default_value;
			}
		}
	}

	public function __set($name, $value) {
		$this->options[$name] = $value;
	}

	public function __get($name) {
		return $this->options[$name];
	}
}
