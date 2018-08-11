<?php
/**企业管理设置**/
namespace app\admin\business;

use think\Db;
use think\Validate;
use app\admin\model\Company;
use app\admin\model\Channel;
use app\admin\model\Useraccount;
use app\admin\model\Usercompany;
use app\admin\model\Marketbind;
use app\admin\model\Marketcompany;
use app\admin\model\Companystatelog;
use app\admin\business\CompanySkinManage;
use app\admin\business\CompanyTemplateManage;
use app\admin\business\TemplateinfoManage;
use app\admin\business\SkinManage;
use app\admin\business\DepartmentManage;
use app\admin\business\CompanyConfigManage;

class CompanyManage
{

    private $pwd;

    /**
     * 生成加密码，用于前端展示
     */
    public function __construct()
    {
        $this->pwd = time();
    }

    /**
     * 获取企业列表
     * @auther 胡博森
     * @param array $arr_data 传入的数组信息
     */
    public function getCompanyList($arr_data)
    {
        $arr_where = [];
        if ($arr_data['company_name']) {
            //判断输入的是企业名称还是企业id
            if (is_numeric($arr_data['company_name'])) {
                $arr_where['c.companyid'] = $arr_data['company_name'];
            } else {
                $arr_where['c.companyname'] = $arr_data['company_name'];
            }
        }
        $arr_company_state = [0, 1, 2, 3, 4];
        if (in_array($arr_data['company_state'], $arr_company_state)){
            $arr_where['companystate'] = $arr_data['company_state'];
        }else{
            $arr_where['companystate'] = ['neq',9];
        }

        if($arr_data['user_role_id'] == 13){ //代表当前登录的人是销售
             $arr_where['marketid'] = $arr_data['user_id'];
        }
        if($arr_data['user_role_id'] == 15){ //当前登录人是销售主管
            //$arr_where['marketid'] = $arr_data['user_id'];
            //查询销售主管以及管理的销售员id
            $obj_market = new Marketbind();
            $arr_market = $obj_market->getAllbindMarketidByLeader($arr_data['user_id']);
            $arr_market[] = $arr_data['user_id'];
            $arr_where['marketid'] = ['in',$arr_market];
        }
        if ($arr_data['chk_month'] && $arr_data['company_state'] == 1) {
            $endtime = date('Y-m-d H:i:s', mktime(23, 59, 59, date('m'), date('t'), date('Y')));
            $starttime = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), 1, date('Y')));
            $arr_where['c.endtime'] = [['gt', $starttime ],[ 'lt',$endtime]];
        }
        if ($arr_data['saleid']) $arr_where['m.marketid'] = $arr_data['saleid'];
        //获取分页信息
        $arr_return_data['page'] = $this->getCompanyPage($arr_data['page'], $arr_where);
        $arr_page['size'] = config('pagesize.admin_companylist');//每页行数
        $page = $arr_return_data['page']['now_page'];
        $arr_page['page'] = $page > 0 ? ($page - 1) * $arr_page['size'] : 0;// 计算起始位置
        $arr_field = ['c.companyid', 'companyname', 'companyfullname', 'starttime', 'endtime', 'seconddomain', 'companystate', 'silentpoint', 'userpoint', 'u.firstname','remark'];//查询的字段
        $obj_company = new Company;
        $arr_company = $obj_company->getCompanyList($arr_where, $arr_field, $arr_page);

        $arr_return_data['data'] = $this->setCompanyPast($arr_company);
        array_walk($arr_return_data['data'],[$this,'handleSales']);
        return return_format($arr_return_data, 0, lang('success'));
    }

    /**
     * 处理企业列表，企业没有销售，企业的名字就是无
     * @auther 胡博森
     * @param $v
     * @param $k
     */
    public function handleSales(&$v,$k){
        if(empty($v['firstname'])){
            $v['firstname'] = '无';
        }
    }

    /**
     * 查询销售列表
     * @auther 胡博森
     */
    public function getSaleList()
    {
        $obj_user_company = new Usercompany();
        $arr_where = ['userroleid' => 13];
        $arr_field = ['userid', 'firstname'];
        $arr_user_list = $obj_user_company->getUserInfo($arr_where, $arr_field);
        return return_format($arr_user_list, 0, lang('success'));
    }

    /**
     * 上传的企业图片
     * @auther 胡博森
     * @param array $arr_data
     */
    public function setCompanyFile($arr_data)
    {
        if (!$arr_data['organid'] || !$arr_data['allpathnode'][0]) return return_format('', 60410, lang('CompanyUploadFileParamError'));
        $obj_upload = new \Upload;
        $arr_file_info = $obj_upload->getUploadFiles($arr_data, 1, $arr_data['organid']);
        if ($arr_file_info['code'] != 0) return return_format('', $arr_file_info['code'], $arr_file_info['info']);
        $arr_return = ['file_url' => $arr_file_info['data']['data']['source_url']];
        $arr_return_info = return_format($arr_return, 0, lang('success'));
        return $arr_return_info;
    }


    /**
     * 获取企业列表分页信息
     * @auther 胡博森
     * @param  int $page
     * @return array
     */
    public function getCompanyPage($page, $arr_where)
    {
        $obj_company = new Company;
        //获取总数据数
        $int_company_number = $obj_company->getCompanyPage($arr_where);
        //总数据量
        $arr_page['sum_data'] = $int_company_number;
        //获取每页显示条数
        $int_size = config('pagesize.admin_companylist');
        //计算总页数
        $arr_page['sum_page'] = ceil($int_company_number / $int_size);
        //计算上一页
        $arr_page['prev_page'] = $page - 1 < 0 ? 1 : $page - 1;
        //计算下一页
        $arr_page['next_page'] = $page + 1 > $arr_page['sum_page'] ? $arr_page['sum_page'] : $page + 1;
        if ($page < $arr_page['prev_page']) {
            $arr_page['now_page'] = (int)$arr_page['prev_page'];
        } else if ($page > $arr_page['next_page']) {
            $arr_page['now_page'] = (int)$arr_page['next_page'];
        } else {
            $arr_page['now_page'] = (int)$page;
        }
        return $arr_page;
    }

    /**
     * 修改企业的过期信息
     * @auther 胡博森
     * @param  array $arr_data 需要处理的企业信息
     */
    public function setCompanyPast($arr_data)
    {
        $obj_company = new Company;
        foreach ($arr_data as $k => $v) {
            if ($v['endtime'] < date('Y-m-d H:i:s')) { //结束时间小于当前时间，公司过期
                if ($v['companystate'] != 0 && $v['companystate'] != 1) {
                    continue;
                }
                $arr_where['companyid'] = $v['companyid'];
                if ($v['companystate'] == 0) { //试用
                    $arr_upd_data = ['companystate' => 3];
                    $arr_data[$k]['companystate'] = 3;
                } elseif ($v['companystate'] == 1) { //正常
                    $arr_upd_data = ['companystate' => 2];
                    $arr_data[$k]['companystate'] = 2;
                }
                $obj_company->setCompanyUpd($arr_where, $arr_upd_data);
            }
        }
        return $arr_data;
    }

    /**
     * 查询企业详情
     * @auther 胡博森
     */
    public function getCompanyDetails($arr_data)
    {
        if (empty($arr_data)) return return_format('', 60413, lang('VerifyCompany'));//没有要查询的数据
        $arr_where = [];
        if (is_numeric($arr_data['company_name'])) {
            $arr_where['c.companyid'] = $arr_data['company_name'];
        } else {
            $arr_where['companyname'] = $arr_data['company_name'];
        }
        $arr_field = ['c.companyid', 'companystate', 'companyfullname', 'seconddomain',
            'authkey', 'silentpoint', 'userpoint', 'starttime', 'endtime',
            'remark', 'smallcharge', 'bigcharge', 'industry', 'paystype',
            'usetype', 'firstname', 'colony', 'u.userid'];
        $obj_company = new Company;
        $arr_company = $obj_company->getCompanyField($arr_where, $arr_field, true);
        $arr_company['companystate'] = $arr_company['companystate'] == 4 ? 1 : 0;
        $arr_company['pwd'] = $this->pwd;//生成假密码
        return return_format($arr_company, 0, lang('success'));
    }


    /**
     * 删除企业
     * @auther 胡博森
     * @param array $arr_where 删除条件
     * @param array $arr_datas 删除后返回列表的搜索条件
     */
    public function setCompanyDel($arr_where,$arr_datas)
    {
        if (empty($arr_where['companyid']) || !is_numeric($arr_where['companyid'])) {
            return return_format('',0,lang('error'));//没有要删除的企业id
        }
        $arr_data = ['companystate'=>9];
        $obj_company = new Company;
        $int_company = $obj_company->setCompanyUpd($arr_where,$arr_data);
        if ($int_company) {//删除成功
            return $this->getCompanyList($arr_datas);
        } else {//删除失败
            return return_format('',60521,lang('DelCompanyError'));
        }
    }

    /**
     * 修改企业备注
     * @auther 胡博森
     */
    public function setCompanyRemark($arr_where,$arr_data){
        if (empty($arr_where['companyid']) || !is_numeric($arr_where['companyid'])) {
            return return_format('',60410,lang('error'));//没有要删除的企业id
        }
        if(mb_strlen($arr_data['remark'],'utf8') > 140){
            return return_format('',60410,lang('RemarkLength'));
        }
        $obj_company = new Company;
        $int_company = $obj_company->setCompanyUpd($arr_where,$arr_data);
        if ($int_company) {//修改成功
            return return_format('',0,lang('success'));
        } else {//修改失败
            return return_format('',60521,lang('UpdCompanyRemarkError'));
        }
    }


    /**
     *查询企业信息
     * @auther 胡博森
     */
    public function getCompanyField($data)
    {
        if (!$data['company_id']) {
            return false;//返回错误
        }
        $arr_where['companyid'] = $data['company_id'];
        $arr_field = ['companyid', 'parentid', 'updatechildcompany', 'videotype',
            'maxvideonum', 'functionitem', 'companytype', 'colony'];
        $obj_company = new Company;
        $arr_company = $obj_company->getCompanyField($arr_where, $arr_field);
        return $arr_company;
    }

    /**
     * 新增企业
     * @auther 胡博森
     * @param  array $arr_data
     */
    public function setCompanyAdd($arr_where, $arr_data)
    {
        //判断input数据是否有空值
        $arr_check_param = $this->checkParam($arr_data);
        //数据不符合验证规则
        if($arr_check_param['code'] != 0) return $arr_check_param;
        //验证公司名称是否被注册
        $arr_check_name = $this->isCompanyRegister($arr_data['companyname']);
        if($arr_check_name['code'] != 0 ) return $arr_check_name;
        //验证密码
        $arr_check_pwd = $this->checkPwd($arr_data['pwd'],$arr_data['pwd_again']);
        if($arr_check_pwd['code'] != 0) return $arr_check_pwd;
        //验证域名和账号是否被注册
        $arr_check_domain = $this->isDomainRegister($arr_data['seconddomain'],$arr_data['account']);
        if($arr_check_domain['code'] != 0) return $arr_check_domain;

        $arr_data['userpoint'] = config('config.ServerConf')['free_point']; //新版：小课点数旧版：交互用户点数
        $arr_data['silentpoint'] = config('config.ServerConf')['free_vroadcast_point'];   //新版：大课点数；旧版：直播用户点数
        $arr_data['countrycode'] = '';//国家码 table:useraccount
        $arr_data['videotype'] = 48; //视频分辨率
        $arr_data['maxvideonum'] = 7;//最大视频数
        $arr_data['numpages'] = 100; //限制课件页数
        $arr_data['coursemaxsize'] = 100; //大课件最大值
        $arr_data['functionitem'] = (new CompanyConfigManage())->getDefaultSetting();
        $arr_data['productmodel'] = 1; //直播
        $arr_data['createchild'] = 0; //不允许创建子企业
        $arr_data['updatechildcompany'] = 0; //不允许同步更新配置项
        $arr_data['pwd'] = $this->createPwd($arr_data['pwd'],$arr_data['countrycode'],$arr_data['account']);//加密登录密码 table：useraccount
        $arr_data['totalstoragesize'] = 0;//存储大小 默认为0
        $arr_data['starttime'] = date('Y-m-d H:i:s');//开始时间
        if($arr_data['companystate'] == 0){ //结束时间 试用期为半年 正常为1年
            $arr_data['endtime'] = date('Y-m-d H:i:s',strtotime('+6 month'));
        }else if($arr_data['companystate'] == 1){
            $arr_data['endtime'] = date('Y-m-d H:i:s',strtotime('+1 year'));
        }
        $arr_data['createtime'] = date('Y-m-d H:i:s'); // 创建时间
        $arr_data['bigcharge'] = 0;//大班课收费模式 0：默认不配置 1：包月 2：包年
        $arr_data['smallcharge'] = 0;//小班课收费模式 0：默认不配置 1：包月 2：包年
        $arr_data['createmode'] = 0;

        $obj_company = new Company();
        $redis = \RedisClient::getInstance();
        if($redis->get('emm:companyid')<10000){
            //查询数据库中最后一条companyid
            $arr_company = $obj_company->getCompanyLastid();
            $redis->set('em::companyid',$arr_company['companyid']);
        }
        $int_company_id = $redis->INCR('emm:companyid');
        $arr_data['authkey'] = $obj_company->CreateAuthkey();
        $arr_data['companyid'] = $int_company_id;//从redis中取出企业id
        $arr_data['version'] = $redis->INCR('emm:version');//从redis中取出版本号

        $int_version = $redis->incr('emm:version');

        //usercompany
        $arr_user_company_data['firstname'] = $arr_data['firstname'];
        $arr_user_company_data['firstname'] = $arr_data['firstname'];
        $arr_user_company_data['userroleid'] = 11; //0： 主讲 1： 助教 2：学员 3：直播用户 4：巡检员 10：系统管理员 11：企业管理员 12：管理员
        $arr_user_company_data['sortid'] = 9999999;
        $arr_user_company_data['ucstate'] = 0;
        $arr_user_company_data['version'] = $int_version;
        $arr_user_company_data['companyid'] = $int_company_id;

        //userinfo
        $arr_user_data['firstname'] = $arr_data['firstname'];
        $arr_user_data['usertype'] = 1;
        $str_product_type = config('config.ServerConf')['product_type'];
        if( $str_product_type== 'S' )
        {
            $arr_user_data['identification'] = $int_company_id.'_'.str_replace('+','',$arr_data['account']);
        }else{
            $arr_user_data['identification'] = str_replace('+','',$arr_data['account']);
        }

        //department
        $arr_department['deptid'] = $arr_data['companyid'];
        $arr_department['companyid'] = $arr_data['companyid'];
        $arr_department['deptname'] = $arr_data['companyfullname'];
        $arr_department['deptparentid'] = 0;
        $arr_department['version'] = $arr_data['version'];
        $arr_department['sortlevel'] = 9999999;

        //useraccount
        $arr_account_data['account'] = $arr_data['account'];
        $arr_account_data['countrycode'] = '';
        $arr_account_data['registmode'] = 1;
        $arr_account_data['md5mobile'] = md5($arr_data['account']);
        $arr_account_data['pwd'] = $arr_data['pwd'];

        //marketcompany
        $arr_market_company['marketid'] = $arr_data['saleid'];
        $arr_market_company['companyid'] = $int_company_id;

        unset($arr_data['pwd_again']);
        unset($arr_data['pwd']);
        unset($arr_data['saleid']);
        unset($arr_data['account']);
        unset($arr_data['firstname']);
        unset($arr_data['user_id']);
        unset($arr_data['countrycode']);
        Db::startTrans();

        try{
            //添加用户
            $obj_user_info = new UserinfoManage();
            $int_user_info_id = $obj_user_info->addUserInfo($arr_user_data);
            if(!$int_user_info_id)throw new \Exception('userinfo',1);
            $arr_data['companyadminid'] = $int_user_info_id;
            //创建企业
            $int_company = $obj_company->setCompanyAdd($arr_data);
            if(!$int_company)throw new \Exception('company',1);
            //获取新增企业id
            $int_company_id = $obj_company->getCompanyAddId();

            //添加department
            $obj_department =  new DepartmentManage();
            $int_department = $obj_department->setDepartAdd($arr_department);
            if(!$int_department)throw new \Exception('department',1);

            //添加usercompany
            $arr_user_company_data['userid'] = $int_user_info_id;
            $arr_user_company_data['deptid'] = $arr_data['companyid'];

            $obj_user_company  = new Usercompany();
            $int_user_company = $obj_user_company->setCompanyUserAdd($arr_user_company_data);
            if(!$int_user_company)throw new \Exception('usercompany',1);

            //添加useraccount
            $arr_account_data['userid'] = $int_user_info_id;
            $obj_user_account = new Useraccount();
            $int_user_info = $obj_user_account->setUserInfoAdd($arr_account_data);
            if(!$int_user_info)throw new \Exception('',1);

            //添加channel
            $arr_channel_date['channelid'] = $arr_data['companyid'];
            $arr_channel_date['userid'] = $int_user_info_id;
            $arr_channel_date['type'] = 1;
            $arr_channel_date['version'] = $int_version;
            $obj_channel = new Channel();
            $int_channel = $obj_channel->setChannelAdd($arr_channel_date);
            if(!$int_channel)throw new \Exception('',1);
            if($arr_market_company['marketid']){
                //添加销售
                $obj_market_company = new Marketcompany();
                $int_market_company = $obj_market_company->setMarketCompanyAdd($arr_market_company);
                if(!$int_market_company)throw new \Exception('',1);
            }
            Db::commit();
            //写入redis
            $redis->hset('user:firstname',$arr_user_data['identification'],$arr_user_data['firstname']);
            return return_format('',0,lang('success'));
        } catch(\Exception $e){
            Db::rollback();
            return return_format('',60521,lang('AddCompanyError'));
        }
    }

    /**
     * 查询账号域名是否被注册
     * @auther 胡博森
     */
    public function isDomainRegister($company_domain, $company_account)
    {
        $obj_company = new Company;
        if ($company_domain && empty($company_account)) {//查询域名是否重复
            $arr_company = $obj_company->getCompanyField(['seconddomain' => $company_domain], ['companyid']);
            if (!empty($arr_company)) return return_format('', 60411, lang('ExistDomain'));
        } else if ($company_domain && $company_account) {//查询域名下的账号是否重复
            $arr_where['c.seconddomain'] = $company_domain;
            $arr_where['ua.account'] = $company_account;
            $arr_company = $obj_company->isDomainRegister($arr_where, ['ua.account']);
            if (!empty($arr_company)) return return_format('', 60411, lang('ExistAccount'));
        } else {
            return return_format('', 60410, lang('AccountDomainNull'));
        }
        return return_format('', 0, lang('Success'));
    }

    /**
     * 查询公司名称是否重复
     * @auther 胡博森
     */
    public function isCompanyRegister($company_name)
    {
        if (empty($company_name)) return return_format('', 60410, lang('CompanyNull'));
        $arr_where['companyname'] = trim($company_name);
        $obj_company = new company;
        $arr_company = $obj_company->getCompanyField($arr_where, ['companyid']);
        if ($arr_company) return return_format('', 60411, lang('ExistCompany'));
        return return_format('', 0, 'success');
    }

    /**
     * 对添加公司的密码检测
     * @auther 胡博森
     * @param string $str_pwd
     * @param string $str_pwd_again
     * @return array
     */
    public function checkPwd($str_pwd, $str_pwd_again)
    {
        if (empty($str_pwd) || empty($str_pwd_again)) {
            return return_format('', 60410, lang('RegistrationInformation'));
        }
        if ($str_pwd != $str_pwd_again) {
            return return_format('', 60412, lang('InconsistentPassword'));
        }
        $str_rule = "/^[\@A-Za-z0-9\!\#\$\%\^\&\*\.\~]{6,20}$/";
        if(!preg_match($str_rule,$str_pwd) || !preg_match($str_rule,$str_pwd_again)){
            return return_format('',60414,lang('PwdInconformityRule'));
        }
        return return_format('', 0, lang('success'));
    }

    /**
     * 参数验证
     * @auther 胡博森
     * @param array $arr_data 要验证的数据
     */
    public function checkParam($arr_data)
    {
        $arr_rule = [
            'companyname' => 'require',
            'companyfullname' => 'require',
            'companystate' => 'require',
            'account' => 'require',
            'firstname' => 'require',
            'seconddomain' => 'require',
            'pwd' => 'require',
            'pwd_again' => 'require',
        ];

        $obj_validate = new Validate($arr_rule);
        $bool_result = $obj_validate->check($arr_data);
        if (!$bool_result) {
            return return_format('', 60410, lang('RegistrationInformation'));
        }
        return return_format('', 0, lang('success'));
    }

    /**
     * 修改企业详情信息
     * @auther 胡博森
     */
    public function setCompanyDetails($arr_where, $arr_data)
    {
        //验证数据
        if (!($this->checkDetails($arr_data))) {
            return return_format('', 60410, lang('ChangeInformation'));
        }
        //验证密码
        $arr_check_pwd = $this->checkPwd($arr_data['admin_pwd'], $arr_data['admin_pwd_again']);
        if ($arr_check_pwd['code'] != 0) {//密码没有验证成功
            return $arr_check_pwd;
        }
        //查询修改后的企业名称是否存在
        $arr_exit_company = $this->isCompanyRegister($arr_data['companyfullname']);
        if (is_array($arr_exit_company['data'])) {
            if ($arr_exit_company['data']['companyid'] != $arr_where['companyid']) return return_format('', 60411, lang('ExistCompany'));
        }

        $arr_company_data = [
            'companyname' => $arr_data['companyname'],
            'colony' => $arr_data['colony'],
            'companyfullname' => $arr_data['companyfullname'],
            'companystate' => $arr_data['companystate'],
            'silentpoint' => $arr_data['silentpoint'],
            'userpoint' => $arr_data['userpoint'],
            'smallcharge' => $arr_data['smallcharge'],
            'bigcharge' => $arr_data['bigcharge'],
            'paystype' => $arr_data['paystype'],
            'usetype' => $arr_data['usetype'],
            'industry' => $arr_data['industry'],
            'starttime' => $arr_data['starttime'],
            'endtime' => $arr_data['endtime'],
            'remark' => $arr_data['remark'],
        ];
        if ($arr_data['companystate'] == 1) { //冻结企业
            $arr_company_data['companystate'] = 4;
        }
        if ($arr_company_data['starttime'] >= $arr_company_data['endtime']) {
            return return_format('', 0, lang('StartGTEnd'));
        }
        $obj_company = new Company;//修改企业信息
        $arr_company_where['companyid'] = $arr_where['companyid'];

        $int_save_useraccount = 1;

        if ($arr_data['admin_pwd'] != $this->pwd) { //接收的密码等于假密码，则不对密码进行修改
            //根据企业id查询管理员id
            $obj_user_company = new Usercompany();
            $arr_admin_where = ['companyid' => $arr_where['companyid'], 'userroleid' => 11];//企业管理员
            $arr_admin_field = ['u.userid', 'a.account', 'a.countrycode'];
            $arr_admin = $obj_user_company->getCompanyAdmin($arr_admin_where, $arr_admin_field);
            $arr_user_data['pwd'] = $this->createPwd($arr_data['admin_pwd'], $arr_admin['countrycode'], $arr_admin['account']);//生成新密码
            $obj_useraccount = new Useraccount();
            $arr_user_where = ['userid' => $arr_admin['userid']];
            $int_save_useraccount = $obj_useraccount->updUserInfo($arr_user_where, $arr_user_data);
        }
        $int_save_company = $obj_company->setCompanyUpd($arr_company_where, $arr_company_data);//修改企业信息

        if ($int_save_company && $int_save_useraccount) {//都修改成功
            return return_format('', 0, lang('ssuccess'));
        } else {
            return return_format('', 60520, lang('SaveCompanyError'));
        }

    }

    /**
     * 获取企业配置
     * @auther 胡博森
     */
    public function getCompanyConfig($arr_where, $arr_data)
    {
        if (empty($arr_where['companyid'])) return return_format('', 60413, lang('VerifyCompany'));
        switch ($arr_data['type']) {
            case 1: //界面显示
                $arr_data = $this->getConfig1($arr_where);
                break;
            case 21: //企业配置项——全局配置项
                $arr_data = $this->getConfig21($arr_where);
                break;
            case 22: //企业配置项——上课相关流程
                $arr_data = $this->getConfig22($arr_where);
                break;
            case 23: //企业配置项——课堂工具
                $arr_data = $this->getConfig23($arr_where);
                break;
            case 24: //企业配置项——版本相关
                $arr_data = $this->getConfig24($arr_where);
                break;
            case 25: //企业配置项——保留项
                $arr_data = $this->getConfig25($arr_where);
                break;
            case 26: //企业配置项——大班课相关
                $arr_data = $this->getConfig26($arr_where);
                break;
            case 3: //回调跳转
                $arr_data = $this->getConfig3($arr_where);
                break;
            case 4: //子企业
                $arr_data = $this->getConfig4($arr_where, $arr_data);
                break;
            default:
                $arr_data = return_format('', 60410, '参数错误');
                break;
        }
        return $arr_data;
    }

    /**
     * 获取企业配置项——界面显示
     * @auther 胡博森
     */
    private function getConfig1($arr_where)
    {
        $arr_field = ['companytitle', 'ico', 'whiteboards', 'dataregionimg', 'functionitem', 'skinver'];
        $obj_company = new Company;
        $arr_data['data'] = $obj_company->getCompanyField($arr_where, $arr_field);
        if ($arr_data['data']['ico']) {
            $arr_data['data']['ico'] = config('config.ServerConf')['tencent_file_url'] . $arr_data['data']['ico'];
        }
        if ($arr_data['data']['dataregionimg']) {
            $arr_data['data']['dataregionimg'] = config('config.ServerConf')['tencent_file_url'] . $arr_data['data']['dataregionimg'];
        }

        $arr_data['template_old_data'] = (new CompanyTemplateManage())->getCompanyTemplate($arr_where);//获取2.0版本企业的模板列表
        $arr_data['template_old_list'] = (new TemplateinfoManage())->getTemplateList(); //查询2.0模板皮肤列表
        $arr_data['template_new_data'] = (new CompanySkinManage())->getCompanySkin($arr_where);//获取3.0版本企业的模板列表
        $arr_data['template_new_list'] = (new SkinManage)->getSkinList($arr_where['companyid']); //查询3.0模板皮肤列表

        $arr_data['data']['chk_whiteboard_impression'] = $arr_data['data']['functionitem'][82];//自定义白板底色
        unset($arr_data['data']['functionitem']);
        return return_format($arr_data, 0, lang('Success'));
    }

    /**
     * 企业配置项——全局配置项
     * @auther 胡博森
     * 说明：localrecordtype :录制格式MP3或MP4
     *       productmodel：是否直播
     *       updatechildcompany：配置项同时更新子企业
     */
    private function getConfig21($arr_where)
    {
        $arr_field = ['videotype', 'maxvideonum', 'numpages', 'coursemaxsize', 'functionitem', 'localrecordtype', 'updatechildcompany', 'createchild'];
        $arr_data['video'] = $this->getVideo();//获取视频分辨率
        $obj_company = new Company;
        $arr_data['data'] = $obj_company->getCompanyField($arr_where, $arr_field);
        $str_functionitem = $arr_data['data']['functionitem'];
        $arr_data['data']['chk_mp'] = $arr_data['data']['localrecordtype']; //本地录制为mp3或者mp4
        $arr_data['data']['chk_create_child'] = $arr_data['data']['createchild']; //是否可以创建子企业
        $arr_data['data']['chk_upd_child'] = $arr_data['data']['updatechildcompany']; //子企业同步更新配置项
        unset($arr_data['data']['functionitem']);
        unset($arr_data['data']['createchild']);
        unset($arr_data['data']['productmodel']);
        unset($arr_data['data']['localrecordtype']);
        unset($arr_data['data']['updatechildcompany']);
        $arr_data['data'] = array_merge($arr_data['data'], $this->disposeConfig21($str_functionitem));
        //处理企业配置
        return return_format($arr_data, 0, lang('success'));
    }

    /**
     * 企业配置项——课堂配置项
     * @auther 胡博森
     * @param $arr_where 查询条件
     */
    private function getConfig22($arr_where)
    {
        $arr_field = ['functionitem'];
        $obj_company = new Company;
        $arr_data = $obj_company->getCompanyField($arr_where, $arr_field);
        $str_functionitem = $arr_data['functionitem'];
        unset($arr_data['functionitem']);
        $arr_data = array_merge($arr_data, $this->disposeConfig22($str_functionitem));
        return return_format($arr_data, 0, lang('success'));
    }

    /**
     * 企业配置项——课堂工具
     * @auther 胡博森
     * @param $arr_where
     * @return array
     */
    private function getConfig23($arr_where)
    {
        $arr_field = ['functionitem'];
        $obj_company = new Company;
        $arr_data = $obj_company->getCompanyField($arr_where, $arr_field);
        $str_functionitem = $arr_data['functionitem'];
        unset($arr_data['functionitem']);
        $arr_data = array_merge($arr_data, $this->disposeConfig23($str_functionitem));
        return return_format($arr_data, 0, lang('success'));
    }

    /**
     * 企业配置项——版本相关
     * @auther 胡博森
     * @param $arr_where
     * @return array
     */
    private function getConfig24($arr_where)
    {

        $arr_field = ['functionitem'];
        $obj_company = new Company;
        $arr_data = $obj_company->getCompanyField($arr_where, $arr_field);
        $str_functionitem = $arr_data['functionitem'];
        unset($arr_data['functionitem']);
        $arr_data = array_merge($arr_data, $this->disposeConfig24($str_functionitem));
        return return_format($arr_data, 0, lang('success'));
    }

    /**
     * 企业配置项——保留项
     * @auther 胡博森
     * @param $arr_where
     * @return array
     */
    private function getConfig25($arr_where)
    {
        $arr_field = ['functionitem'];
        $obj_company = new Company;
        $arr_data = $obj_company->getCompanyField($arr_where, $arr_field);
        $str_functionitem = $arr_data['functionitem'];
        unset($arr_data['functionitem']);
        $arr_data = array_merge($arr_data, $this->disposeConfig25($str_functionitem));
        return return_format($arr_data, 0, lang('success'));
    }

    /**
     * 企业配置项——大班课相关
     * @auther 胡博森
     * @param $arr_where
     * @return array
     */
    private function getConfig26($arr_where)
    {
        $arr_field = ['functionitem', 'productmodel'];
        $obj_company = new Company;
        $arr_data = $obj_company->getCompanyField($arr_where, $arr_field);
        $str_functionitem = $arr_data['functionitem'];
        $arr_data['chk_live'] = $arr_data['productmodel']; //是否直播
        unset($arr_data['functionitem']);
        unset($arr_data['productmodel']);
        $arr_data = array_merge($arr_data, $this->disposeConfig26($str_functionitem));
        return return_format($arr_data, 0, lang('success'));
    }

    /**
     * 回调跳转
     * @auther 胡博森
     */
    private function getConfig3($arr_where)
    {
        $arr_field = [
            'seconddomain',//二级域名
            'roomstartcallbackurl', //上课回调地址
            'callbackurl', //下课回调地址
            'logincallbackurl', //登入登出回调地址
            'recordcallback', //录制完成回调地址
            'filenotifyurl', //文档转换完回调地址
            'helpcallbackurl', //帮助跳转地址
            'recorduploadaddr', //本地录制上传地址
            'jumpserver', //跳转地址
            'livejumpserver', //直播跳转地址
            'livecompanytype', //公司跳转类型 0：缺省  1：使用企业域名 2：用户自定义跳转地址
            'companytype', //公司跳转类型 0：缺省  1：使用企业域名 2：用户自定义跳转地址'
            'functionitem', //配置项
        ];
        $obj_company = new Company;
        $arr_data = $obj_company->getCompanyField($arr_where, $arr_field);
        $arr_config = ['chk_upload_file'];
        $obj_config = new CompanyConfigManage();
        $arr_config = $obj_config->getConfigArr($arr_data['functionitem'], $arr_config);
        unset($arr_data['functionitem']);
        $arr_data = array_merge($arr_data, $arr_config);
        return return_format($arr_data, 0, lang('success'));
    }

    /**
     * 查询子企业
     * @auther 胡博森
     */
    private function getConfig4($arr_where, $arr_data)
    {
        $arr_new_where['parentid'] = $arr_where['companyid'];
        $arr_field = ['companyid', 'companyfullname', 'seconddomain', 'companystate', 'starttime', 'endtime', 'userpoint', 'silentpoint'];
        $obj_company = new Company;
        //获取分页信息
        $arr_data['page'] = $this->getCompanySonPage($arr_data['page'], $arr_new_where);
        $arr_page['size'] = config('pagesize.admin_roomlist');//每页行数
        $page = $arr_data['page']['now_page'];
        $arr_page['page'] = $page > 0 ? ($page - 1) * $arr_page['size'] : 0;// 计算起始位置
        $arr_data['data'] = $obj_company->getCompanySonList($arr_new_where, $arr_field, $arr_page);
        return return_format($arr_data, 0, lang('success'));
    }

    /**
     * 处理企业配置项——全局配置
     * @auther 胡博森
     */
    private function disposeConfig21($str_function)
    {
        $arr_config = [
            'chk_video', 'chk_screen', 'chk_local', 'chk_image_reversal', 'chk_foreground_picture', 'chk_rtmp',
            'chk_voice', 'chk_resolution_consistency', 'chk_client', 'chk_tenminutes_invalid',
            'chk_local_distinguishability', 'chk_assistant_enter', 'chk_hd_audio', 'chk_264_code',
        ];
        $obj_config = new CompanyConfigManage();
        $arr_data = $obj_config->getConfigArr($str_function, $arr_config);
        return $arr_data;
    }

    /**
     * 处理企业配置项——上课相关流程
     * @auther 胡博森
     * @param $str_function
     */
    private function disposeConfig22($str_function)
    {
        $arr_config = [
            'chk_automatic_class', 'chk_students_close', 'chk_hide_button', 'chk_assistant_open',
            'chk_before_class', 'chk_leave_classroom', 'chk_courseware_sync', 'chk_picture_in_picture',
            'chk_close_mp4', 'chk_close_client', 'chk_end_class', 'chk_regional_exchange', 'chk_tour_class',
            'chk_account_login'
        ];
        $obj_config = new CompanyConfigManage();
        $arr_data = $obj_config->getConfigArr($str_function, $arr_config);
        return $arr_data;
    }

    /**
     * 处理企业配置项——课堂工具
     * @auther 胡博森
     */
    private function disposeConfig23($str_function)
    {
        $arr_data = [];
        $arr_config = [
            'chk_procedure_share', 'chk_h5_file', 'chk_strokes_jurisdiction', 'chk_file_paging',
            'chk_file_paging', 'chk_show_answer', 'chk_ppt_remark', 'chk_trophy_voice', 'chk_video_annotation',
            'chk_upload_pictures', 'chk_upload_pictures', 'chk_file_classification', 'chk_strokes_name',
            'chk_screenshot', 'chk_answer', 'chk_turntable', 'chk_timer', 'chk_responder', 'chk_white_board',
            'chk_screen_share', 'chk_file_definition', 'chk_brush_operate', 'chk_QR_code', 'chk_local_video',
            'chk_bothway_share', 'chk_associated_courseware'
        ];
        $obj_config = new CompanyConfigManage();
        $arr_data = $obj_config->getConfigArr($str_function, $arr_config);
        return $arr_data;
    }

    /**
     * 处理企业更多配置——版本相关
     * @auther 胡博森
     * @param $str_function
     */
    private function disposeConfig24($str_function)
    {
        $arr_config = [
            'chk_pointer', 'chk_grouping', 'chk_open_headmaster', 'chk_headmaster_monitoring'
        ];
        $obj_config = new CompanyConfigManage();
        $arr_data = $obj_config->getConfigArr($str_function, $arr_config);
        return $arr_data;
    }

    /**
     * 处理企业更多配置——保留项
     * @auther 胡博森
     * @param $str_function
     */
    private function disposeConfig25($str_function)
    {
        $arr_config = [
            'chk_voice_frequency', 'chk_white_board', 'chk_invite', 'chk_classroom_transcribe', 'chk_share_video',
            'chk_quit_classroom', 'chk_vote', 'chk_file_transfer', 'chk_high_definition', 'chk_questions_answers',
            'chk_hide_chairman', 'chk_hide_teacher', 'chk_text_chat', 'chk_student_list', 'chk_courseware_list',
            'chk_cut_figure', 'chk_web_share', 'chk_automatic_entry_classroom', 'chk_setup_wizard', 'chk_sip_phone',
            'chk_sip_phone', 'chk_h323_mcu', 'chk_automatically_open_video', 'chk_open_classroom',
            'chk_hide_close_button', 'chk_hide_username', 'chk_prioritize', 'chk_play_video', 'chk_server_recorde',
            'chk_automatic_recorde', 'chk_raise_hands'
        ];
        $obj_config = new CompanyConfigManage();
        $arr_data = $obj_config->getConfigArr($str_function, $arr_config);
        return $arr_data;
    }

    /**
     * 处理企业更多配置——大班课相关
     * @auther 胡博森
     * @param $str_function
     */
    private function disposeConfig26($str_function)
    {
        $arr_data = [];
        $arr_data['chk_advertising_position'] = $str_function[88];//广告位
        $arr_data['chk_live_account_login'] = $str_function[84];//直播教室用账号登录
        return $arr_data;
    }

    /**
     * 获取视频分辨率
     * @auther 胡博森
     */
    private function getVideo()
    {
        $arr_video = [];
        $arr_video_lang = config('resolution');
        foreach ($arr_video_lang as $k => $v) {
            $arr_video[] = [
                'video_type' => (int)substr($k, 5),
                'video' => $v,
            ];
        }
        return $arr_video;
    }

    /**
     * 生成登录密码
     * @auther 胡博森
     * @param  string $str_pwd 密码
     * @param  string $str_countrycode 国家码
     * @param  string $str_account 账号
     * @return string
     */
    public function createPwd($str_pwd, $str_countrycode, $str_account)
    {
        return md5(md5($str_pwd) . md5(strtolower(trim($str_countrycode . $str_account))));
    }

    /**
     * 验证修改企业详情信息
     * @auther 胡博森
     */
    private function checkDetails($arr_data)
    {
        $arr_rule = [
            'companyname' => 'require',
            'colony' => 'require',
            'companyfullname' => 'require',
            'silentpoint' => 'require',
            'userpoint' => 'require',
            'admin_pwd' => 'require',
            'admin_pwd_again' => 'require',
            'bigcharge' => 'require',
            'smallcharge' => 'require',
            'paystype' => 'require',
            'usetype' => 'require',
            'industry' => 'require',
            'starttime' => 'require',
            'endtime' => 'require',
        ];
        $obj_validate = new Validate($arr_rule);
        $bool_result = $obj_validate->check($arr_data);
        return $bool_result;
    }

    /**
     * 修改企业配置
     * @author 胡博森
     * @param $int_type 编辑类型
     * @param $arr_data 修改的数据
     */
    public function setCompanyConfig($type, $arr_data)
    {
        if ($type && !is_numeric($type) && !empty($arr_data) && !$arr_data['company_id']) {
            return return_format('', 60410, lang('InputModificationInformation'));
        }
        try {
            $arr_where['companyid'] = $arr_data['company_id'];
            //验证company_id
            if (!$arr_where['companyid'] && !is_numeric($arr_where['companyid'])) throw new \Exception("企业id错误", 1);
        } catch (\Exception $e) {
            return return_format('', 60410, lang('ParamError'));
        }

        unset($arr_data['company_id']);
        switch ($type) {
            case 1:
                $data = $this->setConfig1($arr_where, $arr_data);
                break;
            case 21:
                $data = $this->setConfig21($arr_where, $arr_data);
                break;
            case 22:
                $data = $this->setConfig22($arr_where, $arr_data);
                break;
            case 23:
                $data = $this->setConfig23($arr_where, $arr_data);
                break;
            case 24:
                $data = $this->setConfig24($arr_where, $arr_data);
                break;
            case 25:
                $data = $this->setConfig25($arr_where, $arr_data);
                break;
            case 26:
                $data = $this->setConfig26($arr_where, $arr_data);
                break;
            case 3:
                $data = $this->setConfig3($arr_where, $arr_data);
                break;
            case 4:
                $data = $this->setConfig4($arr_where, $arr_data);
                break;
        }

//        //允许同步的type
//        $arr_type = [1,21,22,23,24,25];
//        if(in_array($type,$arr_type)){
//            //同步更新子企业
//            $int_status = $this->synchronizationCompany($arr_where);
//            if(!$int_status){
//                return return_format('',60511,lang('CompanySonSaveError'));
//            }
//        }
        return $data;
    }

    /**
     * 修改企业更多配置——界面显示
     * @auther 胡博森
     * @param $arr_where
     * @param $arr_data
     */
    private function setConfig1($arr_where, $arr_data)
    {
        try {
            $arr_params['companytitle'] = $arr_data['companytitle'];
            $arr_params['ico'] = $arr_data['ico'];
            $arr_params['whiteboards'] = $arr_data['whiteboards'];
            $arr_params['dataregionimg'] = $arr_data['dataregionimg'];
            $arr_config['chk_whiteboard_impression'] = $arr_data['chk_whiteboard_impression'];
            $arr_template = $arr_data['template_data'];
            $arr_params['skinver'] = $arr_data['skinver'];
        } catch (\Exception $e) {
            return return_format('', 60410, lang('ParamError'));
        }
        $arr_params['ico'] = cutURL($arr_params['ico']);
        $arr_params['dataregionimg'] = cutURL($arr_params['dataregionimg']);
        if ($arr_params['skinver'] == 3) { //使用3.0版本
            $obj_company_skin = new CompanySkinManage();
            $bool_skin = $obj_company_skin->updCompanySkin($arr_where, $arr_template[0]);
        } else { //使用2.0版本
            $obj_company_template = new CompanyTemplateManage();
            $bool_skin = $obj_company_template->updCompanySkin($arr_where, $arr_template[0]);
        }
        if (!$bool_skin) {
            return return_format('', 60520, lang('CompanySkinSaveError'));
        }
        //查询全局配置字符串
        $obj_company = new Company;
        $arr_company = $obj_company->getCompanyField($arr_where, ['functionitem']);
        $obj_config = new CompanyConfigManage;
        $str_fuinctionitem = $obj_config->getConfigKey($arr_company['functionitem'], $arr_config);
        $arr_upd_data = array_merge(['functionitem' => $str_fuinctionitem], $arr_params);

        $int_upd = $obj_company->setCompanyUpd($arr_where, $arr_upd_data);
        if (!$int_upd) {
            return return_format('', 60520, lang('SaveCompanyError'));
        }
        return return_format('', 0, lang('success'));
    }

    /**
     * 修改企业配置项——全局配置
     * @auther 胡博森
     * @param $arr_data 要修改的数据
     * @param $arr_where 要修改的条件
     */
    private function setConfig21($arr_where, $arr_data)
    {
        //接受数据信息
        $arr_config = [];
        try {
            if (!$arr_data['chk_allow_video'] && $arr_data['maxvideonum'] > 7) {
                return return_format('', 60410, lang('MaxVideoNum'));
            }
            $arr_param['videotype'] = $arr_data['videotype'];
            $arr_param['maxvideonum'] = $arr_data['maxvideonum'];
            $arr_param['numpages'] = $arr_data['numpages'];
            $arr_param['coursemaxsize'] = $arr_data['coursemaxsize'];
            $arr_param['videotype'] = $arr_data['videotype'];

            $arr_param['localrecordtype'] = $arr_data['chk_mp'];
            $arr_param['updatechildcompany'] = $arr_data['chk_upd_child']; //子企业同步更新
            $arr_param['createchild'] = $arr_data['chk_create_child'];//创建子企业
            $arr_config['chk_video'] = $arr_data['chk_video'];//视频
            $arr_config['chk_screen'] = $arr_data['chk_screen'];//双屏显示
            $arr_config['chk_local'] = $arr_data['chk_local'];//本地录制
            $arr_config['chk_image_reversal'] = $arr_data['chk_image_reversal'];//自己的视频进行镜像反转
            $arr_config['chk_foreground_picture'] = $arr_data['chk_foreground_picture'];//前景图
            $arr_config['chk_rtmp'] = $arr_data['chk_rtmp'];//RTMP推流
            $arr_config['chk_voice'] = $arr_data['chk_voice'];//切换为纯音频教室
            $arr_config['chk_resolution_consistency'] = $arr_data['chk_resolution_consistency'];//用户分辨率一致
            $arr_config['chk_client'] = $arr_data['chk_client'];//强起客户端
            $arr_config['chk_tenminutes_invalid'] = $arr_data['chk_tenminutes_invalid'];//接口进入房间地址10分钟失效
            $arr_config['chk_local_distinguishability'] = $arr_data['chk_local_distinguishability'];//本地录制分辨率
            $arr_config['chk_assistant_enter'] = $arr_data['chk_assistant_enter'];//不提示助教进入
            $arr_config['chk_hd_audio'] = $arr_data['chk_hd_audio'];//高清音频
            $arr_config['chk_264_code'] = $arr_data['chk_264_code'];//H264编码
        } catch (\Exception $e) {
            return return_format('', 60410, lang('ParamError'));
        }
        //数据验证
        $obj_config = new CompanyConfigManage;
        $bool_config = $obj_config->chkConfig21($arr_config);

        //本地录制可选类型
        $arr_mp = ['mp3', 'mp4'];
        //同步更新子企业可选类型
        $arr_upd_child = [0, 1];
        //是否可以创建企业可选类型
        $arr_upd_company = [0, 1];
        if (!$bool_config || !in_array($arr_param['localrecordtype'], $arr_mp) || !in_array($arr_param['updatechildcompany'], $arr_upd_child) || !in_array($arr_param['createchild'], $arr_upd_company)) {//验证本地录制是否可以录制mp3和mp4,和是否同步更新子企业
            return return_format('', 60410, lang('ParamError'));
        }
        //查询全局配置字符串
        $obj_company = new Company;
        $arr_company = $obj_company->getCompanyField($arr_where, ['functionitem']);

        $str_fuinctionitem = $obj_config->getConfigKey($arr_company['functionitem'], $arr_config);
        $arr_upd_data = array_merge(['functionitem' => $str_fuinctionitem], $arr_param);

        //修改全局配置字符串
        $int_upd = $obj_company->setCompanyUpd($arr_where, $arr_upd_data);
        if (!$int_upd) {
            return return_format('', 60520, lang('SaveCompanyError'));
        }
        return return_format('', 0, lang('success'));
    }

    /**
     * 修改企业配置项——上课流程相关
     * @auther 胡博森
     * @param $arr_where
     * @param $arr_data
     * @return array
     */
    private function setConfig22($arr_where, $arr_data)
    {
        try {
            $arr_config['chk_automatic_class'] = $arr_data['chk_automatic_class'];//自动上课
            $arr_config['chk_students_close'] = $arr_data['chk_students_close'];//学生可自行关闭音视频
            $arr_config['chk_hide_button'] = $arr_data['chk_hide_button'];//隐藏上下课按钮
            $arr_config['chk_assistant_open'] = $arr_data['chk_assistant_open'];//助教是否开启音视频
            $arr_config['chk_before_class'] = $arr_data['chk_before_class'];//上课前发布音视频
            $arr_config['chk_leave_classroom'] = $arr_data['chk_leave_classroom'];//下课后不离开教室
            $arr_config['chk_courseware_sync'] = $arr_data['chk_courseware_sync'];//课件全屏同步
            $arr_config['chk_picture_in_picture'] = $arr_data['chk_picture_in_picture'];//画中画
            $arr_config['chk_close_mp4'] = $arr_data['chk_close_mp4'];//MP4播放完自动关闭
            $arr_config['chk_close_client'] = $arr_data['chk_close_client'];//下课后自动关闭客户端
            $arr_config['chk_end_class'] = $arr_data['chk_end_class'];//按下课时间结束课堂
            $arr_config['chk_regional_exchange'] = $arr_data['chk_regional_exchange'];//本地区域交换
            $arr_config['chk_tour_class'] = $arr_data['chk_tour_class'];//巡课取消点击下课
            $arr_config['chk_account_login'] = $arr_data['chk_account_login'];//交互教室用账号登录
        } catch (\Exception $e) {
            return return_format('', 60410, lang('ParamError'));
        }
        //数据验证
        $obj_config = new CompanyConfigManage;
        $bool_config = $obj_config->chkConfig22($arr_config);
        if (!$bool_config) return return_format('', 60410, lang('ParamError'));
        //查询全局配置字符串
        $obj_company = new Company;
        $arr_company = $obj_company->getCompanyField($arr_where, ['functionitem']);
        $str_functionitem['functionitem'] = $obj_config->getConfigKey($arr_company['functionitem'], $arr_config);
        //修改全局配置字符串
        $int_upd = $obj_company->setCompanyUpd($arr_where, $str_functionitem);
        if (!$int_upd) {
            return return_format('', 60520, lang('SaveCompanyError'));
        }
        return return_format('', 0, lang('success'));
    }

    /**
     * 修改企业配置项——课堂工具
     * @auther 胡博森
     * @param $arr_where
     * @param $arr_data
     * @return array
     */
    private function setConfig23($arr_where, $arr_data)
    {
        try {
            $arr_config['chk_procedure_share'] = $arr_data['chk_procedure_share']; //程序共享
            $arr_config['chk_h5_file'] = $arr_data['chk_h5_file']; //H5文档
            $arr_config['chk_strokes_jurisdiction'] = $arr_data['chk_strokes_jurisdiction'];//画笔权限
            $arr_config['chk_file_paging'] = $arr_data['chk_file_paging'];//允许学生操作翻页
            $arr_config['chk_show_answer'] = $arr_data['chk_show_answer']; //答题结束后自动显示答案
            $arr_config['chk_ppt_remark'] = $arr_data['chk_ppt_remark']; //启用PPT备注
            $arr_config['chk_trophy_voice'] = $arr_data['chk_trophy_voice']; //自定义奖杯声音
            $arr_config['chk_video_annotation'] = $arr_data['chk_video_annotation']; //视频标注
            $arr_config['chk_upload_pictures'] = $arr_data['chk_upload_pictures']; //聊天区上传图片
            $arr_config['chk_file_classification'] = $arr_data['chk_file_classification']; //文件分类
            $arr_config['chk_strokes_name'] = $arr_data['chk_strokes_name'];//画笔落笔显示名字
            $arr_config['chk_screenshot'] = $arr_data['chk_screenshot']; //截图
            $arr_config['chk_answer'] = $arr_data['chk_answer']; //答题器
            $arr_config['chk_turntable'] = $arr_data['chk_turntable']; //转盘
            $arr_config['chk_timer'] = $arr_data['chk_timer']; //计时器
            $arr_config['chk_responder'] = $arr_data['chk_responder']; //抢答器
            $arr_config['chk_white_board'] = $arr_data['chk_white_board']; //小白板
            $arr_config['chk_screen_share'] = $arr_data['chk_screen_share']; //屏幕共享
            $arr_config['chk_file_definition'] = $arr_data['chk_file_definition']; //文档转换清晰度高清
            $arr_config['chk_brush_operate'] = $arr_data['chk_brush_operate'];//按用户区别落笔笔迹
            $arr_config['chk_QR_code'] = $arr_data['chk_QR_code']; //二维码拍照上传
            $arr_config['chk_local_video'] = $arr_data['chk_local_video']; //播放本地媒体文件
            $arr_config['chk_bothway_share'] = $arr_data['chk_bothway_share']; //双向共享
            $arr_config['chk_associated_courseware'] = $arr_data['chk_associated_courseware'];//上课时关联课件免刷新
        } catch (\Exception $e) {
            return return_format('', 60410, lang('ParamError'));
        }
        //数据验证
        $obj_config = new CompanyConfigManage;
        $bool_config = $obj_config->chkConfig23($arr_config);
        if (!$bool_config) return return_format('', 60410, lang('ParamError'));
        //查询全局配置字符串
        $obj_company = new Company;
        $arr_company = $obj_company->getCompanyField($arr_where, ['functionitem']);
        $arr_functionitem['functionitem'] = $obj_config->getConfigKey($arr_company['functionitem'], $arr_config);
        //修改全局配置字符串
        $int_upd = $obj_company->setCompanyUpd($arr_where, $arr_functionitem);
        if (!$int_upd) {
            return return_format('', 60520, lang('SaveCompanyError'));
        }
        return return_format('', 0, lang('success'));
    }

    /**
     * 修改企业配置项——版本相关
     * @auther 胡博森
     * @param $arr_where
     * @param $arr_data
     * @return array
     */
    private function setConfig24($arr_where, $arr_data)
    {
        try {
            $arr_config['chk_pointer'] = $arr_data['chk_pointer'];//教鞭
            $arr_config['chk_grouping'] = $arr_data['chk_grouping'];//分组
            $arr_config['chk_open_headmaster'] = $arr_data['chk_open_headmaster'];//开启班主任
            $arr_config['chk_headmaster_monitoring'] = $arr_data['chk_headmaster_monitoring'];//班主任监控
        } catch (\Exception $e) {
            return return_format('', 60410, lang('ParamError'));
        }
        //数据验证
        $obj_config = new CompanyConfigManage;
        $bool_config = $obj_config->chkConfig24($arr_config);
        if (!$bool_config) return return_format('', 60410, lang('ParamError'));
        //查询全局配置字符串
        $obj_company = new Company;
        $arr_company = $obj_company->getCompanyField($arr_where, ['functionitem']);
        $arr_functionitem['functionitem'] = $obj_config->getConfigKey($arr_company['functionitem'], $arr_config);
        //修改全局配置字符串
        $int_upd = $obj_company->setCompanyUpd($arr_where, $arr_functionitem);
        if (!$int_upd) {
            return return_format('', 60520, lang('SaveCompanyError'));
        }
        return return_format('', 0, lang('success'));
    }

    /**
     * 修改企业配置项——保留项
     * @auther 胡博森
     * @param $arr_where
     * @param $arr_data
     * @return array
     */
    private function setConfig25($arr_where, $arr_data)
    {
        try {
            $arr_config['chk_voice_frequency'] = $arr_data['chk_voice_frequency'];//音频
            $arr_config['chk_white_board'] = $arr_data['chk_white_board'];//白板
            $arr_config['chk_invite'] = $arr_data['chk_invite'];//邀请
            $arr_config['chk_classroom_transcribe'] = $arr_data['chk_classroom_transcribe'];//教室录制
            $arr_config['chk_share_video'] = $arr_data['chk_share_video'];//分享影音
            $arr_config['chk_quit_classroom'] = $arr_data['chk_quit_classroom'];//教室结束自动退出教室
            $arr_config['chk_vote'] = $arr_data['chk_vote'];//投票
            $arr_config['chk_file_transfer'] = $arr_data['chk_file_transfer'];//文件传输
            $arr_config['chk_high_definition'] = $arr_data['chk_high_definition'];//高清
            $arr_config['chk_questions_answers'] = $arr_data['chk_questions_answers'];//问答
            $arr_config['chk_hide_chairman'] = $arr_data['chk_hide_chairman'];//隐藏主席
            $arr_config['chk_hide_teacher'] = $arr_data['chk_hide_teacher'];//隐藏老师
            $arr_config['chk_text_chat'] = $arr_data['chk_text_chat'];//文本聊天
            $arr_config['chk_student_list'] = $arr_data['chk_student_list'];//学员列表
            $arr_config['chk_courseware_list'] = $arr_data['chk_courseware_list'];//课件列表
            $arr_config['chk_cut_figure'] = $arr_data['chk_cut_figure'];//是否切图
            $arr_config['chk_web_share'] = $arr_data['chk_web_share'];//网页共享
            $arr_config['chk_automatic_entry_classroom'] = $arr_data['chk_automatic_entry_classroom'];//自动进入教室
            $arr_config['chk_setup_wizard'] = $arr_data['chk_setup_wizard'];//音视频设置向导
            $arr_config['chk_sip_phone'] = $arr_data['chk_sip_phone'];//sip电话
            $arr_config['chk_h323_mcu'] = $arr_data['chk_h323_mcu'];//呼叫H323终端或MCU
            $arr_config['chk_automatically_open_video'] = $arr_data['chk_automatically_open_video'];//自动开启音视频
            $arr_config['chk_open_classroom'] = $arr_data['chk_open_classroom'];//公开教室
            $arr_config['chk_hide_close_button'] = $arr_data['chk_hide_close_button'];//隐藏视频窗口关闭按钮
            $arr_config['chk_hide_username'] = $arr_data['chk_hide_username'];//隐藏视频窗口用户名
            $arr_config['chk_prioritize'] = $arr_data['chk_prioritize'];//等分视频优先排列
            $arr_config['chk_play_video'] = $arr_data['chk_play_video'];//多线程播放视频
            $arr_config['chk_server_recorde'] = $arr_data['chk_server_recorde'];//服务器录制
            $arr_config['chk_automatic_recorde'] = $arr_data['chk_automatic_recorde'];//自动录制
            $arr_config['chk_raise_hands'] = $arr_data['chk_raise_hands'];//上台后允许举手
        } catch (\Exception $e) {
            return return_format('', 60410, lang('ParamError'));
        }
        //数据验证
        $obj_config = new CompanyConfigManage;
        $bool_config = $obj_config->chkConfig25($arr_config);
        if (!$bool_config) return return_format('', 60410, lang('ParamError'));
        //查询全局配置字符串
        $obj_company = new Company;
        $arr_company = $obj_company->getCompanyField($arr_where, ['functionitem']);
        $arr_functionitem['functionitem'] = $obj_config->getConfigKey($arr_company['functionitem'], $arr_config);
        //修改全局配置字符串
        $int_upd = $obj_company->setCompanyUpd($arr_where, $arr_functionitem);
        if (!$int_upd) {
            return return_format('', 60520, lang('SaveCompanyError'));
        }
        return return_format('', 0, lang('success'));
    }

    /**
     * 修改企业配置项——大班课相关
     * @auther 胡博森
     * @param $arr_where
     * @param $arr_data
     * @return array
     */
    private function setConfig26($arr_where, $arr_data)
    {
        try {
            $arr_param['productmodel'] = $arr_data['chk_live']; //是否直播
            $arr_config['chk_advertising_position'] = $arr_data['chk_advertising_position'];//广告位
            $arr_config['chk_live_account_login'] = $arr_data['chk_live_account_login'];//直播教室用账号登录
        } catch (\Exception $e) {
            return return_format('', 60410, lang('ParamError'));
        }
        //数据验证
        $obj_config = new CompanyConfigManage;
        $bool_config = $obj_config->chkConfig26($arr_config);
        $arr_param_chk = [0, 1];
        if (!$bool_config || !is_numeric($arr_param['productmodel']) || !in_array($arr_param['productmodel'], $arr_param_chk)) return return_format('', 60410, lang('ParamError'));
        //查询全局配置字符串
        $obj_company = new Company;
        $arr_company = $obj_company->getCompanyField($arr_where, ['functionitem']);
        $arr_functionitem['functionitem'] = $obj_config->getConfigKey($arr_company['functionitem'], $arr_config);
        $arr_upd_data = array_merge($arr_param, $arr_functionitem);
        //修改全局配置字符串
        $int_upd = $obj_company->setCompanyUpd($arr_where, $arr_upd_data);
        if (!$int_upd) {
            return return_format('', 60520, lang('SaveCompanyError'));
        }
        return return_format('', 0, lang('success'));
    }

    /**
     * 修改更多配置——回调跳转
     * @auther 胡博森
     */
    private function setConfig3($arr_where, $arr_data)
    {
        try {
            $arr_params['roomstartcallbackurl'] = $arr_data['roomstartcallbackurl']; //上课回调地址
            $arr_params['callbackurl'] = $arr_data['callbackurl'];           //下课回调地址
            $arr_params['logincallbackurl'] = $arr_data['logincallbackurl'];     //登入登出回调地址
            $arr_params['recordcallback'] = $arr_data['recordcallback'];        //录制完成回调地址
            $arr_params['filenotifyurl'] = $arr_data['filenotifyurl'];         //文档转换完回调地址
            $arr_params['helpcallbackurl'] = $arr_data['helpcallbackurl'];       //帮助跳转地址
            $arr_params['recorduploadaddr'] = $arr_data['recorduploadaddr'];      //本地录制上传地址
            $arr_params['jumpserver'] = $arr_data['jumpserver'];            //跳转路径
            $arr_params['livejumpserver'] = $arr_data['livejumpserver'];        //直播跳转路径
            $arr_params['livecompanytype'] = $arr_data['livecompanytype'];       //直播公司跳转类型 0：缺省  1：使用企业域名 2：用户自定义跳转地址
            $arr_params['companytype'] = $arr_data['companytype'];           //公司跳转类型 0：缺省  1：使用企业域名 2：用户自定义跳转地址'
            $arr_config['chk_upload_file'] = $arr_data['chk_upload_file']; //下课后上传本地录制文件
            $arr_params['chk_upload_file'] = $arr_data['chk_upload_file']; //下课后上传本地录制文件
        } catch (\Exception $e) {
            return return_format('', 60410, lang('ParamError'));
        }
        //数据验证
        $obj_config = new CompanyConfigManage;
        $bool_config = $obj_config->chkConfig3($arr_config);
        $bool_chk_params = $this->chkConfig3($arr_params);
        if (!$bool_config || !$bool_chk_params) {
            return return_format('', 60410, lang('ParamError'));
        }
        unset($arr_params['chk_upload_file']);
        //查询全局配置字符串
        $obj_company = new Company;
        $arr_company = $obj_company->getCompanyField($arr_where, ['functionitem']);
        $arr_functionitem['functionitem'] = $obj_config->getConfigKey($arr_company['functionitem'], $arr_config);
        $arr_upd_data = array_merge($arr_params, $arr_functionitem);
        //修改全局配置字符串
        $int_upd = $obj_company->setCompanyUpd($arr_where, $arr_upd_data);
        if (!$int_upd) {
            return return_format('', 60520, lang('SaveCompanyError'));
        }
        return return_format('', 0, lang('success'));
    }

    /**
     * 修改更多配置——子企业 取消关联子企业接口
     * @auther 胡博森
     * @param $arr_where
     * @param $arr_data
     */
    private function setConfig4($arr_where, $arr_data)
    {
        try {
            $arr_params['company_son_id'] = $arr_data['company_son_id'];
        } catch (\Exception $e) {
            return return_format('', 60410, lang('ParamError'));
        }
        if (!is_numeric($arr_params['company_son_id'])) { //数据验证
            return return_format('', 60410, lang('ParamError'));
        }
        $arr_where['parentid'] = $arr_where['companyid'];
        $arr_where['companyid'] = $arr_params['company_son_id'];
        $arr_upd_params['parentid'] = 1; //将取消关联后的子企业父id变为拓课的id
        $obj_company = new Company;
        $int_upd = $obj_company->setCompanyUpd($arr_where, $arr_upd_params);
        if (!$int_upd) {
            return return_format('', 60520, lang('SaveCompanyError'));
        }
        return return_format('', 0, lang('success'));
    }

    /**
     * 数据验证 更多配置——回调跳转
     * @auther 胡博森
     */
    private function chkConfig3($arr_data)
    {
        //上课回调地址
        if (!preg_match("/http:[\/]{2}[a-z]+[.]{1}[a-z\d\-]+[.]{1}[a-z\d]*[\/]*[A-Za-z\d]*[\/]*[A-Za-z\d]*[.]*html/", $arr_data['roomstartcallbackurl'])) {
            return false;
        }
        //下课回调地址
        if (!preg_match("/http:[\/]{2}[a-z]+[.]{1}[a-z\d\-]+[.]{1}[a-z\d]*[\/]*[A-Za-z\d]*[\/]*[A-Za-z\d]*[.]*html/", $arr_data['callbackurl'])) {
            return false;
        }
        //登入登出回调地址
        if (!preg_match("/http:[\/]{2}[a-z]+[.]{1}[a-z\d\-]+[.]{1}[a-z\d]*[\/]*[A-Za-z\d]*[\/]*[A-Za-z\d]*[.]*html/", $arr_data['logincallbackurl'])) {
            return false;
        }
        //录制完成回调地址
        if (!preg_match("/http:[\/]{2}[a-z]+[.]{1}[a-z\d\-]+[.]{1}[a-z\d]*[\/]*[A-Za-z\d]*[\/]*[A-Za-z\d]*[.]*html/", $arr_data['recordcallback'])) {
            return false;
        }
        //文档转换完回调地址
        if (!preg_match("/http:[\/]{2}[a-z]+[.]{1}[a-z\d\-]+[.]{1}[a-z\d]*[\/]*[A-Za-z\d]*[\/]*[A-Za-z\d]*[.]*html/", $arr_data['filenotifyurl'])) {
            return false;
        }
        //帮助跳转地址
        if (!preg_match("/http:[\/]{2}[a-z]+[.]{1}[a-z\d\-]+[.]{1}[a-z\d]*[\/]*[A-Za-z\d]*[\/]*[A-Za-z\d]*[.]*html/", $arr_data['helpcallbackurl'])) {
            return false;
        }
        //本地录制上传地址
        if ($arr_data['chk_upload_file'] && !preg_match("/http:[\/]{2}[a-z]+[.]{1}[a-z\d\-]+[.]{1}[a-z\d]*[\/]*[A-Za-z\d]*[\/]*[A-Za-z\d]*[.]*html/", $arr_data['recorduploadaddr'])) {
            return false;
        }
        //跳转路径
        if (!empty($arr_data['jumpserver']) && $arr_data['companytype'] == 2 && !preg_match("/http:[\/]{2}[a-z]+[.]{1}[a-z\d\-]+[.]{1}[a-z\d]*[\/]*[A-Za-z\d]*[\/]*[A-Za-z\d]*[.]*html/", $arr_data['jumpserver'])) {
            return false;
        }
        //直播跳转路径
        if (!empty($arr_data['livejumpserver']) && $arr_data['livecompanytype'] == 2 && !preg_match("/http:[\/]{2}[a-z]+[.]{1}[a-z\d\-]+[.]{1}[a-z\d]*[\/]*[A-Za-z\d]*[\/]*[A-Za-z\d]*[.]*html/", $arr_data['livejumpserver'])) {
            return false;
        }
        return true;
    }

    /**
     * 重置密码
     * @auther 胡博森
     * @param $arr_data
     * @return array
     */
    public function setCompanyPwd($arr_data)
    {
        //数据验证
        $arr_rule = [
            'companyid' => 'require',
            'pwd' => 'require',
            'pwd_again' => 'require',
        ];
        $obj_validate = new Validate($arr_rule);
        $bool_result = $obj_validate->check($arr_data);
        if (!$bool_result || $arr_data['pwd'] != $arr_data['pwd_again']) {
            return return_format('', 60410, lang('ParamError'));
        }

        $obj_user_company = new Usercompany();
        $arr_admin_where = ['companyid' => $arr_data['companyid'], 'userroleid' => 11];//企业管理员
        $arr_admin_field = ['u.userid', 'a.account', 'a.countrycode'];
        $arr_admin = $obj_user_company->getCompanyAdmin($arr_admin_where, $arr_admin_field);
        $arr_user_data['pwd'] = $this->createPwd($arr_data['pwd'], $arr_admin['countrycode'], $arr_admin['account']);//生成新密码
        $obj_useraccount = new Useraccount();
        $arr_user_where = ['userid' => $arr_admin['userid']];
        $int_save_useraccount = $obj_useraccount->updUserInfo($arr_user_where, $arr_user_data);
        if (!$int_save_useraccount) {
            return return_format('', 60520, lang('SavePasswordError'));
        }
        return return_format('', 0, lang('success'));
    }

    /**
     * 修改企业的状态
     * @auther 胡博森
     */
    public function setCompanyState($arr_data)
    {
        //数据验证
        $arr_rule = [
            'companyid' => 'require|number',
            'operation' => 'require|number',
        ];
        $obj_validate = new Validate($arr_rule);
        $bool_result = $obj_validate->check($arr_data);
        if (!$bool_result) {
            return return_format('', 60410, lang('ParamError'));
        }
        $int_operation = $arr_data['operation'];
        unset($arr_data['operation']);
        $arr_where['companyid'] = $arr_data['companyid'];
        $obj_company = new Company;
        $obj_info = new Companystatelog;
        //查询需要冻结的企业状态
        $arr_company = $obj_company->getCompanyField($arr_where, ['companystate']);
        $arr_data['status'] = $arr_company['companystate'];
        if ($int_operation == 4 && $arr_data['status'] < 4) {
            Db::startTrans();
            try {
                $arr_company_data = ['companystate' => 4];
                //将企业状态改为冻结
                $bool_company = $obj_company->setCompanyUpd($arr_where, $arr_company_data);
                //将冻结前状态存入日志表中
                $bool_info = $obj_info->setState($arr_data);
                if (!$bool_company || !$bool_info) throw new \Exception("error", 1);//事务失败
                Db::commit();
                return return_format($arr_company_data, 0, lang('success'));
            } catch (\Exception $e) { //事务失败
                Db::rollback();
                return return_format('', 60250, lang('SaveCompanyStateError'));
            }
        } else if ($int_operation == 1 && $arr_data['status'] == 4) {
            //查询恢复企业的日志
            $arr_field = ['status'];
            $arr_data_info = $obj_info->getSate($arr_where, $arr_field);
            //修改企业状态
            $arr_company_data['companystate'] = $arr_data_info['status'];
            $bool_company = $obj_company->setCompanyUpd($arr_where, $arr_company_data);
            if (!$bool_company) {
                return return_format('', 60520, lang('SaveCompanyStateError'));
            }
            return return_format($arr_company_data, 0, lang('success'));
        }
        return return_format('', 60410, lang('ParamError'));
    }

    /**
     * 企业关联子企业
     * @auther 胡博森
     * @param $arr_data
     */
    public function setCompanyParent($arr_data)
    {
        $arr_where['companyid'] = $arr_data['companyid'];
        $arr_data['parentid'] = $arr_data['parentid'];
        $arr_rule = [
            'companyid' => 'require|number',
            'parentid' => 'require|number',
        ];
        $obj_validate = new Validate($arr_rule);
        $bool_result = $obj_validate->check($arr_data);
        if (!$bool_result) {
            return return_format('', 60410, lang('ParamError'));
        }
        if ($arr_data['companyid'] == $arr_data['parentid']) {
            return return_format('', 60411, lang('CompanyParentError'));
        }
        $obj_company = new Company;
        $int_company = $obj_company->setCompanyUpd($arr_where, $arr_data);
        if (!$int_company) {
            return return_format('', 60520, lang('CompanyRelevanceSon'));
        }
        return return_format('', 0, lang('success'));
    }

    /**
     * 查询子企业
     * @auther 胡博森
     * @param $arr_data
     */
    public function getCompanySon($arr_data)
    {
        if (empty($arr_data['companyfullname'])) {
            $arr_where = [];
        } else {
            if (is_numeric($arr_data['companyfullname']) && strlen($arr_data['companyfullname']) > 4) {
                $arr_where['companyid'] = $arr_data['companyfullname'];
            } else {
                $arr_where['companyfullname'] = ['like', $arr_data['companyfullname'] . '%'];
            }
        }

        $arr_field = ['companyfullname', 'companyid', 'parentid'];
        $arr_data['page'] = $this->getCompanySonPage($arr_data['page'], $arr_where);
        $arr_page['size'] = config('pagesize.admin_roomlist');//每页行数
        $page = $arr_data['page']['now_page'];
        $arr_page['page'] = $page > 0 ? ($page - 1) * $arr_page['size'] : 0;// 计算起始位置
        $obj_company = new Company;
        $arr_data['data'] = $obj_company->getCompanySonList($arr_where, $arr_field, $arr_page);
        unset($arr_data['companyfullname']);
        return return_format($arr_data, 0, lang('success'));
    }

    /**
     * 查询企业的分页信息
     * @auther 胡博森
     */
    private function getCompanySonPage($page, $arr_where)
    {
        $obj_company = new Company;
        //获取总数据数
        $int_company_number = $obj_company->getCompanySonPage($arr_where);
        //总数据量
        $arr_page['sum_data'] = $int_company_number;
        //获取每页显示条数
        $int_size = config('pagesize.admin_companylist');
        //计算总页数
        $arr_page['sum_page'] = ceil($int_company_number / $int_size);
        //计算上一页
        $arr_page['prev_page'] = $page - 1 < 0 ? 1 : $page - 1;
        //计算下一页
        $arr_page['next_page'] = $page + 1 > $arr_page['sum_page'] ? $arr_page['sum_page'] : $page + 1;
        if ($page < $arr_page['prev_page']) {
            $arr_page['now_page'] = (int)$arr_page['prev_page'];
        } else if ($page > $arr_page['next_page']) {
            $arr_page['now_page'] = (int)$arr_page['next_page'];
        } else {
            $arr_page['now_page'] = (int)$page;
        }
        return $arr_page;
    }

    /**
     * 同步更新子企业
     * 如果同步更新子企业，调用此方法
     * @auther 胡博森
     */
    private function synchronizationCompany($arr_where)
    {
        $obj_company = new Company;
        //查询父企业数据
        $arr_company_where['companyid'] = $arr_where['companyid'];
        $arr_parent_field = ['whiteboards', 'dataregionimg', 'skinver', 'videotype', 'maxvideonum', 'numpages', 'coursemaxsize', 'functionitem', 'localrecordtype', 'updatechildcompany'];
        $arr_parent_data = $obj_company->getCompanyField($arr_company_where, $arr_parent_field);
        if ($arr_parent_data['updatechildcompany'] == 1) {
            unset($arr_parent_data['updatechildcompany']);
            //查询所有的子企业
            $arr_son_field = ['companyid'];
            $arr_son_where = ['parentid' => $arr_where['companyid']];
            $arr_son_id = $obj_company->getCompanySonList($arr_son_where, $arr_son_field);
            if (empty($arr_son_id)) return 1; //如果当前企业没有子企业，则返回1 代表子企业同步成功
            $arr_son_id = array_column($arr_son_id, 'companyid');
            //修改子企业数据
            $arr_new_where['parentid'] = $arr_where['companyid'];
            Db::startTrans();
            try {
                $int_upd = $obj_company->setCompanyUpd($arr_new_where, $arr_parent_data); //修改子企业配置
                if ($arr_parent_data['skinver'] == 2) { //同步2.0皮肤
                    //查询2.0皮肤
                    $bool_skin = (new CompanyTemplateManage())->synchronizationTemplate($arr_company_where, $arr_son_id);

                } else if ($arr_parent_data['skinver'] = 3) { //同步3.0皮肤
                    $bool_skin = (new CompanySkinManage())->synchronizationSkin($arr_company_where, $arr_son_id);
                }
                if (!$int_upd || !$bool_skin) throw new \Exception('子企业同步失败，回滚', 0);
                Db::commit();
                return 1; //子企业同步成功
            } catch (\Exception $e) {
                Db::rollback();
                return 0;
            }
        }
        return 2; //没有配置同步更新子企业
    }
}