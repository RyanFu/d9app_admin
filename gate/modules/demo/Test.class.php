<?php
namespace Gate\Modules\Demo;

use \Gate\Package\Demo\Demo AS Demo;
use \Gate\Package\Meeting\MeetingRoom AS MeetingRoom;
use \Gate\Libs\Base\Pinyin AS Pinyin;

class Test extends \Gate\Libs\Controller {

	protected $view_switch = FALSE;
	protected $twitter_id;

	public function run() {

		if (!$this->_init()) {
			//return FALSE;
		}

		//$twitter_data = Demo::getInstance()->getDataInfo($this->twitter_id);

		$data = Pinyin::getInstance()->convert("好婷ni亭, 参数随意设置");

		$this->view = $data;
	}


	private function _init() {

		$this->twitter_id = isset($this->request->REQUEST['twitter_id']) ? (int)$this->request->REQUEST['twitter_id'] : 1;

		if (empty($this->twitter_id)) {
			//$this->view = array('code' => 400, 'message' => 'Empty twitter_id');
			return FALSE;
		}else{
			return TRUE;
		}

	}

}
