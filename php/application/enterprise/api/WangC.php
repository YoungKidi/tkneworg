<?php
$WangC = [];
$WangC[] = array(
                'url'=>'/enterprise/Company/getCompanyInfo',
                'name'=>'设置=>企业信息=>获取企业信息',
                'type'=>'POST',
                'data'=>"",
                'tip'=>"",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {
                                        'companyid': '企业ID',
                                        'seconddomain':'域名',
                                        'companyfullname': '公司名称',
                                        'account': '管理员',
                                        'userpoint': '普通用户点数(小班课点数)',
                                        'silentpoint': '直播用户点数（大班课点数）',
                                        'starttime': '开始时间',
                                        'endtime': '结束时间',
                                        'ico': '企业logo',
                                        
                                    },    
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                                }"
);   
$WangC[] = array(
                'url'=>'/enterprise/room/getRoomList',
                'name'=>'教室管理=>小班课管理=>小班课列表',
                'type'=>'POST',
                'data'=>"{'page':'1','serial': '1350470591',
                'roomname': '桌面共享双击全屏一对一'}",
                'tip'=>"{'page':'访问的页面 默认为1','serial': '教室号（筛选条件 可为空）',
                'roomname': '房间名称（筛选条件 可为空'}",
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
                                                            'serial': '房间号',
                                                            'roomname': '房间名称',
                                                            'roomtype': '房间类型（0：1对1  3：1对多）',
                                                            'starttime': '开始时间',
                                                            'endtime': '结束时间',
                                                            'roomstate': '房间名称（字段含义暂时不明确）'
                                                        }
                                                    ]
                                        },
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                                }"
);
$WangC[] = array(
                'url'=>'/enterprise/room/getRoomInfo',
                'name'=>'教室管理=>小班课管理=>小班课详情',
                'type'=>'POST',
                'data'=>"{'serial':'1350470591'}",
                'tip'=>"{'serial':'教室ID'}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {
                                        'serial': '教室ID',
                                        'roomname': '桌面共享双击全屏一对一',
                                        'roomtype': '房间类型（0：1对1 3：1对多)',
                                        'starttime': '开始时间',
                                        'endtime': '结束时间',
                                        'chairmanpwd': '老师密码',
                                        'confuserpwd': '学员密码',
                                        'assistantpwd': '助教密码',
                                        'patrolpwd': '巡课密码',
                                        'sidelineuserpwd': '旁听密码',
                                        'videotype': '视频分辨率 0：audio  9：80*45  12: 80*60   20: 176*100    28: 176x144  36:320*180 48: 320x240  72:640*360 96: 640x480   144: 1280x720   216: 1920x1080',
                                        'videoframerate': '帧数',
                                        'teacher_url': '老师地址',
                                        'confuser_url': '学生地址',
                                        'livebypass_url': '（旁听地址）直播地址'
                                        
                                    },    
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                                }"
);   
$WangC[] = array(
                'url'=>'/enterprise/room/getRoomRelationFile',
                'name'=>'教室管理=>小班课管理=>小班课详情=>关联课件列表',
                'type'=>'POST',
                'data'=>"{'serial':'536953341'}",
                'tip'=>"{'serial':'教室ID'}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': [
                                        {
                                            'serial': '教室ID',
                                            'fileid': '文件ID',
                                            'filename': '文件名称',
                                            'status': '文件状态   1：转换完成   其他：正在转换'
                                        }
                                    ],
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                                }"
);   