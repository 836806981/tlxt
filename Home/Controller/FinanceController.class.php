<?php
namespace Home\Controller;
use Think\Controller;
class FinanceController extends CommonController {
    public function _initialize(){
        if(!$_SESSION[C('USER_AUTH_KEY')]['id']){
            echo "<script>alert('请登录！');window.location.href='" . __MODULE__ . "/Index/login.html';</script>";
            exit;
        }
    }


    public function press(){

        $this->authority(array(11,2,3,4));
        $belong = 1;
        $this->assign('belong',$belong);
        $this->display('press');
    }

    public function receive(){

        $this->authority(array(11,2,4));
        $belong = 2;
        $this->assign('belong',$belong);
        $this->display('press');
    }

    public function nursePay(){
        $this->authority(array(11,2,4));
        $belong = 3;
        $this->assign('belong',$belong);
        $this->display('press');
    }

    public function nursePayCheck(){
        $this->authority(array(11));
        $belong = 4;
        $this->assign('belong',$belong);
        $this->display('press');
    }
    public function studentPay(){
        $this->authority(array(11,4));
        $belong = 5;
        $this->assign('belong',$belong);
        $this->display('press');
    }

    public function studentPayCheck(){
        $this->authority(array(11));
        $belong = 6;
        $this->assign('belong',$belong);
        $this->display('press');
    }

    //财务统计列表数据
    public function getFinanceList(){
        $currentpage = I("post.currentpage");
        $pagenum = I("post.pagenum");
        $start = ($currentpage - 1) * $pagenum;
        $post = I("post.");
        $where = '1 ';
        if($post['belong'] == 1){//催款列表
            $where .= 'and status>1 and status<10 ';
            if($post['order_name']){
                $where .= ' and  name LIKE "%'.$post['order_name'].'%"';
            }
            if($post['nurse_name']){
                $where .= ' and  nurse_name LIKE "%'.$post['nurse_name'].'%"';
            }
            if($post['press_status']){
                $where .= ' and  press_status = '.$post['press_status'].'';
            }else{
                $where .= ' and  press_status in(1,2)  ';
            }
            $list = M('order')->where($where)->limit($start,$pagenum)->order('press_status asc,IF(true_s_time!="",true_s_time,true_b_time) desc')->select();
            $press_status_name = ['','未催款','已催款','不需要催款'];
            foreach($list as $k=>$v){
                $list[$k]['press_status_name'] = $press_status_name[$v['press_status']];
            }
//            echo M('order')->getLastSql();die;
            $count = M('order')->where($where)->count();
        }else  if($post['belong'] == 2){//收款列表
            $where .= 'and status>1 and status<10 ';
            if($post['order_name']){
                $where .= ' and  name LIKE "%'.$post['order_name'].'%"';
            }
            if($post['nurse_name']){
                $where .= ' and  nurse_name LIKE "%'.$post['nurse_name'].'%"';
            }
            $list = M('order')->where($where)->limit($start,$pagenum)->order('press_status asc,IF(true_s_time!="",true_s_time,true_b_time) desc')->select();
            $press_status_name = ['','未催款','已催款','不需要催款'];
            foreach($list as $k=>$v){
                $list[$k]['press_status_name'] = $press_status_name[$v['press_status']];
            }
//            echo M('order')->getLastSql();die;
            $count = M('order')->where($where)->count();
        }else  if($post['belong'] == 3){//工资发放
            $where .= ' and true_s_time!="" and order_id!=0';
            if($post['order_name']){
                $where .= ' and  order_name LIKE "%'.$post['order_name'].'%"';
            }
            if($post['nurse_name']){
                $where .= ' and  nurse_name LIKE "%'.$post['nurse_name'].'%"';
            }
            $list = M('order_nurse')->where($where)->limit($start,$pagenum)->order('is_through desc,pay_status asc,true_s_time desc')->select();
            $is_through_name = ['','未审核','已审核'];
            $pay_status_name = ['未发','已发'];
            foreach($list as $k=>$v){
                $list[$k]['is_through_name'] = $is_through_name[$v['is_through']];
                $list[$k]['pay_status_name'] = $pay_status_name[$v['pay_status']];
                $order_contract_money = M('order')->field('contract_money')->where('id='.$v['order_id'].'')->find();
                $list[$k]['contract_money'] = $order_contract_money['contract_money'];
                //获取阿姨银行卡
                $bank_card = M('nurse')->field('bank_card')->where('id='.$v['nurse_id'].'')->find();
                $list[$k]['bank_card'] = $bank_card['bank_card'];
            }
//            echo M('order')->getLastSql();die;
            $count = M('order_nurse')->where($where)->count();
        }else  if($post['belong'] == 4){//收款列表
            $where .= ' and true_s_time!=""  and order_id!=0';
            if($post['order_name']){
                $where .= ' and  order_name LIKE "%'.$post['order_name'].'%"';
            }
            if($post['nurse_name']){
                $where .= ' and  nurse_name LIKE "%'.$post['nurse_name'].'%"';
            }
            $list = M('order_nurse')->where($where)->limit($start,$pagenum)->order('is_through asc,pay_status asc,true_s_time desc')->select();
            $is_through_name = ['','未审核','已审核'];
            $pay_status_name = ['未发','已发'];
            foreach($list as $k=>$v){
                $list[$k]['is_through_name'] = $is_through_name[$v['is_through']];
                $list[$k]['pay_status_name'] = $pay_status_name[$v['pay_status']];


                $order_contract_money = M('order')->field('contract_money')->where('id='.$v['order_id'].'')->find();
                $list[$k]['contract_money'] = $order_contract_money['contract_money'];

            }
            $count = M('order_nurse')->where($where)->count();
        }else  if($post['belong'] == 5){//收款列表
            $where .= ' and true_s_time!="" ';
            if($post['student_name']&&$post['student_name']!=''){
                $where .= ' and  student_name LIKE "%'.$post['student_name'].'%"';
            }
            $list = M('student_practical')->where($where)->limit($start,$pagenum)->order('is_through asc,pay_status asc,true_s_time desc')->select();
            $is_through_name = ['未审核','已审核'];
            $pay_status_name = ['未发','已发'];
            foreach($list as $k=>$v){
                $list[$k]['is_through_name'] = $is_through_name[$v['is_through']];
                $list[$k]['pay_status_name'] = $pay_status_name[$v['pay_status']];
                //获取学员银行卡
                $bank_card = M('student')->field('bank_card')->where('id='.$v['student_id'].'')->find();
                $list[$k]['bank_card'] = $bank_card['bank_card'];
            }
            $count = M('student_practical')->where($where)->count();
        }else  if($post['belong'] == 6){//收款列表
            $where .= ' and true_s_time!="" ';
            if($post['student_name']&&$post['student_name']!=''){
                $where .= ' and  student_name LIKE "%'.$post['student_name'].'%"';
            }
            $list = M('student_practical')->where($where)->limit($start,$pagenum)->order('is_through asc,pay_status asc,true_s_time desc')->select();
            $is_through_name = ['未审核','已审核'];
            $pay_status_name = ['未发','已发'];
            foreach($list as $k=>$v){
                $list[$k]['is_through_name'] = $is_through_name[$v['is_through']];
                $list[$k]['pay_status_name'] = $pay_status_name[$v['pay_status']];
            }
            $count = M('student_practical')->where($where)->count();
        }

        $back['data']['list'] = $list;
        $back['data']['num'] = $count;
        $back['code'] = 1000;
        echo json_encode($back);

    }

    //新增催款

    public function addPress(){
        $this->authority(array(11,3));
        $post = I('post.');
        if(!$post['order_id']){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $post['add_time'] =  time();
        $add_mod = M('order_press')->add($post);
        $order_save['press_status'] = 2;
        $order_save_mod = M('order')->where('id='.$post['order_id'].'')->save($order_save);
        if($order_save_mod===false){
            M('order')->where('id='.$post['order_id'].'')->save($order_save);
        }
        if($add_mod){
            echo "<script>alert('催款成功！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }else{
            echo "<script>alert('催款失败！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }

    }

     //新增收款
    public function addGet(){
        $this->authority(array(11,4));
        $post = I('post.');
        if(!$post['order_id']){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $post['add_time'] =  time();
        $add_mod = M('order_get_money')->add($post);

        $order = M('order')->where('id='.$post['order_id'].'')->find();
        $order_save['press_status'] = 3;
        if(($order['get_money']+$post['get_money'])<$order['contract_money'] && $order['status']==4){
            $order_save['press_status'] = 1;//如果已下户并且没付清钱则需要催款
        }
        $order_save['get_money'] = $order['get_money']+$post['get_money'];//付款后的已收金额
        if($order_save['get_money']>=$order['contract_money']){//是否完成付款
            $order_save['get_all'] = 2;
        }
        $order_save_mod = M('order')->where('id='.$post['order_id'].'')->save($order_save);
        if($order_save_mod!==false){
            echo "<script>alert('收款成功！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }else{
            echo "<script>alert('收款失败！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }

    }

//    审核
    public function doThrough(){
        $this->authority(array(11));
        $post = I('post.');
        $post['is_through'] = 2;
        $save_mod = M('order_nurse')->where('id='.$post['id'].'')->save($post);
        if($save_mod!==false){
            echo "<script>alert('审核成功！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }else{
            echo "<script>alert('审核失败！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }

    }

    //    审核学员工资
    public function doStudentThrough(){
        $this->authority(array(11));
        $post = I('post.');
        $post['is_through'] = 1;
        $save_mod = M('student_practical')->where('id='.$post['id'].'')->save($post);
        if($save_mod!==false){
            echo "<script>alert('审核成功！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }else{
            echo "<script>alert('审核失败！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
    }


//    发放工资
    public function payStatus(){
        $this->authority(array(11,4));
        $post = I('post.');

        $order_nurse = M('order_nurse')->where('id='.$post['id'].'')->find();
        $save_bank['bank_card'] = $post['bank_card'];
        $save_bank_mod = M('nurse')->where('id='.$order_nurse['nurse_id'].'')->save($save_bank);
        if($save_bank_mod!==false){
            M('nurse')->where('id='.$order_nurse['nurse_id'].'')->save($save_bank);
        }
        $pay_status['pay_status'] = 1;
        $save_mod = M('order_nurse')->where('id='.$post['id'].'')->save($pay_status);
        if($save_mod!==false){
            echo "<script>alert('发放成功！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }else{
            echo "<script>alert('发放失败！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }

    }


//    发放学员工资
    public function payStudentStatus(){
        $this->authority(array(11,4));
        $post = I('post.');

        $student_practical = M('student_practical')->where('id='.$post['id'].'')->find();
        $save_bank['bank_card'] = $post['bank_card'];
        $save_bank_mod = M('student')->where('id='.$student_practical['student_id'].'')->save($save_bank);
        if($save_bank_mod!==false){
            M('student')->where('id='.$student_practical['student_id'].'')->save($save_bank);
        }
        $pay_status['pay_status'] = 1;
        $save_mod = M('student_practical')->where('id='.$post['id'].'')->save($pay_status);
        if($save_mod!==false){
            echo "<script>alert('发放成功！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }else{
            echo "<script>alert('发放失败！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }

    }


    public function info(){
        $this->authority(array(11,2,3,4));
        $info = M('order')->where('id='.I('get.id').'')->find();
        $order_get_money = M('order_get_money')->where('order_id='.I('get.id').'')->order('add_time desc')->select();
        $order_press = M('order_press')->where('order_id='.I('get.id').'')->order('add_time desc')->select();
        $order_nurse = M('order_nurse')->where('order_id='.I('get.id').'')->order('add_time desc')->select();

        $this->assign('info',$info);
        $this->assign('order_get_money',$order_get_money);
        $this->assign('order_press',$order_press);
        $this->assign('order_nurse',$order_nurse);
        $this->display();
    }










}