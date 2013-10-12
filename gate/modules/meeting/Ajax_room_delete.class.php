<?php
namespace Gate\Modules\Meeting;

use \Gate\Package\Meeting\MeetingRoom AS MeetingRoom;

class Ajax_room_delete extends \Gate\Libs\Controller {

	protected $view_switch = FALSE;
	protected $room_id;

	public function run() {

		if (!$this->_init()) {
			return FALSE;
		}

		$delete_data = array(
			'room_id'		=> $this->room_id,
		);
		$result = MeetingRoom::getInstance()->deleteData($delete_data);

		if ($result) {
			$this->view = array('code' => 200, 'message' => '删除数据成功！');
		}else{
			$this->view = array('code' => 400, 'message' => '删除数据失败！');
		}

	}

	private function _init() {

		$this->room_id = isset($this->request->REQUEST['inputId']) ? (int)$this->request->REQUEST['inputId'] : 0;

		if (empty($this->room_id)) {
			$this->view = array('code' => 400, 'state' => 'error', 'message' => '数据不能为空！');
			return FALSE;
		}else{
			return TRUE;
		}

	}

}
