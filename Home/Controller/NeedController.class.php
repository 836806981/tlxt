<?php
namespace Home\Controller;
use Think\Controller;
class NeedController extends CommonController {
    public function _initialize(){
        if(!$_SESSION[C('USER_AUTH_KEY')]['id']){
            echo "<script>alert('请登录！');window.location.href='" . __MODULE__ . "/Index/login.html';</script>";
            exit;
        }
    }



//    派单统计信息
    public function sendControl(){
        //渠道实习统计
        $data['sx_count'] = M('order')->where('order_type = 2 and status<23')->count();//累积
        $data['sx_count_month'] = M('order')->where('order_type = 2  and status<23 and  status_1 >="'.strtotime(date('Y-m')) .'"')->count();//累积
        $data['sx_count_day']  = M('order')->where('order_type = 2  and status<23 and  status_1 >="'.strtotime(date('Y-m-d')) .'"')->count();//累积
        //渠道非实习统计
        $data['fsx_count'] = M('order')->where('order_type = 3 and status<23')->count();//累积
        $data['fsx_count_month'] = M('order')->where('order_type = 3 and  status<23 and  status_1 >="'.strtotime(date('Y-m')) .'"')->count();//累积
        $data['fsx_count_day'] = M('order')->where('order_type = 3 and  status<23 and  status_1 >="'.strtotime(date('Y-m-d')) .'"')->count();//累积

        $data['qd_count_day'] = M('order')->where('order_type >1 and  status<23 and  status_1 >="'.strtotime(date('Y-m-d')) .'"')->count();//累积

//        月嫂
        $data['ys_count'] = M('order')->where('order_type = 1 and status<23 and product!="育儿嫂" and product!="3980特价" ')->count();//累积
        $data['ys_count_month'] = M('order')->where('order_type = 1  and status<23 and product!="育儿嫂" and product!="3980特价"  and  status_1 >="'.strtotime(date('Y-m')) .'"')->count();//累积
        $data['ys_count_day']  = M('order')->where('order_type = 1  and status<23 and product!="育儿嫂" and product!="3980特价"  and  status_1 >="'.strtotime(date('Y-m-d')) .'"')->count();//累积
//        育儿嫂
        $data['yes_count'] = M('order')->where('order_type = 1 and status<23 and product="育儿嫂" ')->count();//累积
        $data['yes_count_month'] = M('order')->where('order_type = 1 and  status<23 and product="育儿嫂"  and  status_1 >="'.strtotime(date('Y-m')) .'"')->count();//累积
        $data['yes_count_day'] = M('order')->where('order_type = 1 and  status<23 and product="育儿嫂"  and  status_1 >="'.strtotime(date('Y-m-d')) .'"')->count();//累积

//        3980
        $data['tj_count'] = M('order')->where('order_type = 1 and status<23  and product="3980特价"')->count();//累积
        $data['tj_count_month'] = M('order')->where('order_type = 1 and  status<23 and product="3980特价"  and  status_1 >="'.strtotime(date('Y-m')) .'"')->count();//累积
        $data['tj_count_day'] = M('order')->where('order_type = 1 and  status<23 and product="3980特价"  and  status_1 >="'.strtotime(date('Y-m-d')) .'"')->count();//累积

//        统计
        $data['zg_count'] = M('order')->where('order_type = 1 and status<23')->count();//累积
        $data['zg_count_month'] = M('order')->where('order_type = 1 and  status<23 and  status_1 >="'.strtotime(date('Y-m')) .'"')->count();//累积
        $data['zg_count_day'] = M('order')->where('order_type = 1 and  status<23 and  status_1 >="'.strtotime(date('Y-m-d')) .'"')->count();//累积

        $data['kf_count_day'] = M('order')->where('order_type = 1 and  status<23 and  status_1 >="'.strtotime(date('Y-m-d')) .'"')->count();//累积


        $this->assign('data',$data);
        $this->display();

    }



    ///////////////////////客户派单//////////////////////
//    添加客服派单
    public function addNeed(){
        $this->authority(array(24,1));
        if(I('post.')){
            $post = I('post.');
            $post['add_time'] = time();
            $post['status_1'] = $post['add_time'];
            $post['status'] = 1;
            $post['order_type'] = 1;
            $sales_name = M('employee')->field('real_name')->where('id='.$post['sales_id'].'')->find();
            $post['sales_name'] = $sales_name['real_name'];
            $post['add_employee'] = $_SESSION[C('USER_AUTH_KEY')]['real_name'];
            $post['employee_id'] = $_SESSION[C('USER_AUTH_KEY')]['id'];

            $order_id = M('order')->add($post);
            if($order_id){
                $save['number'] = 'XQ1-' . str_pad($order_id, 6, 0, STR_PAD_LEFT);  //6位数不足补0
                $save_mod = M('order')->where('id=' . $order_id . '')->save($save);
                if ($save_mod === false) {
                    M('order')->where('id=' . $order_id . '')->save($save);
                }
                echo "<script>alert('派单成功'); window.location.href='".__MODULE__."/Need/serviceList.html'</script>";
                exit;
            }else{
                echo "<script>alert('派单失败');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
        }else{
            $employee_2 = M('employee')->where('permission =2 and status=1')->select();
            $this->assign('employee_2',$employee_2);
            $this->display();
        }
    }

    //客服。派单列表。
    public function serviceList(){
        if($_SESSION[C('USER_AUTH_KEY')]['permission'] == 3){
            echo "<script>window.location.href='" . __MODULE__ . "/Qneed/q_serviceList.html';</script>";
            exit;
        }

        $this->authority(array(24,1));
        $pai = M('employee')->where('permission in(1,24)')->select();
        $gu  = M('employee')->where('permission =2 ')->select();
        $this->assign('pai',$pai);
        $this->assign('gu',$gu);
        $this->display();
    }

    public function have_order(){
        $where = '';
        if(I('post.id')){
            $where = ' and id!='.I('post.id').'';
        }
        $sales_name = M('order')->field('sales_name,number,status')->where('order_type=1 and phone="'.I('post.phone').'"   '.$where.'')->find();
        $status_name = ['','待接单','已接单','已完善','待匹配','已匹配','已签单','已上户','已下户','已完结'];
        $status_name[22] = ['已打回'];
        $status_name[23] = ['回收站'];
        if($sales_name){
            $back = $sales_name;
            $back['status_name'] = $status_name[$sales_name['status']];
            $back['code']=1000;
        }else{
            $back['code']=1001;
        }
        echo json_encode($back);
    }

    //客服派单。获取派单列表数据
    public function getServiceList(){
        $currentpage = I("post.currentpage");
        $pagenum = I("post.pagenum");
        $start = ($currentpage - 1) * $pagenum;
        $post = I("post.");
        $where = 'order_type = 1  ';
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
            $list[$k]['time_down'] = 0;
            if(time() - $v['status_1'] < 300){
                $list[$k]['time_down'] = 300 - (time() - $v['status_1']);
            }
            $list[$k]['status_1_str'] = $v['status_1']?date('Y-m-d H:i:s',$v['status_1']):0;
            $list[$k]['status_2_str'] = $v['status_2']?date('Y-m-d H:i:s',$v['status_2']):0;
            $list[$k]['status_name'] = $status_name[$v['status']];
        }
        $count = M('order')->where($where)->count();
        $back['data']['list'] = $list;
        $back['data']['num'] = $count;
        $back['code'] = 1000;
        $back['permission'] = $_SESSION[C('USER_AUTH_KEY')]['permission'];
        echo json_encode($back);
    }

    //修改派单。5分钟内
    public function changeNeed(){
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
            $employee_2 = M('employee')->where('permission =2 and status=1')->select();
            $this->assign('employee_2',$employee_2);
            $this->assign('info', $info);
            $this->display();
        }else{
            if (!I('post.id')) {
                echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            $info = M('order')->where('id=' . I('post.id') . '')->find();
            if (!$info) {
                echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            if ($info['status'] == 2) {
                echo "<script>alert('已接单，无法修改！');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            $post = I('post.');

            if($info['status'] == 22){
                $post['status'] = 1;
            }

            $save_mod = M('order')->where('id='.$post['id'].'')->save($post);
            if($save_mod!==false){
                echo "<script>alert('修改成功'); window.location.href='".__MODULE__."/Need/serviceList.html'</script>";
                exit;
            }else{
                echo "<script>alert('修改失败');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
        }
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
        $info['status_1_str'] = date('Y-m-d H:i:s',$info['status_1']);
        $info['status_2_str'] = date('Y-m-d H:i:s',$info['status_2']);


        $this->assign('info', $info);
        $this->display();
    }

    //放入回收站
    public function delNeed(){

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
        if (!($info['status']==1||$info['status']==22)) {
            echo "<script>alert('已接单，不能放入回收站');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $save['status'] = 23;
        $save['status_23'] = time();
        $save['del_reason'] = $post['del_reason'];
        $save_mod = M('order')->where('id='.$post['order_id'].'')->save($save);
        if($save_mod!==false){
            echo "<script>alert('放入回收站成功'); window.location.href='".__MODULE__."/Need/serviceList.html'</script>";
            exit;
        }else{
            echo "<script>alert('放入回收站失败');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }

    }

    //客服。回收站列表。
    public function returnList(){
        $pai = M('employee')->where('permission in(1,24)')->select();
        $gu  = M('employee')->where('permission =2 ')->select();
        $this->assign('pai',$pai);
        $this->assign('gu',$gu);
        $this->display();
    }

    //客服派单。获取回收站列表数据
    public function getReturnList(){
        $currentpage = I("post.currentpage");
        $pagenum = I("post.pagenum");
        $start = ($currentpage - 1) * $pagenum;
        $post = I("post.");
        $where = 'status = 23 and order_type = 1';
        if($post['number']&&$post['number']!=''){
            $where .= ' and  number LIKE "%'.$post['number'].'%"';
        }
        if($post['status_23']){
            $where .= ' and status_23 <='.(strtotime($post['status_23'])+86400) .' and status_23 >='.strtotime($post['status_23']) .'';
        }
        if($post['sales_id']&&$post['sales_id']!=0){
            $where .= ' and  sales_id LIKE "%'.$post['sales_id'].'%"';
        }
        if($post['employee_id']&&$post['employee_id']!=0){
            $where .= ' and  employee_id ='.$post['employee_id'].'';
        }
        $list = M('order')->where($where)->limit($start,$pagenum)->order('add_time desc')->select();
        foreach($list as $k=>$v){
            $list[$k]['status_1_str'] = $v['status_1']?date('Y-m-d H:i:s',$v['status_1']):0;
            $list[$k]['status_23_str'] = $v['status_23']?date('Y-m-d H:i:s',$v['status_23']):0;
        }
        $count = M('order')->where($where)->count();
        $back['data']['list'] = $list;
        $back['data']['num'] = $count;
        $back['code'] = 1000;
        $back['permission'] = $_SESSION[C('USER_AUTH_KEY')]['permission'];
        echo json_encode($back);
    }

}