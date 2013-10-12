<?php
namespace Gate\Modules\Meeting;

use \Gate\Package\Meeting\MeetingRoom AS MeetingRoom;

class Ajax_room_add extends \Gate\Libs\Controller {

	protected $view_switch = FALSE;
	protected $room_name;
	protected $room_position;
	protected $room_capacity;

	public function run() {

		if (!$this->_init()) {
			return FALSE;
		}

		$new_data = array(
			'room_name'		=> $this->room_name,
			'room_position' => $this->room_position,
			'room_capacity' => $this->room_capacity,
		);
		$result = MeetingRoom::getInstance()->addData($new_data);

		if ($result) {
			$this->view = array('code' => 200, 'message' => '添加数据成功！');
		}else{
			$this->view = array('code' => 400, 'message' => '添加数据失败！');
		}

	}

	private function _init() {

		$this->room_name = isset($this->request->REQUEST['inputName']) ? $this->request->REQUEST['inputName'] : '';
		$this->room_position = isset($this->request->REQUEST['inputPosition']) ? $this->request->REQUEST['inputPosition'] : '';
		$this->room_capacity = isset($this->request->REQUEST['inputCapacity']) ? (int)$this->request->REQUEST['inputCapacity'] : 0;

		if (empty($this->room_name) || empty($this->room_capacity)) {
			$this->view = array('code' => 400, 'state' => 'error', 'message' => '数据不能为空！');
			return FALSE;
		}else{
			return TRUE;
		}

	}

}
