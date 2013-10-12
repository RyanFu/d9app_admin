<?php
namespace Gate\Modules\Time;

use \Gate\Package\Time\TimeManage;

class Ajax_update_data extends \Gate\Libs\Controller {

	protected $view_switch = FALSE;
    protected $time_id;
	protected $dowhat;
	protected $start_time;
	protected $end_time;
	protected $color;

	public function run() {

		if (!$this->_init()) {
			return FALSE;
		}

        $data = array(
			'time_id' => (int)$this->time_id,
            'user_id' => (int)$this->userId,
            'dowhat'  => $this->dowhat,
            'start_time' => date('Y-m-d H:i:s',strtotime($this->start_time)),
	        'end_time' 	=> date('Y-m-d H:i:s',strtotime(str_replace('00:00', '23:59', $this->end_time))),
            'color' => $this->color 
        );

        if ($this->start_time > $this->end_time) {
            $this->view = array('code' => 400, 'message' => '开始时间不能大于结束时间!');
            return false;
        }
		$result = TimeManage::getInstance()->updateData($data);

		if ($result) {
			$this->view = array('code' => 200, 'message' => '更新数据成功！');
		}
		else {
			$this->view = array('code' => 400, 'message' => '更新数据失败！');
		}

	}

	private function _init() {


		$this->time_id= isset($this->request->REQUEST['id']) ? $this->request->REQUEST['id'] : 0;
		$this->dowhat= isset($this->request->REQUEST['title']) ? $this->request->REQUEST['title'] : '';
		$this->start_time = isset($this->request->REQUEST['start']) ? $this->request->REQUEST['start'] : '';
		$this->end_time = isset($this->request->REQUEST['end']) ? $this->request->REQUEST['end'] : '';
		$this->color = isset($this->request->REQUEST['color']) ? $this->request->REQUEST['color'] : '';

		if (empty($this->dowhat) || empty($this->start_time) || empty($this->end_time)) {
			$this->view = array('code' => 400, 'state' => 'error', 'message' => '数据不能为空！');
			return FALSE;
		}else{
			return TRUE;
		}

	}

}
