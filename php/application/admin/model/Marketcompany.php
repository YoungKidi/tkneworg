<?php
namespace app\admin\model;
use think\Model;
use think\Db;

use think\Validate;
class Marketcompany extends Model
{
    
    protected $table = 'marketcompany';
    protected $rule = [
            'marketid' => 'require|number',
            'companyid'   => 'require|number',
        ];
    protected $message = [
            'marketid.require' => '销售id必须填写',
            'marketid.number' => '销售id必须是数字',
            'companyid.require' => '企业id必须填写',
            'companyid.number' => '企业id必须是数字',
        ];

    //查出所有的已经被绑定的企业的companyid的数组
    public function getAllbindCompanyid($where){
        $res = Db::table($this->table)
            ->alias('a')
            ->join('company b','a.companyid=b.companyid','LEFT')
            ->where($where)
            ->column('a.companyid'); 
        return $res;        
    }
    /**
     * [getMarketCompanyInfo 获取销售企业关联表的数据(绑定或者未绑定)]
     * @author zzq
     * @DateTime 2018-07-25
     * @param    [array]                   $where   [搜索条件]
     */
    public function getMarketCompanyInfo($where,$pagenum,$pagesize){
        $field = 'a.id,a.marketid,a.companyid,b.companyfullname,b.companystate';
        $res = Db::table($this->table)
            ->alias('a')
            ->field($field)
            ->where($where)
            ->join('company b','a.companyid=b.companyid','LEFT')
            ->page($pagenum,$pagesize)
            ->select(); 
        //var_dump($this->getLastSql());
        return $res;
    }

    /**
     * [getMarketCompanyInfoCount 获取销售企业关联表的数据(绑定或者未绑定的数目)]
     * @author zzq
     * @DateTime 2018-07-25
     * @param    [array]                   $where   [搜索条件]
     */
    public function getMarketCompanyInfoCount($where){
        $field = 'a.id,a.marketid,a.companyid,b.companyfullname';
        $res = Db::table($this->table)
            ->alias('a')
            ->field($field)
            ->where($where)
            ->join('company b','a.companyid=b.companyid','LEFT')
            ->count();
        return $res;
    }

    
    /**
     * [getBindByMarketidAndCompanyId //查看某个销售和某个企业是否关联有关联的关系]
     * @author zzq
     * @DateTime 2018-07-27
     * @param    [array]                   $where   [筛选条件]
     * @param    [array]                            [查询结果]
     */
    public function getBindByMarketidAndCompanyId($where){
        $field = "id,marketid,companyid";              
        $res = Db::table($this->table)
        ->where($where)
        ->find();
        return $res;
    }
    //添加销售和企业关联关系
    public function addMarketCompanyInfo($data){
        //校验参数
        $validate = new Validate($this->rule, $this->message);
        if( !$validate->check($data) ){
            return return_format('',30006,$validate->getError());
        }else{
            //添加数据
            $res = Db::table($this->table)->insert($data);
            return $res;
        }        
        
    }
    //删除销售和企业关联关系
    public function deleteMarketCompanyInfo($marketid,$companyids)
    {
        try {
            $res = Db::table($this->table)->where('companyid', 'IN', $companyids)->where('marketid', 'EQ', $marketid)->delete();
            if ($res !== false) {
                return return_format('', 0, '删除成功');
            } else {

            }
        } catch (\Exception $e) {
            return return_format('', 30016, $e->getMessage());
        }
    }
    /**
     * 添加企业销售信息
     */
    public function setMarketCompanyAdd($arr_data){
        return Db::table($this->table)->insert($arr_data);
    }
}