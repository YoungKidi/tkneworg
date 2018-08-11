<?php
$ZhaoZQ = [];
/************************************************实时在线*******************************************************/
$ZhaoZQ[] = array(
    			'url'=>'/admin/Systemset/getOnlineCompanyList',
    			'name'=>'监课统计=>实时在线=>企业并发=>在线机构列表',
    			'type'=>'POST',
    			'data'=>"{'pagenum':'1','companykeyword':'','roomtype':'3'}",
    			'tip'=>"{'pagenum':'页码数','companykeyword':'机构名称或者ID,默认为空','roomtype':'教室类型(1表示小班课[一对一小班课和一对多小班课] 2表示大班课 3表示不限'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        'data': [
								            {
								                'companyfullname': '机构全称',
								                'seconddomain': '机构域名',
								                'companyid': '机构ID',
								                'roomnum': '在线课堂数',
								                'usernum': '在线人数'
								            }
								        ],
								        'pageinfo': {
								            'pagesize': '每页显示数目,默认20',
								            'pagenum': '页码数',
								            'count': '符合条件的总条数'
								        },
								        'totalRoomNum': '当前在线课堂数',
								        'totalUserNum': '当前在线人数'
								    },
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"


);
$ZhaoZQ[] = array(
    			'url'=>'/admin/Systemset/getOnlineRoomListByCom',
    			'name'=>'监课统计=>实时在线=>企业并发=>某机构在线教室列表',
    			'type'=>'POST',
    			'data'=>"{'pagenum':'1','companyid':'10618','roomkeyword':''}",
    			'tip'=>"{'pagenum':'页码数','companyid':'机构ID,必填','roomtype':'教室名称或者教室id(搜索)'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        'data': [
								            {
								                'companyfullname': '机构全称',
								                'roomname': '教室名称',
								                'companyid': '机构ID',
								                'roomtype': '教室类型ID',
								                'roomnum': '教室编号',
								                'usernum': '教室人数',
								                'roomtypename': '教室类型名称'
								            }
								        ],
								        'pageinfo': {
								            'pagesize': '每页显示数目,默认20',
								            'pagenum': '页码数',
								            'count': '符合条件的总条数',
								            'companyfullname': '机构全称'
								        },
								    },
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"


);
$ZhaoZQ[] = array(
    			'url'=>'/admin/Systemset/getOnlineRoomList',
    			'name'=>'监课统计=>实时在线=>在线教室=>在线教室列表',
    			'type'=>'POST',
    			'data'=>"{'pagenum':'1','companyname':'','roomname':''}",
    			'tip'=>"{'pagenum':'页码数','companyname':'机构名称,默认为空','roomtype':'教室名称,默认为空'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        'data': [
								            {
								                'companyfullname': '机构名称',
								                'roomname': '教室名称',
								                'companyid': '机构ID',
								                'roomtype': '教室类型ID',
								                'roomnum': '教室编号',
								                'usernum': '教室人数',
								                'roomtypename': '教师类型名称'
								            }
								        ],
								        'pageinfo': {
								            'pagesize': '每页显示数目,默认20',
								            'pagenum': '页码数',
								            'count': '符合条件的总条数'
								        },
								    },    
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"


);
$ZhaoZQ[] = array(
    			'url'=>'/admin/Systemset/getGuardOnlineUserList',
    			'name'=>'监课统计=>实时在线=>实时告警人员列表',
    			'type'=>'POST',
    			'data'=>"{'pagenum':'1','companyname':'','roomname':''}",
    			'tip'=>"{'pagenum':'页码数','companyname':'机构名称,默认为空','roomtype':'教室名称,默认为空'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        'data': [
								            {
								                'buddyid': '用户id',
								                'identification': 'redis中的用户id',
								                'serial': '教室id',
								                'companyid': '机构id',
								                'usertype': '用户类型id',
								                'roomtype': '教室类型id',
								                'userid': '用户id',
								                'username': '用户名',
								                'entertime': '登录时间',
								                'outtime': '退出时间',
								                'companyfullname': '机构名称',
								                'roomname': '教室名称',
								                'userrolename': '用户角色名称'
								            }
								        ],
								        'pageinfo': {
								            'pagesize': '每页显示数目,默认20',
								            'pagenum': '页码数',
								            'count': '符合条件的总条数'
								        },
								    },    
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"


);
$ZhaoZQ[] = array(
    			'url'=>'/admin/Systemset/getOnlineRoomDetail',
    			'name'=>'监课统计=>实时在线=>在线教室详情信息',
    			'type'=>'POST',
    			'data'=>"{'companyid':'10618','roomid':'1062751433'}",
    			'tip'=>"{'comapnyid':'机构id','roomid':'教室ID必填'}",
    			'returns'=>"{
							    'code': '成功的时候返回0,失败或者异常返回其他',
							    'data': {
							    	'companyid': '机构id',
							        'roomname': '教室名称',
							        'serial': '教室ID',
							        'roomtypestr': '教室类型名称',
							        'starttime': '开始时间',
							        'teachername': '教师名',
							        'studentcount': '学生数量'
							    },
							    'info': '成功的时候返回操作成功,失败或者异常返回其他'
						}"
);
$ZhaoZQ[] = array(
    			'url'=>'/admin/Systemset/getOnlineRoomRecordList',
    			'name'=>'监课统计=>实时在线=>教室离在线教人员统计列表',
    			'type'=>'POST',
    			'data'=>"{'companyid':'10618','roomid':'1062751433','roomtype':'3','pagenum':'1'}",
    			'tip'=>"{'companyid':'机构ID','roomid':'教室ID必填','roomtype':'教室类型','pagenum':'当前页码数'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        'data': [
								            {
								                'userid': '用户的userid',
								                'identification': 'redis中的用户id',
								                'entertime': '进入教室时间',
								                'outtime': '离开教室时间(在线用户为空)',
								                'userroleid': '用户的角色ID',
								                'serial': '教室编号',
								                'username': '用户名称',
								                'devicetype': '设备类型',
								                'version': '浏览器版本',
								                'deviceName': '设备名称',
								                'ip':'IP地址',
								                'OSVersion': 'OS版本',
								                'systemversion': '系统版本',
								                'cpuOccupancy': 'cpu占用率(%)',
								                'upNetworkVideoQuality': '该用户的网络上行视频质量分数',
								                'upNetworkAudioQuality': '该用户的网络上行音频质量分数',
								                'upNetworkQuality': '该用户的网络上行信息',
								                'GuardNetwork': '网络警报信息',
								                'GuardNetworkArr': '该用户的情况参数集合',
								                'rolename': '用户的角色名称',
								                'onlinestatus': '离在线状态',
								                'gaptime': '在线时间，当一直在线的时候为0,格式为x年x日x小时x分x秒'

								            }
								        ],
								        'pageinfo': {
								            'pagesize': '每页显示数目,默认20',
								            'pagenum': '页码数',
								            'count': '符合条件的总条数'
								        }
								    },   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"

);
$ZhaoZQ[] = array(
    			'url'=>'/admin/Systemset/getOnlineRoomRecordDetail',
    			'name'=>'监课统计=>实时在线=>在线教室在线人员的详细信息',
    			'type'=>'POST',
    			'data'=>"{'companyid':'10618','roomid':'1062751433','roomtype':'3','userid':'9840287b-5edc-72bf-de8a-5073312bc016'}",
    			'tip'=>"{'companyid':'机构id','roomid':'教室id','roomtype':'该教室的教室类型传0表示1对1,传3表示1对多,传10表示直播课','userid':'在线人员的id'}",
    			'returns'=>"{
							    'code': '成功的时候返回0,失败或者异常返回其他',
							    'data': {
					                'usertype': '用户角色id',
					                'usertypename': '用户角色名称',
					                'devicetype': '设备类型',
					                'version': '浏览器版本',
					                'deviceName': '设备类型名称',
					                'ip': 'ip地址',
					                'systemversion': '系统版本',
					                'OSVersion': 'OS版本',
					                'sdkVersion': 'sdk版本',
					                'cpuArchitecture': 'CPU架构',
					                'companyid': '机构id',
					                'serial': '教室id',
					                'userid': '用户id',
					                'username': '用户名'
							    },
							    'info': '成功的时候返回操作成功,失败或者异常返回其他'
						}"
);

$ZhaoZQ[] = array(
    			'url'=>'/admin/Systemset/getUpNetworkByOnOrOffline',
    			'name'=>'监课统计=>实时在线=>某机构在线教室在线人员网络上行情况',
    			'type'=>'POST',
    			'data'=>"{'companyid':'10618','roomid':'1062751433','roomtype':'3','userid':'9840287b-5edc-72bf-de8a-5073312bc016'}",
    			'tip'=>"{'companyid':'机构ID','roomid':'教室ID必填','roomtype':'教室类型','userid':'用户id'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        'data': [
								            {
								                'datetime': '日期时间',
								                'cpuOccupancy': 'cpu大小',
								                'video': {
								                    'bitsPerSecond': '视频速率',
								                    'packetsLost': '视频丢包率',
								                    'currentDelay': '视频延迟',
								                    'netquality': '视频质量'
								                },
								                'audio': {
								                    'bitsPerSecond': '音频速率',
								                    'packetsLost': '音频丢包率',
								                    'currentDelay': '音频延迟',
								                    'netquality': '音频质量'
								                }
								            }
								        ],
								        'companyid': '机构id',
								        'serial': '教室id',
								        'userid': '用户id',
								        'NowCpuOccupancy': '此时的cpu情况'
								    },   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"

);
$ZhaoZQ[] = array(
    			'url'=>'/admin/Systemset/getDownNetworkByOnOrOffline',
    			'name'=>'监课统计=>实时在线=>某机构在线教室在线人员网络下行情况',
    			'type'=>'POST',
    			'data'=>"{'companyid':'10618','roomid':'1062751433','roomtype':'3','userid':'9840287b-5edc-72bf-de8a-5073312bc016'}",
    			'tip'=>"{'companyid':'机构ID','roomid':'教室ID必填','roomtype':'教室类型','userid':'用户id'}",
    			'returns'=>"{
						    'code': '成功的时候返回0,失败或者异常返回其他',
						    'data': [
						        [
						            {
						                'datetime': '日期时间',
						                'userid': '用户id',
						                'usertype': '用户角色id',
						                'usertypename': '用户角色名称',
						                'username': '用户名',
						                'cpuOccupancy': 'cpu大小',
						                'video': {
								                    'bitsPerSecond': '视频速率',
								                    'packetsLost': '视频丢包率',
								                    'currentDelay': '视频延迟',
								                    'netquality': '视频质量'
						                },
						                'audio': {
								                    'bitsPerSecond': '音频速率',
								                    'packetsLost': '音频丢包率',
								                    'currentDelay': '音频延迟',
								                    'netquality': '音频质量'
						                }
						            }
						        ]
						    ],
						    'info': '成功的时候返回操作成功,失败或者异常返回其他'
						}"

);

/************************************************实时在线*******************************************************/
$ZhaoZQ[] = array(
    			'url'=>'/admin/Systemset/getAnalysisChart',
    			'name'=>'监课统计=>统计查询=>图表',
    			'type'=>'POST',
    			'data'=>"{'sdate':'2014-01-04','edate':'2018-01-04','companykeyword':'','showtype':'1','roomtype':'0,3,10'}",
    			'tip'=>"{'sdate':'开始日期,格式如2014-01-04','edate':'结束日期,格式如2018-07-04,当开始日期结束日期都为空的时候默认查当前时间往前30天的数据','companykeyword':'机构名称或者ID，精准匹配，否则无数据,为空的时候表示所有机构的','showtype':'显示类型 1指课堂数 2指人数','roomtype':'拼接字符串如0,3,10。其中 0=>1对1  3=> 1对多 10 => 直播课'}",
    			'returns'=>"{
							    'code': '成功的时候返回0,失败或者异常返回其他',
							    'data': {
							        'data': [
							            {
							                'historydate': '日期,格式如2018-03-30',
							                'onotoone_roomnum': '1对1课堂数(多选框勾选且showtype=1时有)',
							                'onotomore_roomnum': '1对多课堂数(多选框勾选且showtype=1时有)',
							                'live_roomnum': '直播课堂数(多选框勾选且showtype=1时有)',
							                'onotoone_usernum': '1对1人数(多选框勾选且showtype=2时有)',
							                'onotomore_usernum': '1对多人数(多选框勾选且showtype=2时有)',
							                'live_usernum': '直播课人数(多选框勾选且showtype=2时有)'
							            }
							        ]
							    },
							    'info': '成功的时候返回操作成功,失败或者异常返回其他'
							}"

);
/*
$ZhaoZQ[] = array(
    			'url'=>'/admin/Systemset/getAnalysisList',
    			'name'=>'监课统计=>统计查询=>列表',
    			'type'=>'POST',
    			'data'=>"{'sdate':'2014-01-04','edate':'2018-01-04','companykeyword':'','pagenum':'1'}",
    			'tip'=>"{'sdate':'开始日期,格式如2014-01-04','edate':'结束日期,格式如2018-07-04,当开始日期结束日期都为空的时候默认查当前时间往前30天的数据','companykeyword':'机构名称或者ID，精准匹配，否则无数据,为空的时候表示所有机构的','pagenum':'当前页码数'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        'data': [
								            {
								                'historydate': '日期,格式如2018-03-30',
								                'onotoone_usernum': '1对1人数',
								                'onotoone_roomnum': '1对1课堂数',
								                'onotomore_usernum': '1对多人数',
								                'onotomore_roomnum': '1对多课堂数',
								                'live_usernum': '直播课人数',
								                'live_roomnum': '直播课堂数',
								                'companyfullname':'机构名称'
								            }
								        ],
								        'pageinfo': {
								            'pagesize': '每页显示数目,默认20',
								            'pagenum': '页码数',
								            'count': '符合条件的总条数'
								        }
								    },   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"

);
*/
/*
$ZhaoZQ[] = array(
    			'url'=>'/admin/Systemset/excelGetAnalysisList',
    			'name'=>'监课统计=>统计查询=>Excel导出所有列表数据',
    			'type'=>'POST',
    			'data'=>"{'sdate':'2015-10-20','edate':'2018-07-20','companykeyword':''}",
    			'tip'=>"{'sdate':'开始日期,格式如2015-10-20','edate':'结束日期,格式如2018-07-20,当开始日期结束日期都为空的时候默认查当前时间往前30天的数据','companykeyword':'机构名称或者ID，精准匹配，否则无数据,为空的时候表示所有机构的'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        'data': {
								            'url': '生成的.xlsx文件的绝对地址'
								        }
								    },   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"

);
*/
$ZhaoZQ[] = array(
    			'url'=>'/admin/Systemset/getAnalysisSumByComAndDate',
    			'name'=>'监课统计=>统计查询=>查询全部(某)机构某天的课堂数,人数统计',
    			'type'=>'POST',
    			'data'=>"{'date':'2018-06-22','companyid':'0'}",
    			'tip'=>"{'date':'表示查询日期','companyid':'机构id,0表示全部机构'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        'data': {
									        'date': '日期',
									        'onotoone_roomnum': '1对1课堂数',
									        'onotoone_usernum': '1对1人数',
									        'onotomore_roomnum': '1对多课堂数',
									        'onotomore_usernum': '1对多人数',
									        'live_roomnum': '直播课课堂数',
									        'live_usernum': '直播课人数'
								        }
								    },   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"

);
$ZhaoZQ[] = array(
    			'url'=>'/admin/Systemset/getHisRoomListByComAndDate',
    			'name'=>'监课统计=>统计查询=>查询全部(某)机构某天的课堂列表',
    			'type'=>'POST',
    			'data'=>"{'date':'2018-06-22','companyid':'10500','roomid':'','roomtype':'','pagenum':'1'}",
    			'tip'=>"{'date':'表示查询日期','companyid':'机构id,0表示全部机构','roomid':'教室号(搜索关键字)','roomtype':'教室类型(搜索关键字)传空字符串表示全部,传0表示1对1,传3表示1对多,传10表示直播课','pagenum':'当前页码数默认为1'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        'data': [
								            {
								            	'certaindate': '日期',
								                'serial': '课堂编号',
								                'roomname': '课堂名称',
								                'roomtype': '课堂类型',
								                'starttime': '课堂开始时间',
								                'endtime': '课堂结束时间',
								                'companyfullname': '机构名称',
								                'roomtypename': '课堂类型名称',
								                'companyid': '机构id'
								            }
								        ],
								        'pageinfo': {
								            'pagesize': '每页显示数目,默认20',
								            'pagenum': '页码数',
								            'count': '符合条件的总条数'
								        }
								    },   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"

);

$ZhaoZQ[] = array(
    			'url'=>'/admin/Systemset/getHisRoomDetailByComAndDateAndRoom',
    			'name'=>'监课统计=>统计查询=>查询全部(某)机构某天的课堂的详情',
    			'type'=>'POST',
    			'data'=>"{'date':'2018-06-22','starttime':'2018-06-22 10:10:18','endtime':'2018-06-22 10:27:55', 'companyid':'10500','roomid':'462066812','roomtype':'0'}",
    			'tip'=>"{'date':'表示查询日期','starttime':'开始时间','endtime':'结束时间','companyid':'机构id,0表示全部机构','roomid':'教室号','roomtype':'传0表示1对1,传3表示1对多,传10表示直播课'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        'data': {
								                'serial': '教室编号',
								                'roomname': '教室名称',
								                'roomtype': '教室类型',
								                'starttime': '课堂开始时间',
								                'endtime': '课堂结束时间',
								                'companyfullname': '机构名称',
								                'roomtypename': '教室类型名称',
									            'companyid': '机构id',
										        'currency': '并发学生数'
								        }
								    },   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
);

$ZhaoZQ[] = array(
    			'url'=>'/admin/Systemset/getHisUserListByComAndDateAndRoom',
    			'name'=>'监课统计=>统计查询=>查询某机构某天的课堂人员进出列表',
    			'type'=>'POST',
    			'data'=>"{'date':'2018-06-22','starttime':'2018-06-22 10:10:18','endtime':'2018-06-22 10:27:55','companyid':'10500','roomid':'462066812','roomtype':'0','pagenum':'1'}",
    			'tip'=>"{'date':'表示查询日期','starttime':'开始时间','endtime':'结束时间','companyid':'机构id','roomid':'教室号','roomtype':'该教室的教室类型传0表示1对1,传3表示1对多,传10表示直播课','pagenum':'当前页码数默认为1'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        'data': [
								            {
								                'userid': '用户userid',
								                'roomtype': '房间类型id',
								                'serial': '房间编号',
								                'companyid': '机构id',
								                'userroleid': '用户角色id',
								                'entertime': '进入教室时间',
								                'outtime': '离开教室时间',
								                'starttime': '上课时间',
								                'outtime': '下课时间',								          
								                'devicetype': '设备类型',
								                'deviceName': '设备类型名称',
								                'ip': 'ip地址',
								                'username': '用户名称',
								                'devicetypename': '设备类型名称',
								                'userrolename': '用户角色名称',
								                'gaptime': '上课时长如x年x月x小时x分x秒',
								                'guardtimeArr': '数组,告警时间点的集合'
								            }
								        ],
								        'pageinfo': {
								            'pagesize': '每页显示数目,默认20',
								            'pagenum': '页码数',
								            'count': '符合条件的总条数'
								        }
								    },   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"

);



$ZhaoZQ[] = array(
    			'url'=>'/admin/Systemset/getHisUserDeviceByComDateRoomUser',
    			'name'=>'监课统计=>统计查询=>查询某机构某天的某课堂的某人员的详情信息',
    			'type'=>'POST',
    			'data'=>"{'date':'2018-06-22','companyid':'10500','starttime':'2018-06-22 10:10:18','endtime':'2018-06-22 10:27:55','entertime':'2018-06-22 10:11:27','outtime':'2018-06-22 10:15:56','roomid':'462066812','roomtype':'0','userid':'6cf002e0-fd1d-4a50-b0ce-3c57021654ed'}",
    			'tip'=>"{'date':'表示查询日期','companyid':'机构id','starttime':'开始时间','endtime':'结束时间','entertime':'进入时间','outtime':'离开时间','roomid':'教室号','roomtype':'该教室的教室类型传0表示1对1,传3表示1对多,传10表示直播课','userid':'用户id'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        'data': {
									        'userid': '用户id',
									        'roomtype': '房间类型id',
									        'serial': '房间号',
									        'companyid': '机构id',
									        'userroleid': '用户角色id',
									        'entertime': '进入时间',
									        'outtime': '退出时间',
									        'devicetype': 'iPad6,11',
									        'deviceName': 'iPad',
									        'ip': '192.168.1.216',
									        'systemversion': 'iOS 11.2.1',
									        'sdkVersion': 'TKRoomSDK-2.2.9',
									        'cpuArchitecture': 'ARM64',
									        'username': '登录用户名',
									        'userrolename': '用户角色',
									        'gaptime': '登录时长',
									        'starttime': '课堂开始时间',
									        'endtime': '课堂结束时间',
									        'roomname': '教室名称',
									        'roomtypename': '教室类型',
									        'lastingtime': '停留时间',
									        'classtime':'课时长度'

								        }
								    },   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
);

$ZhaoZQ[] = array(
    			'url'=>'/admin/Systemset/getHisUserUpNetworkByComDateRoomUser',
    			'name'=>'监课统计=>统计查询=>查询某机构某天的某课堂的某人员的网络上行的情况',
    			'type'=>'POST',
    			'data'=>"{'date':'2018-06-22','companyid':'10500','starttime':'2018-06-22 10:10:18','endtime':'2018-06-22 10:27:55','entertime':'2018-06-22 10:11:27','outtime':'2018-06-22 10:15:56','roomid':'462066812','roomtype':'0','userid':'6cf002e0-fd1d-4a50-b0ce-3c57021654ed'}",
    			'tip'=>"{'date':'表示查询日期','companyid':'机构id','starttime':'开始时间','endtime':'结束时间','entertime':'进入时间','outtime':'离开时间','roomid':'教室号','roomtype':'该教室的教室类型传0表示1对1,传3表示1对多,传10表示直播课','userid':'用户id'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        'data': [
								            {
								                'datetime': '日期时间',
								                'cpuOccupancy': 'cpu大小',
								                'video': {
								                    'bitsPerSecond': '视频速率',
								                    'packetsLost': '视频丢包率',
								                    'currentDelay': '视频延迟',
								                    'netquality': '视频质量'
								                },
								                'audio': {
								                    'bitsPerSecond': '音频速率',
								                    'packetsLost': '音频丢包率',
								                    'currentDelay': '音频延迟',
								                    'netquality': '音频质量'
								                }
								            }
								        ],
								        'companyid': '机构id',
								        'serial': '教室id',
								        'userid': '用户id',
								        'NowCpuOccupancy': '此时的cpu情况'
								    }, 
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
);



$ZhaoZQ[] = array(
    			'url'=>'/admin/Systemset/getHisUserDownNetworkByComDateRoomUser',
    			'name'=>'监课统计=>统计查询=>查询某机构某天的某课堂的某人员的网络下行的情况',
    			'type'=>'POST',
    			'data'=>"{'date':'2018-06-22','companyid':'10500','starttime':'2018-06-22 10:10:18','endtime':'2018-06-22 10:27:55','entertime':'2018-06-22 10:11:27','outtime':'2018-06-22 10:15:56','roomid':'462066812','roomtype':'0','userid':'6cf002e0-fd1d-4a50-b0ce-3c57021654ed'}",
    			'tip'=>"{'date':'表示查询日期','companyid':'机构id','starttime':'开始时间','endtime':'结束时间','entertime':'进入时间','outtime':'离开时间','roomid':'教室号','roomtype':'该教室的教室类型传0表示1对1,传3表示1对多,传10表示直播课','userid':'用户id'}",
    			'returns'=>"{
						    'code': '成功的时候返回0,失败或者异常返回其他',
						    'data': [
						        [
						            {
						                'datetime': '日期时间',
						                'userid': '用户id',
						                'userroleid': '用户角色id',
						                'userrolename': '用户角色名称',
						                'username': '用户名',
						                'cpuOccupancy': 'cpu大小',
						                'video': {
								                    'bitsPerSecond': '视频速率',
								                    'packetsLost': '视频丢包率',
								                    'currentDelay': '视频延迟',
								                    'netquality': '视频质量'
						                },
						                'audio': {
								                    'bitsPerSecond': '音频速率',
								                    'packetsLost': '音频丢包率',
								                    'currentDelay': '音频延迟',
								                    'netquality': '音频质量'
						                }
						            }
						        ]
						    ],
						    'info': '成功的时候返回操作成功,失败或者异常返回其他'
						}"

);

$ZhaoZQ[] = array(
    			'url'=>'/admin/Mongo/insertEquipment',
    			'name'=>'监课统计=>导入人员设备数据',
    			'type'=>'POST',
    			'data'=>"{'dbname':'local','collection':'equipment','starttime':'2018-06-22 10:20:18','endtime':'2018-06-22 10:27:51', 'gaptime':'60','companyid':'10500','serial':'462066812','userid':'6cf002e0-fd1d-4a50-b0ce-3c57021654ed'}",
    			'tip'=>"{'dbname':'数据库名','collection':'文档名','starttime':'数据起始时间','endtime':'数据结束时间', 'gaptime':'间隔时间','companyid':'机构id','serial':'教室id','userid':'用户id'}",
    			'returns'=>"{
						    'code': '成功的时候返回0,失败或者异常返回其他',
						    'data': '插入成功的条数',
						    'info': '成功的时候返回操作成功,失败或者异常返回其他'
						}"

);

$ZhaoZQ[] = array(
    			'url'=>'/admin/Mongo/insertNetworkEquipment',
    			'name'=>'监课统计=>导入人员网络数据',
    			'type'=>'POST',
    			'data'=>"{'dbname':'local','collection':'networkequipment','starttime':'2018-06-22 10:20:18','endtime':'2018-06-22 10:27:51', 'gaptime':'60','companyid':'10500','serial':'462066812','userid':'6cf002e0-fd1d-4a50-b0ce-3c57021654ed','otheruserid':'3a7f106e-dea5-406d-a164-d39946fd88de'}",
    			'tip'=>"{'dbname':'数据库名','collection':'文档名','starttime':'数据起始时间','endtime':'数据结束时间', 'gaptime':'间隔时间','companyid':'机构id','serial':'教室id','userid':'用户id','otheruserid':'和userid相同表示该用户的上行网络，不同表示userid的下行网络'}",
    			'returns'=>"{
						    'code': '成功的时候返回0,失败或者异常返回其他',
						    'data': '插入成功的条数',
						    'info': '成功的时候返回操作成功,失败或者异常返回其他'
						}"

);



$ZhaoZQ[] = array(
    			'url'=>'/admin/Systemset/storage',
    			'name'=>'监课统计=>存储查询',
    			'type'=>'POST',
    			'data'=>"{'sdate':'2014-01','edate':'2018-01','companykeyword':'','pagenum':'1'}",
    			'tip'=>"{'sdate':'开始日期为年-月,格式如2014-01','edate':'结束日期为年-月,格式如2018-07,当开始日期结束日期都为空的时候默认查当前时间往前6个月的数据','companykeyword':'机构名称或者ID，模糊查询,为空的时候表示全部','pagenum':'当前页码数'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        'data': [
								            {
								                'companyid': '机构ID',
								                'datemonth': '日期格式如2018-03',
								                'filesize': '课件大小，如54.92',
								                'recordsize': '录制件大小,519.32',
								                'totalsize': '总计大小,如574.24',
								                'companyfullname': '机构名称,如侯韫举'
								            }
								        ],
								        'pageinfo': {
								            'pagesize': '每页显示数目,默认20',
								            'pagenum': '页码数',
								            'count': '符合条件的总条数'
								        },
								    },   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"

);
$ZhaoZQ[] = array(
    			'url'=>'/admin/Systemset/loginfolist',
    			'name'=>'监课统计=>日志查询',
    			'type'=>'POST',
    			'data'=>"{'stime':'2018-05-01','etime':'2018-11-01','infotype':'2','pagenum':'1','ip':'','pid':'','data':'','useraccount':'','userroleid':''}",
    			'tip'=>"{'stime':'开始日期为年-月-日,格式如2018-05-01(公共参数)','etime':'结束日期为年-月-日,格式如2018-07-01(公共参数)','infotype':'1表示操作日志2表示系统日志','pagenum':'当前页码数','ip':'infotype=2的时候可传参数','pid':'infotype=2的时候可传参数','data':'infotype=2的时候可传参数','useraccount':'用户名 infotype=1的时候可传参数','userroleid':'角色 infotype=1的时候可传参数'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        'data': [
								            {
								                'ip': 'ip地址',
								                'pid': 'pid的值',
								                'file': '文件路径',
								                'timestamp': '日期时间',
								                'data': '内容',
								                'level': {
								                    'level': '级别',
								                    'levelStr': '级别名称'
								                },
								                'category': '类型',
				                                'id': 'id(infotype=2)',
				                                'useraccount': '账户名(infotype=2)',
				                                'userroleid': '角色id(infotype=2)',
				                                'addtime': '日期时间(infotype=2)',
				                                'content': '操作内容(infotype=2)',
				                                'userid': '用户id(infotype=2)',
				                                'userrolename': '角色名(infotype=2)'
								            }
								        ],
								        'pageinfo': {
								            'pagesize': '每页显示数目,默认20',
								            'pagenum': '页码数',
											'infotype': '1代表操作日志2代表系统日志',
								            'count': '符合条件的总条数'
								        }
								    },   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"

);
/*
$ZhaoZQ[] = array(
    			'url'=>'/admin/Systemset/conversionlist',
    			'name'=>'监课统计=>转换查询',
    			'type'=>'POST',
    			'data'=>"{'sdate':'2014-01','edate':'2018-01','companyfullname':'','pagenum':'1','converttype':'1','recordstatus':'0','recordtype': 'filestatus':'1','filekeyword':''}",
    			'tip'=>"{'sdate':'开始日期为年-月-日,格式如2014-01-01(公共参数)','edate':'结束日期为年-月-日,格式如2018-07-01(公共参数),','companyfullname':'机构名称，模糊查询,为空的时候表示全部(公共参数)','pagenum':'当前页码数(公共参数)','converttype':'1表示录制件2表示课件(公共参数)','recordstatus':'录制件状态0代表正常1代表删除(converttype=1时填写)','recordtype':'0代表转换1代表超时2代表失败(converttype=1时填写)','filestatus':'课件状态0不可见1课件9删除(converttype=2时填写)','filekeyword':'课件编号或者名称(converttype=2时填写)'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        'data': [
								            {
								                'companyfullname': '机构名称(公共返回)',
								                'roomname': '房间名称(converttype=1返回)',
								                'recordid': '录制件ID(converttype=1返回)',
								                'serial': '房间ID(converttype=1返回)',
								                'companyid': '机构ID(公共返回)',
								                'recordtitle': '录制件标题(converttype=1返回)',
								                'starttime': '开始时间(converttype=1返回)',
								                'duration': '录制件时长(converttype=1返回)',
								                'state': '录制件状态(正常|删除)(converttype=1返回)',
								                'size': '录制件大小(公共返回)',
								                'recordname': '录制件名字(converttype=1返回)',
								                'recordfileurl': '录制件URL(converttype=1返回)',
								                'recordtype': '录制件类型(课堂录制件|直播录制件)(converttype=1返回)',
								                'fileid': '课件ID(converttype=2返回)',
								                'filename': '原课件名(converttype=2返回)',
								                'newfilename': '现课件名(converttype=2返回)',
								                'filetype': '课件类型(converttype=2返回)',
								                'status': '不可见(0)|可见(1)|删除(9)(converttype=2返回)',
								                'uploadtime': '上传时间(converttype=2返回)',
								                'isconvert':'转换类型,(0|1|2|3|4)(converttype=2返回)'
								            }
								        ],
								        'pageinfo': {
								            'pagesize': '每页显示数目,默认20',
								            'pagenum': '页码数',
								            'count': '符合条件的总条数'
								        },
								    },   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"

);*/
$ZhaoZQ[] = array(
    			'url'=>'/admin/Setup/addOrEditUserinfo',
    			'name'=>'设置=>权限管理=>添加或者编辑管理员',
    			'type'=>'POST',
    			'data'=>"{'companyid':'1','method':'1','userid':'0','sortid':'0','oldsortid':'0','userroleid':'12','account':'zs','firstname':'张伟','mobile':'15896547789','email':'12312@sina.com','userpwd':'123456','againpwd':'123456','description':'xx','logo':'1.jpg'}",
    			'tip'=>"{'companyid':'企业id,www域默认为1','method':'1表示添加2表示编辑','userid':'添加的时候填0','sortid':'目标排序id,添加的时候填0','oldsortid':'原有的排序id,添加的时候填0','userroleid':'角色id,4:巡检员(www)　12:管理员(www) 13销售(www) 14财务(www) 15销售主管(www)','account':'账户名','firstname':'姓名','mobile':'手机号','email':'邮箱','userpwd':'密码','againpwd':'重复密码','description':'描述','logo':'头像地址'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': '',   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"

);
$ZhaoZQ[] = array(
    			'url'=>'/admin/Setup/getCompanyUserList',
    			'name'=>'设置=>权限管理=>获取管理员列表',
    			'type'=>'POST',
    			'data'=>"{'companyid':'1','pagenum':'1','name':''}",
    			'tip'=>"{'companyid':'企业id,www域默认为1','pagenum':'当前页码','name':'姓名或者账号(搜索关键字)'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        'data': [
								            {
								                'userid': '用户id',
								                'sortid': '排序id',
								                'firstname': '姓名',
								                'userroleid': '角色ID',
								                'account': '账户名',
								                'roleName': '角色名称'
								            }
								        ],
								        'pageinfo': {
								            'pagesize': '每页显示数目,默认20',
								            'pagenum': '页码数',
								            'count': '符合条件的总条数'
								        },
								    },   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"

);
$ZhaoZQ[] = array(
    			'url'=>'/admin/Setup/getUserDetail',
    			'name'=>'设置=>权限管理=>获取管理员详情',
    			'type'=>'POST',
    			'data'=>"{'userid':'1','companyid':'1'}",
    			'tip'=>"{'userid':'用户id','companyid':'默认www域企业id是1'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        'userid': '用户id',
								        'companyid':'机构id',
								        'nickname': '昵称',
								        'identification': 'redis中唯一表示',
								        'firstname': '姓名',
								        'account': '账户名',
								        'mobile': '手机号',
								        'email': '邮箱',
								        'userico': '头像',
								        'description': '描述信息',
								        'companyid':'企业id,www域是1',
								        'userroleid':'0：主讲  1：助教    2: 学员   3：直播用户 4:巡检员　10:系统超级管理员　11:企业超级管理员　12:企业域管理员 13销售 14财务 15销售主管 16系统普通管理员 17企业域的巡检员',
								        'userrolename':'角色名称'
								    },   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"

);
$ZhaoZQ[] = array(
    			'url'=>'/admin/Setup/delUser',
    			'name'=>'设置=>权限管理=>删除管理员',
    			'type'=>'POST',
    			'data'=>"{'userid':'1','companyid':'1'}",
    			'tip'=>"{'userid':'用户id','companyid':'企业id,www域默认为1'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': '',   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"

);
$ZhaoZQ[] = array(
    			'url'=>'/admin/Setup/getBindOrUnbindCompoanyList',
    			'name'=>'设置=>权限管理=>获取某销售关联和未关联的机构列表',
    			'type'=>'POST',
    			'data'=>"{'marketid':'102111','companyid':'1','pagenum':'1','companykeyword':'','bindtype':'1'}",
    			'tip'=>"{'marketid':'销售的用户id','companyid':'企业id,www域默认为1','pagenum':'当前页码','companykeyword':'企业的名称或者id','bindtype':'1表示已经关联的,2表示没有关联的'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        'data': [
								            {
								                'id': '主键id',
								                'marketid': '销售id',
								                'companyid': '企业id',
								                'companyfullname': '企业全称',
								                'companystate': '企业状态'
								            }
								        ],
								        'pageinfo': {
								            'pagesize': '每页显示数目,默认20',
								            'pagenum': '页码数',
								            'count': '符合条件的总条数'
								        },
								    },   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"

);
$ZhaoZQ[] = array(
    			'url'=>'/admin/Setup/bindCompany',
    			'name'=>'设置=>权限管理=>销售与企业关联',
    			'type'=>'POST',
    			'data'=>"{'marketid':'102111','companyid':'10612'}",
    			'tip'=>"{'marketid':'销售id','companyid':'企业id'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': '',   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
);
$ZhaoZQ[] = array(
    			'url'=>'/admin/Setup/batchUnbindCompany',
    			'name'=>'设置=>权限管理=>销售与企业取消关联(支持批量操作)',
    			'type'=>'POST',
    			'data'=>"{'marketid':'102111','companyids':'10612,10613'}",
    			'tip'=>"{'marketid':'销售id','companyids':'企业id的集合,如果是多个用英文分号分割开'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': '',   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
);
$ZhaoZQ[] = array(
    			'url'=>'/admin/Setup/getBindOrUnbindSaleManagerList',
    			'name'=>'设置=>权限管理=>获取某销售主管所关联(未关联)的销售列表',
    			'type'=>'POST',
    			'data'=>"{'bindtype':'1','marketleaderid':'102120','companyid':'1'}",
    			'tip'=>"{'bindtype':'1表示已经关联的,2表示没有关联的','marketleaderid':'销售主管的用户id','companyid':'企业id,www域默认为1'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': [
								        {
								            'userid': '用户id',
								            'sortid': '排序id',
								            'firstname': '姓名',
								            'userroleid': '角色id',
								            'account': '账户名'
								        },
								    ], 
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"

);
$ZhaoZQ[] = array(
    			'url'=>'/admin/Setup/bindOrUnbindSaleManager',
    			'name'=>'设置=>权限管理=>销售主管与销售关联(取消关联)(支持批量操作)',
    			'type'=>'POST',
    			'data'=>"{'marketleaderid':'102120','bindmarketids':'101932,101956','unbindmarketids':'101957,101959','companyid':'1'}",
    			'tip'=>"{'marketleaderid':'销售主管id','bindmarketids':'需要绑定的销售人员id的集合,如果是多个用英文分号分割开','unbindmarketids':'需要解绑的销售人员id的集合,如果是多个用英文分号分割开','companyid':'企业id,www域默认为1'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': '',   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
);
