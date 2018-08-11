<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
/**
 * [return_format 返回数据格式化]
 * @Author
 * @DateTime 2018-04-18T14:29:25+0800
 * @$data 数据
 * @$code 错误码
 * @$info 返回数据描述
 *
 * @return   ['code'=>'0','data'=>'','info'=>'添加成功']
 * 如果返回值多个 比如有分页data值变为 ['data'=>array,'pageinfo'=>['pagesize','pagenum','total']]
 * 错误 0 代表 没有错误
 * pagesize=> 每页多少行
 * pagenum=> 当前第几页
 * total=> 总记录行数
 */
function return_format($data, $code = 0, $info = '') {
    if($info === ''){
        $info = $code==0 ? lang('success') : lang(strval($code)) ;
    }
	return ['code' => $code, 'data' => $data, 'info' => $info];
}

//转换成utc时间
/**
*@param string '2018-09-01'
*@return string
**/
function convertToUtcDate($date){
	$time = strtotime($date);
    date_default_timezone_set("UTC");
    $newDate = date('Y-m-d H:i:s.000\Z', $time);
    return $newDate;
}


//获取此前的几个月日期格式如2018-07-01包括本月
/**
*@param int $num 
*@return string
**/
function to_several_month($num)
{
	$arr = array();
	for($i = 0;$i <= ($num-1); ++$i)
	{
		$t = strtotime("-$i month");
		$str = date('Y-m',$t);
		$arr[] = $str;
	}
	return $arr;
}

//获取当前房间类型
/**
*@param int $roomtype 
*@return string
**/
function getRoomTypeName($roomtype)
{
	$roomtype = intval($roomtype);
	switch ($roomtype) {
		case 0:
			$str = "小班课1对1";
			break;
		case 3:
			$str = "小班课1对多";
			break;
		case 10:
			$str = "大班课";
			break;		
		default:
			$str = "未知";
			break;
	}
	return $str;
}


//获取当前的角色名称
//0：主讲  1：助教    2: 学员   3：直播用户 4:巡检员　10:系统管理员　11:企业管理员　12:管理员
/**
*@param int $roleId 
*@return string 角色的名称
**/
function getRoleNameByRoleId($roleId){
	$roleId = intval($roleId);
	switch ($roleId) {
		case 0:
			$str = "教师";
			break;
		case 1:
			$str = "助教";
			break;
		case 2:
			$str = "学员";
			break;
		case 3:
			$str = "直播用户";
			break;
		case 4:
			$str = "巡检员";
			break;
		case 10:
			$str = "系统管理员";
			break;
		case 11:
			$str = "企业管理员";
			break;
		case 12:
			$str = "管理员";
			break;
		case 13:
			$str = "销售";
			break;
		case 14:
			$str = "财务";
			break;
		case 15:
			$str = "销售主管";
			break;						
		default:
			$str = "未知";
			break;
	}
	return $str;
}


//获取企业状态
//公司状态 0:试用 1:正式 2:正常到期 3:试用到期 4:冻结
/**
*@param int $companystate 
*@return string 企业状态
**/
function getNameByCompanyState($companystate){
	$companystate = intval($companystate);
	switch ($companystate) {
		case 0:
			$str = "试用";
			break;
		case 1:
			$str = "正式";
			break;
		case 2:
			$str = "正常到期";
			break;
		case 3:
			$str = "试用到期";
			break;
		case 4:
			$str = "冻结";
			break;					
		default:
			$str = "未知";
			break;
	}
	return $str;
}

//将秒数转换友好的转成时间
/**
*@param int $time 
*@return string 角色的名称
**/
function showTime($time){
    if(is_numeric($time)){
    	$str = "";
	    $value = array(
	      "years" => 0, "days" => 0, "hours" => 0,
	      "minutes" => 0, "seconds" => 0,
	    );
	    if($time >= 31556926){
	      $value["years"] = floor($time/31556926);
	      $time = ($time%31556926);
	    }
	    if($time >= 86400){
	      $value["days"] = floor($time/86400);
	      $time = ($time%86400);
	    }
	    if($time >= 3600){
	      $value["hours"] = floor($time/3600);
	      $time = ($time%3600);
	    }
	    if($time >= 60){
	      $value["minutes"] = floor($time/60);
	      $time = ($time%60);
	    }
	    $value["seconds"] = floor($time);
	    //判断
	    if($value['years'] > 0){
	    	$str .= $value["years"] ."年";
	    }
	    if($value['days'] > 0){
	    	$str .= $value["days"] ."天";
	    }
	    if($value['hours'] > 0){
	    	$str .= $value["hours"] ."小时";
	    }
	    if($value['minutes'] > 0){
	    	$str .= $value["minutes"] ."分";
	    }
	    if($value['seconds'] > 0){
	    	$str .= $value["seconds"] ."秒";
	    }		    
	    return $str;
     }else{
    	return false;
    }
 }

//友好的显示文件大小
//格式化size显示 $b的单位是Byte
function formatSize($b,$times=0){
    if($b>1024){
        $temp=$b/1024;
        return formatSize($temp,$times+1);
    }else{
        $unit='B';
        switch($times){
            case '0':$unit='B';break;
            case '1':$unit='KB';break;
            case '2':$unit='MB';break;
            case '3':$unit='GB';break;
            case '4':$unit='TB';break;
            case '5':$unit='PB';break;
            case '6':$unit='EB';break;
            case '7':$unit='ZB';break;
            default: $unit='单位未知';
        }
        return sprintf('%.2f',$b).$unit;
    }
}



/**判断是否是正整数
*@param int $num 
*@return bool true表示是正整数
**/
function isPositiveInteger($num){
	/*if(isset($num)){
		if(is_numeric($num)){
			if($num > 0 && (floor($num)==$num) ){
				return true;
			}
		}
	}
	return false;*/
	if(preg_match("/^[1-9][0-9]*$/",$num)){
		return true;
	}
	return false;
}


//判断是否为非负整数
function isNotNegative($num){
	if(isset($num)){
		if(is_numeric($num)){
			if($num >= 0 && (floor($num)==$num) ){
				return true;
			}
		}
	}
	return false;
}

/**
 * //检测手机号
 * @Author zzq
 * @param $mobile   int  [手机号]
 * @return bool  [返回信息]
 * 
 */
function checkMobile($mobile){
    
    $pattern = "/^[1][0-9]{10}$/";
    if (preg_match($pattern, $mobile)) {
        return true;
    }
    return false;   
}

/**
 * //检测邮箱
 * @Author zzq
 * @param $email   int  [邮箱号]
 * @return bool  [返回信息]
 * 
 */
function checkEmail($email){

    if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$email)) {
      return false; 
    }
    return true;
}

/**
 * //检验日期格式如2018-06-22
 * @Author zzq
 * @param $datestr   string  [邮箱号]
 * @return bool  [返回信息]
 * 
 */
function checkDateFormat($date)
{
    //匹配日期格式
    if (preg_match ("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date, $parts))
    {
        //检测是否为日期
        if(checkdate($parts[2],$parts[3],$parts[1]))
            return true;
        else{
			return false;
        }
    }else{
        return false;
    }
}

/*@param int devicetype 
*@return string 设备类型
**/
//设备类型 0表示flash,1表示客户端，2:ios,3:android,4:telephone,5:323
function getDevicetypeName($devicetype){
	$devicetype = intval($devicetype);
	switch ($devicetype) {
		case 0:
			$str = "flash";
			break;
		case 1:
			$str = "客户端";
			break;
		case 2:
			$str = "ios";
			break;
		case 3:
			$str = "android";
			break;
		case 4:
			$str = "telephone";
			break;
		case 5:
			$str = "323";
			break;						
		default:
			$str = "未知";
			break;
	}
	return $str;
}

/**
 * 剪切文件上传URL
 */
function cutURL($str_url){
    if(empty($str_url)) return false;
    $arr_url = explode('/',$str_url);
    $str_len = count($arr_url);
    $str_new_url = '';
    for($i = 3;$i <$str_len;$i++){
        $str_new_url .= '/'.$arr_url[$i];
    }
    return $str_new_url;
}



//获取网络状态的名称
//网络质量得分1、2为绿灯，3为黄灯，4、5为红灯。上行/下行取实时低
function getNetworkQuality($quality){
	$quality = intval($quality);
	switch ($quality) {
		case 1:
			$str = "优";
			break;
		case 2:
			$str = "优";
			break;
		case 3:
			$str = "良";
			break;
		case 4:
			$str = "差";
			break;
		case 5:
			$str = "差";
			break;					
		default:
			$str = "未知";
			break;
	}
	return $str;
}

/**
 * [GetServerAddr 获取服务器地址]
 * 3.2方法更新至5.0
 */
function GetServerAddr(){
	if( input('server.HTTP_X_FORWARDED_HOST') )
		{
			$host =  input('server.HTTP_X_FORWARDED_HOST');
		}else if( input('server.HTTP_HOST') )
		{
			$host = input('server.HTTP_HOST');
		}
		if (strpos($host, ',')) {
	    $hostArray = explode(',', $host);
	    $host = $hostArray[0];
	  }
		$serveraddr = 'http://'.$host.'/';
	return $serveraddr;
}