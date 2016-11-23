<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends CommonController {
    public function  login_test(){
        if(!$_SESSION[C('USER_AUTH_KEY')]['id']){
            echo "<script>alert('请登录！');window.location.href='" . __MODULE__ . "/Admin/login.html';</script>";
        }
    }
    public function per_test(){
//        $admin_user = M('permission')->where('employee_id='. $_SESSION[C('USER_AUTH_KEY')]['id'].'')->find();
        if($_SESSION[C('USER_AUTH_KEY')]['permission']!=1){
            echo "<script>alert('你没有权限！');window.location.href='" . __MODULE__ . "/Admin/info.html';</script>";
        }
    }

    //登录功能及页面
    public function  login(){
        $post = I('post.');
//        $post['username'] = 'admin';
//        $post['pwd'] = '123123';

        if($post) {
            $map['username'] = trim($post['username']);
            $map['pwd'] = $this->md5_pwd(trim($post['pwd']));
            $user_info = M('employee')->where($map)->find();

            if ($user_info && $user_info != '') {//登录成功的处理
                if($user_info['status']==0){
                    echo "<script>alert('账号被停用或非后台账号，无法登陆！');window.location.href='" . __MODULE__ . "/Index/login.html;</script>";
                }else {
                    $permission_name = ['','客服','顾问','管理员内勤','财务'];
                    $permission_name[11] = '系统管理员';
                    $user_info['permission_name'] = $permission_name[$user_info['permission']];

                    $_SESSION[C('USER_AUTH_KEY')] = $user_info;
                    $session_id = session_id();
                    $ip = $this->getIP();
                    $log['sessionid'] = $session_id;
                    $log['login_ip'] = $ip;
                    $log['employee_id'] = $user_info['id'];
                    $log['login_time'] = time();
                    $log['mac'] = $this->GetMacAddr(PHP_OS);//获取mac 地址
                    switch ($user_info['status']) {
                        case 1:
                            $log['status'] = 1;
                            break;
                        case 2:
                            $log['status'] = 2;
                            break;
                    }
                    M('login_log')->add($log);
                    echo "<script>alert('登录成功！');window.location.href='" . __MODULE__ . "';</script>";
                }
            } else{
                $ip = $this->getIP();
                $log['sessionid']='';
                $log['login_ip']=$ip;
                $log['login_time']=time();
                $log['mac'] = $this->GetMacAddr(PHP_OS);//获取mac 地址
                M('login_log')->add($log);
                echo "<script>alert('用户不存在或密码错误！');window.location.href='".__MODULE__."/Index/login.html'</script>";
            }
        }else{
            $this->display();
        }

    }

    public function index(){

        echo "<script>window.location.href='".__MODULE__."/User/userInfo/id/".$_SESSION[C('USER_AUTH_KEY')]['id'].".html'</script>";
//        $this->display();
    }
    //退出登录
    public function login_out(){
        $_SESSION[C('USER_AUTH_KEY')]=null;
        echo "<script>alert('已成功退出！');window.location.href='".__MODULE__."/Index/login.html';</script>";
    }


}