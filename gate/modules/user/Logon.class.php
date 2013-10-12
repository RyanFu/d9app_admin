<?php
namespace Gate\Modules\User;

class Logon extends \Gate\Libs\Controller {

	protected $username = NULL;
	protected $password = NULL;

	public function run() {
        $this->init();
        $this->load();
	}

	private function init() {
		$this->username = isset($this->request->POST['nickname']) ? $this->request->POST['nickname'] : NULL;
		$this->password = isset($this->request->POST['password']) ? $this->request->POST['password'] : NULL;

		if (empty($this->username) || empty($this->password))
            $this->setError(400, 40001, 'empty paramters');

        return TRUE;
	}

    private function load() {
        $user = \Gate\Package\User\UserModel::objects()
                ->get('login__exact', $this->username);

        if (empty($user))
            $this->setError(400, 40002, 'nickname not exist');

        $verify = $user->hashed_password == sha1($user->salt . sha1($this->password));

        if ($verify) {
            //login
            \Gate\Libs\Session::Singleton()->marked($user);
        }
        else
            $this->setError(400, 40003, 'wrong password');

    }

}
