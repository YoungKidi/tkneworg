<?php
$WangZL = [];
$WangZL[] = array(
                'url'=>'/enterprise/company/editCompanyName',
                'name'=>'设置=>企业信息=>修改企业名称',
                'type'=>'POST',
                'data'=>"{'companyfullname':'拓课云'}",
                'tip'=>"{'companyfullname':'公司名称'}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {},    
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                                }"
);   
$WangZL[] = array(
                'url'=>'/enterprise/company/getCompanySetInfo',
                'name'=>'设置=>企业信息=>开发配置查看',
                'type'=>'POST',
                'data'=>"",
                'tip'=>"",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {
                                        'authkey': 'authkey',
                                        'companytitle': '企业页面标题',
                                        'chk_automatic_recorde': '自动云端录制',
                                        'roomstartcallbackurl': '上课回调',
                                        'callbackurl': '下课回调',
                                        'loginincallbackurl': '登入登出回调',
                                        'recordcallback': '录制回调',
                                        'filenotifyurl': '课件转换回调',
                                        'helpcallbackurl': '帮助页面',
                                        },    
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                                }"
);      
$WangZL[] = array(
                'url'=>'/enterprise/company/editCompanySetInfo',
                'name'=>'设置=>企业信息=>开发配置编辑',
                'type'=>'POST',
                'data'=>"{'companyid':'1','companytitle':'北京拓课云','chk_automatic_recorde':'1','roomstartcallbackurl':'http://www.talk.com/index.html','callbackurl':'http://www.talk.com/index.html','logincallbackurl':'http://www.talk.com/index.html','recordcallback':'http://www.talk.com/index.html','filenotifyurl':'http://www.talk.com/index.html','helpcallbackurl':'http://www.talk.com/index.html'}",
                'tip'=>"{'companyid':'公司id','companytitle':'企业页面标题','chk_automatic_recorde':'自动云端录制','roomstartcallbackurl':'上课回调','callbackurl':'下课回调','logincallbackurl':'登入登出回调','recordcallback':'录制回调','filenotifyurl':'课件转换回调','helpcallbackurl':'帮助页面'}",
                'returns'=>"{
                                    'code': '成功的时候返回0,失败或者异常返回其他',
                                    'data': {},    
                                    'info': '成功的时候返回操作成功,失败或者异常返回其他'
                                }"
);   
