<?php
/**用户信息**/
namespace app\admin\business;

use app\admin\model\Userinfo;

class UserinfoManage{

    /**
     * 添加用户信息
     * @param $arr_data
     */
    public function addUserInfo($arr_data){
        //数据验证
        if(empty($arr_data['firstname'])){
            return return_format('',60410,lange('UserNameEmpty'));
        }
        $arr_data['usertype'] = 1;
        $arr_data['mobile'] = '';
        $arr_data['state'] = 1;
        $arr_data['createdate'] = date('Y-m-d H:i:s');
        $arr_data['sortlevel'] = 1;
        $arr_data['serverid'] = 1;
        $arr_data['identification'] =  time().rand(0,10000);
        $obj_user_info = new Userinfo();
        $int_add = $obj_user_info->setUserInfoAdd($arr_data);
        if($int_add){
            $int_user_id = $obj_user_info->getUserInfoId();
            return $int_user_id;
        }else{
            return 0;
        }
    }

    /**
     * 修改
     * @param $arr_where
     * @param $arr_data
     */
    public function updUserInfo($int_user_id){
        $arr_where['userid'] = $int_user_id;
        $obj_user_info = new Userinfo();
        $obj_user_info->setUserInfoUpd($arr_where);
    }
}
