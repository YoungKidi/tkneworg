<?php
	/**
	 *	存放redis的配置参数
	 *	
	 *
	 */
	return [
		'redis_host'=>'192.168.1.3', //redis地址
		'redis_port'=>'6379', //redis端口
		'redis_pwd'=>'',//redis密码
		'redis_timeout'=>5,//redis连接超时时间
        'admin_login_prefix' => 'admin_login:',//admin登录生成的rediskey前缀
        'admin_login_life_time' => 86400, //admin登录生成的rediskey生存时间
	] ;


