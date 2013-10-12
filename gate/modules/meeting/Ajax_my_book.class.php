<?php
namespace Gate\Modules\Meeting;

use \Gate\Package\Meeting\MeetingRoom AS MeetingRoom;
use \Gate\Package\Meeting\MeetingBook AS MeetingBook;
use \Gate\Package\Meeting\MeetingTime;

class Ajax_my_book extends \Gate\Libs\Controller {

	protected $view_switch = FALSE;
	protected $start;
	protected $end;

	public function run() {

		if (!$this->_init()) {
			return FALSE;
		}

		//$result = MeetingBook::getInstance()->getDataByUser(array($this->userId), $this->start, $this->end);
		$result = MeetingTime::getInstance()->getDataByUser(array($this->userId), $this->start, $this->end);

		$data = array();
		if ($result) {
			foreach ($result as $key => $value) {
				$tmp = array();
				$tmp['id'] = $value['book_id'];
				$tmp['user_id'] = $value['user_id'];
				$tmp['title'] = $value['meeting_topic'];
				$tmp['start'] = $value['start_time'];
				$tmp['end'] = $value['end_time'];
				//$tmp['users'] = $value['invite_users'];
				//$tmp['others'] = $value['others'];
				$tmp['allDay'] = FALSE;

				if (strtotime($value['end_time']) < time()) {
					$tmp['color'] = '#5cb85c';
					$tmp['editable'] = FALSE;
				}else{
					$tmp['color'] = '#d9534f';
					$tmp['editable'] = FALSE;
				}
				$data[] = $tmp;
			}
			$this->view = $data;
		}else{
			$this->view = array('code' => 400, 'message' => '数据错误！');
		}

	}

	private function _init() {

		//$this->start = isset($this->request->REQUEST['start']) ? (int)$this->request->REQUEST['start'] : 0;
		//$this->end = isset($this->request->REQUEST['end']) ? (int)$this->request->REQUEST['end'] : 0;
		$this->start = isset($this->request->REQUEST['start']) ? urldecode($this->request->REQUEST['start']) : 0;
		$this->end = isset($this->request->REQUEST['end']) ? urldecode($this->request->REQUEST['end']) : 0;

		if (empty($this->userId)) {
			$this->view = array('code' => 400, 'message' => '请登录后操作！');
			return FALSE;
		}else{
			return TRUE;
		}

	}

}
