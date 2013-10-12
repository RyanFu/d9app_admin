<?php
namespace Gate\Modules\User;
/**
 * 获取用户feed
 */
use \Gate\Package\User\UserFeed;
use \Gate\Libs\Base\Utils;

class Profile extends \Gate\Libs\Controller {

    //userid
	protected $id = 0;

	public function run() {
        if (!$this->_init()) {
            return FALSE;
        }
        $result = UserFeed::getInstance()->getDataByUser($this->id, 0,20);
        $feed_data = $this->assmbleData($result);
        $this->view = array(
            'user_id' => $this->userId,
            'username' => $this->username,
            'feed_data' => $feed_data
        );
	}

    private function assmbleData($arr) {
        if (empty($arr)) {
            return array();
        }
        foreach($arr as $key => $v) {
            $arr[$key]['user_id'] = $v['user_id'];
            $arr[$key]['user_name'] = $v['user_name'];
            $arr[$key]['feed_type'] = $v['feed_type'];
            $arr[$key]['feed_title'] = $v['feed_title'];
            $arr[$key]['feed_body'] = json_decode($v['feed_body'], true);
            $time_gap = time() - strtotime($v['dateline']);
            $arr[$key]['dateline'] = Utils::getTimeGapMsg($time_gap);
        }
        return $arr;
    }

	private function _init() {
		$this->id = isset($this->request->path_args[0]) ? $this->request->path_args[0] : 0;

		if (empty($this->userId)) {
            $this->setError(400, 40001, 'empty paramters');
            return FALSE;
        }

        return TRUE;
	}
}
