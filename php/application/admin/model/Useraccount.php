<?php
namespace app\admin\model;
use think\Model;
use think\Db;
/*
 * 用户账户表Model
 *
*/
class Useraccount extends Model
{
    protected $table = 'useraccount';
    /**
     * [addUserInfo 添加用户]
     * @author yr
     * @DateTime 2018-07-03
     */
    public function addUserAccount($data)
    {
        $result = Db:: table($this->table)->insert($data);
        return $result;
    }

    public function getUserAccount($userid){
        $res = Db::table($this->table)->where('userid','EQ',$userid)->find();
        return $res;
    }

    public function updateUserAccount($data){
        //var_dump($data);
        $userid = $data['userid'];
        $account = $data['account'];
        unset($data['userid']);
        unset($data['account']);
        $res = Db:: table($this->table)->where('userid',$userid)->where('account',$account)->update($data);
        return $res;
    }


    /**
     * 查询用户信息
     * @param  array $arr_where 查询条件
     */
    public function getUserInfo($arr_where,$arr_field){
        return Db::table($this->table)->field($arr_field)->where($arr_where)->find();
    }

    /**
     * 修改用户信息
     * @param  array $arr_where 修改条件
     * @param  array $arr_data 修改内容
     */
    public function updUserInfo($arr_where,$arr_data){
        return Db::table($this->table)->where($arr_where)->update($arr_data);
    }

    /**
     * 添加用户信息
     * @param $arr_data
     * @return false|int|string
     */
    public function setUserInfoAdd($arr_data){
        return Db::table($this->table)->data($arr_data)->insert();
    }

    /**
     * 查询用户的登录信息
     * @param $arr_where
     */
    public function getUserInfoLogin($arr_where,$arr_field){
        return Db::table($this->table)->alias('ua')
                ->field($arr_field)
                ->join('usercompany uc','ua.userid = uc.userid')
                ->join('company c','uc.companyid = c.companyid')
                ->where($arr_where)
                ->limit(1)
                ->find();

    }
}