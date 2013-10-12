<?php
namespace Gate\Modules\Meeting;

use \Gate\Package\Meeting\MeetingRoom AS MeetingRoom;

class Room_manage extends \Gate\Libs\Controller {

	protected $room_id;

	public function run() {

		$this->_init();

		if (empty($this->room_id)) {
			$room_data = MeetingRoom::getInstance()->getAllData(0, 99);
		}else{
			$room_data = MeetingRoom::getInstance()->getDataById(array($this->room_id));
		}

		$this->view = array('room_data' => $room_data);
	}


	private function _init() {

		$this->room_id = isset($this->request->REQUEST['room_id']) ? (int)$this->request->REQUEST['room_id'] : 0;
		return TRUE;

	}

}
