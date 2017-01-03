<?php
namespace Home\Controller;
use Think\Controller;
class MateController extends CommonController {
    public function _initialize(){
        $this->authority(array(24,5));
        if(!$_SESSION[C('USER_AUTH_KEY')]['id']){
            echo "<script>alert('请登录！');window.location.href='" . __MODULE__ . "/Index/login.html';</script>";
            exit;
        }
    }
    //匹配操作

    //待匹配列表
    public function mateList(){
        $sales_id = M('employee')->where('permission in(2,3)')->select();

        $this->assign('sales_id',$sales_id);
        $this->display();
    }


    //匹配。获取需要匹配订单
    public function getMateList(){

        $post = I("post.");
        $where = 'status = 4 ';

        if($post['name']&&$post['name']!=''){
            $where .= ' and  name LIKE "%'.$post['name'].'%"';
        }
        if($post['order_type']!=0){
            $where .= ' and order_type = '.$post['order_type'].'';
        }
        if($post['status_4']!=''){
            $where .= ' and status_4 >= "'.strtotime($post['status_4']).'" and status_4 <= "'.(strtotime($post['status_4'])+6800).'" ';
        }

        if($post['sales_id']!=0){
            $where .= ' and sales_id = '.$post['sales_id'].'';
        }


        $list = M('order')->where($where)->order('status_4 desc')->select();

        $status_sh_name = ['','未匹配','待上户','已上户'];
        $status_own_name = ['','暂不接单','等单中','私签中','外单中'];
        $type_name = ['','学员','阿姨'];
        foreach($list as $k=>$v){
            $list[$k]['time_down'] = 0;
            if(time() - $v['status_4'] <900){
                $list[$k]['time_down'] = 900 - (time() - $v['status_4']);
            }
            $list[$k]['status_4_str'] = date('Y-m-d H:i:s',$v['status_4']);

//            $list[$k]['type_name'] = $type_name[$v['type']];
//            $list[$k]['status_sh_name'] = $status_sh_name[$v['status_sh']];
//            $list[$k]['status_own_name'] = $status_own_name[$v['status_own']];
//            $list[$k]['is_student_str'] = ($v['is_student']==1?'学员':'外聘');
        }

        $count = M('order')->where($where)->count();

        $back['data']['list'] = $list;
        $back['data']['num'] = $count;
        $back['code'] = 1000;
        echo json_encode($back);

    }


    public function orderInfo(){

        if (!I('get.id')) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('order')->where('id=' . I('get.id') . '')->find();
        if (!$info) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if($info['order_type']==1){
            $info_in = M('order_info')->where('order_id='.I('get.id').'')->find();
            $info =  array_merge($info_in,$info);
            $info['skill_a'] = explode(',',$info['skills']);
            $info['status_1_str'] = $info['status_1'] ? date('Y-m-d H:i:s', $info['status_1']) : 0;
            $info['status_2_str'] = $info['status_2'] ? date('Y-m-d H:i:s', $info['status_2']) : 0;
            $info['price_add_str'] = $info['price_add']==1?'有':'无';
            $order_nurse = M('order_nurse')->field('nurse.id,order_nurse.nurse_name,nurse.title_img')->join('nurse ON nurse.id=order_nurse.nurse_id')->where('order_id='.I('get.id').' and is_service=0')->select();

            $is_service = M('order_nurse')->field('nurse.id,order_nurse.nurse_name,nurse.title_img')->join('nurse ON nurse.id=order_nurse.nurse_id')->where('order_id='.I('get.id').' and is_service=1')->select();
            $this->assign('is_service',$is_service);

            $this->assign('order_nurse',$order_nurse);
            $this->assign('info',$info);
            $this->display();
        }else{
            $order_nurse = M('order_nurse')->field('nurse.id,order_nurse.nurse_name,nurse.title_img')->join('nurse ON nurse.id=order_nurse.nurse_id')->where('order_id='.I('get.id').' and is_service=0')->select();

            $order_type_name = ['','','实习','非实习'];
            $info['order_type_name'] = $order_type_name[$info['order_type']];

            $is_service = M('order_nurse')->field('nurse.id,order_nurse.nurse_name,nurse.title_img')->join('nurse ON nurse.id=order_nurse.nurse_id')->where('order_id='.I('get.id').' and is_service=1')->select();
            $this->assign('is_service',$is_service);
            $this->assign('order_nurse',$order_nurse);
            $this->assign('info',$info);
            $this->display('orderInfo_q');
        }

    }


    //匹配。获取匹配阿姨
    public function getNurseList(){

        $post = I("post.");
        $where = 'IF(type=1,status =2,status =1)   ';

         if($post['name']&&$post['name']!=''){
            $where .= ' and  name LIKE "%'.$post['name'].'%"';
        }
        if($post['status_sh']){
            $where .= ' and status_sh = '.$post['status_sh'].'';
        }
        if($post['number']&&$post['number']!=''){
            $where .= ' and  number LIKE "%'.$post['number'].'%"';
        }
        if($post['type']!=0){
            $where .= ' and type = '.$post['type'].'';
        }


        $list = M('nurse')->where($where)->order('add_time desc')->select();

        $status_sh_name = ['','未匹配','待上户','已上户'];
        $status_own_name = ['','暂不接单','等单中','私签中','外单中'];
        $type_name = ['','学员','阿姨'];
        foreach($list as $k=>$v){
            $our_nurse = M('order_nurse')->field('id')->where('order_id='.$post['order_id'].' and nurse_id='.$v['id'].' and is_service=0')->find();// 是否选择了这个阿姨
            if($our_nurse){
                $list[$k]['our_nurse'] = 1;
            }else{
                $list[$k]['our_nurse'] = 0;
            }

            $list[$k]['type_name'] = $type_name[$v['type']];
            $list[$k]['status_sh_name'] = $status_sh_name[$v['status_sh']];
            $list[$k]['status_own_name'] = $status_own_name[$v['status_own']];
            $list[$k]['is_student_str'] = ($v['is_student']==1?'学员':'外聘');
        }

        $count = M('nurse')->where($where)->count();

        $back['data']['list'] = $list;
        $back['data']['num'] = $count;
        $back['code'] = 1000;
        echo json_encode($back);

    }


    //匹配点击事件
    public function change_order_nurse(){
        $post = I('post.');
        if($post['action']==1){
            $have = M('order_nurse')->field('id')->where('nurse_id='.$post['nurse_id'].' and order_id='.$post['order_id'].' and is_service=0')->find();
            if($have){
                $back['code'] = 1000;
                echo json_encode($back);
                exit;
            }
            $order_info = M('order')->where('id='.$post['order_id'].'')->find();
            $nurse_info = M('nurse')->where('id='.$post['nurse_id'].'')->find();
            $add = $order_info;
            $add['nurse_name'] = $nurse_info['name'];
            $add['nurse_id'] = $nurse_info['id'];
            $add['order_id'] = $order_info['id'];
            $add['order_number'] = $order_info['number'];
            $add['nurse_number'] = $nurse_info['number'];
            $add['order_name'] = $order_info['name'];
            $add['nurse_price'] = $nurse_info['price'];
            $add['add_time'] = time();
            $add['status'] = 5;
            $add['is_service'] = 0;


            unset($add['id']);
            unset($add['number']);
            unset($add['remark']);
            $add_mod =  M('order_nurse')->add($add);
            if($add_mod){

                //判断 阿姨是否还有匹配
                $post['nurse_id'] = I('post.order_id');
                $nurse = M('nurse')->field('status_sh')->where('id='.$post['nurse_id'].'')->find();
                if($nurse['status_sh'] == 1){
                    $status_sh['status_sh'] = 2;
                    M('nurse')->where('id='.$post['nurse_id'].'')->save($status_sh);
                }

                $back['code'] = 1000;
                $back['data'] = $nurse_info;
                echo json_encode($back);
                exit;
            }
        }else{
            $delete_mod = M('order_nurse')->where('nurse_id='.$post['nurse_id'].' and order_id='.$post['order_id'].' and is_service=0')->delete();
            if($delete_mod!==false){

                //判断 阿姨是否还有匹配
                $nurse = M('nurse')->field('status_sh')->where('id='.$post['nurse_id'].'')->find();
                if($nurse['status_sh'] == 2){
                    $have_status = M('order_nurse')->where('nurse_id='.$post['nurse_id'].' and status in(5,6)')->find();
                    if(!$have_status||$have_status==''){
                        $status_sh['status_sh'] = 1;
                        M('nurse')->where('id='.$post['nurse_id'].'')->save($status_sh);
                    }
                }
                $back['code'] = 1000;
                $back['nurse_id'] = $post['nurse_id'];
                echo json_encode($back);
                exit;
            }else{
                $back['code'] = 1001;
                echo json_encode($back);
                exit;
            }
        }
    }

    //完成匹配
    public function status_5(){
        if (!I('get.id')) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('order')->where('id=' . I('get.id') . '')->find();
        if (!$info || $info['status']!=4) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info_in = M('order_nurse')->where('order_id='.I('get.id').'')->find();
        if($info_in==''){
            echo "<script>alert('未匹配阿姨');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $save['status'] = 5;
        $save['status_5'] = time();
        $save_mod = M('order')->where('id=' . I('get.id') . '')->save($save);
        if($save_mod!==false){
            echo "<script>alert('已完成！');window.location.href='" . __MODULE__ . "/Mate/mateList.html';</script>";
            exit;
        }else{
            echo "<script>alert('异常，请重新匹配');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
    }

}