<?php
namespace Gate\Modules\Address;

use \Gate\Package\Address\Staffinfo;

class Ajax_staff_select extends \Gate\Libs\Controller {

	protected $view_switch = FALSE;
    protected $sid;
    public function run(){
        if(!$this->_init()){
			$this->view = array('code' => 401, 'message' => '数据获取失败');
        }
        $param['sid'] = $this->sid;
        $sid = $this->sid;
		$result = Staffinfo::getInstance()->GetStaffinfo($param);
		if (isset($result) && !empty($result[$sid])) {
            if(!empty($result[$sid]['others'])){
                $others = json_decode($result[$sid]['others'],true);
                if(isset($others['qq']) && !empty($others['qq'])){
                    $result[$sid]['qq'] = $others['qq'];
                }else{
                    $result[$sid]['qq'] = 'qq';
                }

            }
			$this->view = array('code' => 200, 'message' => $result[$sid]);
		}else{
			$this->view = array('code' => 400, 'message' => '数据修改失败！');
		}
	}

	private function _init() {
		$this->sid = isset($this->request->REQUEST['sid']) ? $this->request->REQUEST['sid'] : '';
		if (empty($this->sid)) {
			return FALSE;
		}else{
			return TRUE;
		}

	}
}
