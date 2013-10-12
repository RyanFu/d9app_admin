<?php
namespace Gate\Modules\Address;

class Index extends \Gate\Libs\Controller {

	protected $view_switch = FALSE;

	public function run() {

		header("Location:/address/addresslist");

	}

}
