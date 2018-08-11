<?php
namespace app\enterprise\controller;
use think\Controller;
use think\View;

class Docs extends Controller {


    
    public function index(){
        require  "./../application/enterprise/api/WangC.php";
        require  "./../application/enterprise/api/WangZL.php";
        
        // $api[] = array(
        //          'url'=>'/enterprise/Course/getCurricukumList',
        //          'name'=>'课程列表接口',
        //          'type'=>'get',
        //          'data'=>"{'status':1,'coursename':1}",
        //          'tip'=>"{'status':'0下架 1上架','coursename':'课程名称','limit':'第几页'}",
        //          'returns'=>"{'id': '课程id',
        //                      'imageurl': '课程图片',
        //                         'coursename': '课程名称',
        //                         'price': '基础价',
        //                         'status': '状态 0下架 1上架',
        //                         'categoryid': 6,
        //                         'categoryname': '分类名称'}",
        //          );


        $api = array_merge($WangC,$WangZL);
        $view = new View();
        $view->num = sizeof($api);
        $view->api = $api;

        return $view->fetch('Docs/home');
    }
}

   