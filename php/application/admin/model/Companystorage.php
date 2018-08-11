<?php
namespace app\admin\model;
use think\Model;
use think\Db;
class Companystorage extends Model
{
    protected $table = 'companystorage';
    /**
     * [getcompanystarageList 获取企业存储信息]
     * @author zzq
     * @DateTime 2018-07-03
     * @param    [array]                   $where    [搜索条件]
     * @param    [int]                   $pagenum    [页码]
     * @param    [int]                   $pagesize  [每页条数]
     * @return   [array]                            [查询结果]
     */
    public function getcompanystarageList($where,$pagenum,$pagesize)
    {   
        // var_dump($where);
        // die;
        $data  = Db::table($this->table)
            ->alias('a')
            ->where($where)
            ->field('a.companyid,a.datemonth,a.filesize,a.recordsize,a.totalsize,b.companyfullname')
            ->join('company b','a.companyid=b.companyid','LEFT')
            ->order('a.datemonth desc')
            ->page($pagenum,$pagesize)
            ->select();
        //var_dump($data);
        //var_dump($this->getLastSql());
        //die;
        return $data;
    }

    /**
     * [getcompanystarageListCount 获取企业存储信息的总的数目]
     * @author zzq
     * @DateTime 2018-07-03
     * @param    [array]                   $where    [搜索条件]
     * @return   [array]                            [查询结果]
     */
    public function getcompanystarageListCount($where)
    {   
        // var_dump($where);
        // die;
        $count  = Db::table($this->table)
            ->alias('a')
            ->where($where)
            ->field('a.companyid,a.datemonth,a.filesize,a.recordsize,a.totalsize,b.companyfullname')
            ->join('company b','a.companyid=b.companyid','LEFT')
            ->order('a.datemonth desc')
            ->count();
        //var_dump($data);
        //var_dump($this->getLastSql());
        //die;
        return $count;
    }


}	