<?php
namespace Gate\Modules\Address;

use \Gate\Package\Address\Staffinfo;

class Ajax_staff_update extends \Gate\Libs\Controller {

	protected $view_switch = FALSE;
    protected $sid;
    public function run(){
        if(!$this->_init()){
			$this->view = array('code' => 401, 'message' => '数据修改失败！');
        }
        $basearray = array('name_c','mail','departid','extension','phone','position','status','qq','role'); 
        $new_data = array();
        foreach($basearray as $v){
            if(isset($this->request->REQUEST["$v"]) && !empty($this->request->REQUEST["$v"])){
                $new_data["$v"] = $this->request->REQUEST["$v"];
            }
        }
        $new_data['sid'] = $this->sid;
		$result = Staffinfo::getInstance()->UpdateStaff($new_data);

		if ($result) {
			$this->view = array('code' => 200, 'message' => '数据修改成功！');
		}else{
			$this->view = array('code' => 400, 'message' => '数据修改失败！');
		}

	}

	private function _init() {
		$this->sid = isset($this->request->REQUEST['sid']) ? $this->request->REQUEST['sid'] : 0;
		if (empty($this->sid)) {
			return FALSE;
		}else{
			return TRUE;
		}

	}
}
