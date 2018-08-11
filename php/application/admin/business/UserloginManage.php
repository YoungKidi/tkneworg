<?php
/**登录的逻辑的相关方法处理**/
namespace app\admin\business;
use think\Validate;
use app\admin\model\Company;
use app\admin\model\Userinfo;
use app\admin\model\Usercompany;
use app\admin\model\Useraccount;
use RedisClient;

class UserloginManage {

	/**
	 *	
	 *  注销用户信息 目前主要是针对iphone注销devicetoken
	 *	@author zzq
	 *  @date 2018-07-25
	 *	@param  $userid int 用户id
	 *	@return  
	 */
	public function cancellationuserinfo($userid)
	{
		if($userid!=0)
		{
			$userinfo = new Userinfo();
			$where['userid'] = ['EQ',$userid];
			$redis = RedisClient::getInstance();
			$userdata = $userinfo->field('identification')->where($where)->find();
			$this->delUserToken($redis, $userdata['identification']);
			return $userinfo->where($where)->setField('devicetoken','');
		}
	}
	

	/**
	 *	
	 *  添加redis中的user的相关信息
	 *	@author zzq
	 *  @date 2018-07-25
	 *	@param  $redis object redis对象 
	 *	@param  $uid int redis中用户id
	 *	@return  
	 */		
	public function addUserToken($redis, $uid, $token)
	{
		$script =<<< delimiter

			local uid = redis.call('HGET', KEYS[2], ARGV[2])
			
			if uid ~= nil and uid ~= ARGV[1] then
				redis.call('HDEL', KEYS[1], uid)
			end

			redis.call('HDEL', KEYS[2], ARGV[2])
			redis.call('HSET', KEYS[1], ARGV[1], ARGV[2])
			redis.call('HSET', KEYS[2], ARGV[2], ARGV[1])
delimiter;

		$args = Array("user:token", "token:user", $uid, strval($token));
		return $redis->eval($script, $args, (int)2);
	}

	/**
	 *	
	 *  删除redis中的user的相关信息
	 *	@author zzq
	 *  @date 2018-07-25
	 *	@param  $redis object redis对象 
	 *	@param  $uid int |redis中用户id
	 *	@return  
	 */	
	public function delUserToken($redis, $uid)
	{
		$script =<<< delimiter

			if redis.call('HEXISTS', KEYS[1], ARGV[1]) == 1 then
				local token = redis.call('HGET', KEYS[1], ARGV[1])
				redis.call('HDEL', KEYS[2], token)
				redis.call('HDEL', KEYS[1], ARGV[1])
			end
delimiter;
	
		$args = Array("user:token", "token:user", $uid);
		return $redis->eval($script, $args, (int)2);
	}	
}