<?php

namespace Gate\Scripts\Mail;

/**
 *	发送邮件
 */

use \Gate\Package\Mail\MailQueue;
use \Gate\Package\Meeting\MeetingTime;
use \Gate\Package\Meeting\MeetingRoom;
use \Gate\Package\Address\Staffinfo;

class MailRemindSend extends \Gate\Libs\Scripts {

	//统计广告投放数据
	public function run() {

		$time = time()+600;
		$mailData = MailQueue::getInstance()->getRemind($time);

		if (!empty($mailData)) {

			foreach ($mailData as $key => $value) {
				//var_dump($value);
				$queue_id 	= $value['queue_id'];
				$book_id 	= $value['book_id'];
				$bookData 	= MeetingTime::getInstance()->getDataByBook(array($book_id));
				$bookUser 	= array();

				if (!empty($bookData)) {
					foreach ($bookData as $book) {
						$bookUser[] 		= $book['user_id'];
						$founder_user_id 	= $book['founder_user_id'];
						$this->meeting_topic = $book['meeting_topic'];
						$room_id 			= $book['room_id'];
						$this->start_time 	= $book['start_time'];
					}
				}
				$start_time = strtotime($this->start_time);
				$create_time = strtotime($value['create_time']);
				$week = date('w', $start_time);
				$week = self::get_week($week);
				$this->start_time  = date('n月j日H:i ', $start_time);
				$this->start_time .= $week;
				$this->userArray = $bookUser;
				$user_info 		= self::get_user_info($this->userArray);
				$meeting_people = self::get_user_name($user_info);
				$mailothers 	= self::get_mailothers($user_info);
				$founder_user 	= $user_info[$founder_user_id]['name_c'];

				$roomData = MeetingRoom::getInstance()->getDataById(array($room_id));
				$this->room_info = $roomData[0];

				foreach ($user_info as $u => $user) {
					$mailData = array(
						'username'	=> $user['name_c'],
						'email'		=> $user['mail'],
						'phone'		=> $user['phone'],
						'openuser'	=> $founder_user,
						'meeting'	=> $this->room_info['room_name'],
						'meeting_content'	=> $this->meeting_topic,
						'start_time'		=> $start_time,
						'meeting_time'		=> $this->start_time,
						'meeting_people'	=> $meeting_people,
						'mailothers'		=> $mailothers,
						'meeting_title'		=> '会议提醒：'.$this->meeting_topic,
					);
					if ($start_time - $create_time > 600) {
						$sendResult = $this->send($mailData);
						//var_dump($sendResult);
					}
					$this->send_sms($mailData);

					MailQueue::getInstance()->deleteData(array($queue_id));
/*
					if (isset($sendResult['success']) && !empty($sendResult['success'])) {
						MailQueue::getInstance()->deleteData(array($queue_id));
					}else{
						$sendResult['queue_id'] = $queue_id;
						$gateLog = new \Gate\Libs\GateLog('error_maillog', 'normal');
						$gateLog->w_log($sendResult);
					}
*/
				}

			}

		}

		echo date('Ymd H:i:s'),':MailRemindSend:Done',"\n";
		return true;
	}

	private function send($mail = array()) {

		$ch = curl_init();

		$data = array();
		$data['app_key'] = '523943fd9a548';
		$data['project'] = 1085;
		$data['flag'] 	= 1;

		$data['username'] 	= $mail['username'];
		$data['email'] 		= $mail['email'];
		$data['mailothers'] = $mail['mailothers'];
		unset($mail['mailothers']);

		// 邮件模板变量替换
		//$data = array_merge($data, $mail);
		$data['mail_data'] = $mail;
		$data = json_encode($data);

		curl_setopt($ch, CURLOPT_URL, 'http://mail01.meilishuo.com/officesendmail');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: text/plain', 'Charset: utf-8') );
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$return_data = curl_exec($ch);
		curl_close($ch);
		$return = json_decode($return_data, 1);
		return $return;
	}

	private function send_sms($data){
		if (empty($data['phone'])) {
			return false;
		}
		$phone = trim($data['phone']);

		$double = explode("/",$phone);
		if ($double ) {
			$phone = $double[0];
		}

		$time  = date(' H:i ', $data['start_time']);
		$content = '会议提醒：“'.$data['meeting_content'].'”将于'.$time.'在'.$data['meeting'].'举行，记得准时参加哦！';
		$content = str_replace("\n", "+", $content);
		$content = str_replace(" ", "+", $content);

        $sms_url = "x_target_no={$phone}&x_memo={$content}";
        $sms_url = SMS_API.$sms_url;
		//var_dump($sms_url);return FALSE;
        shell_exec('curl "'.$sms_url.'"');
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
	private static function get_mailothers($userData = array()){
		if (empty($userData)) {
			return FALSE;
		}
		$mailothers = array();
		if (!empty($userData)) {
			foreach ($userData as $key => $value) {
				$tmp 	= array();
				$tmp[] 	= $value['mail'];
				$tmp[] 	= empty($value['name_c'])?$value['name_e']:$value['name_c'];
				$mailothers[] = $tmp;
			}
		}
		return $mailothers;
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

	//获取星期
	private static function get_week($week = 0){
		$array = array(
			0 => '星期日',
			1 => '星期一',
			2 => '星期二',
			3 => '星期三',
			4 => '星期四',
			5 => '星期五',
			6 => '星期六',
		);
		return $array[$week];
	}

}
