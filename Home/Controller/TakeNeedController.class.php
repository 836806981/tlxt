<?php
namespace Home\Controller;
use Think\Controller;
class TakeNeedController extends CommonController {
    public function _initialize(){
        if(!$_SESSION[C('USER_AUTH_KEY')]['id']){
            echo "<script>alert('请登录！');window.location.href='" . __MODULE__ . "/Index/login.html';</script>";
            exit;
        }
    }

//顾问接单及后续

    ///////////////////////客户派单//////////////////////

    //客服。派单列表。
    public function serviceList(){

        $pai = M('employee')->where('permission in(1,24)')->select();
        $gu  = M('employee')->where('permission =2 ')->select();
        $this->assign('pai',$pai);
        $this->assign('gu',$gu);
        $this->display();
    }

    //客服派单。获取派单列表数据
    public function getServiceList(){
        $currentpage = I("post.currentpage");
        $pagenum = I("post.pagenum");
        $start = ($currentpage - 1) * $pagenum;
        $post = I("post.");
        $where = 'order_type = 1  and status<22';
        if($post['number']&&$post['number']!=''){
            $where .= ' and  number LIKE "%'.$post['number'].'%"';
        }
        if($post['status_1']){
            $where .= ' and status_1 <='.(strtotime($post['status_1'])+86400) .' and status_1 >='.strtotime($post['status_1']) .'';
        }
        if($post['sales_id']&&$post['sales_id']!=0){
            $where .= ' and  sales_id LIKE "%'.$post['sales_id'].'%"';
        }
        if($post['employee_id']&&$post['employee_id']!=0){
            $where .= ' and  employee_id ='.$post['employee_id'].'';
        }
        if($post['status']==0){
            $where .= ' and status in(22,1,2)';
        }else{
            $where .= ' and status = '.$post['status'].'';
        }
        $list = M('order')->where($where)->limit($start,$pagenum)->order('add_time desc')->select();

        $status_name = ['','待接单','已接单','已完善','已发单','已匹配','已签单','已上户','已下户','已完结'];
        $status_name[22] = ['已打回'];
        $status_name[23] = ['回收站'];
        foreach($list as $k=>$v){
            if(in_array($v['product'],array('小田螺','大田螺','金牌田螺','超级田螺'))){
                $list[$k]['product'] = '月嫂-'.$v['product'];
            }
            $list[$k]['time_down'] = 0;
            if(time() - $v['status_1'] < 300){
                $list[$k]['time_down'] = 300 - (time() - $v['status_1']);
            }
            $list[$k]['status_1_str'] = $v['status_1']?date('Y-m-d H:i:s',$v['status_1']):0;
            $list[$k]['status_2_str'] = $v['status_2']?date('Y-m-d H:i:s',$v['status_2']):0;
            $list[$k]['status_name'] = $status_name[$v['status']];
            $list[$k]['is_service_name'] = ($v['is_service']==1?'售后':'正常');


        }
        $count = M('order')->where($where)->count();
        $back['data']['list'] = $list;
        $back['data']['num'] = $count;
        $back['code'] = 1000;
        $back['permission'] = $_SESSION[C('USER_AUTH_KEY')]['permission'];
        echo json_encode($back);
    }


    //接单的详情

    public function needInfo(){
        if (!I('get.id')) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('order')->where('id=' . I('get.id') . '')->find();
        if (!$info) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info['status_1_str'] = $info['status_1']?date('Y-m-d H:i:s',$info['status_1']):0;
        $info['status_2_str'] = $info['status_2']?date('Y-m-d H:i:s',$info['status_2']):0;


        $this->assign('info', $info);
        $this->display();
    }

    //打回
    public function backNeed(){
        if (!I('post.order_id')) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('order')->where('id=' . I('post.order_id') . '')->find();
        if (!$info) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if($info['status']>4){
            echo "<script>alert('已匹配无法打回！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $save['status_22'] = time();
        $save['status'] = 22;
        $save['back_reason'] = I('post.back_reason');

        $save_mod = M('order')->where('id='.I('post.order_id').'')->save($save);
        if($save_mod!==false){
            echo "<script>alert('成功'); window.location.href='".__MODULE__."/TakeNeed/serviceList.html'</script>";
            exit;
        }else{
            echo "<script>alert('失败');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }





    }


}