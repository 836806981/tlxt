<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends CommonController {
    public function  login_test(){
        if(!$_SESSION[C('USER_AUTH_KEY')]['id']){
            echo "<script>alert('请登录！');window.location.href='" . __MODULE__ . "/Admin/login.html';</script>";
        }
    }


    public function jianli(){
        if(!I('get.number')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('nurse')->where('number="'.I('get.number').'"')->find();
        if(!$info){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if($info['status']!=1){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if($info['type']!=2){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }


        $info['imgs_a'] = explode(',',$info['imgs']);
        $info['skills_a'] = explode(',',$info['skills']);
        $info['family_1_a'] = explode('||',$info['family_1']);
        $info['family_2_a'] = explode('||',$info['family_2']);
        $info['family_3_a'] = explode('||',$info['family_3']);
        $info['good_cuisine_a'] = explode(',',$info['good_cuisine']);
        $info['good_flavor_a'] = explode(',',$info['good_flavor']);
        $info['experience_own1_a'] = explode('||',$info['experience_own1']);
        $info['experience_own2_a'] = explode('||',$info['experience_own2']);
        $info['experience_own3_a'] = explode('||',$info['experience_own3']);

        $status_sh_name = ['','未上户','已上户'];
        $info['status_sh_name'] = $status_sh_name[$info['status_sh']];

        $info['level_name'] = $info['level'].'育婴师'.'、';
        $info['is_tr']==1?($info['level_name'].='通乳师、'):'';
        $info['is_tn']==1?($info['level_name'].='小儿推拿师、'):'';
        $info['is_yy']==1?($info['level_name'].='公共营养师、'):'';
        $info['is_xl']==1?($info['level_name'].='心理咨询师、'):'';
        $info['level_name'] = substr($info['level_name'],0,-3);

        $info['is_stay_name']= ($info['is_stay']==1?'是':'否');

        $character_type_name = ['','活泼型','外向型','踏实型','敏感型','服从型','均衡型'];
        $info['character_type_name'] = $character_type_name[$info['character_type']];

        $good_cuisine_name = ['面食','煲汤','川菜小炒','流食','素食','肉类','小吃','甜品','补品'];
        foreach($info['good_cuisine_a'] as $k=>$v){
            $info['good_cuisine_str'] .= ($v==1?$good_cuisine_name[$k].' ':'');
        }

        $good_flavor_name = ['清淡','咸鲜','甜食','辣','酸'];
        foreach($info['good_flavor_a'] as $k=>$v){
            $info['good_flavor_str'] .= ($v==1?$good_flavor_name[$k].' ':'');
        }

        $level_name = ['','小田螺','小田螺','小田螺','小田螺','小田螺','大田螺','大田螺','大田螺','大田螺','大田螺','超级田螺','超级田螺','超级田螺','超级田螺','超级田螺','金牌田螺'];
        $info['price_level_name'] = $level_name[$info['price_level']];

        $status_7 = M('order_nurse')->where('nurse_id='.$info['id'].' and status=7')->find();



        $this->assign('status_7',$status_7);
        $this->assign('info',$info);
        $this->display();

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
                    $permission_name[24] = '系统管理员';
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
            }else{
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
        $_SESSION[C('USER_AUTH_KEY')] = null;
        echo "<script>alert('已成功退出！');window.location.href='".__MODULE__."/Index/login.html';</script>";
    }


}