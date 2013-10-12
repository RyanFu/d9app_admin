<?php
/*
 * 通讯录相关逻辑
 *
 */ 
namespace Gate\Modules\Address; 
Use  Gate\Package\Address\Staffinfo;
Use  Gate\Package\Address\Departinfo;

class Address_manage extends \Gate\Libs\Controller { 
    private $search="";
    private $flag = 0;
    private $departid;
    public function run(){
        if(!in_array($this->userId,array(11,202,)))
        {
            echo '你没有权限操作此页';
            exit;
        }
        $this->_init();
        $param = $userinfo = $departnames = array();
        if(!empty($this->search)){
            if($this->flag == 0){
                $param['name_c'] = $this->search;
            }else if($this->flag == 2){
                $ismail = preg_match('/@$/',$this->search);
                if($ismail){
                    $param['mail']=$this->search;
                }else{
                    $param['name_e']=$this->search;
                }
            }else if($this->flag == 1){
                if($this->search < 100){
                    $param['departid']=$this->search;
                }else{
                    $param['extension']=$this->search;
                }
            }
            $userinfo = Staffinfo::getInstance()->GetStaffinfo($param);
            if(empty($userinfo)){
                $no_one='没有找到你要查找的人，请确定你输入的正确(第一个字符规则如下：中文代表人名，字母代表姓名拼音,字母+@符号代表邮箱)';
                $userinfo = Staffinfo::getInstance()->GetStaffinfo();
            }
        }else{
            $userinfo = Staffinfo::getInstance()->GetStaffinfo();
//            var_dump($userinfo['departid']);
//            $userinfo='请输入搜索内容(第一个字符规则如下：中文代表人名，字母代表姓名拼音,字母+@符号代表邮箱)';
        }
        $departnames = Departinfo::getInstance()->MulitGetDepartname();
        if(!empty($userinfo) && is_array($userinfo)){
            foreach($userinfo as $k=>$v){
                $userinfo[$k]['departname'] = '-';
                if(isset($v['departid']) && !empty($v['departid'])){
                    $departid =  $v['departid'];
                    if(!empty($departid))
                        $userinfo[$k]['departname'] = $departnames["$departid"]['departname'];
                }
            }             
        }
        $this->view = array('userinfo'=>$userinfo,
                            'content' => '后期可以设置您的常用联系人，现在只能搜索',
                            'search' => $this->search,
                            'departs'=> $departnames,
                            'no_one'=>!empty($no_one) ? $no_one : 0,
                        );

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
            }
        }
    }
}

