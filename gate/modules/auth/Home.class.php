<?php
namespace Gate\Modules\Auth;

use \Gate\Package\Meeting\MeetingBook AS MeetingBook;
use \Gate\Package\Meeting\MeetingTime;
use \Gate\Package\Time\TimeManage;

class Home extends \Gate\Libs\Controller {

	public function run() {
		if (!$this->_init()) {
			return FALSE;
		}

        $start = date('Y-m-d 00:00:00', time());
        $end = date('Y-m-d H:i:s', time()+24*3600);
		$meeting_data = MeetingTime::getInstance()->getDataByUser($this->userId, $start, $end, 0, 5);
		$time_data = TimeManage::getInstance()->getDataByUser($this->userId, $start, $end, 0, 5);
		$data = array();
		if ($meeting_data) {
			foreach ($meeting_data as $key => $value) {
				$tmp = array();
				$tmp['id'] = $value['book_id'];
				$tmp['user_id'] = $value['user_id'];
				$tmp['title'] = $value['meeting_topic'];
				$tmp['start'] = $value['start_time'];
				$tmp['end'] = $value['end_time'];
				$tmp['users'] = isset($value['invite_users']) ? $value['invite_users'] : 0;
				$tmp['others'] = isset($value['others']) ? $value['others'] : '';
				$tmp['allDay'] = FALSE;

				if (strtotime($value['end_time']) < time()) {
					$tmp['color'] = '#101010';
					$tmp['editable'] = FALSE;
				}else{
					$tmp['color'] = '#428bca';
					$tmp['editable'] = FALSE;
				}
				$data[] = $tmp;
			}
		}
        if(!empty($meeting_data)) {
            $time_data = array_merge($time_data, $meeting_data);
        }
        $start = array();
        foreach($time_data as $key => $val) {
            $start[$key] = strtotime($val['start_time']);
        }
        array_multisort($start, SORT_ASC, $time_data);
		$this->view = array(
            'time_data' => $time_data
        );

	}

	private function _init() {
			return TRUE;
	}

}
