<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use RedisClient;
/*
 * 用户组织机构表Model
 *
*/
class Usercompany extends Model
{
    protected $table = 'usercompany';
    /**
     * [addUserCompany 添加信息]
     * @author yr
     * @DateTime 2018-07-03
     * @param    [int]                   $companyid   [当前登录 的公司id]
     */
    public function addUserCompany($data)
    {
        $result = Db::table($this->table)->insert($data);
        return $result;
    }
    /**
     * [updateUserCompany 添加信息]
     * @author yr
     * @DateTime 2018-07-03
     * @param    [int]                   $companyid   [当前登录 的公司id]
     */
    public function updateUserCompany($data){
        //var_dump($data);
        $res = Db:: table($this->table)->where('userid',$data['userid'])->where('companyid',$data['companyid'])->update($data);
        return $res;
    }
    //进行排序的操作
    public function treatusersort($deptid,$userid,$sourcesortid,$destsortid)
    {
        $redis = RedisClient::getInstance();//连接redis
        if( $sourcesortid==0 )
        {
            //echo 1;
            if( $destsortid>0 )
            {
                $where = 'ucstate!=1 and deptid='.$deptid.' and sortid>='.$destsortid.' and userid!='.$userid;
                $tempuserarr = Db::table($this->table)->where($where)->column('userid');
                if( $tempuserarr )
                {
                    $wheretreate['userid'] = ['IN',$tempuserarr];
                    Db::table($this->table)->where($wheretreate)->setInc('sortid',1); // 排序加1
                    $dbstr = 'emm:version';
                    $version = $redis->incr($dbstr);
                    Db::table($this->table)->where($wheretreate)->setField('version',$version);
                }else{
                    $maxsortlevel =Db::table($this->table)->where('ucstate!=1 and deptid='.$deptid.' and userid!='.$userid)->value('max(sortid)');
                    if( $destsortid>$maxsortlevel+1 )
                        Db::table($this->table)->where('deptid='.$deptid.' and userid='.$userid)->setField('sortid',$maxsortlevel+1); // 排序加1
                }
            }elseif($destsortid==0){
                $maxsortlevel =Db::table($this->table)->where('ucstate!=1 and deptid='.$deptid.' and userid!='.$userid)->value('max(sortid)');
                //var_dump($maxsortlevel);
                Db::table($this->table)->where('deptid='.$deptid.' and userid='.$userid)->setField('sortid',$maxsortlevel+1); // 排序加1
                //var_dump($this->getLastSql());
            }
        }elseif( $sourcesortid<$destsortid)
        {
            //echo 2;
            $where = 'ucstate!=1 and deptid='.$deptid.' and sortid>'.$sourcesortid.' and sortid<='.$destsortid.' and userid !='.$userid; 
            $tempuserarr = Db::table($this->table)->where($where)->column('userid');
            if( $tempuserarr )
            {
                $wheretreate['userid'] = ['IN',$tempuserarr];
                Db::table($this->table)->where($wheretreate)->setDec('sortid',1); // 排序加1
                $dbstr = 'emm:version';
                $version = $redis->incr($dbstr);
                Db::table($this->table)->where($wheretreate)->setField('version',$version);
            }
            $maxsortlevel =Db::table($this->table)->where('ucstate!=1 and deptid='.$deptid.' and userid!='.$userid)->value('max(sortid)');
            if( $destsortid>$maxsortlevel+1 )
                Db::table($this->table)->where('deptid='.$deptid.' and userid='.$userid)->setField('sortid',$maxsortlevel+1); // 排序加1
        }elseif( $sourcesortid>$destsortid)
        {
            //echo 3;
            if( $destsortid>0 )
            {
            
                $where = 'ucstate!=1 and deptid='.$deptid.' and sortid>='.$destsortid.' and sortid<'.$sourcesortid.' and userid !='.$userid;
                $tempuserarr = Db::table($this->table)->where($where)->column('userid');
                if( $tempuserarr )
                {
                    $wheretreate['userid'] = ['IN',$tempuserarr];
                    Db::table($this->table)->where($wheretreate)->setInc('sortid',1); // 排序加1
                    $dbstr = 'emm:version';
                    $version = $redis->incr($dbstr);
                    Db::table($this->table)->where($wheretreate)->setField('version',$version);
                }
            }elseif($destsortid==0){
                //$maxsortlevel =$tusercompany->where('ucstate!=1 and deptid='.$deptid.' and userid!='.$userid)->getField('max(sortid)');
                Db::table($this->table)->where('deptid='.$deptid.' and userid='.$userid)->setField('sortid',$sourcesortid); // 排序加1
            }
        }
    }

    /**
     * [getCompanyList  获取机构下的管理员列表]
     * @author zzq
     * @DateTime 2018-07-24
     * @param    [array]                 $where     [筛选条件]
     * @param    [int]                   $pagenum   [页码数]
     * @param    [int]                   $pagesize  [每页条数]
     * @return   [array]                            [查询结果]
     */
    public function getCompanyUserList($where,$pagenum,$pagesize){
        $field = 'a.userid,a.sortid,a.firstname,a.userroleid,d.account' ;
        $res = Db::table($this->table)
               ->alias('a')
               ->field($field)
               ->where($where)
               ->join('company b','a.companyid=b.companyid','LEFT')
               ->join('userinfo c','a.userid=c.userid','LEFT')
               ->join('useraccount d','a.userid=d.userid','LEFT')
               ->page($pagenum,$pagesize)
               ->select(); 
        //var_dump($this->getLastSql());
        //var_dump($res);
        return $res;
    }

    /**
     * [getCompanyList  获取机构下的管理员列表不分页]
     * @author zzq
     * @DateTime 2018-07-24
     * @param    [array]                 $where     [筛选条件]
     * @return   [array]                            [查询结果]
     */
    public function getAllCompanyUserList($where){
        $field = 'a.userid,a.sortid,a.firstname,a.userroleid,d.account' ;
        $res = Db::table($this->table)
               ->alias('a')
               ->field($field)
               ->where($where)
               ->join('userinfo c','a.userid=c.userid','LEFT')
               ->join('useraccount d','a.userid=d.userid','LEFT')
               ->select(); 
        // var_dump($this->getLastSql());
        // var_dump($res);
        // die;
        return $res;
    }

    /**
     * [getCompanyListCount  获取机构下的管理员列表]
     * @author zzq
     * @DateTime 2018-07-24
     * @param    [array]                 $where     [筛选条件]
     * @return   [int]                            [查询结果]
     */
    public function getCompanyUserListCount($where){
        $field = 'a.userid,a.sortid,a.firstname,a.userroleid,d.account' ;
        $count = Db::table($this->table)
               ->alias('a')
               ->field($field)
               ->where($where)
               ->join('company b','a.companyid=b.companyid','LEFT')
               ->join('userinfo c','a.userid=c.userid','LEFT')
               ->join('useraccount d','a.userid=d.userid','LEFT')
               ->count(); 
        //var_dump($this->getLastSql());
        //var_dump($res);
        return $count;
    }

    /**
     * [获取当前usercompany的信息,不连表]
     * @author zzq
     * @DateTime 2018-07-24
     * @param    [array]                 $where     [查询条件]
     * @return   [array]                            [查询结果]
     */
    public function getUserCompanyInfo($where){
        $res = Db::table($this->table)
        ->where($where)
        ->find();
        return $res;
    }


    /* 查询销售列表
     * @author 胡博森
     * @param array $arr_where 查询条件
     * @param array $arr_field 查询的字段
     * @return array
     */
    public function getUserInfo($arr_where,$arr_field=['*']){
        return Db::table($this->table)->field($arr_field)->where($arr_where)->select();
    }

    /**
     * 根据企业id查询用户信息
     */
    public function getCompanyAdmin($arr_where,$arr_field=['*']){
        return Db::table($this->table)->alias('u')
                    ->join('useraccount a','u.userid = a.userid')
                    ->field($arr_field)
                    ->where($arr_where)
                    ->find();
    }

    /**
     * 添加企业用户信息
     * @auther 胡博森
     * @param $arr_data
     * @return false|int|string
     */
    public function setCompanyUserAdd($arr_data){
        return Db::table($this->table)->data($arr_data)->insert();
    }
}