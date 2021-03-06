<?php
namespace Home\Controller;
use Think\Controller;
class TakeNeedController extends CommonController {
    public function _initialize(){
        $this->authority(array(24,2));
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

            //判断需要催款
            if($order_info['status']>=7&& $order_info['status']<=9){
                $price_come = $order_info['price_come'];
                $order_info['price_add']==1?($price_come+=$order_info['add_order_price']):'';
                if($order_info['price_come_true']<$price_come){
                    $post['is_press'] = 1;
                }
            }

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
            $nurse_id_arr = M('order_nurse')->field('nurse_id')->where('order_id='.$info['id'].'')->select();
            foreach($nurse_id_arr as $k=>$v){
                //判断 阿姨是否还有匹配
                $post['nurse_id'] = $v['nurse_id'];
                $nurse = M('nurse')->field('status_sh')->where('id='.$post['nurse_id'].'')->find();
                if($nurse['status_sh'] == 2){
                    $have_status = M('order_nurse')->where('nurse_id='.$post['nurse_id'].' and status in(5,6)')->find();
                    if(!$have_status||$have_status==''){
                        $status_sh['status_sh'] = 1;
                        M('nurse')->where('id='.$post['nurse_id'].'')->save($status_sh);
                    }
                }

            }

            $de = M('order_nurse')->where('order_id='.$info['id'].' and is_service=0')->delete();
            if(!$de){
                M('order_nurse')->where('order_id='.$info['id'].' and is_service=0')->delete();
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
        $de = M('order_nurse')->where('order_id=' . I('post.order_id') . ' and nurse_id=' . I('post.nurse_id') . ' and is_service=0')->delete();
        if($de){
            //判断 阿姨是否还有匹配
            $post['nurse_id'] = I('post.order_id');
            $nurse = M('nurse')->field('status_sh')->where('id='.$post['nurse_id'].'')->find();
            if($nurse['status_sh'] == 2){
                $have_status = M('order_nurse')->where('nurse_id='.$post['nurse_id'].' and status in(5,6)')->find();
                if(!$have_status||$have_status==''){
                    $status_sh['status_sh'] = 1;
                    M('nurse')->where('id='.$post['nurse_id'].'')->save($status_sh);
                }
            }
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
        $post['s_time'] = date('Y-m-d',strtotime($post['b_time'])+$post['service_day']*86400);

        $order_nurse_save = M('order_nurse')->where('order_id=' .$post['order_id'] . ' and nurse_id=' . $post['nurse_id'] . '')->save($post);
        if($order_nurse_save==false){
            M('order_nurse')->where('order_id=' .$post['order_id'] . ' and nurse_id=' . $post['nurse_id'] . '')->save($post);
        }
        $order_save = M('order')->where('id=' .$post['order_id'] . '')->save($post);
        if($order_save==false){
            M('order')->where('id=' .$post['order_id'] . '')->save($post);
        }
        $order_nurse_del = M('order_nurse')->where('order_id=' .$post['order_id'] . ' and nurse_id !=' . $post['nurse_id'] . ' and is_service=0')->delete();
        if(!$order_nurse_del){
            M('order_nurse')->where('order_id=' .$post['order_id'] . ' and nurse_id !=' . $post['nurse_id'] . '')->delete();
        }

        //非售后签单时生成收款记录
        if($order_info['is_service']==0 && $order_info['order_type']==1) {
            $add_price_come['price'] = $post['price_come_true'];
            $add_price_come['order_id'] = $post['order_id'];
            $add_price_come['time'] = date('Y-m-d');
            $add_price_come['add_time'] = time();
            $add_price_come['remark'] = '签单收款';
            $price_add_mod = M('price_come')->add($add_price_come);
            if (!$price_add_mod) {
                M('price_come')->add($add_price_come);
            }
        }
        if($order_info['order_type']==1) {//客户单地址
            echo "<script>alert('签单成功');window.location.href='" . __MODULE__ . "/TakeNeed/overOrderInfo/id/" . $post['order_id'] . ".html'</script>";
            exit;
        }else{//渠道单地址
            echo "<script>alert('选择成功');window.location.href='" . __MODULE__ . "/Qneed/q_needInfo/id/" . $post['order_id'] . ".html'</script>";
            exit;
        }
    }



    //上户
    public function status_7(){
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
        if ($order_info['status']!=6) {
            echo "<script>alert('不是已签单状态，不能上户！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
//        $where_nurse_do = 'status>5 and status<=8 and nurse_id='. $post['nurse_id'] .' and  (("'.$post['b_time'].'" <= IF(true_s_time!="",true_s_time,s_time) and "'.$post['b_time'].'">=IF(true_b_time!="" ,true_b_time , b_time)) OR ("'.$post['s_time'].'" >= IF(true_b_time!="" ,true_b_time , b_time) and "'.$post['s_time'].'" <=IF(true_s_time!="",true_s_time,s_time) ) OR ("'.$post['b_time'].'" <= IF(true_b_time!="" ,true_b_time , b_time) and "'.$post['s_time'].'" >= IF(true_s_time!="",true_s_time,s_time) )   )';
//        $have = M('order_nurse')->where($where_nurse_do)->find();
//        if($have){
//            echo "<script>alert('阿姨档期冲突');window.onload=function(){window.history.go(-1);return false;}</script>";
//            exit;
//        }

        $post['status'] = 7 ;
        $post['status_7'] = time() ;


        $post['s_time'] = date('Y-m-d',strtotime($post['true_b_time'])+$post['service_day']*86400);

        $order_nurse_save = M('order_nurse')->where('order_id=' .$post['order_id'] . ' and nurse_id=' . $post['nurse_id'] . '')->save($post);
        if($order_nurse_save==false){
            M('order_nurse')->where('order_id=' .$post['order_id'] . ' and nurse_id=' . $post['nurse_id'] . '')->save($post);
        }

        if($order_info['order_type'] == 1) {
            //判断需要催款
            $price_come = $order_info['price_come'];
            $order_info['price_add'] == 1 ? ($price_come += $order_info['add_order_price']) : '';
            if ($order_info['price_come_true'] < $price_come) {
                $post['is_press'] = 1;
            }
        }

        $order_save = M('order')->where('id=' .$post['order_id'] . '')->save($post);
        if($order_save==false){
            M('order')->where('id=' .$post['order_id'] . '')->save($post);
        }

        $nurse = M('nurse')->field('name,number')->where('id='.$post['nurse_id'].'')->find();
        //保险提醒添加
//        $add['order_id'] = $post['order_id'];
//        $add['nurse_id'] = $post['nurse_id'];
//
//        $add['order_name'] = $order_info['name'];
//        $add['order_number'] = $order_info['number'];
//        $add['nurse_name'] = $nurse['name'];
//        $add['nurse_number'] = $nurse['number'];
//
//        $add['time'] = $post['time'];
//        $add['add_time'] = time();
//        $add['status'] = 1;
//        $safe_add = M('nurse_safe')->add($add);
//        if(!$safe_add){
//            M('nurse_safe')->add($add);
//        }

        $save_nurse['status_sh'] = 3;
        $save_nurse = M('nurse')->where('id='.$post['nurse_id'].'')->save($save_nurse);
        if($save_nurse==false){
            M('nurse')->where('id='.$post['nurse_id'].'')->save($save_nurse);
        }

        if($order_info['order_type']==1) {//客户单地址
            echo "<script>alert('上户成功');window.location.href='".__MODULE__."/TakeNeed/overOrderInfo/id/".$post['order_id'].".html'</script>";
            exit;
        }else{//渠道单地址
            echo "<script>alert('上户成功');window.location.href='" . __MODULE__ . "/Qneed/q_needInfo/id/" . $post['order_id'] . ".html'</script>";
            exit;
        }

    }


    //完善订单后详情
    public function overOrderInfo(){
        if (!I('get.id')) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('order')->where('id=' . I('get.id') . '')->find();
        if (!$info|| $info['order_type']!=1) {
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


        if($info['status'] == 7){
            $nurse = M('nurse')->where('id='.$order_nurse[0]['id'].'')->find();
            $this->assign('nurse',$nurse);
        }
        $this->assign('order_nurse',$order_nurse);
        $this->assign('info',$info);
        $this->assign('now',date('Y-m-d'));

        $status_name = ['','已派单/待接单','已接单/待完善','已完善/待发单','已发单/待匹配','已匹配/待签约','已签约/待上户','已上户','已下户','已完结'];


        $this->assign('status_name',$status_name);
        $this->display();
    }

    //根据实际下户时间判断数据
    public function sure_price(){
        $post = I('post.');
        $order = M('order')->where('id='.$post['order_id'].'')->find();
        $nurse = M('nurse')->where('id='.$post['nurse_id'].'')->find();

        $data['service_day'] = (strtotime($post['true_s_time']) - strtotime($order['true_b_time']))/86400 ;
        if($data['service_day']<=0){
            $data['code'] = 1001;
            echo json_encode($data);
            exit;
        }
        if($nurse['type'] == 1){
            $price = ($data['service_day']>=15)?(300+ sprintf("%.2f", 3000/28 * ($data['service_day']-15))):$data['service_day']*20;
        }elseif($nurse['type'] == 2){
            if($nurse['agreement_type']==1){
                $price_arr = ['','3000','3200','3400','3600','4000','5000','5200','5400','5600','6000','7000','7200','7400','7600','8000','9000'];
                $price = 0;
                $level = array_search($nurse['price'],$price_arr);
                $day = $data['service_day'];
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
            }else{
                $price = sprintf("%.2f", $nurse['price']/28 * ($data['service_day']));
            }
        }
        $data['code'] = 1000;
        $data['price'] = $price;
        echo json_encode($data);
        exit;
    }

    //下户或者售后
    public function status_8(){
        $post = I('post.');
        if($post['true_s_time']==''||$post['nurse_id']==''||$post['order_id']==''||$post['nurse_pay']==''){
            echo "<script>alert('数据异常，请重新提交！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $order = M('order')->where('id='.$post['order_id'].'')->find();
        $nurse = M('nurse')->where('id='.$post['nurse_id'].'')->find();




        $data['service_day'] = (strtotime($post['true_s_time']) - strtotime($order['true_b_time']))/86400 ;
        if($data['service_day']<=0){
            echo "<script>alert('下户时间异常，请重新提交');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if($nurse['type'] == 1){
            $price = ($data['service_day']>=15)?(300+ sprintf("%.2f", 3000/28 * ($data['service_day']-15))):$data['service_day']*20;
        }elseif($nurse['type'] == 2){
            if($nurse['agreement_type']==1){
                $price_arr = ['','3000','3200','3400','3600','4000','5000','5200','5400','5600','6000','7000','7200','7400','7600','8000','9000'];
                $price = 0;
                $level = array_search($nurse['price'],$price_arr);
                $day = $data['service_day'];
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
            }else{
                $price = sprintf("%.2f", $nurse['price']/28 * ($data['service_day']));
            }
        }

        $post['nurse_pay_do'] = $price;
        $post['service_day'] = $data['service_day'];
        $post['status_8'] = time();
        $save_order_nurse = $post;
        $save_order = $post;
        $save_order_nurse['status'] = 8;
        if($post['status']==8){

        }else{
            $save_order_nurse['is_service'] = 1;
            $save_order['is_service'] = 1;
            $save_order['service_day'] = $order['service_day'] - $day;
            $save_order['is_press'] = 0;
            $save_order['status'] = 9;
        }

        $save_order_nurse['nurse_price'] = $nurse['price'];

        //售后数据处

        $save_order_mod = M('order')->where('id='.$post['order_id'].'')->save($save_order);
        if($save_order_mod==false){
            M('order')->where('id='.$post['order_id'].'')->save($save_order);
        }
        $save_order_nurse_mod = M('order_nurse')->where('order_id='.$post['order_id'].' and nurse_id='.$post['nurse_id'].' and is_service=0')->save($save_order_nurse);
        if($save_order_nurse_mod==false){
            M('order_nurse')->where('order_id='.$post['order_id'].' and nurse_id='.$post['nurse_id'].' and is_service=0')->save($save_order_nurse);
        }

        //判断 阿姨是否还有匹配
        $have_status = M('order_nurse')->where('nurse_id='.$post['nurse_id'].' and status in(5,6)')->find();
        if(!$have_status||$have_status==''){
            $status_sh['status_sh'] = 1;
        }else{
            $status_sh['status_sh'] = 2;
        }
        //判断下户。阿姨的升级操作
        if($post['status']==8 && $nurse['agreement_type']==1&& $nurse['type']==2){
            $level_name = ['','小田螺1.1','小田螺1.2','小田螺1.3','小田螺1.4','小田螺1.5','大田螺2.1','大田螺2.2','大田螺2.3','大田螺2.4','大田螺2.5','超级田螺3.1','超级田螺3.2','超级田螺3.3','超级田螺3.4','超级田螺3.5','金牌田螺'];
            $price_arr = ['','3000','3200','3400','3600','4000','5000','5200','5400','5600','6000','7000','7200','7400','7600','8000','9000'];
            $level_up_number = $data['service_day']/26 + ($data['service_day']%26>=15?1:0);
            if( $status_sh['price_level']<=5) {
                $status_sh['price_level'] = $nurse['price_level'] + $level_up_number;
                $status_sh['price_level'] = ($status_sh['price_level']>=5?5: $status_sh['price_level']);
                $status_sh['price'] = $price_arr[$status_sh['price_level']];
                $status_sh['price_name'] =$level_name[$status_sh['price_level']];
            }elseif($status_sh['price_level']<=10){
                $status_sh['price_level'] = $nurse['price_level'] + $level_up_number;
                $status_sh['price_level'] = ($status_sh['price_level']>=10?10: $status_sh['price_level']);
                $status_sh['price'] = $price_arr[$status_sh['price_level']];
                $status_sh['price_name'] = $level_name[$status_sh['price_level']];
            }elseif($status_sh['price_level']<=15){
                $status_sh['price_level'] = $nurse['price_level'] + $level_up_number;
                $status_sh['price_level'] = ($status_sh['price_level']>=15?15: $status_sh['price_level']);
                $status_sh['price'] = $price_arr[$status_sh['price_level']];
                $status_sh['price_name'] = $level_name[$status_sh['price_level']];
            }
        }

        if($post['status']==8 && $nurse['type']==1){
            $status_sh['type'] = 2;
            $status_sh['status'] = 1;
            $status_sh['type_time'] = time();
        }

        $save_nurse_mod = M('nurse')->where('id='.$post['nurse_id'].'')->save($status_sh);
        if($save_nurse_mod==false){
            M('nurse')->where('id='.$post['nurse_id'].'')->save($status_sh);
        }
        if($order['order_type']==1) {//客户单地址
            echo "<script>alert('成功');window.location.href='".__MODULE__."/TakeNeed/overOrderInfo/id/".$post['order_id'].".html'</script>";
            exit;
        }else{//渠道单地址
            echo "<script>alert('成功');window.location.href='" . __MODULE__ . "/Qneed/q_needInfo/id/" . $post['order_id'] . ".html'</script>";
            exit;
        }

    }


}