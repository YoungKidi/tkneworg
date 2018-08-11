<?php
namespace app\admin\model;
use think\Model;
use think\Db;
/*
 * 用户信息表Model
 *
*/
class Userinfo extends Model
{
    //
    protected $table = 'userinfo';
    /**
     * [addUserInfo 添加用户]
     * @author yr
     * @DateTime 2018-07-03
     * @param    [int]                   $companyid   [当前登录 的公司id]
     */
    public function addUserInfo($info)
    {  
       
        $userid = Db:: table($this->table)->insertGetId($info);
        return $userid;
    }
    
    /**
     * [updateUserInfo 修改用户]
     * @author yr
     * @DateTime 2018-07-03
     * @param    [array]                   $info   [信息]
     */
    public function updateUserInfo($info)
    {  
        //var_dump($info);
        $res = Db:: table($this->table)->where('userid',$info['userid'])->update($info);
        return $res;
    }


    /**
     * [getUserInfoByUserId //根据userid获取用户的昵称等信息]
     * @author zzq
     * @DateTime 2018-07-10
     * @param    [int]                   $userid   [用户id]
     * @return   [array]                 $data   [返回信息]
     */
    public function getUserInfoByUserIdAndCompanyId($where){
        $field = "a.userid,a.nickname,a.identification,a.firstname,c.account,a.mobile,a.email,a.userico,a.description,b.companyid,b.userroleid";
        $data = Db:: table($this->table)
                ->alias('a')
                ->field($field)
                ->join('usercompany b','a.userid = b.userid','LEFT')
                ->join('useraccount c','a.userid = c.userid','LEFT')
                ->where($where)
                ->find();
        return $data;
    }


    /**
     * [getUserInfoByUserId //添加管理员的时候查看当前account是否存在]
     * @author zzq
     * @DateTime 2018-07-010
     * @param    [int]                   $userid   [用户id]
     * @return   [array]                 $data   [返回信息]
     */
    public function getUserInfo($userinfo){
        if($userinfo['userid'] != 0){
            $where['u.userid'] = ['EQ',$userinfo['userid']];
        }
        if($userinfo['account'] != ''){
            $where['ua.account'] = ['EQ',$userinfo['account']];
        }
        if($userinfo['companyid'] != '') {
            $where['uc.companyid'] = ['EQ',$userinfo['companyid']];
        }
        
        /***下边if语句貌似没什么用***/
        if(isset($userinfo['roletype'])){
            $where['uc.userroleid']= ['>',9];
        }
        $usertype = 0;//??
        if($userinfo['usertype'] != ''){
            $usertype= $userinfo['usertype'];
        }
        /***上边if语句貌似没什么用***/
        if($userinfo['companyid'] == ''){
            $user =Db::table($this->table)
            ->alias('u')
            ->field('u.*,ua.account,ua.md5mobile,ua.registmode,ua.pwd,uc.userroleid,uc.companyid,uc.ucstate')
            ->join('useraccount ua','u.userid=ua.userid','LEFT')
            ->join('usercompany uc','u.userid=uc.userid','LEFT')
            ->where($where)
            ->find();
        }else{
            $where['u.usertype'] = ['EQ',1];
            $user =Db::table($this->table)
            ->alias('u')
            ->field('u.*,ua.account,ua.md5mobile,ua.registmode,ua.pwd,uc.userroleid,uc.companyid,uc.ucstate')
            ->join('useraccount ua','u.userid=ua.userid','LEFT')
            ->join('usercompany uc','u.userid=uc.userid','LEFT')
            ->where($where)
            ->find();
        }
        return $user;        
    }

    /**
     * 添加用户
     * @auther 胡博森
     * @param array $arr_data 用户添加的信息
     * @return int
     */
    public function setUserInfoAdd($arr_data){
        return Db::table($this->table)->insert($arr_data);
    }

    /**
     * 查询用户新增id
     * @auther 胡博森
     */
    public function getUserInfoId(){
        return Db::name($this->table)->getLastInsID();
    }
}