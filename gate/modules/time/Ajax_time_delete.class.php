<?php
namespace Gate\Modules\Time;

use \Gate\Package\Time\TimeManage;

class Ajax_time_delete extends \Gate\Libs\Controller {

	protected $view_switch = FALSE;
	protected $time_id;

	public function run() {

		if (!$this->_init()) {
			return FALSE;
		}

		$new_data = array(
			'time_id'	=> $this->time_id,
		);
		$result = TimeManage::getInstance()->updateStatusByTimeId($new_data);

		if ($result) {
			$this->view = array('code' => 200, 'message' => '删除数据成功！');
		}
		else {
			$this->view = array('code' => 400, 'message' => '删除数据失败！');
		}

	}

	private function _init() {

		$this->time_id = isset($this->request->REQUEST['id']) ? $this->request->REQUEST['id'] : 0;

		if (empty($this->time_id)) {
			$this->view = array('code' => 400, 'state' => 'error', 'message' => 'id不能为空！');
			return FALSE;
		}else{
			return TRUE;
		}
	}

}
