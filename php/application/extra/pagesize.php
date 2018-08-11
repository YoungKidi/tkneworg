<?php
	/**
	 *	存放分页信息，每个需要分页的页面单独配置，并注明url 方便后期直接修改，无需查改代码
	 *	命名方式以 分组名_方法名缩略 保证唯一即可 后面备注 统一资源定位符 仅需在 前端查看后 在此处匹配修改即可
	 *
	 */
	return [
		'admin_roomlist' => 20 ,// URL: /admin/Roommanage/getRoomList
		'admin_conversionlist' => 20, // URL: /admin/Systemsetmanage/conversionlist(录制件)
		'admin_loginfolist' => 20, // URL: /admin/Systemsetmanage/loginfolist(日志)
		'admin_storagelist' => 20, // URL: /admin/Systemsetmanage/storage(存储)
		'admin_onlinecompanylist' => 20, // URL: /admin/Systemsetmanage/storage(实时在线的企业机构列表)
		'admin_onlineroomlistbycom' => 20, // URL: /admin/Systemsetmanage/getOnlineRoomListByCom(实时在线机构->某个机构          教室列表)
		'admin_onlineroomlist' => 20, // URL: /admin/Systemsetmanage/getOnlineRoomList(实时在线机构->教室列表)
		'admin_onlineuserlist' => 20, // URL: /admin/Systemsetmanage/getOnlineRoomDetail(实时在线机构->获取在线教室的人员记录)
		'admin_onlineguarduserlist' => 20, // URL: /admin/Systemsetmanage/getGuardOnlineUserList(实时在线机构->获取在线预警人员列表)


		'admin_analysislist' => 20, // URL: /admin/Systemsetmanage/getAnalysisList(统计查询->统计课堂数和上线人数列表)
		'admin_roomlistbycomanddate' => 20, // URL: /admin/Systemsetmanage/getHisRoomListByComAndDate(统计查询->统计某机构某天的课堂列表)
		'admin_roomuserlistbycomanddate' => 20, // URL: /admin/Systemsetmanage/getHisUserListByComAndDateAndRoom(统计查询->统计某机构某天的课堂的进出人员列表)




		'admin_companyuserlist' => 20, // URL: /admin/Setup/getCompanyUserList(管理员列表)
		'admin_marketcompanylist' => 20, // URL: /admin/Setup/getBindOrUnbindCompoanyList(获取销售绑定或者未绑定的企业列表)
		'admin_marketbindedlist' => 20, // URL: /admin/Setup/getBindOrUnbindSaleManagerList(获取销售主管绑定或者未绑定的销售列表)
        'admin_companylist'=>20, //URL: /admin/Company/getCompanyConfig   (企业列表)
        'admin_uploadfilelist'=>20, //URL: /admin/Servermanage/getUpdatefilePage   (更新文件列表)


        'enterprise_roomlist'=>20, //URL: /enterpris/RoomManage/getRoomPage (会议列表)
	] ;



?>