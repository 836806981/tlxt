<?php
namespace Home\Controller;
use Think\Controller;
class SafeController extends CommonController {


    public function _initialize(){
        $this->authority(array(24,5));
        if(!$_SESSION[C('USER_AUTH_KEY')]['id']){
            echo "<script>alert('请登录！');window.location.href='" . __MODULE__ . "/Index/login.html';</script>";
            exit;
        }
    }

    //维系列表页
    public function safeList(){


        $this->display();
    }



    //详情
    public function safeInfo(){

        if(!I('get.id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('nurse')->where('id='.I('get.id').'')->find();
        if(!$info){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }

        $nurse_safe = M('nurse_safe')->where('nurse_id='.I('get.id').'')->order('add_time desc')->select();

        $status_sh_name = ['','未上户','已上户'];
        $info['status_sh_name'] = $status_sh_name[$info['status_sh']];

        $this->assign('nurse_safe',$nurse_safe);
        $this->assign('info',$info);
        $this->display();
    }




    public function getSafeList(){
        $currentpage = I("post.currentpage");
        $pagenum = I("post.pagenum");
        $start = ($currentpage - 1) * $pagenum;
        $post = I("post.");
        $where = 'status = 1 ';
        if($post['order_number']&&$post['order_number']!=''){
            $where .= ' and  order_number LIKE "%'.$post['order_number'].'%"';
        }

        if($post['order_name']&&$post['order_name']!=''){
            $where .= ' and order_name LIKE "%'.$post['order_name'].'%"';
        }

        if($post['nurse_number']&&$post['nurse_number']!=''){
            $where .= ' and  nurse_number LIKE "%'.$post['nurse_number'].'%"';
        }

        if($post['nurse_name']&&$post['nurse_name']!=''){
            $where .= ' and  nurse_name LIKE "%'.$post['nurse_name'].'%"';
        }

        $list = M('nurse_safe')->where($where)->limit($start,$pagenum)->order('add_time desc')->select();

        $status_name = ['','待接单','已接单','已完善','待匹配','已匹配','已签单','已上户','已下户','已完结'];
        $status_name[22] = ['已打回'];
        $status_name[23] = ['回收站'];
        foreach($list as $k=>$v){
           $order = M('order')->where('id='.$v['order_id'].'')->find();
           $id_card = M('nurse')->field('id_card')->where('id='.$v['nurse_id'].'')->find();

            $list[$k]['id_card'] = $id_card['id_card'];
            $list[$k]['status_name'] = $status_name[$order['status']];
            $list[$k]['status_6_str'] = date('Y-m-d',$order['status_6']);;
        }
        $count = M('nurse_safe')->where($where)->count();
        $back['data']['list'] = $list;
        $back['data']['num'] = $count;
        $back['code'] = 1000;
        $back['permission'] = $_SESSION[C('USER_AUTH_KEY')]['permission'];
        echo json_encode($back);
    }


    //包年还是包月
    public function nurseBuyType(){
        if (!I('get.id')) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if (!I('get.type')) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('nurse_safe')->where('id=' . I('get.id') . '')->find();
        if (!$info) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }

        $save['type'] = (I('get.type')==1?'包年':'包月');
        $save_mod = M('nurse_safe')->where('id=' . I('get.id') . '')->save($save);
        if($save_mod!=false) {
            echo "<script>alert('成功'); window.location.href='".__MODULE__."/Safe/SafeList.html'</script>";
            exit;
        }else{
            echo "<script>alert('失败');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }

    }
    public function nurseBuy(){
        if (!I('get.id')) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('nurse')->where('id=' . I('get.id') . '')->find();
        if (!$info) {
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
       $nurse_safe = M('nurse_safe')->where('nurse_id='.I('get.id').' and status =2 ')->order('add_time desc')->select();


        $this->assign('nurse_safe',$nurse_safe);
        $this->assign('info',$info);
        $this->display('safeInfo');
    }

    //保险凭证上传
    public function addSafe(){
        $post = I('post.');
        if($post['id']==''){
            echo "<script>alert('异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if($_FILES['img']['tmp_name']) {
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 2145728;// 设置附件上传大小
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
            $upload->savePath = '/nurse/safe/';// 设置附件上传目录// 上传文件
            $info = $upload->uploadOne($_FILES['img']);
            if (!$info) {// 上传错误提示错误信息
                $this->error($upload->getError());
            } else {//上传成功 获取上传文件信息
                $post['img']  = $info['savepath'] . $info['savename'];
            }
        }
        $post['status'] = 2;
        $save_mod = M('nurse_safe')->where('id='.$post['id'].'')->save($post);
        if($save_mod!=false) {
            echo "<script>alert('成功'); window.location.href='".__MODULE__."/Safe/SafeList.html'</script>";
            exit;
        }else{
            echo "<script>alert('失败');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
    }

    //不购买保险
    public function noSafe(){
        if(I('get.id')==''){
            echo "<script>alert('异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $save_mod = M('nurse_safe')->where('id='.I('get.id').'')->delete();
        if($save_mod!=false) {
            echo "<script> window.location.href='".__MODULE__."/Safe/SafeList.html'</script>";
            exit;
        }else{
            echo "<script>alert('异常，请重试');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
    }


}