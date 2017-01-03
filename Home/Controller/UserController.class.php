<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends CommonController {


    public function _initialize(){
        if(!$_SESSION[C('USER_AUTH_KEY')]['id']){
            echo "<script>alert('请登录！');window.location.href='" . __MODULE__ . "/Index/login.html';</script>";
            exit;
        }
    }

//    员工列表
    public function userList(){

        $this->display();
    }

//获取员工
    public function getUserList(){
//        $post = I('post.');
        $currentpage = I("post.currentpage");
        $pagenum = I("post.pagenum");
        $start = ($currentpage - 1) * $pagenum;

        $list = M('employee')->limit($start,$pagenum)->order('status desc ,add_time desc')->select();
        $count = M('employee')->count();

        $permission_name = ['','客服','顾问','渠道','财务','内勤'];
        $permission_name[24] = '系统管理员';
        foreach($list as $k=>$v){
            $list[$k]['permission_name'] = $permission_name[$v['permission']];
            $list[$k]['status_name'] = $v['status']==1?'<span style="color: #000020">正常</span>':'<span style="color: red">停用</span>';
        }

        $back['data']['list'] = $list;
        $back['data']['num'] = $count;
        $back['code'] = 1000;
        $back['session_id'] = $_SESSION[C('USER_AUTH_KEY')]['id'] ;
        $back['session_per'] = $_SESSION[C('USER_AUTH_KEY')]['permission'] ;
        echo json_encode($back);
    }

    //添加员工
    public function addUser(){
        $this->authority(array(24));
        if(I('post.')){
            $post = I('post.');
            $post['pwd'] = $this->md5_pwd(trim('tlay123'));
            $post['add_time'] = time();
            $post['status'] = 1;
            $add_mod = M('employee')->add($post);
            if($add_mod){
                echo "<script>alert('添加成功'); window.location.href='".__MODULE__."/User/userList'</script>";
                exit;
            }else{
                echo "<script>alert('添加失败');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }

        }else{
         $this->display();
        }
    }

    //修改信息
    public function changeInfo(){
        $this->authority(array(24));
        if(I('post.')){
            $post = I('post.');
            if(!$post['id']){
                echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            $where['id'] = $post['id'];
            $add_mod = M('employee')->where($where)->save($post);
            if($add_mod!==false){
                echo "<script>alert('修改成功'); window.location.href='".__MODULE__."/User/userList'</script>";
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
            if(!($_SESSION[C('USER_AUTH_KEY')]['permission']==24||$_SESSION[C('USER_AUTH_KEY')]['id'] = I('get.id'))){
                echo "<script>alert('你没有权限');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            $user_info = M('employee')->where('id='.I('get.id').'')->find();

            $permission_name = ['','客服','顾问','渠道','财务','内勤'];
            $permission_name[24] = '系统管理员';
            $user_info['permission_name'] = $permission_name[$user_info['permission']];


            $this->assign('info',$user_info);
            $this->display();
        }

    }

    public function resetPwd(){
        $this->authority(array(24));
        if(!I('get.id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if($_SESSION[C('USER_AUTH_KEY')]['permission']!=24){
            echo "<script>alert('无权限！');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }

        $post['pwd'] = $this->md5_pwd(trim('tlay123'));
        $save_mod = M('employee')->where('id='.I('get.id').'')->save($post);
        if($save_mod!==false){
            echo "<script>alert('重置成功');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }else{
            echo "<script>alert('重置失败');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }


    }

    //查看详细
    public function userInfo(){
        if(!I('get.id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }

        $user_info = M('employee')->where('id='.I('get.id').'')->find();

        $permission_name = ['','客服','顾问','渠道','财务','内勤'];
        $permission_name[24] = '系统管理员';
        $user_info['permission_name'] = $permission_name[$user_info['permission']];

        $login_time = M('login_log')->field('login_time')->where('employee_id=' . $user_info['id'] . '')->order('login_time desc')->limit(2)->select();
        $user_info['log_time'] = $login_time[1]['login_time'] ? date('Y-m-d H:i:s', $login_time[1]['login_time']) : '首次登录';

        $user_info['change_per'] = 0;
        if($_SESSION[C('USER_AUTH_KEY')]['permission']==24||$_SESSION[C('USER_AUTH_KEY')]['id'] = I('get.id')){
            $user_info['change_per'] = 1;
        }

        $this->assign('info',$user_info);
        $this->display();
    }

    //停用
    public function set_status_0(){
        $this->authority(array(24));
        if(!I('get.id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $status['status'] = 0;
        $save_mod = M('employee')->where('id='.I('get.id').'')->save($status);
        if($save_mod!==false){
            echo "<script>alert('停用成功');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }else{
            echo "<script>alert('停用失败');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
    }
    //启用
    public function set_status_1(){
        $this->authority(array(24));
        if(!I('get.id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $status['status'] = 1;
        $save_mod = M('employee')->where('id='.I('get.id').'')->save($status);
        if($save_mod!==false){
            echo "<script>alert('停用成功');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }else{
            echo "<script>alert('停用失败');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
    }


    //删除
    public function del_user(){
        $this->authority(array(24));
        if(!I('get.id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $save_mod = M('employee')->where('id='.I('get.id').'')->delete();
        if($save_mod!==false){
            echo "<script>alert('删除成功');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }else{
            echo "<script>alert('删除失败');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
    }

    //修改密码页面
    public function changePass(){
        if(!I('get.id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if(!($_SESSION[C('USER_AUTH_KEY')]['permission']==24||$_SESSION[C('USER_AUTH_KEY')]['id'] = I('get.id'))){
            echo "<script>alert('你没有权限');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }

       $user_info = M('employee')->where('id='.I('get.id').'')->find();
        $this->assign('info',$user_info);
        $this->display();

    }

    public function changePassC(){
        $post = I('post.');
        $user = M('employee')->where('id=' . $post['id'] . '')->find();
        if($post['old']==''||$post['new']==''||$post['newre']==''){
            $back['code']=1001;//密码不能为空
        }elseif(strlen($post['new'])<6||strlen($post['new'])>18){
            $back['code']=1002;//密码长度不正确
        }elseif($user['pwd']!=$this->md5_pwd($post['old'])){
            $back['code']=1003;//原密码不正确
        }else{
            $where['id']=$post['id'];
            $info['pwd']=$this->md5_pwd($post['new']);
            $save = M('employee')->where($where)->save($info);
            if($save!==false){
                $back['code']=1000;//修改成功
            }else{
                $back['code']=1004;//修改失败
            }
        }
        echo json_encode($back);
        exit;

    }

}