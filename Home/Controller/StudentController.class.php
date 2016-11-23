<?php
//
//护理人员管理去控制器
//

namespace Home\Controller;
use Think\Controller;
class StudentController extends CommonController {
    public function  login_test(){
        if(!$_SESSION[C('USER_AUTH_KEY')]['id']){
            echo "<script>alert('请登录！');window.location.href='" . __MODULE__ . "/Admin/login.html';</script>";
            exit;
            //str_pad($str,6,0,STR_PAD_LEFT);  6位数不足补0
        }
    }
    //列表页
    public function studentList(){
        $this->authority(array(11,5));

        $this->display();
    }

    //获取列表数据
    public function getStudentList(){
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

        if($post['study_time']){
            $where .= ' and  study_time LIKE "%'.$post['study_time'].'%" ';

        }
        if($post['practical']){
            $where .= ' and practical = '.$post['practical'].'';
        }


        $list = M('student')->where($where)->limit($start,$pagenum)->order('add_time desc')->select();

        $practical_name = ['','实习中','未实习'];
        foreach($list as $k=>$v){
            $list[$k]['practical_name'] = $practical_name[$v['practical']];
        }

        $count = M('student')->where($where)->count();

        $back['data']['list'] = $list;
        $back['data']['num'] = $count;
        $back['code'] = 1000;
        echo json_encode($back);
    }


    //添加
    public function addStudent(){

        $this->authority(array(11,5));
        if(I('post.')){
            $post = I('post.');
            if($_FILES['title_img']['tmp_name']) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/student/';// 设置附件上传目录// 上传文件
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
                $upload->savePath = '/student/';// 设置附件上传目录// 上传文件
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
                $upload->savePath = '/student/';// 设置附件上传目录// 上传文件
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
                $upload->savePath = '/student/';// 设置附件上传目录// 上传文件
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

//            $post['other'] = $post['other1'].','.$post['other2'].','.$post['other3'].','.$post['other4'];
//            for($i=1;$i<12;$i++){
//                @$post['skill'] .= @$post['skill'.$i].',';
//            }
            $post['add_time'] = time();


            $student_id = M('student')->add($post);
            if($student_id) {
                $save['number'] = 'TLXY-' . str_pad($student_id, 6, 0, STR_PAD_LEFT);  //6位数不足补0
                $save_mod = M('student')->where('id=' . $student_id . '')->save($save);
                if ($save_mod === false) {
                    M('student')->where('id=' . $student_id . '')->save($save);
                }
                echo "<script>alert('添加成功'); window.location.href='".__MODULE__."/Student/studentList'</script>";
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
    public function changeStudent(){

        $this->authority(array(11,5));
        if(I('post.')){
            $post = I('post.');
            $student_info = M('student')->field('id,id_card_img,certificate_img,test_img')->where('id='.$post['id'].'')->find();
            $post['id_card_img'] = $student_info['id_card_img'];
            $post['certificate_img'] = $student_info['certificate_img'];
            $post['test_img'] = $student_info['test_img'];
//            echo $post['id_card_img'].'<br/>'. $post['certificate_img'].'<br/>' .$post['test_img'].'<br/>';
            //处理删除的图片
            if($post['id_card_img_del']){
                $id_card_img_del_arr = explode(',',substr($post['id_card_img_del'],0,-1));
                foreach($id_card_img_del_arr as $v){
                    $post['id_card_img'] = str_replace($v.',','',$post['id_card_img']);
                    $post['id_card_img'] = str_replace(','.$v,'', $post['id_card_img']);
                    $post['id_card_img'] = str_replace($v,'', $post['id_card_img']);
                    $unlink[] = $v;
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
                    $unlink[] = $v;
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
                    $unlink[] = $v;
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
                $upload->savePath = '/student/';// 设置附件上传目录// 上传文件
                $info = $upload->uploadOne($_FILES['title_img']);
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    $post['title_img']  = $info['savepath'] . $info['savename'];
                    $unlink[] = $post['old_title_img'];
                }
            }
            if($_FILES['id_card_img']['tmp_name'][0]) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/student/';// 设置附件上传目录// 上传文件
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
                $upload->savePath = '/student/';// 设置附件上传目录// 上传文件
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
                $upload->savePath = '/student/';// 设置附件上传目录// 上传文件
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
//            $post['other'] = $post['other1'].','.$post['other2'].','.$post['other3'].','.$post['other4'];
//            for($i=1;$i<12;$i++){
//                @$post['skill'] .= @$post['skill'.$i].',';
//            }
//
//            $level_name = ['','小田螺1.1','小田螺1.2','小田螺1.3','小田螺1.4','小田螺1.5','大田螺1.1','大田螺1.2','大田螺1.3','大田螺1.4','大田螺1.5','超级田螺1.1','超级田螺1.2','超级田螺1.3','超级田螺1.4','超级田螺1.5','金牌田螺'];
//            $post['level_name'] = $level_name[$post['level']];

//            $post['add_time'] = time();
            $nurse_id = M('student')->where('id='.$post['id'].'')->save($post);
            if($nurse_id!==false) {
                foreach($unlink as $v){
                    unlink('Uploads/'.$v);
                }
                echo "<script>alert('修改成功'); window.location.href='".__MODULE__."/Student/studentInfo/id/".$post['id'].".html'</script>";
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
            $info = M('student')->where('id='.I('get.id').'')->find();
            if(!$info){
                echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            $info['id_card_img_arr'] = explode(',',$info['id_card_img']);
            $info['certificate_img_arr'] = explode(',',$info['certificate_img']);
            $info['test_img_arr'] = explode(',',$info['test_img']);
//            $info['other'] = explode(',',$info['other']);
//            $info['skill'] = explode(',',$info['skill']);
//            print_r($info['other']);die;
            $this->assign('info',$info);
            $this->display();
        }

    }


    //详情
    public function studentInfo(){
        $this->authority(array(11,2,3,5));
        if(!I('get.id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('student')->where('id='.I('get.id').'')->find();
        if(!$info){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info['id_card_img_arr'] = explode(',',$info['id_card_img']);
        $info['certificate_img_arr'] = explode(',',$info['certificate_img']);
        $info['test_img_arr'] = explode(',',$info['test_img']);


        $student_practical = M('student_practical')->where('student_id='.I('get.id').'')->order('add_time desc')->select();

        $nurse_id = M('nurse')->field('id')->where('student_id = '.$info['id'].'')->find();


        $this->assign('student_practical',$student_practical);
        $this->assign('info',$info);
        $this->assign('nurse_id',$nurse_id);

        $this->display();
    }



    //删除
    public function deleteStudent(){

        $this->authority(array(11,5));
        if(!I('get.id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('student')->where('id='.I('get.id').'')->find();
        if(!$info){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $save['status'] = 11;
        $save_mod = M('student')->where('id='.I('get.id').'')->save($save);
        if(!$save_mod){
            M('student')->where('id='.I('get.id').'')->save($save);
        }
        if(I('get.type')=='list'){
            echo "<script>alert('已删除');window.onload=function(){window.history.go(-1);return false;};</script>";
            exit;
        }else{
            echo "<script>alert('已删除');window.location.href='".__MODULE__."/Student/studentInfo/id/".I('get.id').".html';</script>";
            exit;
        }


    }
    //恢复
    public function backStudent(){

        $this->authority(array(11,5));
        if(!I('get.id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;};</script>";
            exit;
        }
        $info = M('student')->where('id='.I('get.id').'')->find();
        if(!$info){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;};</script>";
            exit;
        }
        $save['status'] = 1;
        $save_mod = M('student')->where('id='.I('get.id').'')->save($save);
        if(!$save_mod){
            M('student')->where('id='.I('get.id').'')->save($save);
        }
        echo "<script>alert('已恢复');window.location.href='".__MODULE__."/Student/studentInfo/id/".I('get.id').".html';</script>";
        exit;

    }

    //新增实习记录
    public function add_practical(){
        $this->authority(array(11,5));
        if(!I('post.student_id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;};</script>";
            exit;
        }
        $info = M('student')->where('id='.I('post.student_id').'')->find();
        if(!$info){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;};</script>";
            exit;
        }
        $post = I('post.');
        $post['add_time'] = time();
        $post['student_name'] = $info['name'];

        $add_mod = M('student_practical')->add($post);
        if($add_mod){
            echo "<script>alert('添加成功');window.location.href='".__MODULE__."/Student/studentInfo/id/".$post['student_id'].".html'</script>";
            exit;
        }else{
            echo "<script>alert('添加失败');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
    }

    //修改实习
    public  function change_practical(){

        $this->authority(array(11,5));
        if(!I('post.practical_id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;};</script>";
            exit;
        }
        $info = M('student_practical')->where('id='.I('post.practical_id').'')->find();
        if(!$info){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;};</script>";
            exit;
        }
        $post = I('post.');


        $save_mod = M('student_practical')->where('id='.$post['practical_id'].'')->save($post);
        if($save_mod!==false){
            echo "<script>alert('修改成功');window.location.href='".__MODULE__."/Student/studentInfo/id/".$info['student_id'].".html'</script>";
            exit;
        }else{
            echo "<script>alert('修改失败');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
    }
    //删除实习
    public function delPractical(){
        $this->authority(array(11,5));
        if(!I('get.id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('student_practical')->where('id='.I('get.id').'')->find();
        if(!$info){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if($info['true_s_time']!=''){
            echo "<script>alert('已经下户不能删除');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }

        $del_mod = M('student_practical')->where('id='.I('get.id').'')->delete();
        if($del_mod!==false){
            echo "<script>alert('成功');window.location.href='".__MODULE__."/Student/studentInfo/id/".$info['student_id'].".html'</script>";
            exit;
        }else{
            echo "<script>alert('失败');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
    }

    //转化阿姨
    public function add_to_nurse(){
        $this->authority(array(11,5));
        if(!I('post.student_id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $nurse_have = M('nurse')->field('id')->where('student_id = '.I('post.student_id').'')->find();
        if($nurse_have){
            echo "<script>alert('已转化过！不能再转化');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }


        $info = M('student')->where('id='.I('post.student_id').'')->find();
        if(!$info){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $post = I('post.');
        $nurse_add = $info;

        $nurse_add['other'] = $post['other1'].','.$post['other2'].','.$post['other3'].','.$post['other4'];
        for($i=1;$i<12;$i++){
            @$nurse_add['skill'] .= @$post['skill'.$i].',';
        }
        $nurse_add['add_time'] = time();
        $level_name = ['','小田螺1.1','小田螺1.2','小田螺1.3','小田螺1.4','小田螺1.5','大田螺1.1','大田螺1.2','大田螺1.3','大田螺1.4','大田螺1.5','超级田螺1.1','超级田螺1.2','超级田螺1.3','超级田螺1.4','超级田螺1.5','金牌田螺'];
        $nurse_add['level_name'] = $level_name[$post['level']];
        $nurse_add['is_student'] = '是';
        $nurse_add['student_id'] = $post['student_id'];
        unset($nurse_add['id']);

        $nurse_id = M('nurse')->add($nurse_add);
        if($nurse_id) {
            $save['number'] = 'TLAY-' . str_pad($nurse_id, 6, 0, STR_PAD_LEFT);  //6位数不足补0
            $save_mod = M('nurse')->where('id=' . $nurse_id . '')->save($save);
            if ($save_mod === false) {
                M('nurse')->where('id=' . $nurse_id . '')->save($save);
            }
            echo "<script>alert('转化成功'); window.location.href='".__MODULE__."/Nurse/nurseInfo/id/".$nurse_id.".html'</script>";
            exit;
        }else{
            echo "<script>alert('转化失败');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }

    }
    //计算学员工资
    public function getStudentPay(){
        if(I('post.do_time')){
            $number = I('post.do_time');
            if($number>=15){
                $price= sprintf("%.2f", 3000/28*($number-15)) + 15*20;
            }else{
                $price = $number*20;
            }
            $back['price'] = $price;
            $back['code'] = 1000;
            echo json_encode($back);
            exit;
        }else{
            $back['code'] = 1001;
            echo json_encode($back);
            exit;
        }
    }

}