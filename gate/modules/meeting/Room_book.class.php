<?php
namespace Gate\Modules\Meeting;

use \Gate\Package\Meeting\MeetingRoom AS MeetingRoom;
use \Gate\Package\Meeting\MeetingBook AS MeetingBook;

class Room_book extends \Gate\Libs\Controller {

	protected $room_id;

	public function run() {

		if (!$this->_init()) {
			return FALSE;
		}

		$room_data = MeetingRoom::getInstance()->getDataById(array($this->room_id));

		if (!empty($room_data)) {
			$room_data = $room_data[0];
		}else{
			$this->view = array('code' => 400, 'message' => '数据错误！');
			return FALSE;
		}

		//$start_time = $end_time = date('Y-m-d H:i:s');
		//$book_data = MeetingBook::getInstance()->getDataById(array($this->room_id), $start_time, $end_time);

		$this->view = array('room_data' => $room_data, );
	}

	private function _init() {

		$this->room_id = isset($this->request->REQUEST['id']) ? (int)$this->request->REQUEST['id'] : 0;

		if (empty($this->room_id)) {
			$this->view = array('code' => 400, 'message' => '数据不能为空！');
			return FALSE;
		}else{
			return TRUE;
		}

	}

	private static function sort_list($data=array(), $key='')
	{
		$newdata = array();
		if(is_array($data)){
			foreach($data as $v){
				$newdata[$v[$key]] = $v;
			}
		}
		return $newdata;
	}

}
