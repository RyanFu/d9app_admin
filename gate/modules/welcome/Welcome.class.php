<?php
namespace Gate\Modules\Welcome;

class Welcome extends \Gate\Libs\Controller {

	public function run() {
        //var_dump($this->request->user);die();
		//$this->setError(400, 10001, "Bad Request");
        $showString = "D9应用管理系统";
        $this->view = array('showString' => $showString);
	}

}
