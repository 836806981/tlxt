<?php
namespace Home\Controller;
use Think\Controller;
class MoneyController extends CommonController {


    public function _initialize(){
        $this->authority(array(24,4,5));
        if(!$_SESSION[C('USER_AUTH_KEY')]['id']){
            echo "<script>alert('请登录！');window.location.href='" . __MODULE__ . "/Index/login.html';</script>";
            exit;
        }
    }

    //催款列表
    public function pressList(){
        if($_SESSION[C('USER_AUTH_KEY')]['permission'] == 4){
            echo "<script>window.location.href='" . __MODULE__ . "/Money/financeList.html';</script>";
            exit;
        }

        $gu  = M('employee')->where('permission =2 ')->select();
        $this->assign('gu',$gu);
        $this->display();
    }

     //收款列表
    public function priceComeList(){
        $this->authority(array(24,5));
        $gu  = M('employee')->where('permission =2 ')->select();
        $this->assign('gu',$gu);
        $this->display();
    }
    //已完成
    public function doneList(){

        $gu  = M('employee')->where('permission =2 ')->select();
        $this->assign('gu',$gu);
        $this->display();
    }

    //j结算列表
    public function statement(){
        $this->authority(array(24,5));
        $this->assign('belong',1);
        $this->display();
    }

    //财务审核
    public function financeList(){
        $this->authority(array(24,4));
        $this->assign('belong',2);
        $this->display('statement');
    }

    //boss审核
    public function bossList(){
        $this->authority(array(24));
        $this->assign('belong',3);
        $this->display('statement');
    }

    //工资发放
    public function giveList(){
        $this->authority(array(24,5));
        $this->assign('belong',4);
        $this->display('statement');
    }



    public function getStatementList(){
        $currentpage = I("post.currentpage");
        $pagenum = I("post.pagenum");
        $start = ($currentpage - 1) * $pagenum;
        $post = I("post.");
        $where = '1';
        if($post['belong']==2){
            $where .= ' and is_statement=2 and is_through in(0,2)';
        }elseif($post['belong']==3){
            $where .= ' and is_statement=2 and is_through =1';
        }elseif($post['belong']==4){
            $where .= ' and is_statement in(2,3) and is_through =3';
        }


        if($post['order_number']&&$post['order_number']!=''){
            $where .= ' and  order_number LIKE "%'.$post['order_number'].'%"';
        }
        if($post['nurse_number']&&$post['nurse_number']!=''){
            $where .= ' and  nurse_number LIKE "%'.$post['nurse_number'].'%"';
        }
        if($post['nurse_name']&&$post['nurse_name']!=''){
            $where .= ' and  nurse_name LIKE "%'.$post['nurse_name'].'%"';
        }
        if($post['is_statement']&&$post['is_statement']!=0){
            $where .= ' and is_statement = '.$post['is_statement'].'';
        }


        $list = M('order_nurse')->where($where)->limit($start,$pagenum)->order('is_statement asc')->select();

        $status_name = ['','待接单','已接单','已完善','待匹配','已匹配','已签单','已上户','已下户','已完结'];
        $status_name[22] = ['已打回'];
        $status_name[23] = ['回收站'];

        $level_name = ['','小田螺1.1','小田螺1.2','小田螺1.3','小田螺1.4','小田螺1.5','大田螺2.1','大田螺2.2','大田螺2.3','大田螺2.4','大田螺2.5','超级田螺3.1','超级田螺3.2','超级田螺3.3','超级田螺3.4','超级田螺3.5','金牌田螺'];
        $price_arr = ['','3000','3200','3400','3600','4000','5000','5200','5400','5600','6000','7000','7200','7400','7600','8000','9000'];

        foreach($list as $k=>$v){
            $level = array_search($v['nurse_price'],$price_arr);
            $list[$k]['level_name'] = $level_name[$level];
            $nurse = M('nurse')->field('come')->where('id='.$v['nurse_id'].'')->find();
            $list[$k]['come'] = $nurse['come'];

            if(in_array($v['product'],array('小田螺','大田螺','金牌田螺','超级田螺'))){
                $list[$k]['product'] = '月嫂-'.$v['product'];
            }
            $list[$k]['status_6_str'] = $v['status_6']?date('Y-m-d H:i:s',$v['status_6']):'';
            $list[$k]['status_7_str'] = $v['status_7']?date('Y-m-d H:i:s',$v['status_7']):'';
            $list[$k]['status_name'] = $status_name[$v['status']];
            $list[$k]['is_service_name'] = ($v['is_service']==1?'售后':'正常');
            $list[$k]['is_defer_name'] = ($v['is_defer']==1?'暂缓发放':'正常发放');
        }
        $count = M('order_nurse')->where($where)->count();
        $back['data']['list'] = $list;
        $back['data']['num'] = $count;
        $back['code'] = 1000;
        $back['permission'] = $_SESSION[C('USER_AUTH_KEY')]['permission'];
        echo json_encode($back);
    }
    //财务审核阿姨工资
    public function finance(){
        $this->authority(array(24,4));
        if (!I('get.id')) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('order_nurse')->where('id=' . I('get.id') . '')->find();
        if (!$info) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if ($info['is_statement']!=2||$info['is_through']!=0) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $save['is_through'] = 1;
        $save_mod = M('order_nurse')->where('id=' . I('get.id') . '')->save($save);
        if($save_mod==false){
            echo "<script>alert('异常，请重试');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }else{
            echo "<script>alert('成功');window.location.href='".__MODULE__."/Money/financeList.html'</script>";
            exit;
        }
    }
    //财务再次审核阿姨工资
    public function finance_re(){
        $this->authority(array(24,4));
        if (!I('get.id')) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('order_nurse')->where('id=' . I('get.id') . '')->find();
        if (!$info) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if ($info['is_statement']!=2||$info['is_through']!=2) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $save['is_through'] = 1;
        $save_mod = M('order_nurse')->where('id=' . I('get.id') . '')->save($save);
        if($save_mod==false){
            echo "<script>alert('异常，请重试');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }else{
            echo "<script>alert('成功');window.location.href='".__MODULE__."/Money/financeList.html'</script>";
            exit;
        }
    }

    //boss通过工资审核
    public function boss3(){
        $this->authority(array(24));
        if (!I('get.id')) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('order_nurse')->where('id=' . I('get.id') . '')->find();
        if (!$info) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if ($info['is_statement']!=2||$info['is_through']!=1) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $save['is_through'] = 3;
        $save_mod = M('order_nurse')->where('id=' . I('get.id') . '')->save($save);
        if($save_mod==false){
            echo "<script>alert('异常，请重试');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }else{
            echo "<script>alert('成功');window.location.href='".__MODULE__."/Money/bossList.html'</script>";
            exit;
        }
    }

    //boss不通过工资审核
    public function boss2(){
        $this->authority(array(24));
        if (!I('post.order_nurse_id')) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('order_nurse')->where('id=' . I('post.order_nurse_id') . '')->find();
        if (!$info) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if ($info['is_statement']!=2||$info['is_through']!=1) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $save['is_through'] = 2;
        $save['no_through_reason'] = I('post.no_through_reason');
        $save_mod = M('order_nurse')->where('id=' .I('post.order_nurse_id') . '')->save($save);
        if($save_mod==false){
            echo "<script>alert('异常，请重试');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }else{
            echo "<script>alert('成功');window.location.href='".__MODULE__."/Money/bossList.html'</script>";
            exit;
        }
    }

    //已发
    public function give(){
        $this->authority(array(24,4));
        if (!I('post.order_nurse_id')) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('order_nurse')->where('id=' . I('post.order_nurse_id') . '')->find();
        if (!$info) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if ($info['is_statement']!=2||$info['is_through']!=3) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $save['pay_status'] = 1;
        $save['is_statement'] = 3;
        $save['status'] = 9;
        $save['status_9'] = time();
        $save['nurse_pay_true'] = I('post.nurse_pay_true');
        $save_mod = M('order_nurse')->where('id=' .I('post.order_nurse_id') . '')->save($save);
        if($save_mod==false){
            echo "<script>alert('异常，请重试');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }else{
            if($info['is_service']!=1){
                $save_order['status'] = 9;
                $save_order['status_9'] = time();
                $save_order_mod = M('order')->where('id='.$info['order_id'].'')->save($save_order);
                if($save_order_mod==false){
                    M('order')->where('id='.$info['order_id'].'')->save($save_order);
                }
            }
            echo "<script>alert('成功');window.location.href='".__MODULE__."/Money/giveList.html'</script>";
            exit;
        }
    }


    //结算阿姨薪资
    public function do_statement(){
        $this->authority(array(24,5));
        $post = I('post.');
        $post['reward_reason'] = $post['reward_reason1'].$post['reward_reason2'];
        $post['is_statement'] = 2;
        if($_FILES['img']['tmp_name'][0]) {
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 2145728;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->savePath = '/nurse/reward/';// 设置附件上传目录// 上传文件
            $info = $upload->upload(array($_FILES['img']));
            if (!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            } else {//上传成功 获取上传文件信息
                foreach($info as $v){
                    $post['img']=  $v['savepath'] . $v['savename'];
                }
            }
        }


        $save_mod = M('order_nurse')->where('id='.$post['id'].'')->save($post);
        if($save_mod==false){
            echo "<script>alert('异常，请重试');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }else{
            echo "<script>alert('成功');window.location.href='".__MODULE__."/Money/statement.html'</script>";
            exit;
        }
    }


    public function getPressList(){
        $currentpage = I("post.currentpage");
        $pagenum = I("post.pagenum");
        $start = ($currentpage - 1) * $pagenum;
        $post = I("post.");
        $where = 'order_type = 1 and is_press = 1 ';
        if($post['number']&&$post['number']!=''){
            $where .= ' and  number LIKE "%'.$post['number'].'%"';
        }
        if($post['name']&&$post['name']!=''){
            $where .= ' and  name LIKE "%'.$post['name'].'%"';
        }
        if($post['sales_id']){
            $where .= ' and sales_id = '.$post['sales_id'].'';
        }
        if($post['time']==0){
        }elseif($post['time']==1){
            $where .= ' and IF(true_b_time!="" ,true_b_time , b_time)  <="'.date('Y-m-d').'"  and IF(true_b_time!="",true_b_time,b_time)>"'.date('Y-m-d',strtotime('-3 days')).'"';
        }elseif($post['time']==3){
            $where .= ' and IF(true_b_time!="",true_b_time,b_time)  <="'.date('Y-m-d',strtotime('-3 days')).'"  and IF(true_b_time!="",true_b_time,b_time)>"'.date('Y-m-d',strtotime('-7 days')).'"';
        }elseif($post['time']==7){
            $where .= ' and IF(true_b_time!="",true_b_time,b_time)  <="'.date('Y-m-d',strtotime('-7 days')).'"  and IF(true_b_time!="",true_b_time,b_time)>"'.date('Y-m-d',strtotime('-10 days')).'"';
        }elseif($post['time']==10){
            $where .= ' and IF(true_b_time!="",true_b_time,b_time)  <="'.date('Y-m-d',strtotime('-10 days')).'" ';
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

    public function getPriceComeList(){
        $currentpage = I("post.currentpage");
        $pagenum = I("post.pagenum");
        $start = ($currentpage - 1) * $pagenum;
        $post = I("post.");
        $where = 'order_type = 1 and status>5 and status <9';
        if($post['number']&&$post['number']!=''){
            $where .= ' and  number LIKE "%'.$post['number'].'%"';
        }
        if($post['name']&&$post['name']!=''){
            $where .= ' and  name LIKE "%'.$post['name'].'%"';
        }
        if($post['sales_id']){
            $where .= ' and sales_id = '.$post['sales_id'].'';
        }
        if($post['time']==0){
        }elseif($post['time']==1){
            $where .= ' and IF(true_b_time!="" ,true_b_time , b_time)  <="'.date('Y-m-d').'"  and IF(true_b_time!="",true_b_time,b_time)>"'.date('Y-m-d',strtotime('-3 days')).'"';
        }elseif($post['time']==3){
            $where .= ' and IF(true_b_time!="",true_b_time,b_time)  <="'.date('Y-m-d',strtotime('-3 days')).'"  and IF(true_b_time!="",true_b_time,b_time)>"'.date('Y-m-d',strtotime('-7 days')).'"';
        }elseif($post['time']==7){
            $where .= ' and IF(true_b_time!="",true_b_time,b_time)  <="'.date('Y-m-d',strtotime('-7 days')).'"  and IF(true_b_time!="",true_b_time,b_time)>"'.date('Y-m-d',strtotime('-10 days')).'"';
        }elseif($post['time']==10){
            $where .= ' and IF(true_b_time!="",true_b_time,b_time)  <="'.date('Y-m-d',strtotime('-10 days')).'" ';
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

    public function getDoneList(){
        $currentpage = I("post.currentpage");
        $pagenum = I("post.pagenum");
        $start = ($currentpage - 1) * $pagenum;
        $post = I("post.");
        $where = 'order_type = 1 and status =9';
        if($post['number']&&$post['number']!=''){
            $where .= ' and  number LIKE "%'.$post['number'].'%"';
        }
        if($post['name']&&$post['name']!=''){
            $where .= ' and  name LIKE "%'.$post['name'].'%"';
        }
        if($post['sales_id']){
            $where .= ' and sales_id = '.$post['sales_id'].'';
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

   //催款详细页
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
            if ($info['status']<6||$info['status']>=9) {
                echo "<script>alert('还未签单或者已打回（回收站/已完成）');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            $info['status_1_str'] = $info['status_1'] ? date('Y-m-d H:i:s', $info['status_1']) : 0;
            $info['status_2_str'] = $info['status_2'] ? date('Y-m-d H:i:s', $info['status_2']) : 0;

            $info['price_add_str'] = $info['price_add']==1?'有':'无';

            $order_nurse = M('order_nurse')->field('nurse_id')->where('order_id='.I('get.id').'  and is_service=0')->find();

            $nurse = M('nurse')->where('id='.$order_nurse['nurse_id'].'')->find();

            $price_come = M('price_come')->where('order_id='.$info['id'].'')->order('add_time desc')->select();
            $press = M('press')->where('order_id='.$info['id'].'')->order('add_time desc')->select();



            $this->assign('price_come',$price_come);
            $this->assign('press',$press);
            $this->assign('nurse',$nurse);

            $this->assign('order_nurse',$order_nurse);
            $this->assign('info',$info);

            $status_name = ['','已派单/待接单','已接单/待完善','已完善/待发单','已发单/待匹配','已匹配/待签约','已签约/待上户','已上户','已下户','已完结'];

            $this->assign('status_name',$status_name);
            $this->display();

    }



    public function orderInfoToDone(){
        if (!I('get.id')) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('order')->where('id=' . I('get.id') . '')->find();
        if (!$info) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if ($info['status']!=8||$info['is_press']!=0) {
            echo "<script>alert('未下户，或未付清款');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }


        $save['status_9'] = time();
        $save['status'] = 9;
        $save_mod = M('order')->where('id=' . I('get.id') . '')->save($save);
        if($save_mod==false){
            echo "<script>alert('异常，请重试');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }else{
            echo "<script>alert('成功');window.location.href='".__MODULE__."/Money/orderInfoDone/id/".I('get.id').".html'</script>";
            exit;
        }
    }

    //已完成详细页
    public function orderInfoDone(){
        if (!I('get.id')) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('order')->where('id=' . I('get.id') . '')->find();
        if (!$info) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if ($info['status']<6||$info['status']>10) {
            echo "<script>alert('还未签单或者已打回（回收站）');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info['status_1_str'] = $info['status_1'] ? date('Y-m-d H:i:s', $info['status_1']) : 0;
        $info['status_2_str'] = $info['status_2'] ? date('Y-m-d H:i:s', $info['status_2']) : 0;

        $info['price_add_str'] = $info['price_add']==1?'有':'无';

        $order_nurse = M('order_nurse')->field('nurse_id')->where('order_id='.I('get.id').'  and is_service=0')->find();

        $nurse = M('nurse')->where('id='.$order_nurse['nurse_id'].'')->find();

        $price_come = M('price_come')->where('order_id='.$info['id'].'')->order('add_time desc')->select();
        $press = M('press')->where('order_id='.$info['id'].'')->order('add_time desc')->select();



        $this->assign('price_come',$price_come);
        $this->assign('press',$press);
        $this->assign('nurse',$nurse);

        $this->assign('order_nurse',$order_nurse);
        $this->assign('info',$info);

        $status_name = ['','已派单/待接单','已接单/待完善','已完善/待发单','已发单/待匹配','已匹配/待签约','已签约/待上户','已上户','已下户','已完结'];

        $this->assign('status_name',$status_name);
        $this->display();

    }

    //新增收款
    public function addPrice(){
        $this->authority(array(24,5));
        $post = I('post.');
        $post['add_time'] = time();
        $add_mod = M('price_come')->add($post);
        if($add_mod){
            $order = M('order')->where('id='.$post['order_id'].'')->find();
            $save_order['price_come_true'] = $order['price_come_true'] + $post['price'];
            $come = $order['price_come'] + ($order['price_add']==1?$order['add_order_price']:0);
            if( $save_order['price_come_true']>=$come){
                $save_order['is_press'] = 0;
            }
            $save_mod = M('order')->where('id='.$post['order_id'].'')->save($save_order);
            if($save_mod==false){
                M('order')->where('id='.$post['order_id'].'')->save($save_order);
            }

            echo "<script>alert('新增成功');window.location.href='".__MODULE__."/Money/orderInfo/id/".$post['order_id'].".html'</script>";
            exit;
        }else{
            echo "<script>alert('新增异常，请重新新增');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
    }

    //新增催款
    public function addPress(){
        $this->authority(array(24,5));
        $post = I('post.');
        $post['add_time'] = time();
        $add_mod = M('press')->add($post);
        if($add_mod){
            echo "<script>alert('新增成功');window.location.href='".__MODULE__."/Money/orderInfo/id/".$post['order_id'].".html'</script>";
            exit;
        }else{
            echo "<script>alert('新增异常，请重新新增');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
    }









    //新增维系
    public function addHold(){
        $this->authority(array(24,5));
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