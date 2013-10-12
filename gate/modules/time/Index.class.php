<?php
namespace Gate\Modules\Time;

class Index extends \Gate\Libs\Controller {

	protected $view_switch = FALSE;

	public function run() {

		header("Location:/time/time_manage");

	}

}
