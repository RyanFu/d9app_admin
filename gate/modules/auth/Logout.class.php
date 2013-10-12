<?php
namespace Gate\Modules\Auth;

class Logout extends \Gate\Libs\Controller {

	public function run() {
        \Gate\Libs\Session::Singleton()->destory();
        header('Location: /auth/logon');
        exit();
	}

}
