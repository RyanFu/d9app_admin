<?php
namespace Gate\Modules\Meeting;

use \Gate\Package\Meeting\MeetingRoom;
use \Gate\Package\Meeting\MeetingBook;
use \Gate\Package\Meeting\MeetingBookuser;
use \Gate\Package\Meeting\MeetingTime;
use \Gate\Package\Address\Staffinfo;
use \Gate\Package\Time\TimeManage;
use \Gate\Package\Mail\MailQueue;
use \Gate\Package\User\UserFeed;

class Ajax_book_add extends \Gate\Libs\Controller {

	protected $view_switch = FALSE;
	protected $room_id;
	protected $room_info;
	protected $meeting_topic;
	protected $invite_users;
	protected $userArray;
	protected $userNum;
	protected $start_time;
	protected $end_time;
	protected $others;

	public function run() {

		if (!$this->_init()) {
			return FALSE;
		}

		//检测会员会议时间
		$checkMeeting = MeetingTime::getInstance()->getDataByUser($this->userArray, $this->start_time, $this->end_time);
		$checkTime = TimeManage::getInstance()->getDataByUser($this->userArray, $this->start_time, $this->end_time);

		$checkResult = array_merge($checkMeeting, $checkTime);

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

		$new_data = array(
			'room_id'		=> $this->room_id,
			'user_id'		=> $this->userId,
			'meeting_topic' => $this->meeting_topic,
			'invite_users' 	=> $this->invite_users,
			'start_time' 	=> $this->start_time,
			'end_time' 		=> $this->end_time,
			'others' 		=> $this->others,
		);
		$result = MeetingBook::getInstance()->addData($new_data);
        $this->addFeed();

		if ($result) {

			$book_id = (int)$result;

			foreach ($this->userArray as $key => $value) {
				$newMap = array(
					'book_id' => $book_id,
					'user_id' => $value,
				);
				MeetingBookuser::getInstance()->addData($newMap);
			}

			$newQueue = array(
				'book_id' => $book_id,
				'room_id' => $this->room_id,
				'user_id' => $this->userId,
				'start_time' => $this->start_time,
			);
			MailQueue::getInstance()->addData($newQueue);

			$this->view = array('code' => 200, 'message' => '添加数据成功！');
		}else{
			$this->view = array('code' => 400, 'message' => '添加数据失败！');
		}

	}

    //add feed
    private function addFeed() {
        $room = MeetingRoom::getInstance()->getDataById($this->room_id);
        $feed_data = array(
            'user_id' => $this->userId,
            'user_name' => $this->username,
            'feed_type' => 'meeting',
            'feed_title' => $this->meeting_topic,
            'feed_body' => json_encode(array('room_name'=> $room[0]['room_name'], 'meeting_topic' => $this->meeting_topic)),
        );
        UserFeed::addFeed($feed_data);
    }

	private function _init() {

		$this->room_id = isset($this->request->REQUEST['room_id']) ? (int)$this->request->REQUEST['room_id'] : 0;
		$this->meeting_topic = isset($this->request->REQUEST['title']) ? $this->request->REQUEST['title'] : '';
		$this->invite_users = isset($this->request->REQUEST['inputUser']) ? $this->request->REQUEST['inputUser'] : '';
		$this->start_time = isset($this->request->REQUEST['start']) ? $this->request->REQUEST['start'] : 0;
		$this->end_time = isset($this->request->REQUEST['end']) ? $this->request->REQUEST['end'] : 0;
		$this->others = isset($this->request->REQUEST['inputMemo']) ? $this->request->REQUEST['inputMemo'] : '';

		if (empty($this->userId)) {
			$this->view = array('code' => 400, 'message' => '请登录后操作！');
			return FALSE;
		}elseif (empty($this->meeting_topic)) {
			$this->view = array('code' => 400, 'message' => '会议主题不能为空！');
			return FALSE;
		}elseif (empty($this->room_id) || empty($this->start_time) || empty($this->end_time)) {
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
				//$this->userArray = array_unique($this->userArray);
				$this->userNum = count($this->userArray);

				$capacity = $result[0]['room_capacity'];
				if ($this->userNum <= 1) {
					$this->view = array('code' => 400, 'message' => '不能只邀请自己！');
					return FALSE;
				}elseif ( $this->userNum > $capacity) {
					$this->view = array('code' => 400, 'message' => '参会人数过多，请更换其它会议室！');
					return FALSE;
				}
			}
			return TRUE;
		}

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
