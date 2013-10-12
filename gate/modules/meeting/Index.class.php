<?php
namespace Gate\Modules\Meeting;

use \Gate\Package\Meeting\MeetingRoom AS MeetingRoom;
use \Gate\Package\Meeting\MeetingBook AS MeetingBook;

class Index extends \Gate\Libs\Controller {

	protected $num;
	protected $start;
	protected $end;
	protected $search;

	public function run() {

		$this->_init();

		if (empty($this->num)) {
			$room_data = MeetingRoom::getInstance()->getAllData(0, 99);
		}else{
			$room_data = MeetingRoom::getInstance()->getDataByCapacity($this->num);
		}

		$roomIds = array();
		if (!empty($room_data)) {
			foreach ($room_data as $key => $value) {
				$value['booking'] = 0;
				$room_data[$key] = $value;
				$roomIds[] = $value['room_id'];
			}
			$room_data = self::sort_list($room_data, 'room_id');
		}

		$start_time = $end_time = date('Y-m-d H:i:s');
		$book_data = MeetingBook::getInstance()->getDataById($roomIds, $this->start, $this->end);

		if (!empty($book_data)) {
			foreach ($book_data as $key => $value) {
				$room_id = $value['room_id'];
				$room_data[$room_id]['booking'] = 1;
			}
		}

		$this->view = array('room_data' => $room_data, 'search' => $this->search, );
	}


	private function _init() {

		$this->num = isset($this->request->REQUEST['num']) ? (int)$this->request->REQUEST['num'] : 0;
		$this->start = isset($this->request->REQUEST['start']) ? urldecode($this->request->REQUEST['start']) : '';
		$this->end = isset($this->request->REQUEST['end']) ? urldecode($this->request->REQUEST['end']) : '';

		$this->search = array(
			'num' 	=> $this->num,
			'start' => $this->start,
			'end' 	=> $this->end,
		);
		return TRUE;

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
