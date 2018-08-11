<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\business\UseraccountManage;
use login\Rsa;
class Login extends Controller
{   

    protected $organid;

    //自定义初始化
    protected function _initialize() {
		header('Access-Control-Allow-Headers:x-requested-with,content-type,starttime,sign,token,lang');
		header('Access-Control-Allow-Origin: *');
        $this->organid = 1;
    }

    /**
     * 后台登录
     * @auther 胡博森
     * @param string $login_data 登录信息
     * 登录信息包含以下内容：
     * @param strign company_domain 登录域名
     * @param string admin_account 登录账号
     * @param strign admin_pwd 登录密码
     *
     * **/
    public function login(){
        $str_data = input('post.login_data','','trim');
        $obj_login = new UseraccountManage;
        $arr_return = $obj_login->getUserInfo($str_data);
        return $this->ajaxReturn($arr_return);
    }


    /**
     * [getPublicKey 返回公钥]
     * @return [type] [description]
     */
    public function getPublicKey(){
        $ret = new Rsa;
        $arr_return = ['key'=>$ret->getPublicKey(),0,lang('success')];
        return $this->ajaxReturn($arr_return);
    }


	/**
	 * [exitLogin 退出登陆]
	 */
    public function exitLogout(){
        $str_token = input('server.HTTP_TOKEN');
        //接受token
        $obj_login = new UseraccountManage;
        $arr_return = $obj_login->LogOut($str_token);
        return $this->ajaxReturn($arr_return);
	}

}
