<?php
/**
 * 企业设置 对接业务逻辑
 */
namespace app\admin\business;
use app\admin\model\Company;
use app\admin\model\Channel;
use app\admin\model\Useraccount;
use app\admin\model\Usercompany;
use app\admin\model\Userinfo;
use app\admin\model\Marketcompany;
use app\admin\model\Marketbind;
use think\Validate;
use RedisClient;
use think\Db;
use app\admin\business\UserloginManage;
class SetupManage{
    //默认用户账号状态1 帐号的状态   0:未激活  1:正常   2：已删除
    protected  $userstate = 1;
    //定义用户初始化密码
    protected  $initpass = '123456';
    /**
     *	企业基本信息
     *	@author yr
     *	@param  $data array
     *	@return array
     */
    public function getCompanyInfo($currcompanyid){
        $companymodel = new Company;
        //获取企业相关信息,暂无大班课点数和小班课点数
        $companyinfo = $companymodel->getcompanyInfo($currcompanyid);
        // $companyinfo['totalstoragesize'] = $companyinfo['totalstoragesize'].'M';
        return return_format($companyinfo,0,lang('success'));
    }

    /**
     *  企业配置信息
     *  @author wangchen
     *  @param  $data array
     *  @return array
     */
    public function getCompanySetInfo($currcompanyid){
        $companymodel = new Company;
        //获取企业相关信息,暂无大班课点数和小班课点数
        $companyinfo = $companymodel->getcompanySetInfo($currcompanyid);
        // $companyinfo['totalstoragesize'] = $companyinfo['totalstoragesize'].'M';
        return return_format($companyinfo,0,lang('success'));
    }
    /**
     *	修改企业配置
     *	@author yr
     *	@param  $data array
     *	@return array
     */
    public function editCompanyInfo($currcompanyid,$post){
        //获取公司id 公司id暂且写死
        $companymodel = new Company;
        $update_result = $companymodel->updateInfo($currcompanyid,$post);
        if($update_result>=0){
            return return_format('',0,lang('success'));
        }else{
            return return_format('',30000,lang('error'));
        }
    }
    /**
     * 权限管理 新增人员
     * @Author yr
     * @param  userid  可选 修改时候必填 用户userid
     * @param  userroleid 用户角色id
     * @param  account 账号名称
     * @param  firstname 名字
     * @param  userpwd 密码
     * @param  againpwd 确认密码
     * @param  mobile 手机号
     * @param  email 邮箱
     * @param  description 简介
     * @param  logo 头像logo
     * POST | URL:/admin/Setup/addOrEditUserinfo
     * return
     */
    public function addOrEditUserinfo($post)
    {
        //第一步，接受并验证传递的参数
        //字段验证
        $rule = [
            'account' => 'require',
            'firstname' => 'require',
            'userpwd' => 'length:6,25|confirm:againpwd',
            'againpwd' => 'length:6,25',
            'mobile' => 'require|length:11',
            'email' => 'require|email',
            'description' => 'length:1,200',
        ];
        $msg = [
            'account.require' => '帐号不能为空',
            'firstname.require' => '姓名不能为空',
            'userpwd.length' => '密码长度不能小于6位',
            'userpwd.confirm' => '两次输入密码不一致',
            'againpwd.length' => '密码长度不能小于6位',
            'mobile.require' => '手机号不能为空',
            'mobile.length' => '手机号长度必须为11位',
            'email.require' => '邮箱不能为空',
            'email.email' => '邮箱格式不正确',
            'description.length' => '简介不能超过200个字符',
        ];

        $validate = new Validate($rule, $msg);
        $result = $validate->check($post);
        if (true !== $result) {
            return return_format('', 30006, $validate->getError());
        }
        //userid校验当userid为非负整数
        if(isset($post['userid'])){
            if( !isNotNegative($post['userid']) ){
                return return_format('',30007, lang('userid_error'));
            }
        }else{
            return return_format('',30007, lang('userid_error'));
        }
        $userid =  $post['userid'];
        //现在可以添加的
        //4巡检 12管理员 13销售 14财务  15销售主管(新增)
        if(!empty($post['userroleid'])){
            $userroleid = $post['userroleid'];
        }else{
            return return_format('',30008,lang('userroleid_error'));
        }
        //账户account
        $account = trim($post['account']);
        //判断是修改还是添加
        //method 1表示增加   2表示修改
        $ret = [1,2];
        if(empty($post['method']) || !in_array($post['method'],$ret)){
            return return_format('',30009,lang('methodid_error'));
        }
        $method = $post['method'];
        //初始化密码
        if ( !empty($post['userpwd']) ) {
            $post['userpwd'] = trim($post['userpwd']);
            $userpwd = $post['userpwd'];
        } else {
            if($method == 1){
                //表示添加
                $userpwd = $this->initpass;
            }elseif($method == 2){
                //表示修改
                $userpwd = '';
            }          
        }
        //重复密码
        if (!empty($post['againpwd'])) {
            $post['againpwd'] = trim($post['againpwd']);
            $againpwd = $post['againpwd'];
        } else {
            if($method == 1){
                //表示添加
                $againpwd = $this->initpass;
            }elseif($method == 2){
                //表示修改
                $againpwd = '';
            }
        }
        //firstname
        $firstname = $post['firstname'];

        //邮箱
        $email = $post['email'];
        //手机号
        $mobile = $post['mobile'];
        //头像地址(选填)
        if(!empty($post['logo'])){
            $userico = $post['logo'];
        }else{
            $userico = "";
        }
        //描述(选填)
        if(!empty($post['description'])){
            $description = $post['description'];
        }else{
            $description = "";
        }
        if( !isset($post['oldsortid']) || !isset($post['sortid']) || ( $post['oldsortid'] == '' ) ||  ($post['sortid'] == '')  ){
            return return_format('',30010,lang('sortid_error'));
        }
        $sortid = $post['sortid'];
        $oldsortid = $post['oldsortid'];
        //默认用户账号状态1 帐号的状态   0:未激活  1:正常   2：已删除
        $state = $this->userstate;
        //创建日期
        $createdate = Date("Y-m-d H:i:s",time());
        //获取版本号
        $redisobj = RedisClient::getInstance();
        //var_dump($redisobj);
        $dbstr = 'emm:version';
        $version = $redisobj->incr($dbstr);
        //获取companyid
        if( empty($post['companyid']) ){
            return return_format('',30013,lang('companyid_error'));
        }
        if( !isPositiveInteger($post['companyid']) ){
            return return_format('',30013,lang('companyid_error'));
        }
        $companyid = $post['companyid'];
        //设置deptid 
        $deptid = $companyid;
        //获取usertype
        $usertype = session('usertype');
        //www域下
        $usertype = 1;
        // die;
        switch ($method) {
            case 1:
                if (isset($companyid)) {
                    //目标排序id
                    //$sortid = 0;
                    //查找是否有这个机构
                    $companymodel = new Company;
                    $companyinfo = $companymodel->getcompanyInfo($companyid);
                    if ($companyinfo) {
                        //countrycode
                        $countrycode = '';
                        $pos = strpos($account, '@');
                        if ($pos === false)   //非邮箱
                        {
                            $registmode = 1;
                            $account = $countrycode . $account;
                        } else {
                            $namestr = ltrim(strstr($account, "@"), "@");
                            $cname = $companyinfo['companyfullname'];
                            if ($namestr != $cname && $pos != false) {
                                return return_format('', 30003, lang('accounttype_error'));
                            }
                            $registmode = 0;
                        }
                        //密码加密
                        if ($userpwd) {
                            //$password = trim(I('userpwd'));
                            //$oldpassword = I('post.userpwdold');
                            $pwd = md5(md5($userpwd) . md5(strtolower(trim($account))));//md5(md5($password).$user['account']);
                        }
                        //logo地址
                        if (!$userico) {
                            $userico = '';
                        }
                        //产品版本号
                        $ProductType = config('config.ServerConf')['ProductType'];
                        if ($ProductType == 'S') {
                            $identification = $companyid . '_' . str_replace('+', '', $account);
                        } else {
                            $identification = str_replace('+', '', $account);
                        }
                        $checkArr = [];
                        $checkArr['userid'] = $userid;
                        $checkArr['companyid'] = $companyid;
                        $checkArr['account'] = $account;
                        $checkArr['usertype'] = $usertype;
                        //先判断这个账户是否已经注册过了
                        $userinfo = new Userinfo();
                        $checkRes = $userinfo->getUserInfo($checkArr);
                        //如果有结果
                        if($checkRes){
                            //判断usercompany的ucstate(0:正常,1:删除或拒绝 ,3  未安装)和userinfo的state(帐号的状态   0:未激活  1:正常   2：已删除)
                            if($checkRes['ucstate'] < 1){
                                return return_format('', 30005, lang('account_exists') );    
                            }
                        }
                        //开启事务 插入到用户表用户组织机构表 用户账户表 Channel表
                        Db::startTrans();
                        //插入userinfo表的字段
                        //identification,usertype,mobile,email,firstname,state,constate,gender,userico,description,version
                        $userinfomodel = new Userinfo;
                        //添加到userinfo表的数据
                        $adduserinfo = [];
                        $adduserinfo['identification'] = $identification;
                        $adduserinfo['usertype'] = $usertype;
                        $adduserinfo['mobile'] = $mobile;
                        $adduserinfo['email'] = $email;
                        $adduserinfo['firstname'] = $firstname;
                        $adduserinfo['state'] = $state;
                        $adduserinfo['userico'] = $userico;
                        $adduserinfo['description'] = $description;
                        $adduserinfo['version'] = $version;
                        $adduserinfo['createdate'] = $createdate;
                        $insertuserid = $userinfomodel->addUserInfo($adduserinfo);
                        //用户账户表
                        //插入useraccout表的字段
                        //userid,account,registmode,md5mobile,pwd,countrycode
                        if($insertuserid){
                            $adduseraccount = [];
                            $adduseraccount['userid'] = $insertuserid;
                            $adduseraccount['account'] = $account;
                            $adduseraccount['registmode'] = $registmode;
                            $adduseraccount['md5mobile'] = md5($mobile);
                            $adduseraccount['pwd'] = $pwd;
                            //这个有没有usertype这个字段
                            //$adduseraccount['usertype'] = $usertype;
                            $adduseraccount['countrycode'] = $countrycode;
                            $useraccount_obj = new Useraccount;
                            $useraccount_res = $useraccount_obj->addUserAccount($adduseraccount);                            
                        }
                        //插入用户表用户组织机构表
                        //插入的字段
                        //userid,companyid,deptid,userroleid,firstname,version,ucstate,mobile,email,locationid,dortid
                        //die;
                        if($deptid && $deptid > 0){
                            $addusercompany['userid'] = $insertuserid;
                            $addusercompany['companyid'] = $companyid;
                            $addusercompany['deptid'] = $deptid;
                            $addusercompany['userroleid'] = $userroleid;
                            $addusercompany['firstname'] = $firstname;
                            $addusercompany['version'] = $version;
                            //ucstate默认是0 0:正常,1:删除或拒绝 ,3  未安装
                            if($state == 1){
                                $addusercompany['ucstate'] = 0;
                            }else{
                                $addusercompany['ucstate'] = 3;
                            }
                            $addusercompany['mobile'] = $mobile;
                            $addusercompany['email'] = $email;
                            $companymodel = new Usercompany;
                            $usercompany_res = $companymodel->addUserCompany($addusercompany);
                            //*增加用户不能直接加入channel,必须同意后才能   9月23日  可以直接插入channel
                            //没用  暂时注释
                            /*$channelinfo=[];
                            $channelinfo['channelid'] = $companyid;
                            $channelinfo['type'] = 1;
                            $channelinfo['userid'] = $insertuserid;
                            $channelinfo['channelid'] = $companyid;
                            $channelinfo['version'] = $version;
                            $channel = new Channel();
                            $channel->addChannel($channelinfo);*/                            
                        }
                        if ($insertuserid && $useraccount_res && $usercompany_res) {
                            Db::commit();
                            $companymodel->treatusersort($deptid, $insertuserid, $oldsortid, $sortid);
                            $redisobj->hset("user:firstname", $identification, $firstname);
                            return return_format($result,0);
                        } else {
                            Db::rollback();
                            return return_format('', 30004, lang('add_exists') );
                        }
                    }
                } else {
                    return return_format('', 30002, lang('param_error') );
                }
                break;
            case 2:
                //修改基本信息
                // 一 先判断该用户是否存在
                $userinfomodel = new Userinfo;
                $where = [];
                $where['a.userid'] = [ 'EQ',$userid ];
                $where['b.companyid'] = [ 'EQ',$companyid ];
                $userInfoRes = $userinfomodel->getUserInfoByUserIdAndCompanyId($where);
                if(!$userInfoRes){
                    //说明这个账户不存在或者账户异常
                    return return_format('', 30012, lang('user_error'));
                }                
                Db::startTrans();
                //修改userinfo中的信息
                $updateuserinfo = [];
                $updateuserinfo['userid'] = $userid;
                $updateuserinfo['mobile'] = $mobile;
                $updateuserinfo['email'] = $email;
                $updateuserinfo['firstname'] = $firstname;
                $updateuserinfo['userico'] = $userico;
                $updateuserinfo['description'] = $description;
                $updateuserinfo['version'] = $version;

                $identification = $userInfoRes['identification'];
                $updateUserInfoRes = $userinfomodel->updateUserInfo($updateuserinfo);   

                $updateuseraccount = [];
                $updateuseraccount['userid'] = $userid;
                $updateuseraccount['md5mobile'] = md5($mobile);
                $useraccount_obj = new Useraccount;
                $accountRes = $useraccount_obj->getUserAccount($userid);
                $updateuseraccount['account'] = $accountRes['account'] ;
                if(!empty($userpwd) ){
                    //表示要修改密码
                    if($accountRes){
                        //重置密码先获取当前的账户
                        $updateuseraccount['pwd'] = md5(md5($userpwd) . md5(strtolower($accountRes['account'])));                    
                    }
                }
                $updateUserAccountRes = $useraccount_obj->updateUserAccount($updateuseraccount);

                //修改usercompany
                $updateusercompany['userid'] = $userid;
                $updateusercompany['companyid'] = $companyid;
                $updateusercompany['version'] = $version;
                $updateusercompany['firstname'] = $firstname;
                $updateusercompany['userroleid'] = $userroleid;
                //ucstate默认是0 0:正常,1:删除或拒绝 ,3  未安装
                $updateusercompany['mobile'] = $mobile;
                $updateusercompany['email'] = $email;
                $companymodel = new Usercompany;        
                $updateUserCompanyRes = $companymodel->updateUserCompany($updateusercompany); 
                if( ($updateUserInfoRes !== false) &&  ($updateUserAccountRes !== false) && ($updateUserCompanyRes !== false) ){
                    //var_dump($updateUserInfoRes);
                    //var_dump($updateUserAccountRes);
                    //var_dump($updateUserCompanyRes);
                    /**原有的有选择部门,已经被注释**/
                    Db::commit();
                    $companymodel->treatusersort($deptid, $userid, $oldsortid, $sortid);
                    $redisobj->hset("user:firstname", $identification, $firstname);
                    return return_format($result,0);
                }else{
                    Db::rollback();
                    return return_format('', 30011, lang('update_error') );                    
                }
                break;
            default:
                return return_format('', 30001, lang('param_method_error'));
        }
    }

    /**
     * [getCompanyUserList  获取机构下的管理员列表]
     * @author zzq
     * @DateTime 2018-07-04
     * @param    [array]                 $data     [筛选条件]
     * @return   [array]                            [查询结果]
     */
    public function getCompanyUserList($data){
        // var_dump($data);
        // die;
        $pagesize = config('pagesize.admin_companyuserlist');//每页行数
        //$pagesize = 5;
        $pagenum  = $data['pagenum'] ;//当前页码
        $where = [];
        if(!empty($data['name'])){
            $where['a.firstname|d.account'] = [ 'like','%'.$data['name'].'%' ];
        }
        //暂时支持4,12,13,14,15
        $where['a.userroleid'] = ['IN',['4','12','13','14','15'] ];
        //获取companyid
        if( empty($data['companyid']) ){
            return return_format('',30013,lang('companyid_error'));
        }
        if( !isPositiveInteger($data['companyid']) ){
            return return_format('',30013,lang('companyid_error'));
        }
        $companyid = $data['companyid'];
        //$companyid = session('curcompanyid');////???原来的代码
        //$companyid = 1;
        $where['a.companyid'] = ['EQ',$companyid];
        $where['a.userid'] = ['NEQ','100000'];/////为什么是100000
        $where['c.state'] = ['NEQ','2'];/////
        $where['a.ucstate'] = ['NEQ','1'];/////

        $usercompany = new Usercompany();         
        //获取企业管理员列表
        $resList = $usercompany->getCompanyUserList($where,$pagenum,$pagesize);
        foreach ($resList as $k => $v) {
            $resList[$k]['roleName'] = getRoleNameByRoleId($v['userroleid']);
        }
        //获取企业管理员列表数目
        $resCount = $usercompany->getCompanyUserListCount($where);
        //返回数组组装
        $result = [
                'data'=>$resList,// 内容结果集
                'pageinfo'=>[
                    'pagesize'=> $pagesize ,// 每页多少条记录
                    'pagenum' => $pagenum ,//当前页码
                    'count'   => $resCount // 符合条件总的记录数
                ],
            ] ;
        return return_format($result,0) ;

    }
    /**
     * [getUserDetail  获取机构下的管理员详情]
     * @author zzq
     * @DateTime 2018-07-04
     * @param    [array]                 $data     [筛选条件]
     * @return   [array]                            [查询结果]
     */
    public function getUserDetail($data){
        $userinfo = new Userinfo();
        //获取管理员详情
        $info['a.userid'] = [ 'EQ',$data['userid'] ];
        $info['b.companyid'] = [ 'EQ',$data['companyid'] ];
        $info['a.state'] = [ 'NEQ','2' ];
        $info['b.ucstate'] = [ 'NEQ','1' ];
        $result = $userinfo->getUserInfoByUserIdAndCompanyId($info);
        $result['rolename'] = getRoleNameByRoleId($result['userroleid']);
        return return_format($result,0);      
    }

    /**
     * 权限管理 //删除管理人员
     * @Author zzq
     * @param  $companyid  企业id
     * @param  $userid  用户id
     * POST | URL:/admin/Setup/delUser
     * return
     */
    public function delUser($data){
        //获取companyid
        if( empty($data['companyid']) ){
            return return_format('',30013,lang('companyid_error'));
        }
        if( !isPositiveInteger($data['companyid']) ){
            return return_format('',30013,lang('companyid_error'));
        }
        $companyid = $data['companyid'];
        //获取userid
        if( empty($data['userid']) ){
            return return_format('',30007,lang('userid_error'));
        }
        if( !isPositiveInteger($data['userid']) ){
            return return_format('',30007,lang('userid_error'));
        }
        //判断这个userid是否存在
        $info['a.userid'] = [ 'EQ',$data['userid'] ];
        $info['b.companyid'] = [ 'EQ',$data['companyid'] ];
        $info['a.state'] = [ 'NEQ','2' ];
        $info['b.ucstate'] = [ 'NEQ','1' ];
        $userinfo = new Userinfo();
        $result = $userinfo->getUserInfoByUserIdAndCompanyId($info);
        if(!$result){
            //表示这个用户不存在
            return return_format('',30012,lang('user_error'));
        }
        $userid = $data['userid'];   
        //获取版本号
        $redisobj = RedisClient::getInstance();
        //var_dump($redisobj);
        $dbstr = 'emm:version';
        $version = $redisobj->incr($dbstr);     
        //更新userinfo中的信息假删除
        $userinfo = new Userinfo();
        $deleteUserInfo= [];
        $deleteUserInfo['userid'] = $userid;
        $deleteUserInfo['version'] = $version;
        $deleteUserInfo['state'] = '2';//删除标识
        $deleteUserInfoRes = $userinfo->updateUserInfo($deleteUserInfo);
        //更新usercompany中的信息 
        $usercompany = new Usercompany();
        $deleteUserCompany['userid'] = $userid;
        $deleteUserCompany['companyid'] = $companyid;
        $deleteUserCompany['ucstate'] = '1';
        $deleteUserCompany['version'] = $version;
        $deleteUserCompanyRes = $usercompany->updateUserCompany($deleteUserCompany);
        if( ($deleteUserInfoRes !== false) && ($deleteUserCompanyRes !== false) ){
            //原代码排序操作,暂时不处理
            $where['userid'] = ['EQ',$userid];
            $where['companyid'] = ['EQ',$companyid];
            $tempsortdata = $usercompany->getUserCompanyInfo($where);
            $usercompany->treatusersort($tempsortdata['deptid'],$userid,$tempsortdata['sortid'],9999999);
            //注销账户,操作redis中的用户的信息
            $userlogin = new UserloginManage();
            $userloginRes = $userlogin->cancellationuserinfo($userid);
            //var_dump($userloginRes);
            return return_format('',0,'删除成功');
        }
    }


    /**
     * 权限管理 //获取某销售人员已关联|未关联的企业
     * @Author zzq
     * @param  $marketid  销售用户id
     * @param  $bindtype  1表示已经绑定的|2表示没有被绑定的
     * return
     */
    public function getBindOrUnbindCompoanyList($data){

        $pagesize = config('pagesize.admin_marketcompanylist');//每页行数
        //$pagesize = 5;
        $pagenum  = $data['pagenum'] ;//当前页码
        //判断marketid
        if(empty($data['marketid'])){
            return return_format('',30007,lang('userid_error'));
        }
        $marketid = $data['marketid'];
        if(!isPositiveInteger($marketid)){
            return return_format('',30007,lang('userid_error'));
        }
        //判断这个marketid对应的是不是销售的角色
        $usercompany = new Usercompany();
        $_where = [];
        $_where['userid'] = $marketid;
        $usercompanyInfo = $usercompany->getUserCompanyInfo($_where);
        if(!$usercompanyInfo){
                return return_format('',30007,lang('userid_error'));
        }else{
            if( ($usercompanyInfo['userroleid'] != '13') && ($usercompanyInfo['userroleid'] != '15') ){
                return return_format('',30014,lang('marketid_error'));
            }
        }
        //判断bindtype
        if(empty($data['bindtype'])){
            return return_format('',30002,lang('param_error'));
        }
        $ret = [1,2];
        $bindtype = $data['bindtype'];
        if(!in_array($bindtype,$ret)){
            return return_format('',30002,lang('param_error'));
        }
        //先查出所有被绑定的企业的companyid
        $marketcompany = new Marketcompany();
        $_allWhere = [];
        $_allWhere['parentid'] = ['EQ','1'];
        $bindCompanyid = $marketcompany->getAllbindCompanyid($_allWhere);
        // var_dump($bindCompanyid);
        // die;
        if($bindtype == 1){
            //已经绑定的企业
            $where['a.marketid'] = ['EQ',$marketid];
        }elseif($bindtype == 2){
            //未被绑定的not in marketcompany表的所有的companyid
            $where['b.companyid'] = ['NOT IN',$bindCompanyid];
        }

        if(!empty($data['companykeyword'])){
            $where['b.companyid|b.companyfullname'] = ['like','%'.$data['companykeyword'].'%'];
        }
        //并且企业的parentid=1为www域下添加的企业
        $where['b.parentid'] = ['EQ','1']; 
        $where['b.companystate'] = ['IN',['0','1']]; 
        if($bindtype == 1){
            $res = $marketcompany->getMarketCompanyInfo($where,$pagenum,$pagesize);
            $count = $marketcompany->getMarketCompanyInfoCount($where);
        }elseif($bindtype == 2){
            $company = new Company();
            $res = $company->getCompanyListBysearch($where,$pagenum,$pagesize);
            $count = $company->getCompanyListBysearchCount($where);
        }
        foreach($res as $k=>$v){
            $res[$k]['companystate'] = getNameByCompanyState($v['companystate']);
        }
        
        
        $result = [
                'data'=>$res,// 内容结果集
                'pageinfo'=>[
                    'pagesize'=> $pagesize ,// 每页多少条记录
                    'pagenum' => $pagenum ,//当前页码
                    'count'   => $count // 符合条件总的记录数
                ]
            ] ;
        return return_format($result,0) ;         
    }

    /**
     * 权限管理 //设置某销售人员关联企业
     * @Author zzq
     * @param  array $data
     * @return  array
     * return
     */
    public function bindCompany($data){
        //检验传递的参数
        //获取companyid
        if( empty($data['companyid']) ){
            return return_format('',30013,lang('companyid_error'));
        }
        if( !isPositiveInteger($data['companyid']) ){
            return return_format('',30013,lang('companyid_error'));
        }
        //判断这个企业是否存在
        $company = new Company();
        $flag = $company->getCompanyInfoById($data['companyid']);
        if(!$flag){
            //说明该企业不存在
            return return_format('',30015,lang('company_notexists'));
        }
        $companyid = $data['companyid'];
        //判断marketid
        if(empty($data['marketid'])){
            return return_format('',30007,lang('userid_error'));
        }
        $marketid = $data['marketid'];
        if(!isPositiveInteger($marketid)){
            return return_format('',30007,lang('userid_error'));
        }
        //判断这个marketid对应的是不是销售的角色
        $usercompany = new Usercompany();
        $_where = [];
        $_where['userid'] = $marketid;
        $usercompanyInfo = $usercompany->getUserCompanyInfo($_where);
        if(!$usercompanyInfo){
                return return_format('',30007,lang('userid_error'));
        }else{
            if( ($usercompanyInfo['userroleid'] != '13') && ($usercompanyInfo['userroleid'] != '15') ){
                return return_format('',30014,lang('marketid_error'));
            }
        }
        //获取这个企业的子企业的companyid
        $company = new Company();
        $childCompanyidArr = $company->getChildCompanyId($companyid);
        //将两个合并
        $companyidArr = $childCompanyidArr;
        array_unshift($companyidArr, (int)$companyid);
        // var_dump($companyidArr);
        // die;
        //插入中间表marketcompany
        $marketcompany = new Marketcompany();
        foreach($companyidArr as $k => $v){
            $insertData = [];
            //判断是否关联过(这里即包括和当前企业与其他企业)
            $bindWhere = [];
            $bindWhere['companyid'] = ['EQ',$v];
            $flag = $marketcompany->getBindByMarketidAndCompanyId($bindWhere);
            if(!$flag){
                //如果没有数据记录
                $insertData['marketid'] = $marketid;
                $insertData['companyid'] = $v;
                $marketcompany->addMarketCompanyInfo($insertData);
                //返回记录
                return return_format('',0,'添加成功' );
            }else{
                return return_format('',30017,lang('bind_again'));
            }
        }
    }

    /**
     * 权限管理 //设置取消关联某个企业(支持批量)
     * @Author zzq
     * @param  array $data
     * @return  array
     * return
     */ 
    public function batchUnbindCompany($data){

        //判断marketid
        if(empty($data['marketid'])){
            return return_format('',30007,lang('userid_error'));
        }
        $marketid = $data['marketid'];
        if(!isPositiveInteger($marketid)){
            return return_format('',30007,lang('userid_error'));
        }
        //判断这个marketid对应的是不是销售的角色
        $usercompany = new Usercompany();
        $_where = [];
        $_where['userid'] = $marketid;
        $usercompanyInfo = $usercompany->getUserCompanyInfo($_where);
        if(!$usercompanyInfo){
                return return_format('',30007,lang('userid_error'));
        }else{
            if( ($usercompanyInfo['userroleid'] != '13') && ($usercompanyInfo['userroleid'] != '15') ){
                return return_format('',30014,lang('marketid_error'));
            }
        }

        //获取companyids
        if( empty($data['companyids']) ){
            return return_format('',30013,lang('companyid_error'));
        }
        $companyids = str_replace('，', ',', $data['companyids']);
        $companyidArr = explode(',',$companyids);
        foreach($companyidArr as $k => $v){
            if( !isPositiveInteger($v) ){
                return return_format('',30013,lang('companyid_error'));
            }
            //判断这个企业是否存在
            $company = new Company();
            $flag = $company->getCompanyInfoById($v);
            if(!$flag){
                //说明该企业不存在
                return return_format('',30015,lang('company_notexists'));
            }
            //还要判断是否存在企业的id和该销售不存在绑定的关系
            $marketcompany = new Marketcompany();
            $bindWhere = [];
            $bindWhere['marketid'] = ['EQ',$marketid];
            $bindWhere['companyid'] = ['EQ',$v];
            $bindFlag = $marketcompany->getBindByMarketidAndCompanyId($bindWhere);
            if(!$bindFlag){
               //说明存在企业没有被绑定这个销售,说明参数有误 
               return return_format('',30018,'companyid:'.$v.lang('bind_never')); 
            }
        }
        //定义一个数组
        $ret = [];
        foreach($companyidArr as $k => $v){
            $companyidArr = [];
            $childCompanyidArr = [];
            //获取这个企业的子企业的companyid
            $company = new Company();
            $childCompanyidArr = $company->getChildCompanyId($v);
            //将两个合并
            $companyidArr = $childCompanyidArr;
            array_unshift($companyidArr, (int)$v);
            //先放到二维数组中
            $ret[] = $companyidArr;            
        }
        //将二维数组转成一维数组
        $deleteArr = [];
        foreach($ret as $k1 => $v1){
            foreach($v1 as $k2 => $v2){
                $deleteArr[] = $v2;
            }
        }
        //去重
        array_unique($deleteArr);
        //删除对应的绑定的关系
        $marketcompany = new Marketcompany();
        $res = $marketcompany->deleteMarketCompanyInfo($marketid,$deleteArr); 
        return $res;

    }



    /**
     * 权限管理 //获取某个销售主管的,关联和未关联的销售人员
     * @Author zzq
     * @param  $companyid  默认为1
     * @param  $marketleaderid  销售用户id
     * @param  $bindtype  1表示已经绑定的|2表示没有被绑定的
     * POST | URL:/admin/Setup/getBindOrUnbindSaleManagerList
     * return
     */
    public function getBindOrUnbindSaleManagerList($data){

        $pagesize = config('pagesize.admin_marketbindedlist');//每页行数
        //$pagesize = 5;
        $pagenum  = $data['pagenum'] ;//当前页码
        //获取companyid
        if( empty($data['companyid']) ){
            return return_format('',30013,lang('companyid_error'));
        }
        if( !isPositiveInteger($data['companyid']) ){
            return return_format('',30013,lang('companyid_error'));
        }
        $companyid = $data['companyid'];
        //判断bindtype
        if(empty($data['bindtype'])){
            return return_format('',30002,lang('param_error'));
        }
        $ret = [1,2];
        $bindtype = $data['bindtype'];
        if(!in_array($bindtype,$ret)){
            return return_format('',30002,lang('param_error'));
        }
        //判断销售主管的id
        if(empty($data['marketleaderid'])){
            //echo 1;
            return return_format('',30019,lang('marketleaderid_error'));
        }
        $marketleaderid = $data['marketleaderid'];
        if(!isPositiveInteger($marketleaderid)){
            //echo 2;
            return return_format('',30019,lang('marketleaderid_error'));
        }
        //判断这个marketleaderid对应的是不是销售主管
        $usercompany = new Usercompany();
        $_where = [];
        $_where['userid'] = $marketleaderid;
        $usercompanyInfo = $usercompany->getUserCompanyInfo($_where);
        if(!$usercompanyInfo){
                //echo 3;
                return return_format('',30019,lang('marketleaderid_error'));
        }else{
            if($usercompanyInfo['userroleid'] != '15'){
                //echo 4;
                return return_format('',30019,lang('marketleaderid_error'));
            }
        }

        //先查出所有被绑定的销售的marketid
        $marketbind = new Marketbind();
        $bindMarketid = $marketbind->getAllbindMarketid();
        //var_dump($bindMarketid);
        //获取该销售主管所关联的所有的销售的列表
        $bindMarketidbyleader = $marketbind->getAllbindMarketidByLeader($marketleaderid);
        //var_dump($bindMarketidbyleader);
        //die;
        $where = [];
        $usercompany = new Usercompany();
        if($bindtype == 1){
            //获取该销售主管所关联的所有的销售的列表
            $where['a.userid'] = ['IN',$bindMarketidbyleader];
            $res = $usercompany->getAllCompanyUserList($where);
        //var_dump($res);
        //die;            
        }elseif($bindtype == 2){
            //获取该销售主管未关联的所有的销售的列表
            $where['a.userid'] = ['NOT IN',$bindMarketid];
            $where['a.userroleid'] = ['EQ','13'];
            $res = $usercompany->getAllCompanyUserList($where);
        //var_dump($res);
        // die;
        }
        return return_format($res,0) ; 
    }


    
    /**
     * 权限管理 ////销售主管 绑定或者解绑 销售(可批量处理)
     * @Author zzq
     * @param  $data   array
     * @return    array
     * return
     */
    public function bindOrUnbindSaleManager($data){
        //获取companyid
        if( empty($data['companyid']) ){
            return return_format('',30013,lang('companyid_error'));
        }
        if( !isPositiveInteger($data['companyid']) ){
            return return_format('',30013,lang('companyid_error'));
        }
        $companyid = $data['companyid'];
        //判断销售主管的id
        if(empty($data['marketleaderid'])){
            return return_format('',30019,lang('marketleaderid_error'));
        }
        $marketleaderid = $data['marketleaderid'];
        if(!isPositiveInteger($marketleaderid)){
            return return_format('',30019,lang('marketleaderid_error'));
        }
        //判断这个marketleaderid对应的是不是销售主管
        $usercompany = new Usercompany();
        $_where = [];
        $_where['userid'] = $marketleaderid;
        $usercompanyInfo = $usercompany->getUserCompanyInfo($_where);
        if(!$usercompanyInfo){
                return return_format('',30019,lang('marketleaderid_error'));
        }else{
            if($usercompanyInfo['userroleid'] != '15'){
                return return_format('',30019,lang('marketleaderid_error'));
            }
        }
        if( !empty($data['bindmarketids']) ){
            $bindmarketids = str_replace('，', ',', $data['bindmarketids']);
            $bindmarketidArr = explode(',',$bindmarketids);
            //判断需要绑定的销售的id
            foreach($bindmarketidArr as $k => $v){
                $usercompanyWhere = [];
                $insertData = [];
                if( !isPositiveInteger($v) ){
                    return return_format('',30021,lang('marketid_error'));
                }
                //判断这个销售用户是否存在,如果有不存在的,表示参数有误
                $usercompany = new Usercompany();
                $usercompanyWhere['userid'] = ['EQ',$v];
                $usercompanyWhere['userroleid'] = ['EQ','13'];
                $flag = $usercompany->getUserCompanyInfo($usercompanyWhere);
                if(!$flag){
                    //说明该销售不存在
                    return return_format('',30021,lang('marketid_error'));
                }
                //判断当前销售是否是否已经被绑定
                $marketbind = new Marketbind();
                $bindWhere = [];
                $bindWhere['marketid'] = ['EQ',$v];
                $bindFlag = $marketbind->getBindByMarketidAndMarketLeaderid($bindWhere);
                if(!$bindFlag){
                   //说明存在企业没有被绑定这个销售,执行添加操作
                   //执行插入
                   $marketbind = new Marketbind();
                   $insertData['marketid'] = $v; 
                   $insertData['marketleaderid'] = $marketleaderid; 
                   $res = $marketbind->addMarketBindLeader($insertData);
                }else{
                    //说明已经绑定,跳过,因为提交的可能有已经绑定的marketid,这些可以忽略
                    continue;
                }

            }            
        }
        if(!empty($data['unbindmarketids'])){
            //设置要取消关联的销售id的数组
            $unbindmarketids = str_replace('，', ',', $data['unbindmarketids']);
            $unbindmarketidArr = explode(',',$unbindmarketids);  
            $deleteMarketidArr = [];
            //判断需要解绑的销售的id
            foreach($unbindmarketidArr as $_k => $_v){
                $usercompanyWhere = [];
                if( !isPositiveInteger($_v) ){
                    return return_format('',30021,lang('marketid_error'));
                }
                //判断这个销售用户是否存在,如果有不存在的,表示参数有误
                $usercompany = new Usercompany();
                $usercompanyWhere['userid'] = ['EQ',$v];
                $usercompanyWhere['userroleid'] = ['EQ','13'];
                $flag = $usercompany->getUserCompanyInfo($usercompanyWhere);
                if(!$flag){
                    //说明该销售不存在
                    return return_format('',30021,lang('marketid_error'));
                }
                //判断当前销售主管和销售是否存在绑定的关系
                $marketbind = new Marketbind();
                $bindWhere = [];
                $bindWhere['marketid'] = ['EQ',$v];
                $bindWhere['marketleaderid'] = ['EQ',$marketleaderid];
                $bindFlag = $marketbind->getBindByMarketidAndMarketLeaderid($bindWhere);
                if(!$bindFlag){
                    //说明存在企业没有被绑定这个销售,跳过解绑操作
                    continue;
                }else{
                    //说明已经绑定,进行解绑
                    $deleteMarketidArr[] = $_v;
                }            
            }
            //删除销售主管和销售的关联
            $marketbind = new Marketbind();
            $res = $marketbind->deleteMarketBindLeader($marketleaderid,$deleteMarketidArr);            
        }
        return return_format('',0,'操作成功') ; 
    }
}

