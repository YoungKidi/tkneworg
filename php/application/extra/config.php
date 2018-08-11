<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/4 0004
 * Time: 16:38
 */
return array(
    //服务器配置
    'ServerConf'=>array(
        'ServerType'=>'M', //M:master;S:slave
        'EnterPrise'=>'T',
        'DocConvertServerAddr'=>'',
        'SlaveMediaServerPort'=>'1935',
        'MediaServerIP'=>'',
        'MediaServerPort'=>'443',
        'flashUdpPort'=>'2935',
        'MediaServerInstName'=>'conference/',
        'UploadFileAddr'=>'/upload.php',
        'UploadFilepath'=>'/upload1/',
        'IMUploadFileAddr'=>'/IMupload.php',
        'InsertImgAddr'=>'/uploadpic.php',
        'SofewareDownLoadAddr'=>'/Updatefiles/',
        'VNCServerIP'=>'',
        'VNCPort'=>'11445',
        'ModulePath'=>'/static/flex/ubi/Meet/Module/',
        'CssPath'=>'/static/flex/css/',
        'MultiStream'=>'F',
        'Cycle'=>'T',
        'HDVideo'=>'T',
        'DOCDownload'=>'F',
        'live	mediaserver'=>'',	// 直播媒体服务器
        'livemediaport'=>'5935',	//端口
        'signalserver'=>'',		// h5程序所在服务器
        'signalserverport'=>'443',  	//端口
        'signalserverwebport'=>'3000',
        'ProductType'=>'S', //S:运营版本;  E：企业版本（标准版）
        'courseserver'=>'global.talk-cloud.neiwang',		// 信令服务器
        'courseserverport'=>'8889',  	//端口
        'recordserver'=>'global.talk-cloud.neiwang',		// 信令服务器
        'recordserverport'=>'8081',  	//端口
        'urlhead'=>'www',
        'RecordFilepath'=>'/uploadrecord/',
        'ClassDocServerAddr'=>'',
        'httpport'=>'80',
        'httpsport'=>'443',
        'product_type'=>'S', //S:运营版本;  E：企业版本（标准版）
        'free_vroadcast_point' =>0,//大班课点数
        'free_point' =>0,//小班课点数
        'total_storage_size'=>0,//企业存储空间大小
        'tencent_file_url'=>'http://51menke-1253417915.cosgz.myqcloud.com', //腾讯云访问文件URL前缀
        'product_type'=>'S', //S:运营版本;  E：企业版本（标准版）
    ),
);
