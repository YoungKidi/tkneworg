<?php
namespace app\admin\controller;

use think\Controller;

/**
 * @name 公共控制器
 */
class Common extends Controller{

    protected $_userinfo; //用户登录信息
    /**
     * 构造方法
     * Common constructor
     */
    public function __construct()
    {
        parent::__construct();
        //接收header头中token
        $str_token = input('server.HTTP_TOKEN');
        //如果token不存在，提示用户登录
        if(!$str_token || !is_string($str_token)){
            $arr_return = ['',60420,lang('PleaseLoginFirst')];
            exit($this->AjaxReturn($arr_return));
        }
        //从redis中获取登录信息
        $redis = \RedisClient::getInstance();
        $arr_info = $redis->get(config('redis.admin_login_life_time').$str_token);
        if(!$arr_info){
            $arr_return = ['',60420,lang('PleaseLoginFirst')];
            exit($this->AjaxReturn($arr_return));
        }
        $this->_userinfo = $arr_info;
    }
}