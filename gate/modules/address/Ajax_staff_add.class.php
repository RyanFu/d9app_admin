<?php
namespace Gate\Modules\Address;

use \Gate\Package\Address\Staffinfo;

class Ajax_staff_add extends \Gate\Libs\Controller {

    protected $view_switch = FALSE;
    protected $name_c;
    protected $mail;
    protected $departid;
    protected $extension;
    protected $phone;
    protected $position;
    protected $qq;
    protected $status=1;
    public function run(){
        $this->_init();
		$new_data = array(
			'name_c'=> $this->name_c,
			'mail' => $this->mail,
            'departid'=>$this->departid,
            'extension'=>$this->extension,
            'phone'=>$this->phone,
            'position'=>$this->position,
            'status'=>$this->status,
            'others'=>'',
            'qq'=>$this->qq,
		);
		$result = Staffinfo::getInstance()->InsertStaff($new_data);

		if ($result) {
			$this->view = array('code' => 200, 'message' => '添加数据成功！');
		}else{
			$this->view = array('code' => 400, 'message' => '添加数据失败！');
		}

	}

	private function _init() {

		$this->name_c = isset($this->request->REQUEST['name_c']) ? $this->request->REQUEST['name_c'] : '';
		$this->mail = isset($this->request->REQUEST['mail']) ? $this->request->REQUEST['mail'] : '';
		$this->departid = isset($this->request->REQUEST['departid']) ? $this->request->REQUEST['departid'] : 0;
		$this->extension = isset($this->request->REQUEST['extension']) ? $this->request->REQUEST['extension'] : 0;
		$this->phone = isset($this->request->REQUEST['phone']) ? $this->request->REQUEST['phone'] : '';
		$this->position = isset($this->request->REQUEST['position']) ? $this->request->REQUEST['position'] :'';
		$this->qq = isset($this->request->REQUEST['qq']) ? $this->request->REQUEST['qq'] : '';
		$this->status = isset($this->request->REQUEST['status']) ? $this->request->REQUEST['status'] : 1;

		if (empty($this->name_c) || empty($this->mail)) {
			$this->view = array('code' => 400, 'state' => 'error', 'message' => '数据不能为空！');
			return FALSE;
		}else{
			return TRUE;
		}

	}
}
