<?php
namespace app\enterprise\model;
use think\Model;
use think\Db;
/*
 * 公司信息表Model
 *
*/
class Company extends Model
{
	protected $table = 'company';
    /**
     * [getcompanyInfo 获取企业基本信息]
     * @author wangchen
     * @DateTime 2018-08-09
     * @param    [int]                   $companyid   [当前登录 的公司id]
     * LEFT 用户帐号表  useraccount; department 部门信息表
     */
    public function getcompanyInfo($companyid)
    {
        $datainfo  = Db::table($this->table.' c')
            ->field('c.companyid,c.seconddomain,c.companyfullname,ua.account,c.userpoint,c.silentpoint,c.starttime,c.endtime,c.ico')
            ->join('department d','c.companyid=d.companyid','LEFT')
            ->join('useraccount ua','c.companyadminid=ua.userid','LEFT')
            ->where('d.deptparentid','EQ','0')
            ->where('c.companyid','eq',$companyid)
            ->find();
        return $datainfo;
    }
    /**
     * [updateName 修改企业名称]
     * @author 汪子龙
     * @DateTime 2018-08-09
     * @param $companyid   [企业id]
     */
    public function updateInfo($companyid,$data)
    {
        //可以写入的字段
        $allowField = 'companyfullname';
        //修改
        $result = $this->allowField($allowField)->save($data, ['companyid' => $companyid]);
        return $result;
    }
    /**
     * [getcompanySetInfo 获取企业设置信息]
     * @author 汪子龙
     * @DateTime 2018-08-09
     * @param    [int]    $companyid   [企业id]
     * @return   [array]     企业某些字段的信息
     */
    public function getcompanySetInfo($companyid,$field='[*]'){

        $data = Db::table($this->table)->field($field)->where('companyid','EQ',$companyid)->find();
        return $data;
    }
    /**
     * [editCompanySetInfo 编辑企业开发配置]
     * @author 汪子龙
     * @DateTime 2018-08-09
     * @param    [int]    $companyid   [企业id]
     */
    public function editCompanySetInfo($companyid,$data)
    {
        $allowField = 'companytitle,functionitem,roomstartcallbackurl,callbackurl,logincallbackurl,recordcallback,filenotifyurl,helpcallbackurl';
        $result = $this->allowField($allowField)->save($data,['companyid' => $companyid]);
        return $result;
    }
}