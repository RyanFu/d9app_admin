<?php
namespace Gate\Modules\Meeting;

use \Gate\Package\Meeting\MeetingRoom AS MeetingRoom;
use \Gate\Package\Meeting\MeetingBook AS MeetingBook;

class My_book extends \Gate\Libs\Controller {

	protected $room_id;

	public function run() {

		if (!$this->_init()) {
			return FALSE;
		}

		$room_data = MeetingRoom::getInstance()->getAllData(0, 99);

		if (empty($room_data)) {
			$this->view = array('code' => 400, 'message' => '数据错误！');
			return FALSE;
		}

		$this->view = array('room_data' => $room_data, );
	}

	private function _init() {

		if (empty($this->userId)) {
			$this->view = array('code' => 400, 'message' => '请登录后操作！');
			return FALSE;
		}else{
			return TRUE;
		}

	}

}
