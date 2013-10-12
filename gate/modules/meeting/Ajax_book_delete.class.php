<?php
namespace Gate\Modules\Meeting;

use \Gate\Package\Meeting\MeetingRoom;
use \Gate\Package\Meeting\MeetingBook;
use \Gate\Package\Meeting\MeetingBookuser;
use \Gate\Package\Mail\MailQueue;

class Ajax_book_delete extends \Gate\Libs\Controller {

	protected $view_switch = FALSE;
	protected $room_id;
	protected $book_id;

	public function run() {

		if (!$this->_init()) {
			return FALSE;
		}

		$delete_data = array(
			'room_id'	=> $this->room_id,
			'user_id'	=> $this->userId,
			'book_id'	=> $this->book_id,
		);

		$result = MeetingBook::getInstance()->deleteData($delete_data);

		if ($result) {

			MeetingBookUser::getInstance()->deleteBookData($this->book_id);
			//删除队列
			MailQueue::getInstance()->deleteDataByBook($this->book_id);

			$this->view = array('code' => 200, 'message' => '删除数据成功！');
		}else{
			$this->view = array('code' => 400, 'message' => '删除数据失败！');
		}

	}

	private function _init() {

		$this->room_id = isset($this->request->REQUEST['room_id']) ? (int)$this->request->REQUEST['room_id'] : 0;
		$this->book_id = isset($this->request->REQUEST['book_id']) ? (int)$this->request->REQUEST['book_id'] : 0;

		if (empty($this->room_id) || empty($this->book_id)) {
			$this->view = array('code' => 400, 'message' => '数据不能为空！');
			return FALSE;
		}elseif(empty($this->book_id)){
			$this->view = array('code' => 400, 'message' => '请登录后操作！');
			return FALSE;
		}else{
			return TRUE;
		}

	}

}
