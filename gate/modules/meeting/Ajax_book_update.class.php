<?php
namespace Gate\Modules\Meeting;

use \Gate\Package\Meeting\MeetingRoom;
use \Gate\Package\Meeting\MeetingBook;
use \Gate\Package\Meeting\MeetingBookuser;
use \Gate\Package\Meeting\MeetingTime;
use \Gate\Package\Address\Staffinfo;
use \Gate\Package\Time\TimeManage;
use \Gate\Package\Mail\MailQueue;

class Ajax_book_update extends \Gate\Libs\Controller {

	protected $view_switch = FALSE;
	protected $book_id;
	protected $room_id;
	protected $room_info;
	protected $meeting_topic;
	protected $invite_users;
	protected $userArray;
	protected $userNum;
	protected $start_time;
	protected $end_time;
	protected $others;
	protected $event; //是否拖动事件

	public function run() {

		if (!$this->_init()) {
			return FALSE;
		}

		//检测会员会议时间
		$checkMeeting = MeetingTime::getInstance()->getDataByUser($this->userArray, $this->start_time, $this->end_time);
		$checkTime = TimeManage::getInstance()->getDataByUser($this->userArray, $this->start_time, $this->end_time);

		$checkResult = array_merge($checkMeeting, $checkTime);
		$checkResult = self::sort_list($checkResult, 'book_id');
		if (isset($checkResult[$this->book_id])) {
			unset($checkResult[$this->book_id]);
		}

		//如果会员没有时间
		if (!empty($checkResult)) {
			$userTmp = array();
			foreach ($checkResult as $key => $value) {
				$userTmp[] = $value['user_id'];
			}
			$userTmp = array_unique($userTmp);
			$userCount = count($userTmp);
			$userIdString = implode(',', $userTmp);
			$userData = Staffinfo::getInstance()->GetStaffinfo(array('sid'=>$userIdString), array('sid', 'name_c', 'name_e'));
			$userName = array();
			if (!empty($userData)) {
				foreach ($userData as $key => $value) {
					$userName[] = empty($value['name_c'])?$value['name_e']:$value['name_c'];
				}
			}
			$userNameString = implode(',', $userName);
			$this->view = array('code' => 400, 'message' => '会议时间与'.$userNameString.$userCount.'人的时间冲突，请修改！');
			return FALSE;
		}

		//更新前的数据
		$bookData = MeetingBookUser::getInstance()->getDataById(array($this->book_id));
		$uids_before = array();
		if (!empty($bookData)) {
			foreach ($bookData as $key => $value) {
				$uids_before[] = $value['user_id'];
			}
		}
		$uids_delete = array_diff($uids_before, $this->userArray);
		$uids_add = array_diff($this->userArray, $uids_before);
		//var_dump($uids_before, $this->userArray, $uids_delete, $uids_add);

		$update_data = array(
			'book_id'		=> $this->book_id,
			'room_id'		=> $this->room_id,
			'user_id'		=> $this->userId,
			'meeting_topic' => $this->meeting_topic,
			'invite_users' 	=> $this->invite_users,
			'start_time' 	=> $this->start_time,
			'end_time' 		=> $this->end_time,
			'others' 		=> $this->others,
		);
		$result = MeetingBook::getInstance()->updateData($update_data);

		if ($result) {

			//邮件队列
			$updateQueue = array(
				'book_id' => $this->book_id,
				'room_id' => $this->room_id,
				'user_id' => $this->userId,
				'start_time' => $this->start_time,
			);
			MailQueue::getInstance()->updateBookData($updateQueue);

			$this->view = array('code' => 200, 'message' => '更新数据成功！');
		}else{
			$this->view = array('code' => 400, 'message' => '没有数据更新！');
		}

	}

	private function _init() {

		$this->room_id = isset($this->request->REQUEST['room_id']) ? (int)$this->request->REQUEST['room_id'] : 0;
		$this->book_id = isset($this->request->REQUEST['book_id']) ? (int)$this->request->REQUEST['book_id'] : 0;
		$this->meeting_topic = isset($this->request->REQUEST['title']) ? $this->request->REQUEST['title'] : '';
		$this->invite_users = isset($this->request->REQUEST['inputUser']) ? $this->request->REQUEST['inputUser'] : '';
		$this->start_time = isset($this->request->REQUEST['start']) ? $this->request->REQUEST['start'] : 0;
		$this->end_time = isset($this->request->REQUEST['end']) ? $this->request->REQUEST['end'] : 0;
		$this->others = isset($this->request->REQUEST['inputMemo']) ? $this->request->REQUEST['inputMemo'] : '';
		$this->event = isset($this->request->REQUEST['event']) ? (bool)$this->request->REQUEST['event'] : FALSE;

		if (empty($this->userId)) {
			$this->view = array('code' => 400, 'message' => '请登录后操作！');
			return FALSE;
		}elseif (empty($this->room_id) || empty($this->book_id) || empty($this->start_time) || empty($this->end_time)) {
			$this->view = array('code' => 400, 'message' => '数据不能为空！');
			return FALSE;
		}else{
			//时间判断
			if (strtotime($this->start_time) < time()) {
				$this->view = array('code' => 400, 'message' => '会议开始时间错误！');
				return FALSE;
			}elseif (strtotime($this->start_time) >= strtotime($this->end_time)) {
				$this->view = array('code' => 400, 'message' => '开始时间不能晚于结束时间！');
				return FALSE;
			}
			//会议室判断
			//非拖动事件再进行判断
			if ($this->event) {
				if (empty($this->invite_users) || !is_array($this->invite_users)) {
					$this->view = array('code' => 400, 'message' => '参会人数据错误！');
					return FALSE;
				}else{
					foreach ($this->invite_users as $key => $value) {
						$this->userArray[] = $value['id'];
					}
					$this->invite_users = implode(',', $this->userArray);
					$this->userArray[] = $this->userId;
				}

			}else{
				$result = MeetingRoom::getInstance()->getDataById(array($this->room_id));
				$this->room_info = $result[0];
				if (empty($result)) {
					$this->view = array('code' => 400, 'message' => '会议室数据错误！');
					return FALSE;
				}else{
					//参会人判断
					if (empty($this->invite_users)) {
						$this->view = array('code' => 400, 'message' => '请输入参会人！');
						return FALSE;
					}
					//参会人员去重，过滤自己
					$this->userArray = explode(',', $this->invite_users);
					$this->userArray = array_unique($this->userArray);
					$this->userArray = array_diff($this->userArray, array($this->userId));
					$this->invite_users = implode(',', $this->userArray);
					//计算总人数
					$this->userArray[] = $this->userId;
					$this->userNum = count($this->userArray);

					$capacity = $result[0]['room_capacity'];
					if ( $this->userNum > $capacity) {
						$this->view = array('code' => 400, 'message' => '参会人数过多，请更换其它会议室！');
						return FALSE;
					}
				}
			}

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

	//个人信息
	private static function get_user_info($user = array()){

		if (empty($user)) {
			return FALSE;
		}
		$userTmp = array_unique($user);
		$userIdString = implode(',', $userTmp);
		$userData = Staffinfo::getInstance()->GetStaffinfo(array('sid'=>$userIdString), array('sid', 'name_c', 'name_e', 'mail', 'phone'));
		return $userData;
	}

	//获取参会人
	private static function get_user_name($userData = array()){
		if (empty($userData)) {
			return FALSE;
		}
		$userName = array();
		if (!empty($userData)) {
			foreach ($userData as $key => $value) {
				$userName[] = empty($value['name_c'])?$value['name_e']:$value['name_c'];
			}
		}
		$userNameString = implode(',', $userName);
		return $userNameString;
	}

}
