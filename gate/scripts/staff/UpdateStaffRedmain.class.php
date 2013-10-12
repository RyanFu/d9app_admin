<?php
namespace Gate\Scripts\Staff;
Use  Gate\Package\Address\Staffinfo;
Use  Gate\Package\Address\Departinfo;
//This is a demo, code the run function

class UpdateStaffRedmain extends \Gate\Libs\Scripts {

    public function run() {
        $getparam = array('sid','mail');
        $param['redmineid'] = 0;
        $userinfos=Staffinfo::getInstance()->GetStaffinfo($param,$getparam);
        foreach($userinfos as $userinfo){
            $users = \Gate\Package\User\UserModel::objects()
                ->get('mail__exact', $userinfo['mail']);
            if($users->id !== null){
                $sqlData['redmineid'] = $users->id;
                $sqlData['sid'] = $userinfo['sid'];
                $ret=Staffinfo::getInstance()->UpdateStaff($sqlData);
            }

        }
        sleep(1);
    }

}
