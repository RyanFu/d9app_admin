<?php
namespace Gate\Modules\Meeting;

use \Gate\Package\Meeting\MeetingRoom AS MeetingRoom;
use \Gate\Package\Meeting\MeetingBook AS MeetingBook;
use \Gate\Package\Address\Staffinfo;

class Ajax_book_get extends \Gate\Libs\Controller {

	protected $view_switch = FALSE;
	protected $room_id;
	protected $start;
	protected $end;

	public function run() {

		if (!$this->_init()) {
			return FALSE;
		}
/*
		if (!empty($this->start)) {
			$this->start = date('Y-m-d H:i:s', $this->start);
		}
		if (!empty($this->end)) {
			$this->end = date('Y-m-d H:i:s', $this->end);
		}
*/
		$result = MeetingBook::getInstance()->getDataById(array($this->room_id), $this->start, $this->end);
		$data = array();
		if ($result) {
			$userTmp = array();
			foreach ($result as $key => $value) {
				$userTmp[] = $value['user_id'];
			}
			$userTmp = array_unique($userTmp);
			$userIdString = implode(',', $userTmp);
			$userData = Staffinfo::getInstance()->GetStaffinfo(array('sid'=>$userIdString), array('sid', 'name_c', 'name_e'));
			//$userData = self::sort_list($userData, 'sid');

			foreach ($result as $key => $value) {
				$tmp = array();
				$tmp['id'] = $value['book_id'];
				$tmp['user_id'] = $value['user_id'];
				$tmp['title'] = $value['meeting_topic'];
				$tmp['start'] = $value['start_time'];
				$tmp['end'] = $value['end_time'];
				//$tmp['users'] = $value['invite_users'];
				$tmp['others'] = $value['others'];
				$tmp['allDay'] = FALSE;

				if (strtotime($value['end_time']) < time()) {
					$tmp['color'] = '#5cb85c';
				}else{
					$tmp['color'] = '#d9534f';
				}

				if($value['user_id'] == $this->userId){
					$tmp['editable'] = TRUE;
					$tmp['color'] = '#357ebd';

					$tmp['users'] = self::get_invite_users($value['invite_users']);
				}else{
					$tmp['editable'] = FALSE;
					$tmp['title'] = $userData[$value['user_id']]['name_c'].'预定';
					$tmp['users'] = array();
				}

				$data[] = $tmp;
			}
			$this->view = $data;
		}else{
			$this->view = array('code' => 400, 'message' => '数据错误！');
		}

	}

	private function _init() {

		$this->room_id = isset($this->request->REQUEST['id']) ? (int)$this->request->REQUEST['id'] : 0;
		//$this->start = isset($this->request->REQUEST['start']) ? (int)$this->request->REQUEST['start'] : 0;
		//$this->end = isset($this->request->REQUEST['end']) ? (int)$this->request->REQUEST['end'] : 0;
		$this->start = isset($this->request->REQUEST['start']) ? urldecode($this->request->REQUEST['start']) : 0;
		$this->end = isset($this->request->REQUEST['end']) ? urldecode($this->request->REQUEST['end']) : 0;

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

	private function get_invite_users($user_id='')
	{
		if (empty($user_id)) {
			return FALSE;
		}

		$userData = Staffinfo::getInstance()->GetStaffinfo(array('sid'=>$user_id), array('sid', 'name_c', 'name_e'));
		//去掉自己
		if (isset($userData[$this->userId])) {
			unset($userData[$this->userId]);
		}
		$newdata = array();
		if (!empty($userData)) {
			foreach ($userData as $key => $value) {
				$tmp = array();
				$tmp['id'] = intval($value['sid']);
				$tmp['name'] = $value['name_c'];
				$newdata[] = $tmp;
			}
		}
		return $newdata;
	}

}
