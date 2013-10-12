<?php
namespace Gate\Modules\Meeting;

use \Gate\Package\Meeting\MeetingRoom AS MeetingRoom;

class Ajax_room_get extends \Gate\Libs\Controller {

	protected $view_switch = FALSE;
	protected $room_id;

	public function run() {

		if (!$this->_init()) {
			return FALSE;
		}

		$result = MeetingRoom::getInstance()->getDataById(array($this->room_id));

		if ($result) {
			$this->view = array('code' => 200, 'data' => $result[0]);
		}else{
			$this->view = array('code' => 400, 'message' => '数据错误！');
		}

	}

	private function _init() {

		$this->room_id = isset($this->request->REQUEST['inputId']) ? (int)$this->request->REQUEST['inputId'] : 0;

		if (empty($this->room_id)) {
			$this->view = array('code' => 400, 'message' => '数据不能为空！');
			return FALSE;
		}else{
			return TRUE;
		}

	}

}
