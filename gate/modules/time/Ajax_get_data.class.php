<?php
namespace Gate\Modules\Time;

use \Gate\Package\Time\TimeManage;
use \Gate\Package\Meeting\MeetingBook;
use \Gate\Package\Meeting\MeetingRoom;
use \Gate\Package\Meeting\MeetingTime;
use \Gate\Package\Address\Staffinfo;

class Ajax_get_data extends \Gate\Libs\Controller {

	protected $view_switch = FALSE;
	protected $dowhat;
	protected $start_time;
	protected $end_time;

	public function run() {

		if (!$this->_init()) {
			return FALSE;
		}

		$result = TimeManage::getInstance()->getDataByUser($this->userId, $this->start_time, $this->end_time, 0, 200);
        //会议数据
        //$meeting_data = MeetingBook::getInstance()->getMeetingDataByUser($this->userId, $this->start_time, $this->end_time, 0, 200);
        $meeting_data = MeetingTime::getInstance()->getDataByUser($this->userId, $this->start_time, $this->end_time, 0, 200);
        $result = array_merge($result, $meeting_data);
        $result = $this->assembleData($result);

		if ($result) {
			$this->view = $result;
		}
		else {
			$this->view = array('code' => 400, 'message' => '获取数据失败！');
		}

	}

    private function assembleData($arr) {
        $new_result = array();
        foreach($arr as $key => $val) {
            $new_result[$key]['id'] = !empty($val['time_id']) ? $val['time_id'] : 0;
            $new_result[$key]['book_id'] = !empty($val['book_id']) ? $val['book_id'] : 0;
            $new_result[$key]['title'] = !empty($val['dowhat']) ? $val['dowhat'] : $val['meeting_topic'];
            $new_result[$key]['start'] = date('Y-m-d H:i', strtotime($val['start_time']));
            $new_result[$key]['end'] = date('Y-m-d H:i',strtotime($val['end_time']));
            $new_result[$key]['color'] = !empty($val['color']) ? $val['color'] : '';
            $new_result[$key]['others'] = !empty($val['others']) ? $val['others'] : '';
            $new_result[$key]['editable'] = true;
            $new_result[$key]['users'] = '';
            $new_result[$key]['user_id'] = $val['user_id'];
            if (!empty($val['room_id'])) {
                $room_info = MeetingRoom::getInstance()->getDataById($val['room_id']);
                $new_result[$key]['room_info'] = $room_info[0];
                $new_result[$key]['founder_user_id'] = $val['founder_user_id'];
                $new_result[$key]['editable'] = ($val['founder_user_id'] == $this->userId) ? true : false;
                if ($val['founder_user_id'] == $this->userId) {
                    $new_result[$key]['users'] = self::get_invite_users($val['book_id']);
                }
            }
            $new_result[$key]['display_title'] = '会议主题：' . (!empty($val['dowhat']) ? $val['dowhat'] : $val['meeting_topic']);
            $new_result[$key]['allDay'] = false;
        }
        return $new_result;
    }

	private function get_invite_users($book_id='')
	{
		if (empty($book_id)) {
			return FALSE;
		}
        //从meeting_book 取invite_users
        $book_info = MeetingBook::getInstance()->getDataByBook(array($book_id));
        $user_id = $book_info[0]['invite_users'];


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

	private function _init() {

		$this->start_time = isset($this->request->REQUEST['start']) ? $this->request->REQUEST['start'] : '';
		$this->end_time = isset($this->request->REQUEST['end']) ? $this->request->REQUEST['end'] : '';

		if (empty($this->start_time) || empty($this->end_time)) {
			$this->view = array('code' => 400, 'state' => 'error', 'message' => '数据不能为空！');
			return FALSE;
		}else{
			return TRUE;
		}

	}

}
