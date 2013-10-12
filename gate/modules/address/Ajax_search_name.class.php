<?php
namespace Gate\Modules\Address;

Use  Gate\Package\Address\Staffinfo;
Use  Gate\Package\Address\Departinfo;

class Ajax_search_name extends \Gate\Libs\Controller {

    protected $view_switch = FALSE;
    private $search="";
    private $getparam=array();
    private $flag = 0;
    private $isajax = 0;

    public function run(){

        if (!$this->_init()) {
            return FALSE;
        }

        $param['status'] = 1;
        if(ord($this->search) <= 122){
            $param['name_e']=$this->search;
        }else{
            $param['name_c'] = $this->search;
        }

        $userInfo = Staffinfo::getInstance()->GetStaffinfo($param,$this->getparam);
        $result = array();
        if (!empty($userInfo)) {
            foreach ($userInfo as $key => $value) {
                $tmp = array();
                $tmp['id'] = $value['sid'];
                $tmp['name'] = $value['name_c'];
                $result[] = $tmp;
            }
        }

        $this->view = $result;

    }

    private function _init(){
        $this->search = isset($this->request->REQUEST['q']) ? trim($this->request->REQUEST['q']) : '';

        if (empty($this->search)) {
            $this->view = array('code' => 400, 'message' => '请输入名字！');
            return FALSE;
        }else{
            return TRUE;
        }
    }
}

