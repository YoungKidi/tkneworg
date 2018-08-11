<?php
namespace app\admin\model;
use think\Model;
use think\Db;
/*
 * 服务器表Model
 *
*/
class Serverinfo extends Model
{
    //
    protected $table = 'serverinfo';
    /**
     *	获取服务器List
     *	@author yr
     * @param $condition where条件
     *	@return array
     */
    public function getServerList($condition){
        //定义查询条件 默认空
        //更新数据在线人数为最新的
        $field = 'serverid,servername,serverdomain,serverport,usedpoint,totalpoint,isactive,supportlanguage,clusterid';
        $this->RecountServerpoint();
        if( $condition )
            $result = Db::table($this->table)->field($field)->where($condition)->select();
        else
            $result = Db::table($this->table)->field($field)->select();
        return $result;

    }
    /**
     *	添加服务器
     *	@author yr
     *	@return insertid
     */
    public function addServer($post){
        $allowfiled = 'serverid,servername,serverdomain,serverport,totalpoint';
        $result = $this->allowField($allowfiled)->save($post);
        return $result;
    }
    public function updateServerStatus($serverid,$isactive){
        $where['serverid'] = $serverid;
        $data['isactive'] = $isactive;
        $result = Db::table($this->table)->where($where)->update($data);
        return $result;
    }
    /**
     * [recountServerpoint 重新计算服务器在线人数]
     * @author yr
     * @DateTime 2018-07-03
     * @param    [int]                   $companyid   [当前登录 的公司id]
     */
    public function recountServerpoint($serverid=0){
        //从usedepoint 的表里统计出每个服务器的在线人数 在更新到serverinfo 暂且用原生的sql,有时间优化
        $wherearr1 = [];
        $sql="update serverinfo s set s.usedpoint=0 ";
        if( $serverid > 0 )
        {
            $wherearr1[] = $serverid;
            $sql.=" and s.serverid='%s';";
        }
        $this->execute($sql,$wherearr1);
        $sql="update serverinfo s,(select rp.serviceid as rserviceid,count(rp.buddyid) as tcount from roomusepoint rp ";
        $sql.=" group by rserviceid) online set s.usedpoint=online.tcount where s.serverid=online.rserviceid ";
        $wherearr = [];
        if( $serverid > 0 )
        {
            $wherearr[] = $serverid;
            $sql.=" and s.serverid='%s';";
        }
        $this->execute($sql,$wherearr);
    }
    
    /**
     * [getServerInfoByServerid //通过serverid获取服务器的ip等信息]
     * @author zzq
     * @DateTime 2018-07-010
     * @param    [int]                   $serverid   [服务器id]
     * @param    [array]                 $data       [返回信息]
     */
    public function getServerInfoByServerid($serverid){
        $field = "serverdomain";
        $data = Db::table($this->table)
                    ->field($field)
                    ->where('serverid','EQ',$serverid)
                    ->find();
        return $data;
    }
}