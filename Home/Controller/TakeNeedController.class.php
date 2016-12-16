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
        $where = 'order_type = 1  and status<=22';
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
            $where .= ' and status in(22,1,2,3,4,5,6)';
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


    //客服。派单列表。
    public function upServiceList(){

        $pai = M('employee')->where('permission in(1,24)')->select();
        $gu  = M('employee')->where('permission =2 ')->select();
        $this->assign('pai',$pai);
        $this->assign('gu',$gu);
        $this->display();
    }

  //签单列表。
    public function getUpServiceList(){
        $currentpage = I("post.currentpage");
        $pagenum = I("post.pagenum");
        $start = ($currentpage - 1) * $pagenum;
        $post = I("post.");
        $where = 'order_type = 1  and status<22';
        if($post['number']&&$post['number']!=''){
            $where .= ' and  number LIKE "%'.$post['number'].'%"';
        }
        if($post['status_6']){
            $where .= ' and status_6 <='.(strtotime($post['status_6'])+86400) .' and status_6 >='.strtotime($post['status_6']) .'';
        }
        if($post['sales_id']&&$post['sales_id']!=0){
            $where .= ' and  sales_id LIKE "%'.$post['sales_id'].'%"';
        }
        if($post['employee_id']&&$post['employee_id']!=0){
            $where .= ' and  employee_id ='.$post['employee_id'].'';
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
        //若已完善则跳转到overOrderinfo
        if($info['status']>=3){
            echo "<script> window.location.href='".__MODULE__."/TakeNeed/overOrderInfo/id/".I('get.id').".html'</script>";
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

    //完善订单
    public function overOrder(){
        if (I('post.')) {
            $post = I('post.');
            $order_info = M('order')->where('id='.$post['order_id'].'')->find();
            if($order_info['status']==2){
                $post['status'] = 3;
                $post['status_3'] = time();
            }
            if($post['price_add'] == 0){
                $post['add_reason'] = '';
                $post['add_order_price'] = 0;
                $post['add_nurse_price'] = 0;
            }
            $post['number'] = 'DD-' . str_pad($post['order_id'], 6, 0, STR_PAD_LEFT);  //6位数不足补0
            $save_mod = M('order')->where('id='.$post['order_id'].'')->save($post);
            $have_order = M('order_info')->where('order_id='.$post['order_id'].'')->find();



            for($i = 1;$i < 19;$i++){
                $post['skills'] .= $post['skills'.$i].',';
            }
            if($have_order){
                M('order_info')->where('order_id='.$post['order_id'].'')->save($post);
            }else{
                M('order_info')->add($post);
            }

            //修改订单然后去修改匹配里面的订单信息
            $order_info = M('order')->where('id='.$post['order_id'].'')->find();
            $add = $order_info;
            $add['order_id'] = $order_info['id'];
            $add['order_name'] = $order_info['name'];

            unset($add['id']);
            unset($add['number']);
            unset($add['remark']);
            $add_mod =  M('order_nurse')->where('order_id='.$post['order_id'].'')->save($add);
            if($add_mod==false){
                M('order_nurse')->where('order_id='.$post['order_id'].'')->save($add);
            }

            if($save_mod!==false){
                echo "<script>alert('成功'); window.location.href='".__MODULE__."/TakeNeed/overOrderInfo/id/".$post['order_id'].".html'</script>";
                exit;
            }else{
                echo "<script>alert('失败');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
        } else {
            if (!I('get.id')) {
                echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            $info = M('order')->where('id=' . I('get.id') . '')->find();
            if (!$info) {
                echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            if($info['status']==1){
                $status['status'] = 2;
                $status['status_2'] = time();
                $save_mod = M('order')->where('id='.I('get.id').'')->save($status);
                if($save_mod===false){
                    M('order')->where('id='.I('get.id').'')->save($status);
                }
                $info['status'] = 2;
                $info['status_2'] = $status['status_2'] ;
            }
            $info['work_type'] = ($info['work_type']!='育儿嫂'?'月嫂':'育儿嫂');
            $info_in = M('order_info')->where('order_id='.I('get.id').'')->find();
            if($info_in) {
                $info = array_merge($info_in, $info);
            }

            $info['skill_a'] = explode(',',$info['skills']);


            $info['status_1_str'] = $info['status_1'] ? date('Y-m-d H:i:s', $info['status_1']) : 0;
            $info['status_2_str'] = $info['status_2'] ? date('Y-m-d H:i:s', $info['status_2']) : 0;

            $this->assign('info', $info);
            $this->display();
        }
    }

    //发单
    public function status_4(){
        if (!I('get.id')) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('order')->where('id=' . I('get.id') . '')->find();
        if (!$info) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if ($info['status']!=3) {
            echo "<script>alert('未完善或已经发单');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $save['status'] = 4;
        $save['status_4'] = time();

        $save_mod = M('order')->where('id='.$info['id'].'')->save($save);
        if($save_mod!==false){
            echo "<script>alert('成功'); window.location.href='".__MODULE__."/TakeNeed/overOrderInfo/id/".$info['id'].".html'</script>";
            exit;
        }else{
            echo "<script>alert('失败');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
    }


    //重新匹配
    public function status_re_4(){
        if (!I('get.id')) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('order')->where('id=' . I('get.id') . '')->find();
        if (!$info) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if ($info['status']!=5) {
            echo "<script>alert('操作异常！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $save['status'] = 4;
        $save['status_4'] = time();
        $save['status_5'] = '';

        $save_mod = M('order')->where('id='.$info['id'].'')->save($save);
        if($save_mod!==false){
            $de = M('order_nurse')->where('order_id='.$info['id'].'')->delete();
            if(!$de){
                M('order_nurse')->where('order_id='.$info['id'].'')->delete();
            }

            echo "<script>alert('成功'); window.location.href='".__MODULE__."/TakeNeed/overOrderInfo/id/".$info['id'].".html'</script>";
            exit;
        }else{
            echo "<script>alert('失败');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
    }

    //删除匹配阿姨
    public function del_nurse(){
        if ((!I('post.order_id')&&I('post.order_id'))) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('order_nurse')->where('order_id=' . I('post.order_id') . ' and nurse_id=' . I('post.nurse_id') . '')->find();
        if (!$info) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if ($info['status']!=5) {
            echo "<script>alert('操作异常！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $de = M('order_nurse')->where('order_id=' . I('post.order_id') . ' and nurse_id=' . I('post.nurse_id') . '')->delete();
        if($de){
            $code = 1;
        }else{
            $code = 0;
        }
        echo json_encode($code);

    }

    //签约
    public function status_6(){
       $post = I('post.');

        if (!($post['order_id']&&$post['nurse_id'])) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('order_nurse')->where('order_id=' .$post['order_id'] . ' and nurse_id=' . $post['nurse_id'] . '')->find();
        $order_info = M('order')->where('id=' .$post['order_id'] . '')->find();
        if (!$info) {
            echo "<script>alert('未匹配该阿姨！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if ($order_info['status']!=5) {
            echo "<script>alert('不是已匹配完成状态。不能签单！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $where_nurse_do = 'status>5 and status<=8 and nurse_id='. $post['nurse_id'] .' and  (("'.$post['b_time'].'" <= IF(true_s_time!="",true_s_time,s_time) and "'.$post['b_time'].'">=IF(true_b_time!="" ,true_b_time , b_time)) OR ("'.$post['s_time'].'" >= IF(true_b_time!="" ,true_b_time , b_time) and "'.$post['s_time'].'" <=IF(true_s_time!="",true_s_time,s_time) ) OR ("'.$post['b_time'].'" <= IF(true_b_time!="" ,true_b_time , b_time) and "'.$post['s_time'].'" >= IF(true_s_time!="",true_s_time,s_time) )   )';
        $have = M('order_nurse')->where($where_nurse_do)->find();
        if($have){
            echo "<script>alert('阿姨档期冲突');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }

        $post['status'] = 6 ;
        $post['status_6'] = time() ;

        M('order_nurse')->where('order_id=' .$post['order_id'] . ' and nurse_id=' . $post['nurse_id'] . '')->save($post);
        M('order')->where('id=' .$post['order_id'] . '')->save($post);

        M('order_nurse')->where('order_id=' .$post['order_id'] . ' and nurse_id !=' . $post['nurse_id'] . '')->delete();













    }



    //完善订单后详情
    public function overOrderInfo(){
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

        $order_nurse = M('order_nurse')->field('nurse.id,order_nurse.nurse_name,nurse.title_img,nurse.number as nurse_number')->join('nurse ON nurse.id=order_nurse.nurse_id')->where('order_id='.I('get.id').'')->select();
        $this->assign('order_nurse',$order_nurse);

        $this->assign('info',$info);
        $this->display();
    }





}