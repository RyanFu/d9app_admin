<?php
namespace Gate\Modules\User;

class Test extends \Gate\Libs\Controller {

	protected $username = NULL;
	protected $password = NULL;

	public function run() {
        $this->init();
        die();
	}

    private function init() {
        $users = \Gate\Package\User\UserModel::objects()
            ->filter();
        
        foreach ($users as $user) {
            echo $user->mail . "</br>";
            echo $user->id . "</br>";
        }

    }

}
