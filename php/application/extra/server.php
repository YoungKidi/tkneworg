<?php
/**
 * server 配置  服务器模块
 * author WangChen
 * Date: 2018/8/8 
 */

return array(
	//更新文件上传 文件类型
	'updatefile_filetype' => array(
		'0' => 'IM',
		'1' => 'PC ',
		'2' => 'Android pad',
		'3' => 'IOS pad',
		'4' => 'Android mobile platform',
		'5' => 'IOS mobile platform',
		'6' => 'tv盒子',
		'7' => '新学问pc',
		'8' => '新学问tv',
	),

	//模板管理=>添加模板=>适用教室
	'template_roomType' => array(
		'0' => '小班课1对1',
		'3' => '小班课1对多',
		'10' => '大班课',
	),

	//模板管理=>添加皮肤=>适用终端 
	'skin_clientType' => array(
		'1' => 'PC',
		'2' => '手机端',
		'3' => '平板端',
	), 
);