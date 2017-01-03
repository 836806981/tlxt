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
        $this->login_test();
        if(in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(24,3,5))) {
            $data['1_name'] = M('employee')->where('status=1 and permission=3')->select();// 渠道统计
            $count_1_name = count($data['1_name']);
            $data['count_1_name'] = $count_1_name;
        }

        if(in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(24,2,5))) {
            $data['2_name'] = M('employee')->where('status=1 and permission=2')->select();// 顾问统计
            $count_2_name = count($data['2_name']);
            $data['count_2_name'] = $count_2_name;
        }
//渠道派单统计1
        if(in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(24,3))) {
            //根据渠道获取信息
            foreach( $data['1_name'] as $k=>$v){
                $data['1_'.($k+1).'_1'] = M('order')->where('sales_id='.$v['id'].' and status>3 and  status<23')->count();//累积
                $data['1_'.($k+1).'_2'] = M('order')->where('sales_id='.$v['id'].' and status>3 and  status<23 and  status_4 >="' . strtotime(date('Y-m')) . '"')->count();//月
                $data['1_'.($k+1).'_3'] = M('order')->where('sales_id='.$v['id'].' and status>3 and  status<23 and  status_4 >="' . strtotime(date('Y-m-d')) . '"')->count();//当日
            }
            //统计
            $data['1_'.($count_1_name+1).'_1'] = M('order')->where('order_type >1 and status>3 and  status<23')->count();//累积
            $data['1_'.($count_1_name+1).'_2'] = M('order')->where('order_type >1 and status>3 and  status<23 and  status_4 >="' . strtotime(date('Y-m')) . '"')->count();//月
            $data['1_'.($count_1_name+1).'_3'] = M('order')->where('order_type >1 and status>3 and  status<23 and  status_4 >="' . strtotime(date('Y-m-d')) . '"')->count();//当日

            $data['1_0'] = M('order')->where('order_type >1 and  status<23 and  status_4 >="' . strtotime(date('Y-m-d')) . '"')->count();// 今日总共派出
        }

//客服派单统计2
        if(in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(24,1))) {
            //月嫂
            $data['2_1_1'] = M('order')->where('order_type = 1 and status<23 and product!="育儿嫂" and product!="3980特价" ')->count();//累积
            $data['2_1_2'] = M('order')->where('order_type = 1 and status<23 and product!="育儿嫂" and product!="3980特价"  and  status_1 >="' . strtotime(date('Y-m')) . '"')->count();//月
            $data['2_1_3'] = M('order')->where('order_type = 1 and status<23 and product!="育儿嫂" and product!="3980特价"  and  status_1 >="' . strtotime(date('Y-m-d')) . '"')->count();//当日
            //育儿嫂
            $data['2_2_1'] = M('order')->where('order_type = 1 and status<23 and product="育儿嫂" ')->count();//累积
            $data['2_2_2'] = M('order')->where('order_type = 1 and status<23 and product="育儿嫂"  and  status_1 >="' . strtotime(date('Y-m')) . '"')->count();//月
            $data['2_2_3'] = M('order')->where('order_type = 1 and status<23 and product="育儿嫂"  and  status_1 >="' . strtotime(date('Y-m-d')) . '"')->count();//当日
            //3980
            $data['2_3_1'] = M('order')->where('order_type = 1 and status<23  and product="3980特价"')->count();//累积
            $data['2_3_2'] = M('order')->where('order_type = 1 and status<23 and product="3980特价"  and  status_1 >="' . strtotime(date('Y-m')) . '"')->count();//月
            $data['2_3_3'] = M('order')->where('order_type = 1 and status<23 and product="3980特价"  and  status_1 >="' . strtotime(date('Y-m-d')) . '"')->count();//当日
            //统计
            $data['2_4_1'] = M('order')->where('order_type = 1 and status<23')->count();//累积
            $data['2_4_2'] = M('order')->where('order_type = 1 and status<23 and  status_1 >="' . strtotime(date('Y-m')) . '"')->count();//月
            $data['2_4_3'] = M('order')->where('order_type = 1 and status<23 and  status_1 >="' . strtotime(date('Y-m-d')) . '"')->count();//当日

            $data['2_0'] = M('order')->where('order_type = 1 and  status<23 and  status_1 >="' . strtotime(date('Y-m-d')) . '"')->count();//今日派出客户单
        }
//渠道订单匹配3
        if(in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(24,3,5))) {
            //根据渠道获取信息
            $data['3_name'] = $data['1_name'];// 今日总共派出
            foreach( $data['3_name'] as $k=>$v){
                $data['3_'.($k+1).'_1'] = M('order')->where('sales_id='.$v['id'].' and status>4 and status<23')->count();//累积
                $data['3_'.($k+1).'_2'] = M('order')->where('sales_id='.$v['id'].' and status>4 and status<23 and  status_5 >="' . strtotime(date('Y-m')) . '"')->count();//月
                $data['3_'.($k+1).'_3'] = M('order')->where('sales_id='.$v['id'].' and status>4 and status<23 and  status_5 >="' . strtotime(date('Y-m-d')) . '"')->count();//当日
            }
            //统计
            $data['3_'.($count_1_name+1).'_1'] = M('order')->where('order_type >1 and status>4 and status<23')->count();//累积
            $data['3_'.($count_1_name+1).'_2'] = M('order')->where('order_type >1 and status>4 and status<23 and  status_5 >="' . strtotime(date('Y-m')) . '"')->count();//月
            $data['3_'.($count_1_name+1).'_3'] = M('order')->where('order_type >1 and status>4 and status<23 and  status_5 >="' . strtotime(date('Y-m-d')) . '"')->count();//当日

            $data['3_0'] = M('order')->where('order_type >1 and  status<23 and  status_5 >="' . strtotime(date('Y-m-d')) . '"')->count();// 今日总共匹配
        }
//渠道签单4
        if(in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(24,3))) {
            //根据渠道获取信息
            $data['4_name'] = $data['1_name'];// 今日总共派出
            foreach( $data['4_name'] as $k=>$v){
                $data['4_'.($k+1).'_1'] = M('order')->where('sales_id='.$v['id'].' and status>5 and status<23')->count();//累积
                $data['4_'.($k+1).'_2'] = M('order')->where('sales_id='.$v['id'].' and status>5 and status<23 and  status_6 >="' . strtotime(date('Y-m')) . '"')->count();//月
                $data['4_'.($k+1).'_3'] = M('order')->where('sales_id='.$v['id'].' and status>5 and status<23 and  status_6 >="' . strtotime(date('Y-m-d')) . '"')->count();//当日
            }
            //统计
            $data['4_'.($count_1_name+1).'_1'] = M('order')->where('order_type >1 and status>5 and status<23')->count();//累积
            $data['4_'.($count_1_name+1).'_2'] = M('order')->where('order_type >1 and status>5 and status<23 and  status_6 >="' . strtotime(date('Y-m')) . '"')->count();//月
            $data['4_'.($count_1_name+1).'_3'] = M('order')->where('order_type >1 and status>5 and status<23 and  status_6 >="' . strtotime(date('Y-m-d')) . '"')->count();//当日

            $data['4_0'] = M('order')->where('order_type >1 and  status<23 and  status_6 >="' . strtotime(date('Y-m-d')) . '"')->count();// 今日总共签单
        }

//渠道订单状态5
        if(in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(24,3))) {
            //根据渠道获取信息
            $data['5_name'] = $data['1_name'];// 今日总共派出
            foreach( $data['5_name'] as $k=>$v){
                $data['5_'.($k+1).'_1'] = M('order')->where('sales_id='.$v['id'].' and status=6 and status<23')->count();//未上户
                $data['5_'.($k+1).'_2'] = M('order')->where('sales_id='.$v['id'].' and status=7 and status<23')->count();//已上户
                $data['5_'.($k+1).'_3'] = M('order')->where('sales_id='.$v['id'].' and status>7 and status<23')->count();//已下户
            }
            //统计
            $data['5_'.($count_1_name+1).'_1'] = M('order')->where('order_type >1 and status=6 and status<23')->count();//未上户
            $data['5_'.($count_1_name+1).'_2'] = M('order')->where('order_type >1 and status=7 and status<23')->count();//已上户
            $data['5_'.($count_1_name+1).'_3'] = M('order')->where('order_type >1 and status>7 and status<23')->count();//已下户

            $data['5_0'] = M('order')->where('order_type >1 and  status<23 and  status_7 >="' . strtotime(date('Y-m-d')) . '"')->count();// 今日总共上户
        }

//顾问接单统计6
        if(in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(24,2))) {
            //根据顾问统计
            $data['6_name'] = $data['2_name'];
            foreach( $data['6_name'] as $k=>$v){
                //月嫂 包括3980
                $data['6_'.($k+1).'_1'] = M('order')->where('sales_id='.$v['id'].' and status>2 and status<23 and product!="育儿嫂"')->count();//累积
                $data['6_'.($k+1).'_2'] = M('order')->where('sales_id='.$v['id'].' and status>2 and status<23 and product!="育儿嫂" and status_2 >="' . strtotime(date('Y-m')) . '"')->count();//当月
                $data['6_'.($k+1).'_3'] = M('order')->where('sales_id='.$v['id'].' and status>2 and status<23 and product!="育儿嫂" and status_2 >="' . strtotime(date('Y-m-d')) . '"')->count();//当日
                //育儿嫂
                $data['6_'.($k+1).'_4'] = M('order')->where('sales_id='.$v['id'].' and status>2 and status<23 and product="育儿嫂"')->count();//累积
                $data['6_'.($k+1).'_5'] = M('order')->where('sales_id='.$v['id'].' and status>2 and status<23 and product="育儿嫂" and status_2 >="' . strtotime(date('Y-m')) . '"')->count();//当月
                $data['6_'.($k+1).'_6'] = M('order')->where('sales_id='.$v['id'].' and status>2 and status<23 and product="育儿嫂" and status_2 >="' . strtotime(date('Y-m-d')) . '"')->count();//当日
            }
            //统计月嫂 包括3980
            $data['6_'.($count_2_name+1).'_1'] = M('order')->where('order_type =1 and status>2 and status<23 and product!="育儿嫂"')->count();//累积
            $data['6_'.($count_2_name+1).'_2'] = M('order')->where('order_type =1 and status>2 and status<23 and product!="育儿嫂" and status_2 >="' . strtotime(date('Y-m')) . '"')->count();//当月
            $data['6_'.($count_2_name+1).'_3'] = M('order')->where('order_type =1 and status>2 and status<23 and product!="育儿嫂" and status_2 >="' . strtotime(date('Y-m-d')) . '"')->count();//当日

            //统计育儿嫂
            $data['6_'.($count_2_name+1).'_4'] = M('order')->where('order_type =1 and status>2 and status<23 and product="育儿嫂"')->count();//累积
            $data['6_'.($count_2_name+1).'_5'] = M('order')->where('order_type =1 and status>2 and status<23 and product="育儿嫂" and status_2 >="' . strtotime(date('Y-m')) . '"')->count();//当月
            $data['6_'.($count_2_name+1).'_6'] = M('order')->where('order_type =1 and status>2 and status<23 and product="育儿嫂" and status_2 >="' . strtotime(date('Y-m-d')) . '"')->count();//当日

            $data['6_0'] = M('order')->where('order_type =1 and status>2 and status<23 and status_2 >="' . strtotime(date('Y-m-d')) . '"')->count();// 今日总共接单
        }
//顾问匹配订单统计7
        if(in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(24,2,5))) {
            //根据顾问统计
            $data['7_name'] = $data['2_name'];
            foreach( $data['7_name'] as $k=>$v){
                //月嫂 包括3980
                $data['7_'.($k+1).'_1'] = M('order')->where('sales_id='.$v['id'].' and status>4 and status<23 and product!="育儿嫂"')->count();//累积
                $data['7_'.($k+1).'_2'] = M('order')->where('sales_id='.$v['id'].' and status>4 and status<23 and product!="育儿嫂" and status_5 >="' . strtotime(date('Y-m')) . '"')->count();//当月
                $data['7_'.($k+1).'_3'] = M('order')->where('sales_id='.$v['id'].' and status>4 and status<23 and product!="育儿嫂" and status_5 >="' . strtotime(date('Y-m-d')) . '"')->count();//当日
                //育儿嫂
                $data['7_'.($k+1).'_4'] = M('order')->where('sales_id='.$v['id'].' and status>4 and status<23 and product="育儿嫂"')->count();//累积
                $data['7_'.($k+1).'_5'] = M('order')->where('sales_id='.$v['id'].' and status>4 and status<23 and product="育儿嫂" and status_5 >="' . strtotime(date('Y-m')) . '"')->count();//当月
                $data['7_'.($k+1).'_6'] = M('order')->where('sales_id='.$v['id'].' and status>4 and status<23 and product="育儿嫂" and status_5 >="' . strtotime(date('Y-m-d')) . '"')->count();//当日
            }
            //统计月嫂 包括3980
            $data['7_'.($count_2_name+1).'_1'] = M('order')->where('order_type =1 and status>4 and status<23 and product!="育儿嫂"')->count();//累积
            $data['7_'.($count_2_name+1).'_2'] = M('order')->where('order_type =1 and status>4 and status<23 and product!="育儿嫂" and status_5 >="' . strtotime(date('Y-m')) . '"')->count();//当月
            $data['7_'.($count_2_name+1).'_3'] = M('order')->where('order_type =1 and status>4 and status<23 and product!="育儿嫂" and status_5 >="' . strtotime(date('Y-m-d')) . '"')->count();//当日

            //统计育儿嫂
            $data['7_'.($count_2_name+1).'_4'] = M('order')->where('order_type =1 and status>4 and status<23 and product="育儿嫂"')->count();//累积
            $data['7_'.($count_2_name+1).'_5'] = M('order')->where('order_type =1 and status>4 and status<23 and product="育儿嫂" and status_5 >="' . strtotime(date('Y-m')) . '"')->count();//当月
            $data['7_'.($count_2_name+1).'_6'] = M('order')->where('order_type =1 and status>4 and status<23 and product="育儿嫂" and status_5 >="' . strtotime(date('Y-m-d')) . '"')->count();//当日

            $data['7_0'] = M('order')->where('order_type =1 and status>2 and status<23 and status_5 >="' . strtotime(date('Y-m-d')) . '"')->count();// 今日总共匹配
        }
//顾问签单统计8
        if(in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(24,2))) {
            //根据顾问统计
            $data['8_name'] = $data['2_name'];
            foreach( $data['8_name'] as $k=>$v){
                //月嫂 包括3980
                $data['8_'.($k+1).'_1'] = M('order')->where('sales_id='.$v['id'].' and status>5 and status<23 and product!="育儿嫂"')->count();//累积
                $data['8_'.($k+1).'_2'] = M('order')->where('sales_id='.$v['id'].' and status>5 and status<23 and product!="育儿嫂" and status_6 >="' . strtotime(date('Y-m')) . '"')->count();//当月
                $data['8_'.($k+1).'_3'] = M('order')->where('sales_id='.$v['id'].' and status>5 and status<23 and product!="育儿嫂" and status_6 >="' . strtotime(date('Y-m-d')) . '"')->count();//当日
                //育儿嫂
                $data['8_'.($k+1).'_4'] = M('order')->where('sales_id='.$v['id'].' and status>5 and status<23 and product="育儿嫂"')->count();//累积
                $data['8_'.($k+1).'_5'] = M('order')->where('sales_id='.$v['id'].' and status>5 and status<23 and product="育儿嫂" and status_6 >="' . strtotime(date('Y-m')) . '"')->count();//当月
                $data['8_'.($k+1).'_6'] = M('order')->where('sales_id='.$v['id'].' and status>5 and status<23 and product="育儿嫂" and status_6 >="' . strtotime(date('Y-m-d')) . '"')->count();//当日
            }
            //统计月嫂 包括3980
            $data['8_'.($count_2_name+1).'_1'] = M('order')->where('order_type =1 and status>5 and status<23 and product!="育儿嫂"')->count();//累积
            $data['8_'.($count_2_name+1).'_2'] = M('order')->where('order_type =1 and status>5 and status<23 and product!="育儿嫂" and status_6 >="' . strtotime(date('Y-m')) . '"')->count();//当月
            $data['8_'.($count_2_name+1).'_3'] = M('order')->where('order_type =1 and status>5 and status<23 and product!="育儿嫂" and status_6 >="' . strtotime(date('Y-m-d')) . '"')->count();//当日

            //统计育儿嫂
            $data['8_'.($count_2_name+1).'_4'] = M('order')->where('order_type =1 and status>5 and status<23 and product="育儿嫂"')->count();//累积
            $data['8_'.($count_2_name+1).'_5'] = M('order')->where('order_type =1 and status>5 and status<23 and product="育儿嫂" and status_6 >="' . strtotime(date('Y-m')) . '"')->count();//当月
            $data['8_'.($count_2_name+1).'_6'] = M('order')->where('order_type =1 and status>5 and status<23 and product="育儿嫂" and status_6 >="' . strtotime(date('Y-m-d')) . '"')->count();//当日

            $data['8_0'] = M('order')->where('order_type =1 and status>2 and status<23 and status_6 >="' . strtotime(date('Y-m-d')) . '"')->count();// 今日总共签单
        }
//需要催款统计9
        if(in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(24,2,5))) {
            //根据顾问统计
            $data['9_name'] = $data['2_name'];
            foreach( $data['8_name'] as $k=>$v){
                $data['9_'.($k+1).'_1'] = M('order')->where('sales_id='.$v['id'].' and is_press=1 and status<23 and product!="育儿嫂"')->count();//月嫂
                $data['9_'.($k+1).'_2'] = M('order')->where('sales_id='.$v['id'].' and is_press=1 and status<23 and product="育儿嫂"')->count();//育儿嫂
            }
            $data['9_'.($count_2_name+1).'_1'] = M('order')->where('order_type =1 and is_press=1 and status<23 and product!="育儿嫂"')->count();//月嫂
            $data['9_'.($count_2_name+1).'_2'] = M('order')->where('order_type =1 and is_press=1 and status<23 and product="育儿嫂"')->count();//育儿嫂

            $data['9_0'] = M('order')->where('order_type =1 and is_press=1 and status<23')->count();// 今日总共签单
        }
//需要催款金额10
        if(in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(24,2,5))) {
            //月嫂
            $data['10_1_1'] = M('order')->where('order_type=1 and is_press=1 and status<23 and product!="育儿嫂"')->count();//人数
            $sum_ys = M('order')->field('sum(price_come_true) as come_true,sum(price_come) as come')->where('order_type=1 and is_press=1 and status<23 and product!="育儿嫂"')->find();//金额
            $data['10_1_2'] = $sum_ys['come'] - $sum_ys ['come_true'];

            //育儿嫂
            $data['10_2_1'] = M('order')->where('order_type=1 and is_press=1 and status<23 and product="育儿嫂"')->count();//人数
            $sum_yes = M('order')->field('sum(price_come_true) as come_true,sum(price_come) as come')->where('order_type=1 and is_press=1 and status<23 and product="育儿嫂"')->find();//金额
            $data['10_2_2'] = $sum_yes['come'] - $sum_yes ['come_true'];

            //统计
            $data['10_3_1'] = M('order')->where('order_type=1 and is_press=1 and status<23')->count();//人数
            $sum_all = M('order')->field('sum(price_come_true) as come_true,sum(price_come) as come')->where('order_type=1 and is_press=1 and status<23')->find();//金额
            $data['10_3_2'] = $sum_all['come'] - $sum_all ['come_true'];

            $data['10_0'] = $data['10_3_2'];// 今日总共签单
        }
//学员统计11
        if(in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(24,5))) {
            //渠道
            $data['11_1_1'] = M('nurse')->where('status!=24 and come="渠道" and is_student=1')->count();//累积
            $data['11_1_2'] = M('nurse')->where('status!=24 and come="渠道" and is_student=1 and add_time >="' . strtotime(date('Y-m')) . '"')->count();//当月
            $data['11_1_3'] = M('nurse')->where('status!=24 and come="渠道" and is_student=1 and add_time >="' . strtotime(date('Y-m-d')) . '"')->count();//当日

            //自费
            $data['11_2_1'] = M('nurse')->where('status!=24 and come="自费" and is_student=1')->count();//累积
            $data['11_2_2'] = M('nurse')->where('status!=24 and come="自费" and is_student=1 and add_time >="' . strtotime(date('Y-m')) . '"')->count();//当月
            $data['11_2_3'] = M('nurse')->where('status!=24 and come="自费" and is_student=1 and add_time >="' . strtotime(date('Y-m-d')) . '"')->count();//当日


            //统计
            $data['11_3_1'] = M('nurse')->where('status!=24 and is_student=1')->count();//累积
            $data['11_3_2'] = M('nurse')->where('status!=24 and is_student=1 and add_time >="' . strtotime(date('Y-m')) . '"')->count();//当月
            $data['11_3_3'] = M('nurse')->where('status!=24 and is_student=1 and add_time >="' . strtotime(date('Y-m-d')) . '"')->count();//当日

            $data['11_0'] = M('nurse')->where('status!=24 and is_student=1')->count();// 今日总共签单
        }
//学员转阿姨统计12
        if(in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(24,5))) {
            //渠道
            $data['12_1_1'] = M('nurse')->where('type=2 and status!=24 and come="渠道" and is_student=1')->count();//累积
            $data['12_1_2'] = M('nurse')->where('type=2 and status!=24 and come="渠道" and is_student=1 and add_time >="' . strtotime(date('Y-m')) . '"')->count();//当月
            $data['12_1_3'] = M('nurse')->where('type=2 and status!=24 and come="渠道" and is_student=1 and add_time >="' . strtotime(date('Y-m-d')) . '"')->count();//当日

            //自费
            $data['12_2_1'] = M('nurse')->where('type=2 and status!=24 and come="自费" and is_student=1')->count();//累积
            $data['12_2_2'] = M('nurse')->where('type=2 and status!=24 and come="自费" and is_student=1 and add_time >="' . strtotime(date('Y-m')) . '"')->count();//当月
            $data['12_2_3'] = M('nurse')->where('type=2 and status!=24 and come="自费" and is_student=1 and add_time >="' . strtotime(date('Y-m-d')) . '"')->count();//当日


            //统计
            $data['12_3_1'] = M('nurse')->where('type=2 and status!=24 and is_student=1')->count();//累积
            $data['12_3_2'] = M('nurse')->where('type=2 and status!=24 and is_student=1 and add_time >="' . strtotime(date('Y-m')) . '"')->count();//当月
            $data['12_3_3'] = M('nurse')->where('type=2 and status!=24 and is_student=1 and add_time >="' . strtotime(date('Y-m-d')) . '"')->count();//当日

            $data['12_0'] = M('nurse')->where('type=2 and status!=24 and is_student=1')->count();// 今日总共签单
        }
//阿姨统计13
        if(in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(24,5))) {
            $data['13_1_1'] = M('nurse')->where('type=2 and status!=24 and agreement_type=3')->count();//3单试用
            $data['13_1_2'] = M('nurse')->where('type=2 and status!=24 and agreement_type=1')->count();//一年合同

            $data['13_0'] = M('nurse')->where('type=2 and status!=24')->count();// 总共阿姨
        }
//顾问单统计14
        if(in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(24,2))) {
            //根据顾问统计
            $data['14_name'] = $data['2_name'];
            foreach( $data['14_name'] as $k=>$v){
                $data['14_'.($k+1).'_1'] = M('order')->where('order_type=1 and sales_id='.$v['id'].' and status<23 and product!="育儿嫂"')->count();//月嫂
                $data['14_'.($k+1).'_2'] = M('order')->where('order_type=1 and sales_id='.$v['id'].' and status<23 and product="育儿嫂"')->count();//月嫂
            }
            //统计月嫂 包括3980
            $data['14_'.($count_2_name+1).'_1'] = M('order')->where('order_type=1 and status<23 and product!="育儿嫂"')->count();//月嫂
            $data['14_'.($count_2_name+1).'_2'] = M('order')->where('order_type=1 and status<23 and product="育儿嫂"')->count();//月嫂

            $data['14_0'] = M('order')->where('order_type=1 and status<23')->count();//月嫂;// 订单总数
        }

//渠道单15
        if(in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(24,3))) {
            //根据渠道获取信息
            $data['15_name'] = $data['1_name'];
            foreach( $data['15_name'] as $k=>$v){
                $data['15_1_'.($k+1).''] = M('order')->where('order_type>1 and sales_id='.$v['id'].' and status<23')->count();//每个渠道统计
            }
            //统计
            $data['15_0'] = M('order')->where('order_type>1 and status<23')->count();// 今日总共渠道
        }
        $this->assign('data',$data);
//        echo "<script>window.location.href='".__MODULE__."/User/userInfo/id/".$_SESSION[C('USER_AUTH_KEY')]['id'].".html'</script>";
        $this->display();
    }
    //退出登录
    public function login_out(){
        $_SESSION[C('USER_AUTH_KEY')] = null;
        echo "<script>alert('已成功退出！');window.location.href='".__MODULE__."/Index/login.html';</script>";
    }


}