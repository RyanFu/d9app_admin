<?php
namespace Gate\Modules\Time;

use \Gate\Package\Time\TimeManage;
use \Gate\Package\User\UserFeed;

class Ajax_time_add extends \Gate\Libs\Controller {

	protected $view_switch = FALSE;
	protected $dowhat;
	protected $start_time;
	protected $end_time;
    protected $color;

	public function run() {

		if (!$this->_init()) {
			return FALSE;
		}

		$new_data = array(
			'user_id'	=> $this->userId,
			'dowhat'	=> $this->dowhat,
			'start_time'	=> $this->start_time,
			'end_time' 	=> str_replace('00:00', '23:59', $this->end_time),  //if 00:00, 会少一天
			'color' 	=> $this->color,
		);
        if ($this->start_time > $this->end_time) {
			$this->view = array('code' => 400, 'message' => '开始时间不能大于结束时间！');
            return ;
        }
		$result = TimeManage::getInstance()->addData($new_data);
        //$this->addFeed();

		if ($result) {
			$this->view = array('code' => 200, 'message' => '添加数据成功！');
		}
		else {
			$this->view = array('code' => 400, 'message' => '添加数据失败！');
		}

	}

    //add feed
    private function addFeed() {
        $feed_data = array(
            'user_id' => $this->userId,
            'user_name' => $this->username,
            'feed_type' => 'time',
            'feed_title' => $this->dowhat,
            'feed_body' => json_encode(array('content' => $this->dowhat)),
        );
        UserFeed::addFeed($feed_data);
    }

	private function _init() {

		$this->dowhat= isset($this->request->REQUEST['title']) ? $this->request->REQUEST['title'] : '';
		$this->start_time = isset($this->request->REQUEST['start']) ? $this->request->REQUEST['start'] : '';
		$this->end_time = isset($this->request->REQUEST['end']) ? $this->request->REQUEST['end'] : '';
		$this->color = isset($this->request->REQUEST['color']) ? $this->request->REQUEST['color'] : '';

		if (empty($this->dowhat) || empty($this->start_time) || empty($this->end_time) || empty($this->color)) {
			$this->view = array('code' => 400, 'state' => 'error', 'message' => '数据不能为空！');
			return FALSE;
		}else{
			return TRUE;
		}

	}

}
