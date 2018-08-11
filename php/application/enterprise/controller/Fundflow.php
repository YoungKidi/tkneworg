<?php
//namespace app\enterprise\controller;
//use think\Request;
//use think\Controller;
//use app\enterprise\business\FundflowManage;
//
///**
//* @name 财务管理
//* @auther 汪子龙
// */
//class Fundflow extends Controller{
//	private $companyid //公司id
//    public function __construct()
//    {
//        $this->companyid = 1;
//    }
//
//    /**
//     * 获取订单列表
//     */
//    public function getFundflowList()
//    {
//    	$arr_data = [];
//    	$obj_fundflow = new FundflowManage;
//    	$arr_data = $obj_fundflow->getFundflowList();
//        return $this->ajaxReturn($arr_data);
//    }
//｝