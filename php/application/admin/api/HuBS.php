<?php
$HuBS[] = [
				'url'=>'/admin/Company/getCompanyList',
    			'name'=>'企业管理=>企业列表',
    			'type'=>'POST',
    			'data'=>"{'company_name':'拓课云','company_state':1,'sale_id':0,'page':1,'chk_month':1}",
    			'tip'=>"{'company_name':'企业名称或企业id','company_state':'企业状态 0:试用 1:正式 2:正常到期 3:试用到期 4:冻结 10：全部','sale_id':'销售id','page':'当前页','chk_month':'0：不选择 1：选择'}",
    			'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
								        page: {
								                sum_data: '总数据量',
                                            sum_page: '总页数',
                                            prev_page: '上一页',
                                            next_page: '下一页',
                                            now_page: '当前页'
                                            },
										data: [
												{
													companyid: '企业id',
													companyname: '企业名称',
													companyfullname: '企业全称',
													starttime: '开始时间',
													endtime: '结束时间',
													seconddomain: '企业域名',
													companystate: '企业状态',
													silentpoint: '大班课点数',
													userpoint: '小班课点数',
                                                    remark: '备注',
												}
											 ]
								    },
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
							}"
];
$HuBS[] = [
                'url'=>'/admin/Company/getCompanyDetails',
                'name'=>'企业管理=>企业详情=>企业基本信息',
                'type'=>'POST',
                'data'=>"{'company_name':'拓课云'}",
                'tip'=>"{'company_name':'企业名称'}",
                'returns'=>"{
								    'code': '成功的时候返回0,失败或者异常返回其他',
								    'data': {
                                        companyid: '企业编号',
                                        companystate: '是否冻结 0否 1是',
                                        companyfullname: '企业名称',
                                        seconddomain: '企业域名',
                                        authkey: 'authkey',
                                        silentpoint: '大班课',
                                        userpoint: '小班课',
                                        starttime: '开始时间',
                                        endtime: '结束时间',
                                        remark: '备注',
                                        smallcharge: '小班课付费方式',
                                        bigcharge: '大班课付费方式',
                                        industry: '所在行业',
                                        paystype: '支付类型',
                                        usetype: '应用类型',
                                        firstname: '管理员姓名',
                                        colony: '集群设置',
                                        pwd: '密码'
                                        },
								    'info': '成功的时候返回操作成功,失败或者异常返回其他'
							}"
];
$HuBS[] = [
                'url'=>'/admin/Company/getSaleList',
                'name'=>'企业管理=>企业列表=>销售员列表',
                'type'=>'POST',
                'data'=>"{}",
                'tip'=>"{}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {
                                            userid: '销售员编号',
                                            firstname: '销售员名字'
                                        },
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                            }"
];
$HuBS[] = [
                'url'=>'/admin/Company/getCompanyConfig',
                'name'=>'企业管理=>企业详情=>更多配置',
                'type'=>'POST',
                'data'=>"{'type':1,'company_id':''}",
                'tip'=>"{'type':'1：界面显示 21：全局配置项 22：上课流程相关 23 课堂工具 24：版本相关 25：保留项 26：大班课相关 3：企业配置 4：回调跳转 5：子企业','company':'企业id 默认是当前登录的企业'}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {},
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                            }"
];
$HuBS[] = [
                'url'=>'/admin/Company/setCompanyDetails',
                'name'=>'企业管理=>企业详情=>修改企业基本信息',
                'type'=>'POST',
                'data'=>"{'company_id':'10627','company_name':'zmq','silentpoint':'10','userpoint':'0','admin_pwd':'123123','admin_pwd_again':'123123','smallcharge':'1','bigcharge':'1','paystype':'1','usetype':'2','industry':'1','starttime':'2018-07-30 11:40:32','endtime':'2018-07-30 11:41:32','remark':'备注','company_state':'1','company_colony':'1'}",
                'tip'=>"{'company_id':'修改的企业编号','company_name':'zmq','silentpoint':'10','userpoint':'0','admin_pwd':'123123','admin_pwd_again':'再次确认密码','smallcharge':'小班课计费模式','bigcharge':'大班课计费模式','paystype':'预付费方式','usetype':'应用类型','industry':'所在行业','starttime':'开始时间','endtime':'结束时间','remark':'备注','company_state':'冻结企业 1：冻结 0：不冻结','company_colony':'1：集群1 2：集群2 3：集群3'}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {},
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                            }"
];
$HuBS[] = [
                'url'=>'/admin/Company/setCompanyFile',
                'name'=>'企业管理=>企业详情=>文件上传',
                'type'=>'POST',
                'data'=>"{'fiels':'','file_type':'1','company_id':'1111'}",
                'tip'=>"{'files':'上传的文件数据,上传的文件名字为uploadFile','file_type':'1：企业LOGO 2：企业数据区缺省图片','company_id':'企业id'}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {'file_url':'上传成功返回的文件路径'},
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                            }"
];
$HuBS[] = [
                'url'=>'/admin/Company/setCompanyConfigs',
                'name'=>'企业管理=>企业详情=>修改企业详情——更多配置',
                'type'=>'POST',
                'data'=>"{'company_id':'10033','type':'21'}",
                'tip'=>"{'company_id':'企业编号','type':'修改类型 21：全局配置项 22：上课流程相关 23 课堂工具 24：版本相关 25：保留项 26：大班课相关 3：回调跳转 4：子企业'}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {},
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                            }"
];
$HuBS[] = [
                'url'=>'/admin/Company/setCompanyPwd',
                'name'=>'企业管理=>企业列表=>重置密码',
                'type'=>'POST',
                'data'=>"{'company_id':'10033','admin_pwd':'11111','admin_pwd_again':'11111'}",
                'tip'=>"{'company_id':'企业编号','admin_pwd':'修改的密码','admin_pwd_again':'确认修改的密码'}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {},
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                            }"
];
$HuBS[] = [
                'url'=>'/admin/Company/setCompanyState',
                'name'=>'企业管理=>企业列表=>企业的冻结和恢复',
                'type'=>'POST',
                'data'=>"{'company_id':'10033','operation':'4'}",
                'tip'=>"{'company_id':'企业编号','operation':'修改的标识 4：冻结 1：恢复'}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {
                                        'companystat': '修改后的企业状态 企业状态 0:试用 1:正式 2:正常到期 3:试用到期 4:冻结'
                                    },
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                            }"
];
$HuBS[] = [
                'url'=>'/admin/Company/setCompanyParent',
                'name'=>'企业管理=>企业详情=>关联子企业',
                'type'=>'POST',
                'data'=>"{'company_id':'10033','company_son_id':'10035'}",
                'tip'=>"{'company_id':'关联的企业编号','company_son_id':'被关联的企业编号'}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {},
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                            }"
];
$HuBS[] = [
                'url'=>'/admin/Company/getCompanySon',
                'name'=>'企业管理=>企业详情=>查询子企业列表',
                'type'=>'POST',
                'data'=>"{'company_name':'10033','page':'1'}",
                'tip'=>"{'company_name':'查询的企业编号或名称','page':'跳转页'}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {
                                                'page': {
                                                    'sum_data': '总数据数',
                                                    'sum_page': '总页数',
                                                    'prev_page': '上一页',
                                                    'next_page': '下一页',
                                                    'now_page': '当前页'
                                                },
                                                'data': [
                                                            {
                                                                'companyfullname': '企业名称',
                                                                'companyid': '企业编号'
                                                            }
                                                        ]
                                    },
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                            }"
];
$HuBS[] = [
                'url'=>'/admin/Company/setCompanyRemark',
                'name'=>'企业管理=>企业列表=>修改备注',
                'type'=>'POST',
                'data'=>"{'company_id':'10033','company_remark':'10035'}",
                'tip'=>"{'company_id':'企业编号','company_son_id':'修改的备注'}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {},
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                            }"
];
$HuBS[] = [
                'url'=>'/admin/Company/setCompanyDel',
                'name'=>'企业管理=>企业列表=>删除企业',
                'type'=>'POST',
                'data'=>"{'company_name':'拓课云','company_state':1,'sale_id':0,'page':1,'chk_month':1,'company_id':'10033'}",
                'tip'=>"{'company_name':'企业名称或企业id','company_state':'企业状态 0:试用 1:正式 2:正常到期 3:试用到期 4:冻结 10：全部','sale_id':'销售id','page':'当前页','chk_month':'0：不选择 1：选择','company_id':'企业编号'}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {
                                        page: {
                                                sum_data: '总数据量',
                                            sum_page: '总页数',
                                            prev_page: '上一页',
                                            next_page: '下一页',
                                            now_page: '当前页'
                                            },
                                        data: [
                                                {
                                                    companyid: '企业id',
                                                    companyname: '企业名称',
                                                    companyfullname: '企业全称',
                                                    starttime: '开始时间',
                                                    endtime: '结束时间',
                                                    seconddomain: '企业域名',
                                                    companystate: '企业状态',
                                                    silentpoint: '大班课点数',
                                                    userpoint: '小班课点数',
                                                    remark: '备注',
                                                }
                                             ]
                                    },
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                            }"
];
$HuBS[] = [
                'url'=>'/admin/Company/setCompanyAdd',
                'name'=>'企业管理=>企业列表=>新增企业',
                'type'=>'POST',
                'data'=>"{'company_full_name':'娃哈哈','company_domain':'whh','company_state':'1','admin_account':'admin','admin_name':'娃哈哈','admin_pwd':'123123','admin_pwd_again':'123123','sale_id':'1'}",
                'tip'=>"{'company_id':'企业编号','company_domain':'企业域名','company_state':'企业状态','admin_account':'管理员账号','admin_name':'管理员姓名','admin_pwd':'管理员密码','admin_pwd_again':'管理员确认密码','sale_id':'销售id'}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {},
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                            }"
];

$HuBS[] = [
                'url'=>'/admin/Company/getDomainRegister',
                'name'=>'企业管理=>企业列表=>检测域名和账号是否被注册',
                'type'=>'POST',
                'data'=>"{'company_domain':'www','company_account':'admin'}",
                'tip'=>"{'company_domain':'域名','company_account':'企业账号'}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {},
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                            }"
];
$HuBS[] = [
                'url'=>'/admin/Company/getCompanyRegister',
                'name'=>'企业管理=>企业列表=>查询新增企业名称是否重复',
                'type'=>'POST',
                'data'=>"{'company_name':'拓课'}",
                'tip'=>"{'company_name':'企业名称'}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {},
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                            }"
];
$HuBS[] = [
                'url'=>'/admin/Login/getPublicKey',
                'name'=>'登陆=>获取加密公钥',
                'type'=>'POST',
                'data'=>"{'':''}",
                'tip'=>"{'':''}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {},
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                            }"
];
$HuBS[] = [
                'url'=>'/admin/Login/login',
                'name'=>'登陆=>请求登录',
                'type'=>'POST',
                'data'=>"{'login_data':''}",
                'tip'=>"{'login_data':'加密后的数据，加密的数据前格式为json,json中的数据为admin_account：登录账号 admin_pwd：登录密码'}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {
                                        token:'验证的token',
                                        role:'角色id 10:系统管理员　11:企业管理员　12:管理员 13销售 14财务 15销售主管 16 巡视',
                                    },
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                            }"
];
$HuBS[] = [
                'url'=>'/admin/Node/setNodeAdd',
                'name'=>'节点管理=>添加节点',
                'type'=>'POST',
                'data'=>"{'module':'admin','controller':'Company','function':'test','apiexplain':'企业管理','particular':'测试接口'}",
                'tip'=>"{'module':'模块','controller':'控制器','function':'方法名','apiexplain':'接口分类','particular':'接口详细说明'}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {},
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                            }"
];
$HuBS[] = [
                'url'=>'/admin/Node/setNodeDel',
                'name'=>'节点管理=>删除节点',
                'type'=>'POST',
                'data'=>"{'node_id':'1','module':'admin','controller':'Company','function':'test'}",
                'tip'=>"{'node_id':'节点id,当节点id存在时优先根据节点id删除，不存在时其余三项必填','module':'模块','controller':'控制器','function':'方法名'}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {},
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                            }"
];
$HuBS[] = [
                'url'=>'/admin/Node/setNodeUpd',
                'name'=>'节点管理=>修改企业节点',
                'type'=>'POST',
                'data'=>"{'node_id':'1','module':'admin','controller':'Company','function':'test1','apiexplain':'企业管理','particular':'接口修改','state':'1'}",
                'tip'=>"{'node_id':'接口id ,必填','module':'模块名称，选填','controller':'控制器名，选填','function':'方法名，选填','apiexplain':'接口分类，选填','particular':'接口详细说明，选填','state':'接口状态 1：使用 0：停用 ，选填'}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {},
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                            }"
];
$HuBS[] = [
                'url'=>'/admin/Node/getNodeList',
                'name'=>'节点管理=>节点列表',
                'type'=>'POST',
                'data'=>"{'node_id':'1','module':'admin','controller':'Company','function':'test1'}",
                'tip'=>"{'node_id':'节点id，选填','module':'模块名称，选填','controller':'控制器名，选填','function':'方法名，选填'}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': [
                                        {
                                            id: '节点id',
                                            module: '模块名称',
                                            controller: '控制器名称',
                                            function: '方法名',
                                            apiexplain: '分类说明',
                                            particular: '详细说明',
                                            ctime: '创建时间',
                                            state: '接口状态 1：使用 0：停用'
                                        }
                                    ],
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                            }"
];
$HuBS[] = [
                'url'=>'/admin/Node/autoNodeAdd',
                'name'=>'节点管理=>导入节点列表',
                'type'=>'POST',
                'data'=>"{'':''}",
                'tip'=>"{'':''}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {},
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                            }"
];