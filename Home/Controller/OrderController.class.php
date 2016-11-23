<?php
namespace Home\Controller;
use Think\Controller;
class OrderController extends CommonController {

    public function _initialize(){
        if(!$_SESSION[C('USER_AUTH_KEY')]['id']){
            echo "<script>alert('请登录！');window.location.href='" . __MODULE__ . "/Index/login.html';</script>";
            exit;
        }
    }

    //新增派单
    public function addOrder(){
        $this->authority(array(11,1));
        if(I('post.')){
            $post = I('post.');

            $post['add_time'] = time();
            $post['status'] = 1;//默认状态
            $post['add_mac'] = $this->GetMacAddr(PHP_OS);

            $employee_name = M('employee')->field('real_name')->where('id='.$post['employee_id'].'')->find();
            $post['employee_name']  = $employee_name['real_name'];
            $order_id = M('order')->add($post);
            if($order_id){
                $save['number'] = 'TLAYHT-' . str_pad($order_id, 6, 0, STR_PAD_LEFT);  //6位数不足补0
                $save_mod = M('order')->where('id=' . $order_id . '')->save($save);
                if ($save_mod === false) {
                    M('order')->where('id=' . $order_id . '')->save($save);
                }
                echo "<script>alert('派单成功'); window.location.href='".__MODULE__."/Order/orderList'</script>";
                exit;
            }else{
                echo "<script>alert('派单失败');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
        }else{
            $employee = M('employee')->where('permission = 2')->select();
            $this->assign('employee',$employee);
            $this->display();
        }
    }

    //修改

    public function changeOrder(){
        $this->authority(array(11,1));
        if(I('post.')){
            $post = I('post.');
            if($post['employee_id']) {
                $employee_name = M('employee')->field('real_name')->where('id=' . $post['employee_id'] . '')->find();
                $post['employee_name'] = $employee_name['real_name'];
            }
            $order_id = M('order')->where('id='.$post['id'].'')->save($post);
            if($order_id){
                echo "<script>alert('修改成功'); window.location.href='".__MODULE__."/Order/orderList'</script>";
                exit;
            }else{
                echo "<script>alert('修改失败');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
        }else{
            if(!I('get.id')){
                echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            $info = M('order')->where('id='.I('get.id').'')->find();
            if(!$info){
                echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            $employee = M('employee')->where('permission = 2')->select();
            $this->assign('info',$info);
            $this->assign('employee',$employee);
            $this->assign('permission',$_SESSION[C('USER_AUTH_KEY')]['permission']);
            $this->display();
        }
    }

    //列表页
    public function orderList(){
        $this->authority(array(1,11));

        $employee = M('employee')->where('permission = 2')->select();
        $this->assign('employee',$employee);
        $this->display();
    }

    //获取派单列表
    public function getOrderList(){
        $this->authority(array(11,1));
        $currentpage = I("post.currentpage");
        $pagenum = I("post.pagenum");
        $start = ($currentpage - 1) * $pagenum;
        $post = I("post.");
        $where = 'status = 1 ';


        if($post['number']&&$post['number']!=''){
            $where .= ' and  number LIKE "%'.$post['number'].'%"';
        }
        if($post['name']&&$post['name']!=''){
            $where .= ' and  name LIKE "%'.$post['name'].'%"';
        }
        if($post['phone']&&$post['phone']!=''){
            $where .= ' and  phone LIKE "%'.$post['phone'].'%"';
        }
        if($post['employee_id']){
            $where .= ' and employee_id ='.$post['employee_id'].'';
        }
        if($post['add_time']){
            $where .= ' and add_time <='.(strtotime($post['add_time'])+86400) .' and add_time >='.strtotime($post['add_time']) .'';
        }

        $list = M('order')->where($where)->limit($start,$pagenum)->order('add_time desc')->select();

        foreach($list as $k=>$v){
            $list[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
        }

        $count = M('order')->where($where)->count();

        $back['data']['list'] = $list;
        $back['data']['num'] = $count;
        $back['code'] = 1000;
        $back['permission'] = $_SESSION[C('USER_AUTH_KEY')]['permission'];
        echo json_encode($back);
    }

    //订单逻辑删除
    public function del_order(){
        $this->authority(array(11,1,2));

        if($_SESSION[C('USER_AUTH_KEY')]['permission']!=11){
            echo "<script>alert('无权限！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if(!I('post.id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('order')->where('id='.I('post.id').'')->find();
        if(!$info){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $save['status'] = 11;
        $save['reason'] = I('post.reason');
        $order_id = M('order')->where('id='.I('post.id').'')->save($save);
        if($order_id!==false){
            echo "<script>alert('成功');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }else{
            echo "<script>alert('失败');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
    }


    //订单逻辑恢复
    public function back_order(){
        $this->authority(array(11,1));
        if($_SESSION[C('USER_AUTH_KEY')]['permission']!=11){
            echo "<script>alert('无权限！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if(!I('get.id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('order')->where('id='.I('get.id').'')->find();
        if(!$info){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $save['status'] = 1;
        $order_id = M('order')->where('id='.I('get.id').'')->save($save);
        if($order_id!==false){
            echo "<script>alert('成功');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }else{
            echo "<script>alert('失败');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
    }



    //档案列表
    public function fileList(){
        $this->authority(array(11,2));

        $employee = M('employee')->where('permission = 2')->select();
        $this->assign('employee',$employee);
        $this->display();
    }

    //获取档案列表数据
    public function getFileList(){
        $this->authority(array(11,2));
        $currentpage = I("post.currentpage");
        $pagenum = I("post.pagenum");
        $start = ($currentpage - 1) * $pagenum;
        $post = I("post.");
        $where = 'status = 1 ';

        if($_SESSION[C('USER_AUTH_KEY')]['permission'] == 2){
            $where .= ' and employee_id ='.$_SESSION[C('USER_AUTH_KEY')]['id'].' ';
        }

        if($post['other']){
            $where .= ' and other ="'.$post['other'].'"';
        }

        if($post['name']&&$post['name']!=''){
            $where .= ' and  name LIKE "%'.$post['name'].'%"';
        }
        if($post['phone']&&$post['phone']!=''){
            $where .= ' and  phone LIKE "%'.$post['phone'].'%"';
        }
        if($post['employee_id']){
            $where .= ' and employee_id ='.$post['employee_id'].'';
        }
        if($post['add_time']){
            $where .= ' and add_time <='.(strtotime($post['add_time'])+86400) .' and add_time >='.strtotime($post['add_time']) .'';
        }

        $list = M('order')->where($where)->limit($start,$pagenum)->order('add_time desc')->select();

        foreach($list as $k=>$v){
            $list[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
        }

        $count = M('order')->where($where)->count();

        $back['data']['list'] = $list;
        $back['data']['num'] = $count;
        $back['code'] = 1000;
        echo json_encode($back);
    }

    //查看档案
    public function clientFile(){
        $this->authority(array(11,2));
        if(!I('get.id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('order')->where('id='.I('get.id').'')->find();
        if(!$info){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if($info['is_read']==0&&$_SESSION[C('USER_AUTH_KEY')]['id']==$info['employee_id']){
            $is_read['is_read'] = 1;
            M('order')->where('id='.I('get.id').'')->save($is_read);
        }
        $this->assign('info',$info);
        $this->display();
    }

    //修改档案
    public function changeFile(){
        $this->authority(array(11,2));
        if(I('post.')){
            $post = I('post.');
            if (!$post['id']) {
                echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }

            $order = M('order')->where('id='.$post['id'].'')->save($post);
            if($order!==false){
                echo "<script>alert('修改成功');window.location.href='".__MODULE__."/Order/clientFile/id/".$post['id'].".html'</script>";
                exit;
            }else{
                echo "<script>alert('修改失败');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
        }else {
            if (!I('get.id')) {
                echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            $info = M('order')->where('id=' . I('get.id') . '')->find();
            if (!$info) {
                echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }

            $this->assign('info', $info);
            $this->display();
        }
    }

    //维系列表
    public function contactList(){
        $this->authority(array(11,2,3,4));

        if (!I('get.id')) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $order_info = M('order')->field('id,name')->where('id='.I('get.id').'')->find();

        $contact = M('contact')->where('order_id='.I('get.id').'')->order('add_time desc')->select();


        $this->assign('contact', $contact);
        $this->assign('order_info', $order_info);
        $this->display();

    }


    //维系获取阿姨编号信息验证
    public function getNurseInfo(){
        $number = I('post.number');
        if($number){
            $nurse = M('nurse')->where('number="'.$number.'"')->find();
            if($nurse&&$nurse!=''){
                $price_arr = ['','3000','3200','3400','3600','4000','5000','5200','5400','5600','6000','7000','7200','7400','7600','8000','9000'];
                $nurse['level_price'] = $price_arr[$nurse['level']];
                $back['data']= $nurse;
                $back['code'] = 1000;//正常
            }else{
                $back['code'] = 1002;//没找到
            }
        }else{
//            $back['data']= $list;
            $back['code'] = 1003;//数据异常
        }
        echo json_encode($back);
    }
    //添加维系
    public function addContact(){
        $this->authority(array(11,2));
        $post = I('post.');
        if($post){
            $post['add_time'] = time();
            $add_mod = M('contact')->add($post);
            if($add_mod){
                echo "<script>alert('添加成功');window.location.href='".__MODULE__."/Order/contactList/id/".$post['order_id'].".html'</script>";
                exit;
            }else{
                echo "<script>alert('添加失败');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
        }else{
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
    }

    //修改维系
    public function editContact(){
        $this->authority(array(11,2));
        $post = I('post.');
        if($post){
            $add_mod = M('contact')->where('id='.$post['contact_id'].'')->save($post);
            if($add_mod!==false){
                echo "<script>alert('修改成功');window.location.href='".__MODULE__."/Order/contactList/id/".$post['order_id'].".html'</script>";
                exit;
            }else{
                echo "<script>alert('修改失败');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
        }else{
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
    }


//获取回收站档案列表数据
    public function getReturnFileList(){

        $currentpage = I("post.currentpage");
        $pagenum = I("post.pagenum");
        $start = ($currentpage - 1) * $pagenum;
        $post = I("post.");
        $where = 'status = 11 ';

        if($post['other']){
            $where .= ' and other ="'.$post['other'].'"';
        }

        if($post['name']&&$post['name']!=''){
            $where .= ' and  name LIKE "%'.$post['name'].'%"';
        }
        if($post['phone']&&$post['phone']!=''){
            $where .= ' and  phone LIKE "%'.$post['phone'].'%"';
        }
        if($post['employee_id']){
            $where .= ' and employee_id ='.$post['employee_id'].'';
        }
        if($post['add_time']){
            $where .= ' and add_time <='.(strtotime($post['add_time'])+86400) .' and add_time >='.strtotime($post['add_time']) .'';
        }

        $list = M('order')->where($where)->limit($start,$pagenum)->order('add_time desc')->select();

        foreach($list as $k=>$v){
            $list[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
        }

        $count = M('order')->where($where)->count();

        $back['data']['list'] = $list;
        $back['data']['num'] = $count;
        $back['code'] = 1000;
        $back['permission'] = $_SESSION[C('USER_AUTH_KEY')]['permission'];
        echo json_encode($back);
    }



    //形成订单功能
    public function make_order(){
        $this->authority(array(11,2));

        $post = I('post.');
        $order_info = M('order')->where('id='.$post['id'].'')->find();
        if (!$order_info || $order_info['status']!=1) {
            echo "<script>alert('未找到该档案或该档案已删除、已形成');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $nurse_info = M('nurse')->where('id='.$post['nurse_id'].'')->find();
        if (!$nurse_info) {
            echo "<script>alert('未找到该阿姨的记录');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }

        //判断档期是否和已签订档期冲突
        $where_order_nurse = 'nurse_id='.$post['nurse_id'].' and ( ("'.$post['b_time'].'" <= IF(true_s_time!="",true_s_time,s_time) and "'.$post['b_time'].'">=IF(true_b_time!="" ,true_b_time , b_time) ) OR ("'.$post['s_time'].'" >= IF(true_b_time!="" ,true_b_time , b_time) and "'.$post['s_time'].'" <=IF(true_s_time!="",true_s_time,s_time) )  OR("'.$post['b_time'].'" <= IF(true_b_time!="" ,true_b_time , b_time) and "'.$post['s_time'].'" >= IF(true_s_time!="",true_s_time,s_time) )   )';
        $have_re = M('order_nurse')->field('id')->where($where_order_nurse)->find();

        if($have_re){
            echo "<script>alert('阿姨档期冲突');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }


//        print_r($post);die;
        //修改订单表信息
        $save_order['contract_money'] = $post['contract_money'];
        $save_order['get_money'] = $post['get_money'];
        $save_order['get_all'] = $save_order['get_money'] >=$save_order['contract_money']?2:1;
        $save_order['nurse_id'] = $post['nurse_id'];
        $save_order['nurse_name'] = $post['nurse_name'];
        $save_order['nurse_pay'] = $post['nurse_pay'];
        $save_order['safe_time'] = $post['safe_time'];
        $save_order['b_time'] = $post['b_time'];
        $save_order['s_time'] = $post['s_time'];
        $save_order['status'] = 2;


        $save_order_mod = M('order')->where('id='.$post['id'].'')->save($save_order);
        if($save_order_mod===false){
            M('order')->where('id='.$post['id'].'')->save($save_order);
        }

        //收款处理
        $save_get_money['order_id'] = $post['id'];
        $save_get_money['add_time'] = time();
        $save_get_money['get_time'] = date('Y-m-d H:i:s');
        $save_get_money['remark'] = '形成订单首付款';
        $save_get_money['get_money'] = $post['get_money'];

        $order_ger_money_id = M('order_get_money')->add($save_get_money);
        if(!$order_ger_money_id){
            M('order_get_money')->add($save_get_money);
        }

        //订单生产后的order_nurse表记录级各种数据处理
        $save_order_nurse['order_id'] = $post['id'];
        $save_order_nurse['nurse_id'] = $post['nurse_id'];
        $save_order_nurse['order_name'] = $order_info['name'];
        $save_order_nurse['nurse_name'] = $nurse_info['name'];
        $save_order_nurse['order_number'] = $order_info['number'];
        $save_order_nurse['nurse_number'] = $nurse_info['number'];
        $save_order_nurse['nurse_id_card'] = $nurse_info['id_card'];
        $save_order_nurse['safe_time'] = $post['safe_time'];
        $save_order_nurse['is_safe'] = 1;
        $save_order_nurse['b_time'] = $post['b_time'];
        $save_order_nurse['s_time'] = $post['s_time'];
        $save_order_nurse['is_normal'] = 1;
        $save_order_nurse['nurse_pay'] = $post['nurse_pay'];
        $save_order_nurse['add_time'] = time();
        $order_nurse_id = M('order_nurse')->add($save_order_nurse);
        if(!$order_nurse_id){
            M('order_nurse')->add($save_order_nurse);
        }
        echo "<script>alert('已完成！');window.location.href='".__MODULE__."/Order/clientFile/id/".$post['id'].".html'</script>";
        exit;


    }




}