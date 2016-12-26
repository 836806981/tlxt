<?php
namespace Home\Controller;
use Think\Controller;
class QneedController extends CommonController {
    public function _initialize(){
        if(!$_SESSION[C('USER_AUTH_KEY')]['id']){
            echo "<script>alert('请登录！');window.location.href='" . __MODULE__ . "/Index/login.html';</script>";
            exit;
        }
    }

    ///////////////////////渠道派单//////////////////////
//    添加渠道派单
    public function q_addNeed(){
        if(I('post.')){
            if(!I('get.status')){
                echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            $post = I('post.');

            $employee = M('employee')->where('id='.$post['source'].'')->find();
            $post['source'] = $employee['real_name'];
            $post['sales_id'] = $employee['id'];
            $post['sales_name'] = $employee['real_name'];

            $post['add_time'] = time();
            $post['status_1'] = $post['add_time'];
            $post['status_2'] = $post['add_time'];
            $post['status_3'] = $post['add_time'];
            $post['status'] = I('get.status');
            if($post['status']==4){
                $post['status_4'] = $post['add_time'];
            }
            $order_type_name = ['','','实习','非实习'];

            $post['s_time'] = date('Y-m-d',strtotime($post['b_time'])+$post['service_day']*86400);

            $post['name'] = $post['source'].$order_type_name[$post['order_type']].'单';
            $post['product'] = $post['source'].$order_type_name[$post['order_type']];
            $post['service_place'] = $post['source'];
            $post['add_employee'] = $_SESSION[C('USER_AUTH_KEY')]['real_name'];
            $post['employee_id'] = $_SESSION[C('USER_AUTH_KEY')]['id'];
           if( $post['price_add']==0){
                unset($post['add_reason']); unset($post['add_order_price']); unset($post['add_nurse_price']);
           }

            $order_id = M('order')->add($post);
            if($order_id){
                $save['number'] = 'XQ'.$post['order_type'].'-' . str_pad($order_id, 6, 0, STR_PAD_LEFT);  //6位数不足补0
                $save_mod = M('order')->where('id=' . $order_id . '')->save($save);
                if ($save_mod === false) {
                    M('order')->where('id=' . $order_id . '')->save($save);
                }
                echo "<script>alert('派单成功'); window.location.href='".__MODULE__."/Qneed/q_serviceList.html'</script>";
                exit;
            }else{
                echo "<script>alert('派单失败');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
        }else{
            $employee_3 = M('employee')->where('permission =3 and status=1')->select();
            $this->assign('employee_3',$employee_3);
            $this->display();
        }
    }

    //渠道派单列表
    public function q_serviceList(){

        $pai = M('employee')->where('permission in(3,24)')->select();
        $this->assign('pai',$pai);
        $this->display();
    }

    //渠道派单。获取派单列表数据
    public function q_getServiceList(){
        $currentpage = I("post.currentpage");
        $pagenum = I("post.pagenum");
        $start = ($currentpage - 1) * $pagenum;
        $post = I("post.");
        $where = '1';
        if($post['order_type']&&$post['order_type']!=0) {
            $where .= ' and order_type = ' . $post['order_type'] . '';
        }elseif($post['order_type']==0){
            $where .= ' and order_type>1';
        }
        if($post['number']&&$post['number']!=''){
            $where .= ' and  number LIKE "%'.$post['number'].'%"';
        }
        if($post['status_1']){
            $where .= ' and status_1 <='.(strtotime($post['status_1'])+86400) .' and status_1 >='.strtotime($post['status_1']) .'';
        }
        if($post['employee_id']&&$post['employee_id']!=0){
            $where .= ' and  employee_id ='.$post['employee_id'].'';
        }
        if($post['status']==0){
            $where .= ' and status in(1,3,4,5,6,7,8,9)';
        }else{
            $where .= ' and status = '.$post['status'].'';
        }
        $list = M('order')->where($where)->limit($start,$pagenum)->order('add_time desc')->select();

        $status_name = ['','待接单','已接单','已完善','已发单','已匹配','已匹配','已上户','已下户','已完结'];
        $status_name[22] = ['已打回'];
        $status_name[23] = ['回收站'];
        $order_type_name = ['','','实习单','非实习单'];
        foreach($list as $k=>$v){
            $list[$k]['status_3_str'] = $v['status_3']?date('Y-m-d H:i:s',$v['status_3']):'';
            $list[$k]['status_name'] = $status_name[$v['status']];
            $list[$k]['order_type_name'] = $order_type_name[$v['order_type']];
        }
        $count = M('order')->where($where)->count();
        $back['data']['list'] = $list;
        $back['data']['num'] = $count;
        $back['code'] = 1000;
        $back['permission'] = $_SESSION[C('USER_AUTH_KEY')]['permission'];
        echo json_encode($back);
    }

    //修改派单。
    public function q_changeNeed(){
        if(!I('post.')) {
            if (!I('get.id')) {
                echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            $info = M('order')->where('id=' . I('get.id') . '')->find();
            if (!$info) {
                echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            if ($info['status'] == 2) {
                echo "<script>alert('已接单！');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            $employee_3 = M('employee')->where('permission =3 and status=1')->select();
            $this->assign('employee_3',$employee_3);
            $this->assign('info', $info);
            $this->display();
        }else{
            $info = M('order')->where('id=' . I('post.order_id') . '')->find();
            if (!$info) {
                echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            if ($info['status']>4) {
                echo "<script>alert('已匹配不能修改');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }

            $post = I('post.');
            $order_type_name = ['','','实习','非实习'];

            $post['name'] = $info['source'].$order_type_name[$post['order_type']].'单';
            $post['product'] = $info['source'].$order_type_name[$post['order_type']];
            if( $post['price_add']==0){
                unset($post['add_reason']); unset($post['add_order_price']); unset($post['add_nurse_price']);
            }
            $post['s_time'] = date('Y-m-d',strtotime($post['b_time'])+$post['service_day']*86400);

            $save_mod = M('order')->where('id='.$post['order_id'].'')->save($post);
            if($save_mod!==false){
                $save['number'] = 'XQ'.$post['order_type'].'-' . str_pad($post['order_id'], 6, 0, STR_PAD_LEFT);  //6位数不足补0
                $save_mod = M('order')->where('id=' . $post['order_id'] . '')->save($save);
                if ($save_mod === false) {
                    M('order')->where('id=' . $post['order_id'] . '')->save($save);
                }
                echo "<script>alert('成功'); window.location.href='".__MODULE__."/Qneed/q_needInfo/id/".$post['order_id'].".html'</script>";
                exit;
            }else{
                echo "<script>alert('失败');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
        }
    }

    //接单的详情

    public function q_needInfo(){
        if (!I('get.id')) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('order')->where('id=' . I('get.id') . '')->find();
        if (!$info) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info['status_1_str'] = date('Y-m-d H:i:s',$info['status_1']);
        $info['status_2_str'] = date('Y-m-d H:i:s',$info['status_2']);
        $info['status_3_str'] = date('Y-m-d H:i:s',$info['status_3']);
        $info['status_4_str'] = date('Y-m-d H:i:s',$info['status_4']);
        $order_type_name = ['','','实习','非实习'];
        $info['order_type_name'] = $order_type_name[$info['order_type']];

        $order_nurse = M('order_nurse')->field('nurse.id,order_nurse.nurse_name,nurse.title_img,nurse.number as nurse_number')->join('nurse ON nurse.id=order_nurse.nurse_id')->where('order_id='.I('get.id').'  and is_service=0')->select();
        if($info['status'] == 7){
            $nurse = M('nurse')->where('id='.$order_nurse[0]['id'].'')->find();
            $this->assign('nurse',$nurse);
        }
        $this->assign('order_nurse', $order_nurse);
        $this->assign('info', $info);
        $this->assign('now',date('Y-m-d'));
        $this->display();
    }

    //渠道单 发单。去匹配
    public function q_changeNeedStatus(){
        if (!I('get.id')) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('order')->where('id=' . I('get.id') . ' and order_type >1')->find();
        if (!$info) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }

        $status['status'] = 4;
        $status['status_4'] = time();
        $save_mod = M('order')->where('id='.I('get.id').'')->save($status);
        if($save_mod!==false){
            echo "<script>alert('发单成功'); window.location.href='".__MODULE__."/Qneed/q_needInfo/id/".I('get.id').".html'</script>";
            exit;
        }else{
            echo "<script>alert('发单失败');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }



    }

    //放入回收站
    public function q_delNeed(){

        if (!I('post.order_id')) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $post = I('post.');
        $info = M('order')->where('id=' . $post['order_id'] . '')->find();
        if (!$info) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if (($info['status'] > 4)) {
            echo "<script>alert('已匹配，不能放入回收站');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $save['status'] = 23;
        $save['status_23'] = time();
        $save['del_reason'] = $post['del_reason'];
        $save_mod = M('order')->where('id='.$post['order_id'].'')->save($save);
        if($save_mod!==false){
            echo "<script>alert('放入回收站成功'); window.location.href='".__MODULE__."/Qneed/q_serviceList.html'</script>";
            exit;
        }else{
            echo "<script>alert('放入回收站失败');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }

    }

    //渠道。回收站列表。
    public function q_returnList(){
        $pai = M('employee')->where('permission in(1,24)')->select();
        $gu  = M('employee')->where('permission =2 ')->select();
        $this->assign('pai',$pai);
        $this->assign('gu',$gu);
        $this->display();
    }

    //渠道派单。获取回收站列表数据
    public function q_getReturnList(){
        $currentpage = I("post.currentpage");
        $pagenum = I("post.pagenum");
        $start = ($currentpage - 1) * $pagenum;
        $post = I("post.");
        $where = 'status = 23';
        if($post['order_type']&&$post['order_type']!= 0 ){
            $where .= ' and  order_type ='.$post['order_type'].'';
        }else{
            $where .= ' and  order_type >1';
        }

        if($post['number']&&$post['number']!=''){
            $where .= ' and  number LIKE "%'.$post['number'].'%"';
        }
        if($post['status_23']){
            $where .= ' and status_23 <='.(strtotime($post['status_23'])+86400) .' and status_23 >='.strtotime($post['status_23']) .'';
        }
        $list = M('order')->where($where)->limit($start,$pagenum)->order('add_time desc')->select();

        $order_type_name = ['','','实习','非实习'];
        foreach($list as $k=>$v){
            $list[$k]['status_1_str'] = date('Y-m-d H:i:s',$v['status_1']);
            $list[$k]['status_23_str'] = date('Y-m-d H:i:s',$v['status_23']);
            $list[$k]['order_type_name'] = $order_type_name[$v['order_type']];
        }
        $count = M('order')->where($where)->count();
        $back['data']['list'] = $list;
        $back['data']['num'] = $count;
        $back['code'] = 1000;
        $back['permission'] = $_SESSION[C('USER_AUTH_KEY')]['permission'];
        echo json_encode($back);
    }


}