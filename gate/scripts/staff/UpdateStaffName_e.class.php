<?php
namespace Gate\Scripts\Staff;
Use  Gate\Package\Address\Staffinfo;
Use  Gate\Package\Address\Departinfo;
use \Gate\Libs\Base\Util_Pinyin;

class UpdateStaffName_e extends \Gate\Libs\Scripts {

    public function run() {
        $getparam = array('sid','name_c');
        $param = array();
        $userinfos=Staffinfo::getInstance()->GetStaffinfo($param,$getparam);
        foreach($userinfos as $userinfo){
            $name_c_gbk = mb_convert_encoding($userinfo['name_c'],'gbk','utf-8');
            $sqlData['name_e'] = Util_Pinyin::convertToPinYin($name_c_gbk);
            $sqlData['sid'] = $userinfo['sid'];
            $ret=Staffinfo::getInstance()->UpdateStaff($sqlData);
        }
        sleep(1);
    }

}
