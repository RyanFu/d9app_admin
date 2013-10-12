<?php
namespace Gate\Modules\Auth;
use \Gate\Package\User\UserModel;

class Login extends \Gate\Libs\Controller {

	protected $username = NULL;
	protected $password = NULL;

	public function run() {
        $status = $this->init();
        if (!$status) {
            return FALSE;
        }
        $this->load();

	}

	private function init() {
		$this->username = isset($this->request->POST['nickname']) ? $this->request->POST['nickname'] : NULL;
		$this->password = isset($this->request->POST['password']) ? $this->request->POST['password'] : NULL;

		if (empty($this->username) || empty($this->password)) {
            $this->setError(400, 40001, 'empty paramters');
            return FALSE;
        }

        return TRUE;
	}

    private function load() {
        $user =  UserModel::getConn()->from('user')->field('user_id,user_name,password,salt')->where('user_name=:user_name', array('user_name'=>$this->username))->limit(1)->fetch();
        if (empty($user))
            $this->setError(400, 40002, 'nickname not exist');

        $verify = $user->password == sha1($user->salt . sha1($this->password));

        if ($verify) {
            //login
            \Gate\Libs\Session::Singleton()->marked($user);
            header('Location: /auth/home');
            exit();
        }
        else
            $this->setError(400, 40003, 'wrong password');

    }

}
