<?php
namespace Gate\Modules\Bad;

class Badrequest extends \Gate\Libs\Controller {

	public function run() {
		$this->setError(400, 10001, "Bad Request");
	}

}
