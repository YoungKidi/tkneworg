<?php
namespace app\admin\business;
use login\Rsa;
use think\Validate;
/**
 * 用户登录
 */
use app\admin\model\Useraccount;

class UseraccountManage{

    /**
     * 用户登录
     * @auther 胡博森
     * @param strign $str_data 登录加密信息
     * @return array
     */
    public function getUserInfo($str_data){
        //数据验证
        if(empty($str_data) || !is_string($str_data)){
            return return_format('',60410,lang('UserLoginInfoError'));
        }
        //解密数据
        $obj_ret = new Rsa;
        $arr_login_info = json_decode($obj_ret->rsaDecryptorign($str_data),true);
        //对解密完的数据验证
        $bool_result = $this->inspectionLoginInfo($arr_login_info);
        if(!$bool_result){
            return return_format('',60410,lang('InputUserNameAndPwd'));
        }
        $arr_where = ['c.companyid'=>1,'ua.account'=>$arr_login_info['admin_account']];
        $arr_field = ['c.companyid','uc.userroleid','uc.userid','uc.firstname','ua.countrycode','ua.pwd'];
        //用登录信息查询数据库
        $obj_user_account = new Useraccount();
        $arr_info = $obj_user_account->getUserInfoLogin($arr_where,$arr_field);
        //验证密码
        $str_new_pwd = md5(md5($arr_login_info['admin_pwd']) . md5(strtolower(trim($arr_info['countrycode'] . $arr_login_info['admin_account']))));
        if(!$arr_info || $str_new_pwd  !=  $arr_info['pwd']){
            return return_format('',60410,lang('InputUserNameAndPwd'));
        }
        unset($arr_info['pwd']);
        unset($arr_info['countrycode']);
        $redis = \RedisClient::getInstance();
        $uuid = $this->getUUID();
        $str_key = config('redis.admin_login_prefix').$uuid;
        $bool_redis_set = $redis->set($str_key,$arr_info,config('redis.admin_login_life_time'));
        if(!$bool_redis_set){ //redis添加失败
            return return_format('',60310,lang('FailAgain'));
        }
        $arr_return['token'] = $uuid;
        $arr_return['role'] = $arr_info['userroleid'];
        return return_format($arr_return,0,lang('success'));
    }

    /**
     * 退出登录
     */
    public function LogOut($str_data){
        $redis = \RedisClient::getInstance();
        $redis->del(config('redis.admin_login_prefix').$str_data);
        return return_format('',0,lang('success'));
    }


    /**
     * 检查数据是否符合规则
     * @auther 胡博森
     * @param array $arr_data 要检测的数据
     */
    private function inspectionLoginInfo($arr_data){
        $arr_rule = [
            'admin_account'  => 'require',
            'admin_pwd'      => 'require',
        ];
        $obj_val = new Validate($arr_rule);
        return $obj_val->check($arr_data);
    }

    /**
     * 用户登录成功，生成uuid
     * @param string $prefix 前缀
     * @return string
     */
    private function getUUID(){
            $str = md5(uniqid(mt_rand(), true));
            $uuid  = substr($str,0,8) . '-';
            $uuid .= substr($str,8,4) . '-';
            $uuid .= substr($str,12,4) . '-';
            $uuid .= substr($str,16,4) . '-';
            $uuid .= substr($str,20,12);
            return $uuid;
    }
}