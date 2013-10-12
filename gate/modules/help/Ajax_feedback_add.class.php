<?PHP
namespace Gate\Modules\Help;

use \Gate\Package\Feedback\Feedback;

class Ajax_feedback_add extends \Gate\Libs\Controller {

    protected $view_switch = FALSE;
    private $text = '';

    public function run() {

        if (!$this->_init()) {
            return FALSE;
        }

        $params = array(
            'user_id' => $this->userId,
            'feedback_detail' => $this->text,
            'type' => $this->type,
        );

        $result = Feedback::getInstance()->addData($params);

        if ($result) {
            $this->view = array('code' => 200, 'message' => '添加成功，谢谢您的反馈！');
        }else{
            $this->view = array('code' => 400, 'message' => '添加失败！');
        }

    }

    private function _init() {

        $this->text = isset($this->request->REQUEST['text']) ? trim($this->request->REQUEST['text']) : '';
        $this->type = isset($this->request->REQUEST['type']) ? (int)$this->request->REQUEST['type'] : 0;

        if (empty($this->userId)) {
            $this->view = array('code' => 400, 'message' => '请登录后操作！');
            return FALSE;
        }elseif (empty($this->text)) {
            $this->view = array('code' => 400, 'message' => '内容不能为空！');
            return FALSE;
        }

        return TRUE;
    }

}
