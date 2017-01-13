<?php
namespace Home\Controller;
use Think\Controller;
class HoldController extends CommonController {


    public function _initialize(){
        if(!$_SESSION[C('USER_AUTH_KEY')]['id']){
            echo "<script>alert('请登录！');window.location.href='" . __MODULE__ . "/Index/login.html';</script>";
            exit;
        }
    }

    //维系列表页
    public function holdList(){
        $this->authority(array(24,2,5));
        $this->display();
    }

    public function getHoldList(){
        $this->authority(array(24,2,5));
        $currentpage = I("post.currentpage");
        $pagenum = I("post.pagenum");
        $start = ($currentpage - 1) * $pagenum;
        $post = I("post.");
        $where = 'order_type = 1 ';
        if($post['number']&&$post['number']!=''){
            $where .= ' and  number LIKE "%'.$post['number'].'%"';
        }
        if($post['name']&&$post['name']!=''){
            $where .= ' and  name LIKE "%'.$post['name'].'%"';
        }
        if($post['status_6']){
            $where .= ' and status_6 <='.(strtotime($post['status_6'])+86400) .' and status_6 >='.strtotime($post['status_6']) .'';
        }
        if($post['status']==0){
            $where .= ' and status in(6,7,8,9)';
        }else{
            $where .= ' and status = '.$post['status'].'';
        }
        $list = M('order')->where($where)->limit($start,$pagenum)->order('add_time desc')->select();

        $status_name = ['','待接单','已接单','已完善','待匹配','已匹配','已签单','已上户','已下户','已完结'];
        $status_name[22] = ['已打回'];
        $status_name[23] = ['回收站'];
        foreach($list as $k=>$v){
            if(in_array($v['product'],array('小田螺','大田螺','金牌田螺','超级田螺'))){
                $list[$k]['product'] = '月嫂-'.$v['product'];
            }
            $list[$k]['status_6_str'] = $v['status_6']?date('Y-m-d H:i:s',$v['status_6']):'';
            $list[$k]['status_7_str'] = $v['status_7']?date('Y-m-d H:i:s',$v['status_7']):'';
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

    public function holdInfo(){
        $this->authority(array(24,2,5));

        if (!I('get.id')) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('order')->where('id=' . I('get.id') . '')->find();
        if (!$info) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info_in = M('order_info')->where('order_id='.I('get.id').'')->find();
        if($info_in) {
            $info = array_merge($info_in, $info);
        }

        $info['skill_a'] = explode(',',$info['skills']);
        $info['status_1_str'] = $info['status_1'] ? date('Y-m-d H:i:s', $info['status_1']) : 0;
        $info['status_2_str'] = $info['status_2'] ? date('Y-m-d H:i:s', $info['status_2']) : 0;

        $info['price_add_str'] = $info['price_add']==1?'有':'无';

        $order_nurse = M('order_nurse')->field('nurse.id,order_nurse.nurse_name,nurse.title_img,nurse.number as nurse_number')->join('nurse ON nurse.id=order_nurse.nurse_id')->where('order_id='.I('get.id').'  and is_service=0')->select();

        //售后
        $is_service = M('order_nurse')->field('nurse.id,order_nurse.nurse_name,nurse.title_img')->join('nurse ON nurse.id=order_nurse.nurse_id')->where('order_id='.I('get.id').' and is_service=1')->select();
        $this->assign('is_service',$is_service);


        $nurse_hold = M('hold')->where('order_id='.$info['id'].' and hold_type = 1')->order('add_time desc')->select();
        $order_hold = M('hold')->where('order_id='.$info['id'].' and hold_type = 2')->order('add_time desc')->select();
        $this->assign('nurse_hold',$nurse_hold);
        $this->assign('order_hold',$order_hold);


        $this->assign('order_nurse',$order_nurse);
        $this->assign('info',$info);
        $this->assign('now',date('Y-m-d'));

        $status_name = ['','已派单/待接单','已接单/待完善','已完善/待发单','已发单/待匹配','已匹配/待签约','已签约/待上户','已上户','已下户','已完结'];

        $this->assign('status_name',$status_name);
        $this->display();
    }

    //新增维系
    public function addHold(){
        $this->authority(array(24,2,5));
        $post = I('post.');
        $post['add_time'] = time();
        $add_mod = M('hold')->add($post);
        if($add_mod){
            echo "<script>alert('新增成功');window.location.href='".__MODULE__."/Hold/holdInfo/id/".$post['order_id'].".html'</script>";
            exit;
        }else{
            echo "<script>alert('新增异常，请重新新增');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }


    }


}