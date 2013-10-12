<?php
/*
 * 通讯录相关基础类
 * @auth tingtingyi@2013-09-06
 */ 
namespace Gate\Package\Address;
use \Gate\Package\Helper\DBGateHelper;

class Departinfo{
    private static $instance;
    private $table = "t_crab_staff_depart";

    private static $fields=array(
        'departid'=>'int',
        'departname'=>'string',
        'departinfo'=>'string',
        'father'=>'int',
        'child'=>'int',
        'others'=>'string',        
    );

    public static function getInstance(){
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function GetDepartinfo($param=array()){
        $result = array();
        $sql = "select * from $this->table where 1"; 
        $sqlData = array();
        if (is_array($param)){
            $where = "";
            foreach ($param as $k => $v){
                if(in_array($k,array('departname'))){
                    $where .= " and $k like '%$v%'";
                }else{
                    $where .= " and $k = :_$k";
                    $sqlData["_$k"] = $v;
                }
            }
            $sql .= $where;
        }
        $result = DBGateHelper::getConn()->read($sql, $sqlData,false,'departid');
        return $result;
    }


    //根据部门id批量查询部门名称接口  $param 传departids 逗号分隔
    public function MulitGetDepartname($param=array()){
        $result = array();
        $sql = "select departid,departname from $this->table ";
        if(!empty($param) && is_array($param)){
            $departid_a = array_unique($param);
            $departids = implode(',',$departid_a);
            $where = " where departid in  ($departids)";
            $sql .= $where;
        }
        $result = DBGateHelper::getConn()->read($sql,array(), FALSE, 'departid');
        return $result;
    }

    // 添加用户
    public function InsertStaff($params){
        if (!isset($params['departid']) || !isset($params['departname']) || !isset($params['father'])){
            return FALSE;
        }
        if(is_array($params) && !empty($params)){
            $sqlData = array();
            $sqlData['departid'] = $params['departid'];
            $sqlData['departname'] = $params['departname'];
            $sqlData['father'] = isset($params['father']) ? $params['father'] : 0;
            $sqlData['departinfo'] = isset($params['departinfo']) ? $params['departinfo'] : 'D9应用'.$sqlData['departname'];
            $sqlData['others'] = isset($params['others']) ? json_encode($params['others']) : "";
            $key = $value = "";
            foreach ($sqlData as $k=>$v){
                $key .= "`$k`,";  
                $value .= ":$k,";
            }
            $key = rtrim($key, ",");
            $value = rtrim($value, ",");
            $sql = "INSERT INTO $this->table ($key) VALUES ($value)";
            $result = DBGateHelper::getConn()->write($sql, $sqlData);
            return $result;
        }
        return FALSE;
    }

    // 更新用户信息为了操作安全不支持全部跟新
    public function UpdateStaff($param){
        if (!isset($params['departid'])){
            return FALSE;
        }
        $sql = "UPDATE $this->table SET";  
        $sqlData = array();

        foreach ($param as $key => $value) {
            switch (self::$update_fields[$key]) {
                case 'int':
                    $sql .= "`{$key}` = :{$key},";
                    $sqlData["{$key}"] = $value;
                    break;
                case 'string':
                    $sql .= "`{$key}` = :{$key},";
                    $sqlData["{$key}"] = $value;
                    break;
                default:
                    break;
            }
        }
        $sql = rtrim($sql,',');
        $sql .= "WHERE departid=:departid";
        $sqlData['departid'] = $param['departid'];
        $result = DBGateHelper::getConn()->write($sql, $sqlData);
        return $result;
    }





}
