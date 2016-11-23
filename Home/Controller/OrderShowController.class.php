<?php
namespace Home\Controller;
use Think\Controller;
class OrderShowController extends CommonController {
    public function _initialize(){
        if(!$_SESSION[C('USER_AUTH_KEY')]['id']){
            echo "<script>alert('请登录！');window.location.href='" . __MODULE__ . "/Index/login.html';</script>";
            exit;
        }
    }


    //订单列表
    public function orderList(){
        $this->authority(array(11,2,3));

        $employee = M('employee')->where('permission = 2')->select();
        $this->assign('employee',$employee);

        $this->assign('belong',1);

        $this->display('orderList');
    }
    //上户列表
    public function onOrderList(){
        $this->authority(array(11,2,3));

        $employee = M('employee')->where('permission = 2')->select();
        $this->assign('employee',$employee);

        $this->assign('belong',2);
        $this->display('orderList');
    }
    //下户列表
    public function downOrderList(){

        $this->authority(array(11,2,3));

        $employee = M('employee')->where('permission = 2')->select();
        $this->assign('employee',$employee);
        $this->assign('belong',3);
        $this->display('orderList');
    }

    //上户列表
    public function onOrderDoList(){

        $this->authority(array(11,2,3));

        $employee = M('employee')->where('permission = 2')->select();
        $this->assign('employee',$employee);

        $this->assign('belong',4);
        $this->display('orderList');
    }
    //下户列表
    public function downOrderDoList(){

        $this->authority(array(11,2,3));

        $employee = M('employee')->where('permission = 2')->select();
        $this->assign('employee',$employee);
        $this->assign('belong',5);
        $this->display('orderList');
    }


    //获取档案列表数据
    public function getOrderList(){

        $currentpage = I("post.currentpage");
        $pagenum = I("post.pagenum");
        $start = ($currentpage - 1) * $pagenum;
        $post = I("post.");

        switch($post['belong']){
            case 1:
                $where = 'status >1 and status<5 ';
                break;
            case 2:
                $where = 'status = 3 ';
                break;
            case 3:
                $where = 'status = 4 ';
                break;
            case 4:
                $where = 'status = 2 ';
                break;
            case 5:
                $where = 'status = 3 ';
                break;
            default:
                $where = 'status >1 and status<5 ';
                break;
        }
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

        $list = M('order')->where($where)->limit($start,$pagenum)->order('add_time desc')->select();

        $status_name = ['','','未上户','已上户','已下户'];

        foreach($list as $k=>$v){
            $list[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
            $list[$k]['status_name'] = $status_name[$v['status']] .($v['is_service']==1?'(售后)':'');
        }

        $count = M('order')->where($where)->count();

        $back['data']['list'] = $list;
        $back['data']['num'] = $count;
        $back['code'] = 1000;
        echo json_encode($back);
    }


    //查看订单信息
    public function OrderInfo(){

        $this->authority(array(11,2,3,4));
        if(!I('get.id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('order')->where('id='.I('get.id').'')->find();
        if(!$info){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }

        $nurse = M('nurse')->where('id='.$info['nurse_id'].'')->find();
        $status_sh_name = ['','已上户','已下户'];
        $status_name = ['','未签单','已签单','已上户','已下户'];


        $order_nurse = M('order_nurse')->where('order_id='.$info['id'].' and nurse_id!='.$info['nurse_id'].'')->order('add_time desc')->select();//售后阿姨

        $nurse['status_sh_name'] = $status_sh_name[$nurse['status_sh']];
        $info['status_name'] = $status_name[$info['status']];

        $this->assign('order_nurse',$order_nurse);
        $this->assign('nurse',$nurse);
        $this->assign('info',$info);
        $this->display();
    }

    //上户操作 及数据处理
    public function up_order(){
        $this->authority(array(11,2,3));
        if(I('post.id')){
            $post = I('post.');
            $order = M('order')->where('id='.$post['id'].'')->find();
            if(!$order){
                echo "<script>alert('数据异常');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            if($order['status']!=2){
                echo "<script>alert('订单状态异常');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            $nurse = M('nurse')->where('id = '.$order['nurse_id'].'')->find();
            if(!$nurse || $nurse['status'] != 1 ){
                echo "<script>alert('找不到阿姨或者阿姨已被删除！');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }

            //订单处理 。状态。及催款状态
            $save_order['true_b_time']  =  $post['true_b_time'];
            $save_order['status']  =  3;//上户状态
            $save_order['press_status']  =  $order['get_money']>=$order['contract_money']?3:1;//是否需要催款
            $save_order_mod = M('order')->where('id='.$post['id'].'')->save($save_order);
            if($save_order_mod===false){
                M('order')->where('id='.$post['id'].'')->save($save_order);
            }


            //订单阿姨表处理。order_nurse
            $save_order_nurse['true_b_time']  =  $post['true_b_time'];
            $save_order_nurse_mod = M('order_nurse')->where('order_id='.$post['id'].' and nurse_id='.$order['nurse_id'].'')->save($save_order_nurse);
            if($save_order_nurse_mod===false){
                M('order_nurse')->where('order_id='.$post['id'].' and nurse_id='.$order['nurse_id'].'')->save($save_order_nurse);
            }

            //阿姨表修改状态
            $save_nurse['status_sh']  = 1;
            $save_nurse_mod = M('nurse')->where('id='.$order['nurse_id'].'')->save($save_nurse);
            if($save_nurse_mod===false){
                M('nurse')->where('id='.$order['nurse_id'].'')->save($save_nurse);
            }


            echo "<script>alert('上户成功');window.location.href='" . __MODULE__ . "/OrderShow/orderInfo/id/".$post['id'].".html';</script>";
            exit;

        }else{
            echo "<script>alert('数据异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
    }

    //下户操作 及数据处理
    public function down_order(){

        $this->authority(array(11,2,3));
        if(I('post.id')){
            $post = I('post.');
            $order = M('order')->where('id='.$post['id'].'')->find();
            if(!$order){
                echo "<script>alert('数据异常');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            if($order['status']!=3){
                echo "<script>alert('订单状态异常');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            $nurse = M('nurse')->where('id = '.$order['nurse_id'].'')->find();
            if(!$nurse || $nurse['status'] != 1 ){
                echo "<script>alert('找不到阿姨或者阿姨已被删除！');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }

            //订单处理 。状态。及催款状态
            $save_order['true_s_time']  =  $post['true_s_time'];
            $save_order['status']  =  4;//上户状态
            $save_order['press_status']  =  $order['get_money']>=$order['contract_money']?3:1;//上催款状态
            $save_order_mod = M('order')->where('id='.$post['id'].'')->save($save_order);
            if($save_order_mod===false){
                M('order')->where('id='.$post['id'].'')->save($save_order);
            }


            //订单阿姨表处理。order_nurse 以及下户工资
            $save_order_nurse['true_s_time']  =  $post['true_s_time'];
            $save_order_nurse['nurse_pay_true']  =  $post['old_nurse_pay'];
            $save_order_nurse['remark']  =  '完成订单自动下户';
            $save_order_nurse['do_time']  =  $post['do_time'];
            $save_order_nurse_mod = M('order_nurse')->where('order_id='.$post['id'].' and nurse_id='.$order['nurse_id'].'')->save($save_order_nurse);
            if($save_order_nurse_mod===false){
                M('order_nurse')->where('order_id='.$post['id'].' and nurse_id='.$order['nurse_id'].'')->save($save_order_nurse);
            }

            //阿姨表修改状态
            $save_nurse['status_sh']  = 2;
            $save_nurse['level_add'] = $post['do_time']>=26?1:0;
//            $price_arr = ['','3000','3200','3400','3600','4000','5000','5200','5400','5600','6000','7000','7200','7400','7600','8000','9000'];
//            $price = 0;
//            $level = array_search($nurse['nurse_pay'],$price_arr);



            $save_nurse_mod = M('nurse')->where('id='.$order['nurse_id'].'')->save($save_nurse);
            if($save_nurse_mod===false){
                M('nurse')->where('id='.$order['nurse_id'].'')->save($save_nurse);
            }


            echo "<script>alert('下户成功');window.location.href='" . __MODULE__ . "/OrderShow/orderInfo/id/".$post['id'].".html';</script>";
            exit;

        }else{
            echo "<script>alert('数据异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
    }


    //修改订单
    public function changeOrder(){

        $this->authority(array(11));
        if(I('post.')){
            $post = I('post.');
            $order = M('order')->where('id='.$post['id'].'')->find();
            if(!$order){
                echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            $save_order['name'] = $post['name'];
            $save_order['other'] = $post['other'];
            $save_order['phone'] = $post['phone'];
            $save_order['b_time'] = $post['b_time'];
            $save_order['s_time'] = $post['s_time'];
            $save_order['contract_money'] = $post['contract_money'];
            $save_order['nurse_pay'] = $post['nurse_pay'];
            $save_order_mod  = M('order')->where('id='.$post['id'].'')->save($save_order);
            if($save_order_mod===false){
                M('order')->where('id='.$post['id'].'')->save($save_order);
            }
            $save_order_nurse['order_name'] = $post['name'];
            $save_order_nurse['b_time'] = $post['b_time'];
            $save_order_nurse['s_time'] = $post['s_time'];
            $save_order_nurse['nurse_pay'] = $post['nurse_pay'];

            $save_order_nurse_mod  = M('order_nurse')->where('id='.$post['id'].' and nurse_id='.$order['nurse_id'].'')->save($save_order_nurse);
            if($save_order_nurse_mod===false){
                M('order_nurse')->where('id='.$post['id'].' and nurse_id='.$order['nurse_id'].'')->save($save_order_nurse);
            }

            echo "<script>alert('修改成功');window.location.href='" . __MODULE__ . "/OrderShow/orderInfo/id/".$post['id'].".html';</script>";
            exit;

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
            $nurse = M('nurse')->where('id='.$info['nurse_id'].'')->find();
            $this->assign('nurse',$nurse);
            $this->assign('info',$info);
            $this->display();
        }
    }


    //形成订单售后
    public function make_order(){

        $this->authority(array(11,2));
        $post = I('post.');
        $order_info = M('order')->where('id='.$post['id'].'')->find();
        if (!$order_info ) {
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
        $save_order['nurse_id'] = $post['nurse_id'];
        $save_order['nurse_name'] = $post['nurse_name'];
        $save_order['nurse_pay'] = $post['nurse_pay'];
        $save_order['safe_time'] = $post['safe_time'];
        $save_order['true_b_time'] = '';
        $save_order['b_time'] = $post['b_time'];
        $save_order['s_time'] = $post['s_time'];
        $save_order['status'] = 2;
        $save_order['is_service'] = 1;


        $save_order_mod = M('order')->where('id='.$post['id'].'')->save($save_order);
        if($save_order_mod===false){
            M('order')->where('id='.$post['id'].'')->save($save_order);
        }

        //售后阿姨订单的处理
        $order_nurse = M('order_nurse')->where('order_id='.$order_info['id'].' and nurse_id='.$order_info['nurse_id'].'')->find();
        
        $save_order_nurse['true_s_time'] = $post['true_s_time'];
        $save_order_nurse['is_service'] = 2;
        $save_order_nurse['nurse_pay_true'] = $post['old_nurse_pay'];
        $save_order_nurse['do_time'] = $post['do_time'];
        $save_order_nurse['remark'] = '售后自动下户';
        $save_order_nurse_mod = M('order_nurse')->where('order_id='.$order_info['id'].' and nurse_id='.$order_info['nurse_id'].'')->save($save_order_nurse);

        if($save_order_nurse_mod===false){
            M('order_nurse')->where('order_id='.$order_info['id'].' and nurse_id='.$order_info['nurse_id'].'')->save($save_order_nurse);
        }

        // 修改售后阿姨下户状态
        $save_nurse['status_sh'] = 2;
        $save_nurse_mod = M('nurse')->where('id='.$nurse_info['id'].'')->save($save_nurse);
        if($save_nurse_mod===false){
            M('nurse')->where('id='.$nurse_info['id'].'')->save($save_nurse);
        }


        //订单生产后的order_nurse表记录级各种数据处理
        $add_order_nurse['order_id'] = $post['id'];
        $add_order_nurse['nurse_id'] = $post['nurse_id'];
        $add_order_nurse['order_name'] = $order_info['name'];
        $add_order_nurse['nurse_name'] = $nurse_info['name'];
        $add_order_nurse['order_number'] = $order_info['number'];
        $add_order_nurse['nurse_number'] = $nurse_info['number'];
        $add_order_nurse['nurse_id_card'] = $nurse_info['id_card'];
        $add_order_nurse['safe_time'] = $post['safe_time'];
        $add_order_nurse['is_safe'] = 1;
        $add_order_nurse['b_time'] = $post['b_time'];
        $add_order_nurse['s_time'] = $post['s_time'];
        $add_order_nurse['is_normal'] = 1;
        $add_order_nurse['nurse_pay'] = $post['nurse_pay'];
        $add_order_nurse['add_time'] = time();
        $order_nurse_id = M('order_nurse')->add($add_order_nurse);
        if(!$order_nurse_id){
            M('order_nurse')->add($add_order_nurse);
        }
        echo "<script>alert('已完成！');window.location.href='".__MODULE__."/OrderShow/OrderInfo/id/".$post['id'].".html'</script>";
        exit;


    }

    //核算阿姨工资
    public function getNursePay(){
        if(I('post.id')){
            $nurse = M('order')->where('id='.I('post.id').'')->find();
            if(!$nurse||$nurse==''){
                $back['code'] = 1001;//参数错误
                echo json_encode($back);
                exit;
            }
            $price_arr = ['','3000','3200','3400','3600','4000','5000','5200','5400','5600','6000','7000','7200','7400','7600','8000','9000'];
            $price = 0;
            $level = array_search($nurse['nurse_pay'],$price_arr);
            $day = I('post.do_time');

            $number = ceil($day/26);//多少单
            for($i=1;$i<=$number;$i++){
                $price +=   ($day-26>=0) ?  $price_arr[$level]  :  sprintf("%.2f", ($price_arr[$level]/26) * $day);
                $day = $day-26;
                $day = $day<0?0:$day;
                if($level<5) {
                    $level++;
                }elseif($level>5&&$level<10){
                    $level++;
                }elseif($level>10&&$level<15){
                    $level++;
                }
            }
            $back['price'] = $price;
            $back['code'] = 1000;
            echo json_encode($back);
            exit;

        }else{
            $back['code'] = 1001;//参数错误
            echo json_encode($back);
            exit;
        }
    }


    public function calc_nurse_pay(){
        $price_arr = ['','3000','3200','3400','3600','4000','5000','5200','5400','5600','6000','7000','7200','7400','7600','8000','9000'];


        $post['do_time'] = 35;
        $post['nurse_pay'] = 3200;
        $post['nurse_pay_level'] = 2;
        $price = 0;
        $level = 12;
        $day = 70;
        $price_arr = ['','3000','3200','3400','3600','4000','5000','5200','5400','5600','6000','7000','7200','7400','7600','8000','9000'];
        $number = ceil($day/26);//多少单
        for($i=1;$i<=$number;$i++){
               $price +=   ($day-26>=0) ?  $price_arr[$level]  :  sprintf("%.2f", ($price_arr[$level]/26) * $day);
                $day = $day-26;
                $day = $day<0?0:$day;
                if($level<5) {
                    $level++;
                }elseif($level>5&&$level<10){
                    $level++;
                }elseif($level>10&&$level<15){
                    $level++;
                }
        }
        echo $price;

    }


}