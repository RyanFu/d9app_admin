<?php
/*
 * 通讯录相关逻辑
 *
 */ 
namespace Gate\Modules\User; 
use Gate\Package\Userinfo\User;
class User extends \Gate\Libs\Controller { 
    private $search;
    private $flag = false;
    private $departid;
    public function run(){
        $param = $userinfo = array();
        if($this->flag === 0){
            if(stripos($this->search,'@') === false){
                $param['name_c']=$this->search;
            }else{
                $param['mail']=$this->search;
            }

        }else if($this->flag == 2){
            $param['name_e']=$this->search;
        }else if($this->flag == 1){
            if($this->search < 100){
                $param['departid']=$this->search;
            }else{
                $param['extension']=$this->search;
            }
        }
        $user_obj = new User;
        $userinfo = $user_obj->getuserinfo($param);

    }

    private function _init(){
        $this->search = isset($this->request->REQUEST['search']) ? trim($this->request->REQUEST['search']) : '';
        if (!empty($this->search)){
            if(ord($this->search) <= 57){
                // 0-9
                $this->flag = 1;
            }else if(ord($this->search) <= 122){
                // a-z & A-Z
                $this->flag = 2;
            }else{
                // 汉字
                $this->flag = 0;
            }
        }

        $this->departid = isset($this->request->REQUEST['departid']) ? intval($this->request->REQUEST['departid']) : 0;

    }
}

