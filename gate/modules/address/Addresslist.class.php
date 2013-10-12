<?php
/*
 * 通讯录相关逻辑
 *
 */ 
namespace Gate\Modules\Address; 
Use  Gate\Package\Address\Staffinfo;
Use  Gate\Package\Address\Departinfo;

class Addresslist extends \Gate\Libs\Controller { 
    private $search="";
    private $getparam=array();
    private $flag = 0;
    private $isajax = 0;
    protected $view_switch = true;

//    private $departid;

    public function run(){
        $this->_init();
        if($this->isajax != 0){
            $this->view_switch = FALSE;
        }
        $param = $userinfo = $departnames = array();
        // 只能查询到在职人员
        if(!empty($this->search)){
            $userinfo1 = $userinfo2 = $userinfo3 = array();
            if($this->flag == 0){
                $param['name_c'] = $this->search;
            }else if($this->flag == 2){
                $param['name_e']=$this->search;
            }else if($this->flag == 1){
                $param['extension']=$this->search;
            }
            $param['status'] = 1;
            $userinfo1 = Staffinfo::getInstance()->GetStaffinfo($param,$this->getparam);

            $param = array();
            if($this->flag == 0){
                $param['position'] = $this->search;
            }elseif($this->flag == 2){
                $param['mail']=$this->search;
            }else if($this->flag == 1){
                $param['phone']=$this->search;
            }
            $param['status'] = 1;
            $userinfo2 = Staffinfo::getInstance()->GetStaffinfo($param,$this->getparam);

//          按部门搜索
            $d_param['departname'] = $this->search;
            if($this->flag == 2){
                $d_param['departname'] = strtoupper($this->search);
            }
            $departinfo = Departinfo::getInstance()->GetDepartinfo($d_param); 
            $departid = array();
            if(!empty($departinfo)){
                foreach($departinfo as $k=>$v){
                    $departid[] = $v['departid'];
                }
                $param = array();
                $param['departid'] = implode(',',$departid);
                $param['status'] = 1;
                $userinfo3 = Staffinfo::getInstance()->GetStaffinfo($param,$this->getparam);
            }

            $key1 = array_keys($userinfo1);
            $key2 = array_keys($userinfo2);
            $key3 = array_keys($userinfo3);
            $info_key=(array_unique(array_merge($key1,$key2,$key3)));
            foreach($info_key as $k=> $key){
                if(isset($userinfo1["$key"])){
                    $userinfo["$k"] = $userinfo1["$key"];
                }elseif(isset($userinfo2["$key"])){
                    $userinfo["$k"] = $userinfo2["$key"];
                }elseif(isset($userinfo3["$key"])){
                    $userinfo["$k"] = $userinfo3["$key"];
                }
            }

            if(empty($userinfo)){
                $no_one='没有找到你要查找的人，请确定你输入的正确(第一个字符规则如下：中文代表人名，字母代表姓名拼音,字母+@符号代表邮箱)';
                $param=array();
                $param['status'] = 1;
                $userinfo = Staffinfo::getInstance()->GetStaffinfo($param);
            }
        }else{
            $param=array();
            $param['status'] = 1;
            $userinfo = Staffinfo::getInstance()->GetStaffinfo($param);
//            $userinfo = Staffinfo::getInstance()->GetStaffinfo();
//            $userinfo='请输入搜索内容(第一个字符规则如下：中文代表人名，字母代表姓名拼音,字母+@符号代表邮箱)';
        }
        if(!empty($userinfo) && is_array($userinfo)){
            foreach($userinfo as $k=>$uinfo){ 
                if(isset($uinfo['departid']) && !empty($uinfo['departid'])){
                    $departparam[$k] = $uinfo['departid'];
                }
            }
            if(!empty($departparam)){
                $departnames = Departinfo::getInstance()->MulitGetDepartname($departparam);
            }
            foreach($userinfo as $k=>$v){
                $userinfo[$k]['departname'] = '-';
                if(isset($v['departid']) && !empty($uinfo['departid'])){
                    $departid =  $v['departid'];
                    if(!empty($departid))
                        $userinfo[$k]['departname'] = $departnames["$departid"]['departname'];
                }
            }             
        }
        $this->view = array('userinfo'=>$userinfo,
                            'content' => '后期可以设置您的常用联系人，现在只能搜索',
                            'search' => $this->search,
                            'no_one'=>!empty($no_one) ? $no_one : 0,
                        );

    }

    private function _init(){
        $this->search = isset($this->request->REQUEST['search']) ? trim($this->request->REQUEST['search']) : '';
        $this->isajax = isset($this->request->REQUEST['isajax']) ? trim($this->request->REQUEST['isajax']) : 0;
        $this->getparam = isset($this->request->REQUEST['getparam']) ? $this->request->REQUEST['getparam'] : array();
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

