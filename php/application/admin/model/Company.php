<?php
namespace app\admin\model;
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
     * @author yr
     * @DateTime 2018-07-03
     * @param    [int]                   $companyid   [当前登录 的公司id]
     * LEFT 用户帐号表  useraccount; department 部门信息表
     */
    public function getcompanyInfo($companyid)
    {
        $datainfo  = Db::table($this->table.' c')
            ->field('c.companyid,c.seconddomain,c.companyfullname,c.authkey,ua.account,c.userpoint,c.silentpoint,c.starttime,c.endtime,c.ico')

            // ->field('c.*,d.deptid,d.deptname,ua.account')
            ->join('department d','c.companyid=d.companyid','LEFT')
            ->join('useraccount ua','c.companyadminid=ua.userid','LEFT')
            ->where('d.deptparentid','EQ','0')
            ->where('c.companyid','eq',$companyid)
            ->find();
        return $datainfo;
    }

    /**
     * [getcompanySetInfo 获取企业设置信息]
     * @author wangchen
     * @DateTime 2018-08-09
     * @param    [int]                   $companyid   [企业id]
     * @return   [array]                   企业某些字段的信息
     */
    public function getcompanySetInfo($companyid){
        $field = "seconddomain,authkey,companytitle,roomstartcallbackurl,callbackurl,logincallbackurl,recordcallback,filenotifyurl,helpcallbackurl,ico,dataregionimg,companyfullname";
        $data = Db::table($this->table)->field($field)->where('companyid','EQ',$companyid)->find();
        return $data;
    }


    /**
     * [updateInfo  修改企业设置信息]
     * @Author yr
     * @DateTime 2018-07-03
     * @param  companyid 公司id where条件
     * @param  seconddomain ww域名
     * @param  authkey authkey
     * @param  companyfullname 企业名称
     * @param  companytitle 企业页面标题
     * @param  roomstartcallbackurl 上课回调地址
     * @param  callbackurl 下课回调地址
     * @param  logincallbackurl 登入登出回调地址
     * @param  recordcallback 录制完成回调地址
     * @param  filenotifyurl 文档转换完回调地址
     * @param  helpcallbackurl 帮助跳转地址
     * @param  ico 企业Logo
     * @param  dataregionimg数据区域缺省图片
     * @return   [int]                 返回符合的记录数目
     */
    public function updateInfo($companyid,$data){
        //允许写入的字段
        $allowField= 'seconddomain,authkey,companyfullname,companytitle,roomstartcallbackurl,allbackurl,logincallbackurl,recordcallback,filenotifyurl,helpcallbackurl,ico,dataregionimg';
        //修改
        $result = $this->allowField($allowField)->save($data, ['companyid' => $companyid]);
        return $result;
    }

    /**
     * [getcompanyInfo 获取企业某些字段的信息]
     * @author zzq
     * @DateTime 2018-07-05
     * @param    [int]                   $companyid   [企业id]
     * @return   [array]                   企业某些字段的信息
     */
    public function getCompanyInfoById($companyid){
        $field = "companyfullname";
        $data = Db::table($this->table)->field($field)->where('companyid','EQ',$companyid)->find();
        return $data;
    }

    /**
     * [getCompanyIdsbyName //模糊查询某个企业名称的关键字所对应的所有的companyid]
     * @author zzq
     * @DateTime 2018-07-06
     * @param    [string]                   $companyfullname   [企业全称]
     * @return   [array]                 符合条件的企业的id的集合
     */
    public function getCompanyIdsbyName($companyfullname){
        $field = "companyid";
        $data = Db::table($this->table)->field($field)->where("companyfullname","like","%".$companyfullname."%")->select()->toArray();
        $ids = [];
        foreach($data as $k => $v){
            $ids[] = $v['companyid'];
        }
        return $ids;
    }

    
    /**
     * [getCompanyIdsbyKeyword //通过一个关键字精准找到企业的id]
     * @author zzq
     * @DateTime 2018-07-06
     * @param    [string]                   $companykeyword   [企业全称]
     * @return   [array]                 符合条件的企业的id的集合
     */
    public function getCompanyIdsbyKeyword($companykeyword){
        $field = "companyid";
        $data = Db::table($this->table)
                ->field($field)
                ->whereOr('companyid','EQ',$companykeyword)
                ->whereOr('companyfullname','EQ',$companykeyword)
                ->find();
        return $data['companyid'];
    }

    //递归获取企业下的子企业的id
    public function getChildCompanyId($companyid){
        
        $res = DB::table($this->table)->field('companyid,parentid')->where('parentid','EQ',$companyid)->order('companyid asc')->select();
        $arr = $this->get_all_child($res,$companyid);
        return $arr;
    }

    //递归获取所有的子分类的ID
    public function  get_all_child($array,$companyid){
        $arr = array();
        foreach($array as $v){
            if($v['parentid'] == $companyid){
                $arr[] = $v['companyid'];
                $arr = array_merge($arr,$this->get_all_child($array,$v['companyid']));
            };
        };
        return $arr;
    }
    /**
     * 获取企业列表
     * @author hbs
     * @param array $arr_where 查询条件
     * @param array $arr_field 查询的字段
     */
    public function getCompanyList($arr_where,$arr_field='*',$arr_page){
        return Db::table($this->table)->alias('c')
            ->field($arr_field)
            ->join('marketcompany m','c.companyid = m.companyid','left')
            ->join('usercompany u','m.marketid = u.userid','left')
            ->where($arr_where)
            ->limit($arr_page['page'],$arr_page['size'])
            ->order('createtime','desc')
            ->select();
    }
    /**
     * 获取子企业列表
     * @author hbs
     * @param array $arr_where 查询条件
     * @param array $arr_field 查询的字段
     */
    public function getCompanySonList($arr_where,$arr_field='*',$arr_page=[]){
        $obj_db = Db::table($this->table)
            ->field($arr_field)
            ->where($arr_where);
        if($arr_page){
            $obj_db =   $obj_db->limit($arr_page['page'],$arr_page['size']);
        }
        return $obj_db->order('createtime','desc')
               ->select();
    }

    /**
     * 删除企业
     * @author 胡博森
     * @param array $arr_where 删除条件
     * @return false|int
     */
    public function setCompanyDel($arr_where){
        return Db::table($this->table)->where($arr_where)->delete();
    }

    /**
     * 新增企业
     * @author 胡博森
     * @param array $arr_data
     * @return mixed
     */
    public function setCompanyAdd($arr_data){
        return Db::table($this->table)->insert($arr_data);
    }

    /**
     * 获取新增企业的id
     * @auther 胡博森
     */
    public function getCompanyAddId(){
        return Db::table($this->table)->getLastInsID();
    }
    /**
     * 查询某个企业详细信息
     * @author 胡博森
     * @param $arr_where 查询条件
     * @param $arr_field 要查询的字段
     * @param $str_admin 是否查询管理员信息
     */
    public function getCompanyField($arr_where,$arr_field,$str_admin = false){
        if($str_admin){
            return Db::table($this->table)->alias('c')->field($arr_field)
                    ->join('usercompany u','c.companyid = u.companyid')
                    ->where($arr_where)
                    ->limit(1)
                    ->find();
        }else{
            return Db::table($this->table)->field($arr_field)->where($arr_where)->limit(1)->find();
        }
    }

    /**
     * 查询账号是否被注册
     * @author 胡博森
     * @author hbs
     * @param array $arr_where 查询条件
     * @param array $arr_field 查询字段
     */
    public function isDomainRegister($arr_where,$arr_field){
        return Db::table($this->table)->alias('c')
                ->field($arr_field)
                ->join('useraccount ua','ua.userid = c.companyadminid','left')
                ->where($arr_where)
                ->find();
    }

    /**
     * 修改企业信息
     * @author 胡博森
     * @param array $arr_where 修改条件
     * @param array $arr_data 修改内容
     */
    public function setCompanyUpd($arr_where,$arr_data){
        return Db::table($this->table)->where($arr_where)->update($arr_data);
    }

    /**
     * 获取企业分页信息
     * @author 胡博森
     * @param  array $arr_where 查询条件
     * @return int
     */
    public function getCompanyPage($arr_where){
        return $this->alias('c')
            ->join('marketcompany m','c.companyid = m.companyid','left')
            ->join('usercompany u','m.marketid = u.userid','left')
            ->where($arr_where)
            ->count();
    }

    /**
     * 获取企业列表要分页
     * @author zzq
     * @param array $where 查询条件
     * @param int $pagenum 页码数
     * @param int $pagesize 每页个数
     */
    public function getCompanyListBysearch($where,$pagenum,$pagesize){
        $field = 'b.companyid,b.companyfullname,b.companystate';
        $res = Db::table($this->table)
            ->alias('b')
            ->field($field)
            ->where($where)
            ->page($pagenum,$pagesize)
            ->select(); 
        //var_dump($this->getLastSql());
        return $res;
    }

    /**
     * 获取企业列表要分页
     * @author zzq
     * @param array $where 查询条件
     * @param int $pagenum 页码数
     * @param int $pagesize 每页个数
     */
    public function getCompanyListBysearchCount($where){
        $field = 'b.companyid,b.companyfullname,b.companystate';
        $count = Db::table($this->table)
            ->alias('b')
            ->field($field)
            ->where($where)
            ->count(); 
        //var_dump($this->getLastSql());
        return $count;
    }
    /**
     * 查询子企业分页信息
     */
    public function getCompanySonPage($arr_where){
        return Db::table($this->table)->where($arr_where)->count();
    }

    /**
     * 生成authkey
     * @return mixed
     */
    public function CreateAuthkey()
    {
        $autykey = $this->getRandChar(16);
        $tempdata = Db::table($this->table)->field(['companyid'])->where(['authkey'=>$autykey])->limit(1)->find();
        if($tempdata)
        {
            return $this->CreateAuthkey();
        }else{
            return $autykey;
        }
    }

    private function getRandChar($length)
    {
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
        $max = strlen($strPol)-1;
        for($i=0;$i<$length;$i++){
            $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }
        return $str;
    }

    /**
     * 获取company最后一条id
     * @return array|false|mixed|\PDOStatement|string|Model
     */
    public function getCompanyLastid(){
        return Db::table($this->table)->field('companyid')->order('companyid desc')->limit(1)->find();
    }

}