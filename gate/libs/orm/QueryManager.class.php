<?php
namespace Gate\Libs\ORM;

class QueryManager {

	private $model;
	private $query;

	public function __construct($model) {
		$this->model = $model;
	}

	public function all() {
		return $this->getQuerySet();
	}

	public function count() {
		return $this->getQuerySet()->count();
	}

	public function get() {
		// when calling QuerySet::filter(), make sure all arguments are
		// passed just like when they passed-in
		return call_user_func_array(array($this->getQuerySet(), 'get'), func_get_args());
	}

	public function create($args) {
		return $this->getQuerySet()->create($args);
	}

	public function forcesave($args) {
		return $this->getQuerySet()->forcesave($args);
	}

	public function filter() {
		// when calling QuerySet::filter(), make sure all arguments are
		// passed just like when they passed-in
		return call_user_func_array(array($this->getQuerySet(), 'filter'), func_get_args());
	}

	public function exclude() {

	}

	private function getQuerySet() {
		if (!$this->query) {
			$this->query = new QuerySet($this->model);
		}
		return $this->query;
	}
}
