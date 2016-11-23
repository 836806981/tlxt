<?php
//
//护理人员管理去控制器
//

namespace Home\Controller;
use Think\Controller;
class NurseController extends CommonController {
    public function  login_test(){
        if(!$_SESSION[C('USER_AUTH_KEY')]['id']){
            echo "<script>alert('请登录！');window.location.href='" . __MODULE__ . "/Admin/login.html';</script>";
            exit;
            //str_pad($str,6,0,STR_PAD_LEFT);  6位数不足补0
        }
    }
    //列表页
    public function nurseList(){

        $this->authority(array(11,2,3,4));

        $this->display();
    }

    //获取列表数据
    public function getNurseList(){
        $currentpage = I("post.currentpage");
        $pagenum = I("post.pagenum");
        $start = ($currentpage - 1) * $pagenum;
        $post = I("post.");
        $where = 'status = 1 ';
        if($post['name']&&$post['name']!=''){
            $where .= ' and  name LIKE "%'.$post['name'].'%"';
        }
        if($post['age1']){
            $where .= ' and age >='.$post['age1'].'';
        }
        if($post['age2']){
            $where .= ' and age <='.$post['age2'].'';
        }

        if($post['other']){
            $where .= ' and  other LIKE "%'.$post['other'].'%" ';

        }
        if($post['status_sh']){
            $where .= ' and status_sh <= '.$post['status_sh'].'';
        }
        if($post['level_name']){
            $where .= ' and level = "'.$post['level_name'].'"';
        }


        $list = M('nurse')->where($where)->limit($start,$pagenum)->order('add_time desc')->select();

        $status_sh_name = ['','已上户','未上户'];
        foreach($list as $k=>$v){
            $list[$k]['status_sh_name'] = $status_sh_name[$v['status_sh']];
        }

        $count = M('nurse')->where($where)->count();

        $back['data']['list'] = $list;
        $back['data']['num'] = $count;
        $back['code'] = 1000;
        echo json_encode($back);
    }


    //添加
    public function addNurseInfo(){

        $this->authority(array(11,2,3));
        if(I('post.')){
            $post = I('post.');
            if($_FILES['title_img']['tmp_name']) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/';// 设置附件上传目录// 上传文件
                $info = $upload->uploadOne($_FILES['title_img']);
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                        $post['title_img']  = $info['savepath'] . $info['savename'];
                }
            }
            if($_FILES['id_card_img']['tmp_name'][0]) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/';// 设置附件上传目录// 上传文件
                $info = $upload->upload(array($_FILES['id_card_img']));
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    foreach($info as $v){
                        $post['id_card_img']  .=  $v['savepath'] . $v['savename'].',';
                    }
                    $post['id_card_img'] = substr($post['id_card_img'],0,-1);
                }
            }
            if($_FILES['certificate_img']['tmp_name'][0]) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/';// 设置附件上传目录// 上传文件
                $info = $upload->upload(array($_FILES['certificate_img']));
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    foreach($info as $v){
                        $post['certificate_img']  .=   $v['savepath'] . $v['savename'].',';
                    }
                    $post['certificate_img'] = substr($post['certificate_img'],0,-1);
                }
            }
            if($_FILES['test_img']['tmp_name'][0]) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/';// 设置附件上传目录// 上传文件
                $info = $upload->upload(array($_FILES['test_img']));
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    foreach($info as $v){
                        $post['test_img']  .=   $v['savepath'] . $v['savename'].',';
                    }
                    $post['test_img'] = substr($post['test_img'],0,-1);
                }
            }

            $post['other'] = $post['other1'].','.$post['other2'].','.$post['other3'].','.$post['other4'];
            for($i=1;$i<12;$i++){
                @$post['skill'] .= @$post['skill'.$i].',';
            }
            $post['add_time'] = time();
            $level_name = ['','小田螺1.1','小田螺1.2','小田螺1.3','小田螺1.4','小田螺1.5','大田螺1.1','大田螺1.2','大田螺1.3','大田螺1.4','大田螺1.5','超级田螺1.1','超级田螺1.2','超级田螺1.3','超级田螺1.4','超级田螺1.5','金牌田螺'];
            $post['level_name'] = $level_name[$post['level']];
            $post['is_student'] = '否';

            $nurse_id = M('nurse')->add($post);
            if($nurse_id) {
                $save['number'] = 'TLAY-' . str_pad($nurse_id, 6, 0, STR_PAD_LEFT);  //6位数不足补0
                $save_mod = M('nurse')->where('id=' . $nurse_id . '')->save($save);
                if ($save_mod === false) {
                    M('nurse')->where('id=' . $nurse_id . '')->save($save);
                }
                echo "<script>alert('添加成功'); window.location.href='".__MODULE__."/Nurse/nurseList'</script>";
                exit;
            }else{
                echo "<script>alert('添加失败');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }

        }else{
            $this->display();
        }
    }


    //修改
    public function changeNurseInfo(){

        $this->authority(array(11,2,3));
        if(I('post.')){
            $post = I('post.');
            $nurse_info = M('nurse')->field('id,id_card_img,certificate_img,test_img')->where('id='.$post['id'].'')->find();
            $post['id_card_img'] = $nurse_info['id_card_img'];
            $post['certificate_img'] = $nurse_info['certificate_img'];
            $post['test_img'] = $nurse_info['test_img'];
//            echo $post['id_card_img'].'<br/>'. $post['certificate_img'].'<br/>' .$post['test_img'].'<br/>';
            //处理删除的图片
            if($post['id_card_img_del']){
                $id_card_img_del_arr = explode(',',substr($post['id_card_img_del'],0,-1));
                foreach($id_card_img_del_arr as $v){
                    $post['id_card_img'] = str_replace($v.',','',$post['id_card_img']);
                    $post['id_card_img'] = str_replace(','.$v,'', $post['id_card_img']);
                    $post['id_card_img'] = str_replace($v,'', $post['id_card_img']);
                    if(strstr($v,'student')===false){
                        $unlink[] = $v;
                    }
                }
                $post['id_card_img']? $post['id_card_img'].=',':'';
            }
            //处理删除的图片
            if($post['certificate_img_del']){
                $id_card_img_del_arr = explode(',',substr($post['certificate_img_del'],0,-1));
                foreach($id_card_img_del_arr as $v){
                    $post['certificate_img'] = str_replace($v.',','',$post['certificate_img']);
                    $post['certificate_img'] = str_replace(','.$v,'', $post['certificate_img']);
                    $post['certificate_img'] = str_replace($v,'', $post['certificate_img']);
                    if(strstr($v,'student')===false){
                        $unlink[] = $v;
                    }
                }
                $post['certificate_img']? $post['certificate_img'].=',':'';
            }
            //处理删除的图片
            if($post['test_img_del']){
                $id_card_img_del_arr = explode(',',substr($post['test_img_del'],0,-1));
                foreach($id_card_img_del_arr as $v){
                    $post['test_img'] = str_replace($v.',','',$post['test_img']);
                    $post['test_img'] = str_replace(','.$v,'', $post['test_img']);
                    $post['test_img'] = str_replace($v,'', $post['test_img']);
                    if(strstr($v,'student')===false){
                        $unlink[] = $v;
                    }
                }
                $post['test_img']? $post['test_img'].=',':'';
            }
//            echo $post['id_card_img'].'<br/>'. $post['certificate_img'].'<br/>' .$post['test_img'].'<br/>';
//            print_r($unlink);die;
            unset($post['title_img']);
            if($_FILES['title_img']['tmp_name']) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/';// 设置附件上传目录// 上传文件
                $info = $upload->uploadOne($_FILES['title_img']);
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    $post['title_img']  = $info['savepath'] . $info['savename'];
                    if(strstr($post['old_title_img'],'student')===false){
                        $unlink[] = $post['old_title_img'];
                    }
                }
            }
            if($_FILES['id_card_img']['tmp_name'][0]) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/';// 设置附件上传目录// 上传文件
                $info = $upload->upload(array($_FILES['id_card_img']));
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    foreach($info as $v){
                        $post['id_card_img']  .=  $v['savepath'] . $v['savename'].',';
                    }
                    $post['id_card_img'] = substr($post['id_card_img'],0,-1);
                }
            }
            if($_FILES['certificate_img']['tmp_name'][0]) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/';// 设置附件上传目录// 上传文件
                $info = $upload->upload(array($_FILES['certificate_img']));
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    foreach($info as $v){
                        $post['certificate_img']  .=   $v['savepath'] . $v['savename'].',';
                    }
                    $post['certificate_img'] = substr($post['certificate_img'],0,-1);
                }
            }
            if($_FILES['test_img']['tmp_name'][0]) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/';// 设置附件上传目录// 上传文件
                $info = $upload->upload(array($_FILES['test_img']));
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    foreach($info as $v){
                        $post['test_img']  .=   $v['savepath'] . $v['savename'].',';
                    }
                    $post['test_img'] = substr($post['test_img'],0,-1);
                }
            }
            $post['other'] = $post['other1'].','.$post['other2'].','.$post['other3'].','.$post['other4'];
            for($i=1;$i<12;$i++){
                @$post['skill'] .= @$post['skill'.$i].',';
            }

            $level_name = ['','小田螺1.1','小田螺1.2','小田螺1.3','小田螺1.4','小田螺1.5','大田螺1.1','大田螺1.2','大田螺1.3','大田螺1.4','大田螺1.5','超级田螺1.1','超级田螺1.2','超级田螺1.3','超级田螺1.4','超级田螺1.5','金牌田螺'];
            $post['level_name'] = $level_name[$post['level']];

//            $post['add_time'] = time();
            $nurse_id = M('nurse')->where('id='.$post['id'].'')->save($post);
            if($nurse_id!==false) {
                foreach($unlink as $v){
                    unlink('Uploads/'.$v);
                }
                echo "<script>alert('修改成功'); window.location.href='".__MODULE__."/Nurse/nurseInfo/id/".$post['id'].".html'</script>";
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
            $info = M('nurse')->where('id='.I('get.id').'')->find();
            if(!$info){
                echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            $info['id_card_img_arr'] = explode(',',$info['id_card_img']);
            $info['certificate_img_arr'] = explode(',',$info['certificate_img']);
            $info['test_img_arr'] = explode(',',$info['test_img']);
            $info['other'] = explode(',',$info['other']);
            $info['skill'] = explode(',',$info['skill']);
//            print_r($info['other']);die;
            $this->assign('info',$info);
            $this->display();
        }

    }


    //详情
    public function nurseInfo(){

        $this->authority(array(11,2,3,4));
        if(!I('get.id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('nurse')->where('id='.I('get.id').'')->find();
        if(!$info){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info['id_card_img_arr'] = explode(',',$info['id_card_img']);
        $info['certificate_img_arr'] = explode(',',$info['certificate_img']);
        $info['test_img_arr'] = explode(',',$info['test_img']);
        $info['other'] = explode(',',$info['other']);
        $info['skill'] = explode(',',$info['skill']);

        $status_sh_name = ['','已上户','已下户'];
        $info['status_sh_name'] = $status_sh_name[$info['status_sh']];
        $level_name = ['','小田螺1.1','小田螺1.2','小田螺1.3','小田螺1.4','小田螺1.5','大田螺1.1','大田螺1.2','大田螺1.3','大田螺1.4','大田螺1.5','超级田螺1.1','超级田螺1.2','超级田螺1.3','超级田螺1.4','超级田螺1.5','金牌田螺'];
        $info['next_level_name'] = $level_name[$info['level']+1];

        $contact =  M('contact')->where('nurse_id='.I('get.id').'')->select();


        $is_service_name = ['','否','是'];
        $order_nurse = M('order_nurse')->field("IF(true_b_time!='',true_b_time,b_time) as after_time,b_time,true_b_time,IF(true_s_time!='',true_s_time,s_time) as before_time,s_time,true_s_time,order_id,order_name,do_time,is_service")->where('nurse_id='.I('get.id').'')->order("IF(true_s_time!='',true_s_time,s_time) desc")->select();
        foreach($order_nurse as $k=>$v){
            $order_nurse[$k]['is_service_name'] = $is_service_name[$v['is_service']];
            $order_nurse[$k]['b_time_show'] = $v['true_b_time']?$v['true_b_time'].'(实)':$v['b_time'].'(预)';
            $order_nurse[$k]['s_time_show'] = $v['true_s_time']?$v['true_s_time'].'(实)':$v['s_time'].'(预)';
        }

        $order_nurse_after = M('order_nurse')->field("IF(true_b_time!='',true_b_time,b_time) as after_time,b_time,true_b_time,IF(true_s_time!='',true_s_time,s_time) as before_time,s_time,true_s_time,order_id,order_name,do_time,is_service")->where('nurse_id='.I('get.id').'  and IF(true_s_time!="",true_s_time,s_time)  >="'.date('Y-m-d').'"')->order("before_time asc")->select();
        foreach($order_nurse_after as $k=>$v){
            if($v['order_id']){
                $order_nurse_after[$k]['customer_name'] = '<a href="'.__MODULE__.'/OrderShow/OrderInfo/id/'.$v['order_id'].'.html">'.$v['order_name'].'</a>('.$v['do_time'].')';
            }else{
                $order_nurse_after[$k]['customer_name'] = '';
            }
        }

        $this->assign('info',$info);
        $this->assign('contact',$contact);
        $this->assign('order_nurse',$order_nurse);
        $this->assign('order_nurse_after',$order_nurse_after);

        $this->display();
    }

    //升级阿姨
    public function add_nurse_level(){
        if(!I('post.id')){
            echo "<script>alert('数据异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $nurse = M('nurse')->where('id='.I('post.id').'')->find();
        if(!$nurse || $nurse==''){
            echo "<script>alert('数据异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if($nurse['level']>=16){
            echo "<script>alert('已经是金牌阿姨了');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $level_name = ['','小田螺1.1','小田螺1.2','小田螺1.3','小田螺1.4','小田螺1.5','大田螺1.1','大田螺1.2','大田螺1.3','大田螺1.4','大田螺1.5','超级田螺1.1','超级田螺1.2','超级田螺1.3','超级田螺1.4','超级田螺1.5','金牌田螺'];
        $save['level'] = $nurse['level']+1;
        $save['level_add'] = 0;
        $save['level_name'] = $level_name[$nurse['level']+1];
        $save_mod = M('nurse')->where('id='.I('post.id').'')->save($save);
        if($save_mod!==false){
            echo "<script>alert('升级成功');window.location.href='".__MODULE__."/Nurse/nurseInfo/id/".I('post.id').".html';</script>";
            exit;
        }else{
            echo "<script>alert('升级失败请重试');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
    }

    //删除
    public function deleteNurse(){

        $this->authority(array(11,3));
        if(!I('get.id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('nurse')->where('id='.I('get.id').'')->find();
        if(!$info){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $save['status'] = 2;
        $save_mod = M('nurse')->where('id='.I('get.id').'')->save($save);
        if(!$save_mod){
            M('nurse')->where('id='.I('get.id').'')->save($save);
        }
        if(I('get.type')=='list'){
            echo "<script>alert('已删除');window.onload=function(){window.history.go(-1);return false;};</script>";
            exit;
        }else{
            echo "<script>alert('已删除');window.location.href='".__MODULE__."/Nurse/nurseInfo/id/".I('get.id').".html';</script>";
            exit;
        }


    }
    //恢复
    public function backNurse(){

        $this->authority(array(11,3));
        if(!I('get.id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;};</script>";
            exit;
        }
        $info = M('nurse')->where('id='.I('get.id').'')->find();
        if(!$info){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;};</script>";
            exit;
        }
        $save['status'] = 1;
        $save_mod = M('nurse')->where('id='.I('get.id').'')->save($save);
        if(!$save_mod){
            M('nurse')->where('id='.I('get.id').'')->save($save);
        }
        echo "<script>alert('已恢复');window.location.href='".__MODULE__."/Nurse/nurseInfo/id/".I('get.id').".html';</script>";
        exit;

    }

    //动态添加上单
    public function add_order_nurse(){
        $this->authority(array(11,3));
        if(!I('post.nurse_id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;};</script>";
            exit;
        }
        //订单生产后的order_nurse表记录级各种数据处理
        $post = I('post.');

        $where_order_nurse = 'nurse_id='.$post['nurse_id'].' and ( ("'.$post['true_b_time'].'" <= IF(true_s_time!="",true_s_time,s_time) and "'.$post['true_b_time'].'">=IF(true_b_time!="" ,true_b_time , b_time) ) OR ("'.$post['true_s_time'].'" >= IF(true_b_time!="" ,true_b_time , b_time) and "'.$post['true_s_time'].'" <=IF(true_s_time!="",true_s_time,s_time) )  OR("'.$post['true_b_time'].'" <= IF(true_b_time!="" ,true_b_time , b_time) and "'.$post['true_s_time'].'" >= IF(true_s_time!="",true_s_time,s_time) )   )';
        $have_re = M('order_nurse')->field('id')->where($where_order_nurse)->find();

        if($have_re){
            echo "<script>alert('阿姨档期冲突');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }


        $nurse_info = M('nurse')->where('id='.I('post.nurse_id').'')->find();
        $add_order_nurse['nurse_id'] = $post['nurse_id'];
        $add_order_nurse['order_id'] = 0;
//        $add_order_nurse['order_name'] = $order_info['name'];
        $add_order_nurse['nurse_name'] = $nurse_info['name'];
//        $add_order_nurse['order_number'] = $order_info['number'];
        $add_order_nurse['nurse_number'] = $nurse_info['number'];
        $add_order_nurse['nurse_id_card'] = $nurse_info['id_card'];
//        $add_order_nurse['safe_time'] = $post['safe_time'];
//        $add_order_nurse['is_safe'] = 1;
        $add_order_nurse['b_time'] = $post['true_b_time'];
        $add_order_nurse['true_b_time'] = $post['true_b_time'];
        $add_order_nurse['s_time'] = $post['true_s_time'];
        $add_order_nurse['true_s_time'] = $post['true_s_time'];
//        $add_order_nurse['is_normal'] = 1;
//        $add_order_nurse['nurse_pay'] = $post['nurse_pay'];
        $add_order_nurse['add_time'] = time();
        $order_nurse_id = M('order_nurse')->add($add_order_nurse);
        if(!$order_nurse_id){
            M('order_nurse')->add($add_order_nurse);
        }
        echo "<script>alert('已完成！');window.location.href='".__MODULE__."/Nurse/nurseInfo/id/".$post['nurse_id'].".html'</script>";
        exit;



    }

    //列表页
    public function nurseUpList(){

        $this->authority(array(11,2,3,4));

        $this->display();
    }

    //获取列表数据
    public function getNurseUpList(){
        $currentpage = I("post.currentpage");
        $pagenum = I("post.pagenum");
        $start = ($currentpage - 1) * $pagenum;
        $post = I("post.");
        $where = 'status = 1 and level_add = 1 ';
        if($post['name']&&$post['name']!=''){
            $where .= ' and  name LIKE "%'.$post['name'].'%"';
        }
        if($post['age1']){
            $where .= ' and age >='.$post['age1'].'';
        }
        if($post['age2']){
            $where .= ' and age <='.$post['age2'].'';
        }

        if($post['other']){
            $where .= ' and  other LIKE "%'.$post['other'].'%" ';

        }
        if($post['status_sh']){
            $where .= ' and status_sh <= '.$post['status_sh'].'';
        }
        if($post['level_name']){
            $where .= ' and level = "'.$post['level_name'].'"';
        }


        $list = M('nurse')->where($where)->limit($start,$pagenum)->order('add_time desc')->select();

        $status_sh_name = ['','已上户','未上户'];
        foreach($list as $k=>$v){
            $list[$k]['status_sh_name'] = $status_sh_name[$v['status_sh']];
            $do_time  = M('order_nurse')->field('do_time')->where('nurse_id='.$v['id'].' and true_s_time!=""')->order('true_s_time desc')->find();
            $list[$k]['do_time'] = $do_time['do_time'];
        }

        $count = M('nurse')->where($where)->count();

        $back['data']['list'] = $list;
        $back['data']['num'] = $count;
        $back['code'] = 1000;
        echo json_encode($back);
    }

    //移除提醒表
    public function nurseUpPass(){
        if(!I('get.id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;};</script>";
            exit;
        }
        $info = M('nurse')->where('id='.I('get.id').'')->find();
        if(!$info){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;};</script>";
            exit;
        }
        $save['level_add'] = 0;
        $save_mod = M('nurse')->where('id='.I('get.id').'')->save($save);
        if($save_mod!==false){
            echo "<script>alert('移除成功');window.onload=function(){window.history.go(-1);return false;};</script>";
            exit;
        }else{
            echo "<script>alert('已恢复');window.onload=function(){window.history.go(-1);return false;};</script>";
            exit;
        }

    }




}