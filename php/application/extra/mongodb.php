<?php
	/**
	 *	存放mongodb的配置参数
	 *	
	 *
	 */
	return [

		//'Mongodb_server'=> '127.0.0.1',
		'Mongodb_server'=> '192.168.1.3',
		'Mongodb_db'	=> 'talkdb',
		'Mongodb_user' 	=> '',
		'Mongodb_pwd'	=> '',
		'Mongodb_port'	=> '27017',
		'MongoDB_useDB' => 'talkdb',
		'MongoDB_useDB1'=> 'interactive',
		'MongoDB_useDB2'=> 'convertLog',
		'equipment_dbname'=>'local',
		'equipment_collection'=>'equipment',
		'networkequipment_dbname'=>'local',
		'networkequipment_collection'=>'networkequipment',
		'select_time' => 120,//查询数据的时间段,暂时设为120s

	] ;



?>