<?php
$WangC = [];
$WangC[] = array(
    			'url'=>'/admin/Server/getSmtpinfo',
    			'name'=>'服务器管理=>SMTP服务器设置=>SMTP服务器信息',
    			'type'=>'POST',
    			'data'=>"{'companyid':'1'}",
    			'tip'=>"{'companyid':'公司ID www域下默认为1'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        'companyid': '公司ID',
								        'smtpserver': 'SMTP服务器端口',
								        'smtpusername': 'SMTP用户名',
								        'smtppassword': 'SMTP密码',
								        'isssl': '通过ssl协议发送邮件（0-否,1-是）',
								    },   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
);
$WangC[] = array(
    			'url'=>'/admin/Server/updateSmtpinfo',
    			'name'=>'服务器管理=>SMTP服务器设置=>修改SMTP服务器设置',
    			'type'=>'POST',
    			'data'=>"{'companyid':'1','smtpserver':'xx@xx.com','smtpport':'456','smtpusername':'root','smtppassword':'root','isssl':'1'}",
    			'tip'=>"{'companyid':'公司ID www域下默认为1','smtpserver':'SMTP服务器','smtpport':'SMTP服务器端口','smtpusername':'SMTP用户名','smtppassword':'SMTP密码','isssl':'通过ssl协议发送邮件（0-否,1-是）'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {},
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
);
$WangC[] = array(
    			'url'=>'/admin/Server/getUpdatefileList',
    			'name'=>'服务器管理=>更新文件管理=>文件列表',
    			'type'=>'POST',
    			'data'=>"{'page':'1','companyid':'1'}",
    			'tip'=>"{'page':'访问的页面 默认为1','companyid':'公司ID www域下默认为1'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								    		'pageinfo': {
                                                    'sum_data': '总数据数',
                                                    'sum_page': '总页数',
                                                    'prev_page': '上一页',
                                                    'next_page': '下一页',
                                                    'now_page': '当前页',
                                                    'size': '每页数据量'
                                                },
                                            'data': [
                                                        {
                                                            'id': '文件编号',
                                                            'filename': '文件名称',
                                                            'filesize': '文件大小',
                                                            'filedate': '文件打包日期',
                                                            'isupdate': '操作类别 0安装包 1升级包',
                                                            'uploadtime': '上传时间'
                                                        }
                                                    ]
								    	},
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
);
$WangC[] = array(
    			'url'=>'/admin/Server/getUpdatefileInfo',
    			'name'=>'服务器管理=>更新文件管理=>文件详细信息',
    			'type'=>'POST',
    			'data'=>"{'fileid':'1'}",
    			'tip'=>"{'fileid':'文件ID'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        'id': '文件ID',
								        'version': '文件名称',
								        'filename': 'SMTP用户名',
								        'filetype': '文件类型（0:IM   1:PC Conference 2:Android pad 3:ios pad  4:Android mobile platform   5:IOS mobile platform 6:tv盒子 7:新学问pc 8:新学问tv）',
								        'isupdate': '操作类型（1：升级包   0：安装包）',
								        'filedate': '文件时间',
						                'filesize': '文件大小',
						                'uploadtime': '更新时间',
						                'updateflag': '升级标志(0，不升级，1，强制升级，2不强制)',
						                'companyname': '公司名称'
								    },   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
);
$WangC[] = array(
                'url'=>'/admin/Server/getUpdateFiletype',
                'name'=>'服务器管理=>更新文件管理=>添加更新文件=>获取文件类别',
                'type'=>'POST',
                'data'=>"",
                'tip'=>"",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {
                                        'IM',
                                        'PC ',
                                        'Android pad',
                                        'IOS pad',
                                        'Android mobile platform',
                                        'IOS mobile platform',
                                        'tv盒子',
                                        '新学问pc',
                                        '新学问tv'
                                        },
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                                }"
);
$WangC[] = array(
    			'url'=>'/admin/Server/setUpdatefileAdd',
    			'name'=>'服务器管理=>更新文件管理=>添加更新文件',
    			'type'=>'POST',
    			'data'=>"{'filetype':'1','isupdate':'1','updateflag':'1','version':'2.2.2.2','companyname':'拓课','companyid':'1','uploadFile':'XXX.jpg'}",
    			'tip'=>"{'filetype':'文件类型（0:IM   1:PC Conference 2:Android pad 3:ios pad  4:Android mobile platform   5:IOS mobile platform 6:tv盒子 7:新学问pc 8:新学问tv）','isupdate':'操作类型（1：升级包   0：安装包）','updateflag':'升级标志(0，不升级，1，强制升级，2不强制)','version':'客户端版本','companyname':'公司名称','companyid':'公司ID','uploadFile':'上传的文件'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {},
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
);
$WangC[] = array(
    			'url'=>'/admin/Server/setUpdatefileUpdate',
    			'name'=>'服务器管理=>更新文件管理=>编辑更新文件信息',
    			'type'=>'POST',
    			'data'=>"{'id':'1','filetype':'1','isupdate':'1','updateflag':'1','version':'2.2.2.2','companyname':'拓课','companyid':'1'}",
    			'tip'=>"{'id':'文件ID','filetype':'文件类型（0:IM   1:PC Conference 2:Android pad 3:ios pad  4:Android mobile platform   5:IOS mobile platform 6:tv盒子 7:新学问pc 8:新学问tv）','isupdate':'操作类型（1：升级包   0：安装包）','updateflag':'升级标志(0，不升级，1，强制升级，2不强制)','version':'客户端版本','companyname':'公司名称','companyid':'公司ID'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {},
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
);
$WangC[] = array(
    			'url'=>'/admin/Server/setUpdatefileDel',
    			'name'=>'服务器管理=>更新文件管理=>删除文件信息',
    			'type'=>'POST',
    			'data'=>"{'id':'15'}",
    			'tip'=>"{'id':'文件ID'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {},
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
);
$WangC[] = array(
    			'url'=>'/admin/Server/getTemplateSkinList',
    			'name'=>'服务器管理=>模板管理=>模板皮肤列表',
    			'type'=>'POST',
    			'data'=>"{'companyid':'1'}",
    			'tip'=>"{'companyid':'企业ID,默认为当前登陆企业'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {								     
						                'name': '模板名称',
						                'id': '模板ID',
						                'sign': '模板标识',
						                'roomType': [
						                	'房间类型 （0:一对一 3:一对多 10:大班课）'
						                ],
						                'skin': [{
						                	'name': '皮肤名称',
							                'id': '皮肤ID',
							                'sign': '皮肤标识',
							                'clientType':'适用终端（1 PC 2Android3.IOS）(单选)'
						                }],
								    },
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
);
$WangC[] = array(
                'url'=>'/admin/Server/getTemplateList',
                'name'=>'服务器管理=>模板管理=>模板列表',
                'type'=>'POST',
                'data'=>"",
                'tip'=>"",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {
                                        'id': '模板ID',
                                        'name': '模板名称',
                                    },   
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                                }"
);
$WangC[] = array(
    			'url'=>'/admin/Server/setTemplateAdd',
    			'name'=>'服务器管理=>模板管理=>添加模板',
    			'type'=>'POST',
    			'data'=>"{'name':'经典','sign':'classic','roomType':[0,3]}",
    			'tip'=>"{'name':'模板名称','sign':'模板标识','roomType':'房间类型（checkbox数组提交）（0:一对一 3:一对多 10:大班课）'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {},
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
);
$WangC[] = array(
    			'url'=>'/admin/Server/setTemplateUpdate',
    			'name'=>'服务器管理=>模板管理=>修改模板',
    			'type'=>'POST',
    			'data'=>"{'id':'1','name':'经典','sign':'classic'}",
    			'tip'=>"{'id':'模板ID','name':'模板名称','sign':'模板标识'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {},
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
);
$WangC[] = array(
    			'url'=>'/admin/Server/setSkinFileAdd',
    			'name'=>'服务器管理=>模板管理=>添加皮肤=>文件上传',
    			'type'=>'POST',
    			'data'=>"{'uploadFile':'XXX.jpg'}",
    			'tip'=>"{'uploadFile':'上传的文件信息'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {'resource':'文件资源路径'},
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
);
$WangC[] = array(
                'url'=>'/admin/Server/getTemplateRoomType',
                'name'=>'服务器管理=>模板管理=>添加模板=>适用教室',
                'type'=>'POST',
                'data'=>"",
                'tip'=>"",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {
                                        '0': '小班课1对1',
                                        '3': '小班课1对多',
                                        '10': '大班课'
                                        },
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                                }"
);
$WangC[] = array(
                'url'=>'/admin/Server/getSkinClientType',
                'name'=>'服务器管理=>模板管理=>添加皮肤=>适用终端',
                'type'=>'POST',
                'data'=>"",
                'tip'=>"",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {
                                        '1': 'PC',
                                        '2': '手机端',
                                        '3': '平板端'
                                        },
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                                }"
);
$WangC[] = array(
    			'url'=>'/admin/Server/setSkinAdd',
    			'name'=>'服务器管理=>模板管理=>添加皮肤',
    			'type'=>'POST',
    			'data'=>"{'name':'黑色严肃','sign':'black','clientType':'1','tplId':'1','type':'1','companyId':'1','resource':'http://51menke-1253417915.cosgz.myqcloud.com/skin_resource/1/server/1.jpg'}",
    			'tip'=>"{'name':'皮肤名称','sign':'皮肤标识','clientType':'适用终端（1 PC 2Android3.IOS）','tplId':'所属模板ID','type':'所属类型（1 代表公用 2代表私有）','companyId':'企业ID（只有私有时才可以提交）','resource':'文件资源地址'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {},
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
);
$WangC[] = array(
    			'url'=>'/admin/Server/setSkinUpdate',
    			'name'=>'服务器管理=>模板管理=>编辑皮肤',
    			'type'=>'POST',
    			'data'=>"{'id':'1','name':'黑色严肃','sign':'black'}",
    			'tip'=>"{'id':'皮肤ID','name':'皮肤名称','sign':'皮肤标识'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {},
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
);
$WangC[] = array(
    			'url'=>'/admin/Server/setTemplateDel',
    			'name'=>'服务器管理=>模板管理=>删除模板',
    			'type'=>'POST',
    			'data'=>"{'id':'15'}",
    			'tip'=>"{'id':'模板ID'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {},
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
);
$WangC[] = array(
    			'url'=>'/admin/Server/setSkinDel',
    			'name'=>'服务器管理=>模板管理=>删除皮肤',
    			'type'=>'POST',
    			'data'=>"{'id':'15'}",
    			'tip'=>"{'id':'皮肤ID'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {},
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
);

$WangC[] = array(
    			'url'=>'/admin/Server/getServerList',
    			'name'=>'服务器管理=>服务器管理=>服务器管理列表',
    			'type'=>'POST',
    			'data'=>"{'serverid':'192.168.1.249','serverdomain':'192.168.1.249'}",
    			'tip'=>"{'serverids':'服务器id 可选','serverdomain':'IP地址或域名 可选'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        'serverid': '服务器id',
								        'servername': '服务器名字',
								        'serverdomain': '服务器域名(ip)',
								        'serverport': '服务器端口',
								        'usedpoint': '服务器使用的点数',
								        'totalpoint': '服务器总点数',
						                'isactive': '是否激活',
						                'clusterid': '',
								    },   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
);
$WangC[] = array(
    			'url'=>'/admin/Server/addServer',
    			'name'=>'服务器管理=>服务器管理=>添加服务器',
    			'type'=>'POST',
    			'data'=>"{'serverid':'1.1.1.1','servername':'测试','serverdomain':'1.1.1.1','serverport':'8080','totalpoint':'100'}",
    			'tip'=>"{'serverids':'服务器id','servername':'服务器名字','serverdomain':'服务器域名(ip)','serverport':'服务器端口','totalpoint':'服务器总点数'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								    },   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
);  
$WangC[] = array(
    			'url'=>'/admin/Server/updateServerStatus',
    			'name'=>'服务器管理=>服务器管理=>修改服务器状态',
    			'type'=>'POST',
    			'data'=>"{'isactive':'1','serverid':'192.168.1.249'}",
    			'tip'=>"{'isactive':'服务器状态1激活0停用','serverid':'服务器id'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
                                        'isactive':'服务器状态1激活0停用',
								    },   
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
); 
$WangC[] = array(
    			'url'=>'/admin/Server/getResourceinfo',
    			'name'=>'服务器管理=>资源统计=>获取资源统计',
    			'type'=>'POST',
    			'data'=>"",
    			'tip'=>"",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        'resourceid': '资源id',
								        'startdate': '开始时间',
								        'expirydate': '过期时间',
								        'normalmaxpoint': '最大交互用户数',
								        'sidelinemaxpoint': '最大旁听用户数',
								        'maxaudiofeeds': '最大音频数',
								        'maxvideonum': '最大视频数',
								    },    
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
								}"
);   
$WangC[] = array(
                'url'=>'/admin/Setup/getCompanyInfo',
                'name'=>'设置=>企业信息=>获取企业基本信息',
                'type'=>'POST',
                'data'=>"",
                'tip'=>"",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {
                                        'companyid': '企业ID',
                                        'companyfullname': '公司名称',
                                        'userpoint': '普通用户点数(小班课点数)',
                                        'silentpoint': '直播用户点数（大班课点数）',
                                        'starttime': '开始时间',
                                        'endtime': '结束时间',
                                        'authkey': '认证key',
                                        'ico': '企业logo',
                                        'account': '管理员'
                                    },    
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                                }"
);   
$WangC[] = array(
                'url'=>'/admin/Setup/getCompanySetInfo',
                'name'=>'设置=>企业设置=>获取企业设置信息',
                'type'=>'POST',
                'data'=>"",
                'tip'=>"",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {
                                        'companyid':'获取当前登录的公司id(暂时写死)',
                                        'seconddomain':'ww域名',
                                        'authkey':'验证Key',
                                        'companyfullname':'企业名称',
                                        'companytitle':'企业页面标题',
                                        'roomstartcallbackurl':'上课回调地址',
                                        'callbackurl':'下课回调地址',
                                        'logincallbackurl':'登入登出回调地址',
                                        'recordcallback':'录制完成回调地址',
                                        'filenotifyurl':'文档转换完回调地址',
                                        'helpcallbackurl':'帮助跳转地址',
                                        'ico':'企业Logo',
                                        'dataregionimg':'数据区域缺省图片'
                                    },    
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                                }"
);   
$WangC[] = array(
                'url'=>'/admin/Setup/editCompanyInfo',
                'name'=>'设置=>企业设置=>企业设置基本信息修改',
                'type'=>'POST',
                'data'=>"{'companyid':'1','seconddomain':'www','authkey':'Fadf323dffaf3','companyfullname':'拓课云','companytitle':'北京拓课','roomstartcallbackurl':'http://www.talk.com/index.html','callbackurl':'http://www.talk.com/index.html','logincallbackurl':'http://www.talk.com/index.html','recordcallback':'http://www.talk.com/index.html ','filenotifyurl':'http://www.talk.com/index.html','helpcallbackurl':'http://www.talk.com/index.html','ico':'/upload1/20180523_095453_poehrehx.png','dataregionimg':'/uploadfile'}",
                'tip'=>"{'companyid':'获取当前登录的公司id(暂时写死)','seconddomain':'ww域名','authkey':'验证Key','companyfullname':'企业名称','companytitle':'企业页面标题','roomstartcallbackurl':'上课回调地址','callbackurl':'下课回调地址','logincallbackurl':'登入登出回调地址','recordcallback':'录制完成回调地址','filenotifyurl':'文档转换完回调地址','helpcallbackurl':'帮助跳转地址','ico':'企业Logo','dataregionimg':'数据区域缺省图片'}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {},    
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                                }"
);   
?>