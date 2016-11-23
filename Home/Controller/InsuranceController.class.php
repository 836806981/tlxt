<?php
namespace Home\Controller;
use Think\Controller;
class InsuranceController extends CommonController {
    public function  login_test(){
        if(!$_SESSION[C('USER_AUTH_KEY')]['id']){
            echo "<script>alert('请登录！');window.location.href='" . __MODULE__ . "/Admin/login.html';</script>";
            exit;
            //str_pad($str,6,0,STR_PAD_LEFT);  6位数不足补0
        }
    }


    public function index(){

        $this->authority(array(11,3));

        $this->display();
    }



    //获取列表数据
    public function getInsuranceList(){
        $currentpage = I("post.currentpage");
        $pagenum = I("post.pagenum");
        $start = ($currentpage - 1) * $pagenum;
        $post = I("post.");

        $where = '1  ';
        if($post['name']&&$post['name']!=''){
            $where .= ' and  nurse_name LIKE "%'.$post['name'].'%"';
        }
        if($post['b_time']){
            $where .= ' and (b_time ="'.$post['b_time'].'" OR true_b_time ="'.$post['b_time'].'") ';
        }
        if($post['id_card']){
            $where .= ' and nurse_id_card ="'.$post['id_card'].'"';
        }
        if($post['safe_time']){
            $where .= ' and safe_time ="'.$post['safe_time'].'"';
        }

        if($post['is_safe']){
            $where .= ' and is_safe = '.$post['is_safe'].'';
        }


        $list = M('order_nurse')->where($where)->limit($start,$pagenum)->order('is_safe asc,   IF(true_b_time!="",true_b_time,b_time) desc')->select();

        $is_safe_name = ['','未购买','已购买','不购买'];
        foreach($list as $k=>$v){
            $list[$k]['is_safe_name'] = $is_safe_name[$v['is_safe']];
        }

        $count = M('order_nurse')->where($where)->count();

        $back['data']['list'] = $list;
        $back['data']['num'] = $count;
        $back['code'] = 1000;
        echo json_encode($back);
    }

    //购买保险
    public function buy(){

        $this->authority(array(11,3));
        if(!I('get.id')) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $order_nurse = M('order_nurse')->where('id='.I('get.id').'')->find();
        if(!$order_nurse){
            echo "<script>alert('地址异常！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }

        $save_order_nurse['is_safe'] = 2;
        $save_order_nurse_mod = M('order_nurse')->where('id='.I('get.id').'')->save($save_order_nurse);
        if($save_order_nurse_mod!==false){
            echo "<script>alert('成功！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }else{
            echo "<script>alert('失败，请重试！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
    }

    //忽略保险
    public function no_buy(){

        $this->authority(array(11,3));
        if(!I('get.id')) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $order_nurse = M('order_nurse')->where('id='.I('get.id').'')->find();
        if(!$order_nurse){
            echo "<script>alert('地址异常！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }

        $save_order_nurse['is_safe'] = 3;
        $save_order_nurse_mod = M('order_nurse')->where('id='.I('get.id').'')->save($save_order_nurse);
        if($save_order_nurse_mod!==false){
            echo "<script>alert('成功！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }else{
            echo "<script>alert('失败，请重试！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
    }





















}