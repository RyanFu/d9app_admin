<?php
/*
 * 通讯录相关基础类
 */ 
namespace Gate\Package\Userinfo;
class User{
    private $table = "t_crab_staff_info";

    public function getuserinfo($param){
        if (is_array($param)){
            $sql = "select * from $this->table where status=1"; 
            $where = "";
            foreach ($param as $k => $v){
                $where .= "and $k='".$v."' ";
            }
        }
        
    }
}
