<?php
/**
*Smtp服务器设置
**/
namespace app\admin\model;
use think\Model;
use think\Db;
class Smtpinfo extends Model
{
	protected $table = 'smtpinfo';
	/**
	 * [getSmtpinfo  获取smtp信息]
	 * @author wangchen
	 * @DateTime 2018-07-30
	 */

	 public function getSmtpinfo($arr_where){
        $field = "companyid,smtpserver,smtpport,smtpusername,smtppassword,isssl";
        $result  = $this
            ->field($field)
            ->where($arr_where)
            ->find();
        return $result;        
    }
	/**
     * [setSmtpinfoUpdate  修改smtp信息]
     * @Author wangchen
     * @DateTime 2018-07-30
     * @param  $companyid 公司id 
     * @param  smtpserver SMTP服务器
     * @param  smtpport SMTP服务器端口
     * @param  smtpusername SMTP用户
     * @param  smtppassword SMTP密码
     * @param  isssl 通过ssl协议发送邮件（1-是,0-否）
     * @return   [int]                 返回修改状态
     */
    public function setSmtpinfoUpdate($companyid,$data){
        //修改
        $result = Db::table($this->table)->where(['companyid' => $companyid])->update($data);
        return $result;
    }
    /**
     * [setSmtpinfoAdd  添加smtp信息]
     * @Author wangchen
     * @DateTime 2018-07-31
     * @param  smtpserver SMTP服务器
     * @param  smtpport SMTP服务器端口
     * @param  smtpusername SMTP用户
     * @param  smtppassword SMTP密码
     * @param  isssl 通过ssl协议发送邮件（1-是,0-否）
     * @return   [int]                 返回修改状态
     */
    public function setSmtpinfoAdd($data){
        //添加
        $result = $this->insert($data);
        return $result;
    }
}