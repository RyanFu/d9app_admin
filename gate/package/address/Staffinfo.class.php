<?php
/*
 * 通讯录相关基础类
 * @auth tingtingyi@2013-09-06
 */ 
namespace Gate\Package\Address;
use \Gate\Package\Helper\DBGateHelper;
use \Gate\Libs\Base\Util_Pinyin;

class Staffinfo{
    private static $instance;
    private $table = "t_crab_staff_info";

    private static $update_fields=array(
        'sid'=>'int',
        'name_c'=>'string',        
        'name_e'=>'string',        
        'extension'=>'string',        
        'phone'=>'string',        
        'departid'=>'int',        
        'position'=>'string',        
        'mail'=>'string',        
        'status'=>'int',        
        'password'=>'string',        
        'others'=>'string',        
        'role'=>'string',        
        'qq'=>'string',        
        'redmineid'=>'int',

    );
    private static $select_fields=array(
        'sid',
        'name_c',        
        'name_e',        
        'extension',        
        'phone',        
        'departid',        
        'position',        
        'mail',        
        'status',        
        'password',        
        'others',        
        'role',
        'qq',
        'redmineid',
    );

    public static function getInstance(){
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function GetStaffinfo($param=array(),$getparam=array(),$flag=1){
        $result = array();
        if (is_array($param)){
            $selects = '*';
            if(!empty($getparam)){
                $select = array();
                foreach($getparam as $v){
                    if(in_array($v,self::$select_fields)){
                        $select[] = $v;
                    }
                }
                if(!empty($select)){
                    $selects = implode(',',$select);
                }
            }
            $sql = "select $selects from $this->table where 1 "; 
            $where = "";
            $sqlData = array();
            foreach ($param as $k => $v){
                if(in_array($k,array('name_c','name_e','mail','extension','position','phone','qq'))){
                    $where .= " and $k like '%$v%'";
                }
                if(in_array($k,array('sid','departid','status','redmineid'))){
                    $where .= " and $k in ($v)";
                }
            }
            $sql .= $where;
            if($flag == 1){
                $result = DBGateHelper::getConn()->read($sql, $sqlData,false,'sid');
            }else{
                $result = DBGateHelper::getConn()->read($sql, $sqlData);
            }
        }
        return $result;
    }

    // 添加用户
    public function InsertStaff($params=array()){
        if (!isset($params['name_c']) || !isset($params['mail'])){
            return FALSE;
        }
        if(is_array($params) && !empty($params)){
            $sqlData = array();
            $sqlData['name_c'] = $params['name_c'];
            if(!isset($params['name_e']))
            {
                $name_c_gbk = mb_convert_encoding($params['name_c'],'gbk','utf-8');
                $sqlData['name_e'] = Util_Pinyin::convertToPinYin($name_c_gbk);
            }
            $sqlData['extension'] = isset($params['extension']) ? $params['extension'] : 0;
            $sqlData['phone'] = isset($params['phone']) ? $params['phone'] : '';
            $sqlData['departid'] = isset($params['departid']) ? $params['departid'] : 1;
            $sqlData['position'] = isset($params['position']) ? $params['position'] : 1;
            $sqlData['mail'] = $params['mail'];
            $sqlData['status'] = isset($params['status']) ? $params['status'] : 1;
            $sqlData['others'] = isset($params['others']) ? json_encode($params['others']) : "";
            $sqlData['qq'] = isset($params['qq']) ? $params['qq'] : 0;
            $sqlData['role'] = isset($params['role']) ? $params['role'] : 1;
            $sqlData['redmineid'] = isset($params['redmineid']) ? $params['redmineid'] : 0;
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
    public function UpdateStaff($param=array()){
        if (!isset($param['sid'])){
            return FALSE;
        }
        $sql = "UPDATE $this->table SET";  
        $sqlData = array();

        $sqlData['sid'] = $param['sid'];
        unset($param['sid']);
        foreach ($param as $key => $value) {
            switch (self::$update_fields[$key]) {
                case 'int':
                    $sql .= "`{$key}` = :{$key},";
                    $sqlData["{$key}"] = $value;
                    break;
                case 'string':
                    $sql .= "`{$key}` = :{$key},";
                    $sqlData["$key"] = $value;
                    break;
                default:
                    break;
            }
        }
        $sql = rtrim($sql,',');
        $sql .= "  WHERE sid=:sid";
        $result = DBGateHelper::getConn()->write($sql, $sqlData);
        return $result;
    }





}
