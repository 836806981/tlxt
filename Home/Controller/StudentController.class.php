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
    //学习中
    public function studentList_1(){

        $this->assign('now_day',date('Y-m-d'));
        $this->assign('belong',1);
        $this->display('studentList');
    }
    //待实习
    public function studentList_2(){

        $this->assign('now_day',date('Y-m-d'));
        $this->assign('belong',2);
        $this->display('studentList');
    }
    //实习中
    public function studentList_3(){

        $this->assign('now_day',date('Y-m-d'));
        $this->assign('belong',3);
        $this->display('studentList');
    }
    //淘汰
    public function studentList_24(){

        $this->assign('now_day',date('Y-m-d'));
        $this->assign('belong',24);
        $this->display('studentList');
    }




    //获取列表数据
    public function getStudentList(){
        $currentpage = I("post.currentpage");
        $pagenum = I("post.pagenum");
        $start = ($currentpage - 1) * $pagenum;
        $post = I("post.");
        $where = 'status = '.$post['belong'].' and type=1 ';
        if($post['name']&&$post['name']!=''){
            $where .= ' and  name LIKE "%'.$post['name'].'%"';
        }
        if($post['come']){
            $where .= ' and come ="'.$post['come'].'"';
        }
        if($post['teacher']){
            $where .= ' and teacher ="'.$post['teacher'].'"';
        }
        if($post['class_name']){
            $where .= ' and class_name ="'.$post['class_name'].'"';
        }
        if($post['class_number']){
            $where .= ' and class_number ="'.$post['class_number'].'"';
        }


        $list = M('nurse')->where($where)->limit($start,$pagenum)->order('add_time desc')->select();

        $status_name = ['','学习中','待实习','实习中'];
        $status_name[24] = '已淘汰';
        foreach($list as $k=>$v){
            $list[$k]['status_name'] = $status_name[$v['status']];
            if(date('Y-m-d') == $v['finish_time']){
                $list[$k]['finish_now'] = 2;
            }elseif(date('Y-m-d') > $v['finish_time']){
                $list[$k]['finish_now'] = 3;
            }elseif(date('Y-m-d') < $v['finish_time']){
                $list[$k]['finish_now'] = 1;
            }
        }

        $count = M('nurse')->where($where)->count();
        $back['data']['list'] = $list;
        $back['data']['num'] = $count;
        $back['code'] = 1000;
        echo json_encode($back);
    }


    //添加
    public function addStudent(){
        if(I('post.')){
            $post = I('post.');
            if($post['name']==''||$post['phone']==''){
                echo "<script>alert('请填写姓名和电话');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            $have = M('nurse')->where('name="'.$post['name'].'"  and phone ="'.$post['phone'].'"')->find();
            if($have){
                echo "<script>alert('已存在该学员或阿姨');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }

            if($_FILES['title_img']['tmp_name']) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/title_img/';// 设置附件上传目录// 上传文件
                $info = $upload->uploadOne($_FILES['title_img']);
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    $post['title_img']  = $info['savepath'] . $info['savename'];

                }
            }
            if($_FILES['life_img1']['tmp_name']) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/life/';// 设置附件上传目录// 上传文件
                $info = $upload->uploadOne($_FILES['life_img1']);
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    $post['life_img1']  = $info['savepath'] . $info['savename'];
                    $img_s = $this->shuiyin($post['life_img1']);
                    if($img_s){
                        $post['life_img1'] = $img_s;
                    }
                }
            }
            if($_FILES['life_img2']['tmp_name']) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/life/';// 设置附件上传目录// 上传文件
                $info = $upload->uploadOne($_FILES['life_img2']);
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    $post['life_img2']  = $info['savepath'] . $info['savename'];
                    $img_s = $this->shuiyin($post['life_img2']);
                    if($img_s){
                        $post['life_img2'] = $img_s;
                    }
                }
            }
            if($_FILES['id_img']['tmp_name']) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/id_img/';// 设置附件上传目录// 上传文件
                $info = $upload->uploadOne($_FILES['id_img']);
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    $post['id_img']  = $info['savepath'] . $info['savename'];
                    $img_s = $this->shuiyin($post['id_img']);
                    if($img_s){
                        $post['id_img'] = $img_s;
                    }
                }
            }
            if($_FILES['test_img']['tmp_name']) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/test_img/';// 设置附件上传目录// 上传文件
                $info = $upload->uploadOne($_FILES['test_img']);
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    $post['test_img']  = $info['savepath'] . $info['savename'];
                    $img_s = $this->shuiyin($post['test_img']);
                    if($img_s){
                        $post['test_img'] = $img_s;
                    }
                }
            }
//            if($_FILES['zs_img']['tmp_name']) {
//                $upload = new \Think\Upload();// 实例化上传类
//                $upload->maxSize = 2145728;// 设置附件上传大小
//                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
//                $upload->savePath = '/nurse/zs_img/';// 设置附件上传目录// 上传文件
//                $info = $upload->uploadOne($_FILES['zs_img']);
//                if (!$info) {// 上传错误提示错误信息
//                    $this->error($upload->getError());
//                } else {//上传成功 获取上传文件信息
//                    $post['zs_img']  = $info['savepath'] . $info['savename'];
//                    $img_s = $this->shuiyin($post['zs_img']);
//                    if($img_s){
//                        $post['zs_img'] = $img_s;
//                    }
//                }
//            }
            if($_FILES['imgs']['tmp_name'][0]) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/imgs/';// 设置附件上传目录// 上传文件
                $info = $upload->upload(array($_FILES['imgs']));
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    foreach($info as $v){
                        $img_g =  $v['savepath'] . $v['savename'];
                        $img_s = $this->shuiyin($img_g);
                        if($img_s){
                            $post['imgs'] .= $img_s.',';
                        }else{
                            $post['imgs'] .= $img_g.',';
                        }
                    }
                    $post['imgs'] = substr($post['imgs'],0,-1);
                }
            }
            //性格
//            $rand = rand(1,4);
//            $post['character_img'] =  '/character_type/'.($post['character_type']*$rand).'.jpg';

            //定薪
//            $level_name = ['','小田螺1.1','小田螺1.2','小田螺1.3','小田螺1.4','小田螺1.5','大田螺2.1','大田螺2.2','大田螺2.3','大田螺2.4','大田螺2.5','超级田螺3.1','超级田螺3.2','超级田螺3.3','超级田螺3.4','超级田螺3.5','金牌田螺'];
//            if($post['agreement_type'] == 1){
//                $price_arr = ['','3000','3200','3400','3600','4000','5000','5200','5400','5600','6000','7000','7200','7400','7600','8000','9000'];
//                $price_level = array_keys($level_name,$post['price_name']);
//                $post['price_level'] = $price_level[0];
//                $post['price'] = $price_arr[$post['price_level']];
//            }
            //等级
//            for($i=1;$i<=11;$i++){
//                $post['skills'] .= ($post['skills'.$i]?$post['skills'.$i]:0).',';
//            }
//            $post['skills'] = substr($post['skills'],0,-1);

            // 学时算预计完成时间
            $post['finish_time'] = date('Y-m-d',strtotime('+'.$post['study_day'].' days',strtotime($post['come_time'])));


            //家庭成员
            for($i=1;$i<=5;$i++){
                $post['family_1'] .= ($post['family_1_'.$i]?$post['family_1_'.$i]:' ').'||';
            }
            $post['family_urgent'] == 1?( $post['family_1'].=1):( $post['family_1'].=0);

            for($i=1;$i<=5;$i++){
                $post['family_2'] .= ($post['family_2_'.$i]?$post['family_2_'.$i]:' ').'||';
            }
            $post['family_urgent'] == 2?( $post['family_2'].=1):( $post['family_2'].=0);
            for($i=1;$i<=5;$i++){
                $post['family_3'] .= ($post['family_3_'.$i]?$post['family_3_'.$i]:' ').'||';
            }
            $post['family_urgent'] == 3?( $post['family_3'].=1):( $post['family_3'].=0);

            //擅长菜系处理
            for($i=1;$i<=9;$i++){
                $post['good_cuisine'] .= ($post['good_cuisine'.$i]?$post['good_cuisine'.$i]:' ').',';
            }
            $post['good_cuisine'] = substr($post['good_cuisine'],0,-1);

            //擅长口味处理
            for($i=1;$i<=5;$i++){
                $post['good_flavor'] .=($post['good_flavor'.$i]?$post['good_flavor'.$i]:' ').',';
            }
            $post['good_flavor'] = substr($post['good_flavor'],0,-1);



            //个人经历成员
            for($i=1;$i<=8;$i++){
                $post['experience_own1'] .= ($post['experience_own1_'.$i]?$post['experience_own1_'.$i]:' ').'||';
            }
            $post['experience_own1'] = substr($post['experience_own1'],0,-2);

            for($i=1;$i<=8;$i++){
                $post['experience_own2'] .= ($post['experience_own2_'.$i]?$post['experience_own2_'.$i]:' ').'||';
            }
            $post['experience_own2'] = substr($post['experience_own2'],0,-2);
            for($i=1;$i<=8;$i++){
                $post['experience_own3'] .= ($post['experience_own3_'.$i]?$post['experience_own3_'.$i]:' ').'||';
            }
            $post['experience_own3'] = substr($post['experience_own3'],0,-2);

            $post['is_student'] = 1;
            $post['add_time'] = time();
            $post['status'] = 1;
            $post['status_sh'] = 1;
            $post['type'] = 1;



            $nurse_id = M('nurse')->add($post);
            if($nurse_id) {
                $save['number'] = 'TLXY-' . str_pad($nurse_id, 6, 0, STR_PAD_LEFT);  //6位数不足补0
                $save_mod = M('nurse')->where('id=' . $nurse_id . '')->save($save);
                if ($save_mod === false) {
                    M('nurse')->where('id=' . $nurse_id . '')->save($save);
                }
                echo "<script>alert('添加成功'); window.location.href='".__MODULE__."/Student/studentList_1.html'</script>";
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
        if(I('post.')){
            $post = I('post.');
            if($post['name']==''||$post['phone']==''){
                echo "<script>alert('请填写姓名和电话');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            $have = M('nurse')->where('name="'.$post['name'].'"  and phone ="'.$post['phone'].'" and id!='.$post['id'].'')->find();
            if($have){
                echo "<script>alert('已存在该学员或阿姨');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            $nurse_info = M('nurse')->where('id='.$post['id'].'')->find();

            if($_FILES['title_img']['tmp_name']) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/title_img/';// 设置附件上传目录// 上传文件
                $info = $upload->uploadOne($_FILES['title_img']);
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    $post['title_img']  = $info['savepath'] . $info['savename'];
                    $unlink[] = $nurse_info['title_img'];
                }
            }
            if($_FILES['life_img1']['tmp_name']) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/life/';// 设置附件上传目录// 上传文件
                $info = $upload->uploadOne($_FILES['life_img1']);
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    $post['life_img1']  = $info['savepath'] . $info['savename'];
                    $img_s = $this->shuiyin($post['life_img1']);
                    if($img_s){
                        $post['life_img1'] = $img_s;
                    }
                    $unlink[] = $nurse_info['life_img1'];
                    $del_s = explode('_s',$nurse_info['life_img1']);
                    $unlink[] = $del_s[0].$del_s[1];
                }
            }
            if($_FILES['life_img2']['tmp_name']) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/life/';// 设置附件上传目录// 上传文件
                $info = $upload->uploadOne($_FILES['life_img2']);
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    $post['life_img2']  = $info['savepath'] . $info['savename'];
                    $img_s = $this->shuiyin($post['life_img2']);
                    if($img_s){
                        $post['life_img2'] = $img_s;
                    }
                    $unlink[] = $nurse_info['life_img2'];
                    $del_s = explode('_s',$nurse_info['life_img2']);
                    $unlink[] = $del_s[0].$del_s[1];
                }
            }
            if($_FILES['id_img']['tmp_name']) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/id_img/';// 设置附件上传目录// 上传文件
                $info = $upload->uploadOne($_FILES['id_img']);
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    $post['id_img']  = $info['savepath'] . $info['savename'];
                    $img_s = $this->shuiyin($post['id_img']);
                    if($img_s){
                        $post['id_img'] = $img_s;
                    }
                    $unlink[] = $nurse_info['id_img'];
                    $del_s = explode('_s',$nurse_info['id_img']);
                    $unlink[] = $del_s[0].$del_s[1];
                }
            }
            if($_FILES['test_img']['tmp_name']) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/test_img/';// 设置附件上传目录// 上传文件
                $info = $upload->uploadOne($_FILES['test_img']);
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    $post['test_img']  = $info['savepath'] . $info['savename'];
                    $img_s = $this->shuiyin($post['test_img']);
                    if($img_s){
                        $post['test_img'] = $img_s;
                    }
                    $unlink[] = $nurse_info['test_img'];
                    $del_s = explode('_s',$nurse_info['test_img']);
                    $unlink[] = $del_s[0].$del_s[1];
                }
            }
//            if($_FILES['zs_img']['tmp_name']) {
//                $upload = new \Think\Upload();// 实例化上传类
//                $upload->maxSize = 2145728;// 设置附件上传大小
//                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
//                $upload->savePath = '/nurse/zs_img/';// 设置附件上传目录// 上传文件
//                $info = $upload->uploadOne($_FILES['zs_img']);
//                if (!$info) {// 上传错误提示错误信息
//                    $this->error($upload->getError());
//                } else {//上传成功 获取上传文件信息
//                    $post['zs_img']  = $info['savepath'] . $info['savename'];
//                    $img_s = $this->shuiyin($post['zs_img']);
//                    if($img_s){
//                        $post['zs_img'] = $img_s;
//                    }
//                    $unlink[] = $nurse_info['zs_img'];
//                    $del_s = explode('_s',$nurse_info['zs_img']);
//                    $unlink[] = $del_s[0].$del_s[1];
//                }
//            }
            $post['imgs'] = $nurse_info['imgs']!=''?($nurse_info['imgs'].','):'';
            if($_FILES['imgs']['tmp_name'][0]) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/imgs/';// 设置附件上传目录// 上传文件
                $info = $upload->upload(array($_FILES['imgs']));
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    foreach($info as $v){
                        $img_g =  $v['savepath'] . $v['savename'];
                        $img_s = $this->shuiyin($img_g);
                        if($img_s){
                            $post['imgs'] .= $img_s.',';
                        }else{
                            $post['imgs'] .= $img_g.',';
                        }
                    }
                }
            }
            $post['imgs'] = substr($post['imgs'],0,-1);


            if($post['imgs_del']&&$post['imgs_del']!=''){
                $del_imgs_a = explode(',',substr($post['imgs_del'],0,-1));
                foreach($del_imgs_a as $v){
                    $post['imgs'] = str_replace($v.',','',$post['imgs']);
                    $post['imgs'] = str_replace(','.$v,'', $post['imgs']);
                    $post['imgs'] = str_replace($v,'', $post['imgs']);
                    $unlink[] = $v;
                    $del_s = explode('_s',$v);
                    $unlink[] = $del_s[0].$del_s[1];
                }
            }


            // 学时算预计完成时间
            $post['finish_time'] = date('Y-m-d',strtotime('+'.$post['study_day'].' days',strtotime($post['come_time'])));
//            //性格
//            if($post['character_type']!=$nurse_info['character_type']){
//                $rand = rand(1,4);
//                $post['character_img'] =  '/character_type/'.($post['character_type']*$rand).'.jpg';
//            }

//            //定薪
//            $post['price_level'] = 0;
//            $level_name = ['','小田螺1.1','小田螺1.2','小田螺1.3','小田螺1.4','小田螺1.5','大田螺2.1','大田螺2.2','大田螺2.3','大田螺2.4','大田螺2.5','超级田螺3.1','超级田螺3.2','超级田螺3.3','超级田螺3.4','超级田螺3.5','金牌田螺'];
//            if($post['agreement_type'] == 1){
//                $price_arr = ['','3000','3200','3400','3600','4000','5000','5200','5400','5600','6000','7000','7200','7400','7600','8000','9000'];
//                $price_level = array_keys($level_name,$post['price_name']);
//                $post['price_level'] = $price_level[0];
//                $post['price'] = $price_arr[$post['price_level']];
//            }
//            //等级
//            for($i=1;$i<=11;$i++){
//                $post['skills'] .= ($post['skills'.$i]?$post['skills'.$i]:0).',';
//            }
//            $post['skills'] = substr($post['skills'],0,-1);

            //家庭成员
            for($i=1;$i<=5;$i++){
                $post['family_1'] .= ($post['family_1_'.$i]?$post['family_1_'.$i]:' ').'||';
            }
            $post['family_urgent'] == 1?( $post['family_1'].=1):( $post['family_1'].=0);

            for($i=1;$i<=5;$i++){
                $post['family_2'] .= ($post['family_2_'.$i]?$post['family_2_'.$i]:' ').'||';
            }
            $post['family_urgent'] == 2?( $post['family_2'].=1):( $post['family_2'].=0);
            for($i=1;$i<=5;$i++){
                $post['family_3'] .= ($post['family_3_'.$i]?$post['family_3_'.$i]:' ').'||';
            }
            $post['family_urgent'] == 3?( $post['family_3'].=1):( $post['family_3'].=0);

            //擅长菜系处理
            for($i=1;$i<=9;$i++){
                $post['good_cuisine'] .= ($post['good_cuisine'.$i]?$post['good_cuisine'.$i]:' ').',';
            }
            $post['good_cuisine'] = substr($post['good_cuisine'],0,-1);

            //擅长口味处理
            for($i=1;$i<=5;$i++){
                $post['good_flavor'] .=($post['good_flavor'.$i]?$post['good_flavor'.$i]:' ').',';
            }
            $post['good_flavor'] = substr($post['good_flavor'],0,-1);



            //个人经历成员
            for($i=1;$i<=8;$i++){
                $post['experience_own1'] .= ($post['experience_own1_'.$i]?$post['experience_own1_'.$i]:' ').'||';
            }
            $post['experience_own1'] = substr($post['experience_own1'],0,-2);

            for($i=1;$i<=8;$i++){
                $post['experience_own2'] .= ($post['experience_own2_'.$i]?$post['experience_own2_'.$i]:' ').'||';
            }
            $post['experience_own2'] = substr($post['experience_own2'],0,-2);
            for($i=1;$i<=8;$i++){
                $post['experience_own3'] .= ($post['experience_own3_'.$i]?$post['experience_own3_'.$i]:' ').'||';
            }
            $post['experience_own3'] = substr($post['experience_own3'],0,-2);

//            $post['is_student'] = 0;
//            $post['add_time'] = time();
//            $post['status'] = 1;



            $nurse_id = M('nurse')->where('id='.$post['id'].'')->save($post);
            if($nurse_id!==false) {
                foreach($unlink as $v){
                    unlink('Uploads/'.$v);
                }
                $save['number'] = 'TLXY-' . str_pad($post['id'], 6, 0, STR_PAD_LEFT);  //6位数不足补0
                $save_mod = M('nurse')->where('id=' . $post['id'] . '')->save($save);
                if ($save_mod === false) {
                    M('nurse')->where('id=' . $post['id'] . '')->save($save);
                }
                echo "<script>alert('修改成功'); window.location.href='".__MODULE__."/Student/studentList'</script>";
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

            $this->assign('info',$info);
            $this->display();
        }
    }


    //详情
    public function studentInfo(){

        if(!I('get.id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('nurse')->where('id='.I('get.id').'')->find();
        if(!$info){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info['imgs_a'] = explode(',',$info['imgs']);
        $info['family_1_a'] = explode('||',$info['family_1']);
        $info['family_2_a'] = explode('||',$info['family_2']);
        $info['family_3_a'] = explode('||',$info['family_3']);
        $info['good_cuisine_a'] = explode(',',$info['good_cuisine']);
        $info['good_flavor_a'] = explode(',',$info['good_flavor']);
        $info['experience_own1_a'] = explode('||',$info['experience_own1']);
        $info['experience_own2_a'] = explode('||',$info['experience_own2']);
        $info['experience_own3_a'] = explode('||',$info['experience_own3']);


        $info['is_stay_name']= ($info['is_stay']==1?'是':'否');

        $good_cuisine_name = ['面食','煲汤','川菜小炒','流食','素食','肉类','小吃','甜品','补品'];
        foreach($info['good_cuisine_a'] as $k=>$v){
            $info['good_cuisine_str'] .= ($v==1?$good_cuisine_name[$k].' ':'');
        }

        $good_flavor_name = ['清淡','咸鲜','甜食','辣','酸'];
        foreach($info['good_flavor_a'] as $k=>$v){
            $info['good_flavor_str'] .= ($v==1?$good_flavor_name[$k].' ':'');
        }

        $status_name = ['','学习中','待实习','实习中'];
        $info['status_name'] = $status_name[$info['status']];

        $this->assign('info',$info);
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

    //完善学员
    public function doneNurse(){
        if(I('post.')){
            $post = I('post.');
            if($post['name']==''||$post['phone']==''){
                echo "<script>alert('请填写姓名和电话');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            $have = M('nurse')->where('name="'.$post['name'].'"  and phone ="'.$post['phone'].'" and id!='.$post['id'].'')->find();
            if($have){
                echo "<script>alert('已存在该学员或阿姨');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            $nurse_info = M('nurse')->where('id='.$post['id'].'')->find();

            if($_FILES['title_img']['tmp_name']) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/title_img/';// 设置附件上传目录// 上传文件
                $info = $upload->uploadOne($_FILES['title_img']);
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    $post['title_img']  = $info['savepath'] . $info['savename'];
                    $unlink[] = $nurse_info['title_img'];
                }
            }
            if($_FILES['life_img1']['tmp_name']) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/life/';// 设置附件上传目录// 上传文件
                $info = $upload->uploadOne($_FILES['life_img1']);
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    $post['life_img1']  = $info['savepath'] . $info['savename'];
                    $img_s = $this->shuiyin($post['life_img1']);
                    if($img_s){
                        $post['life_img1'] = $img_s;
                    }
                    $unlink[] = $nurse_info['life_img1'];
                    $del_s = explode('_s',$nurse_info['life_img1']);
                    $unlink[] = $del_s[0].$del_s[1];
                }
            }
            if($_FILES['life_img2']['tmp_name']) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/life/';// 设置附件上传目录// 上传文件
                $info = $upload->uploadOne($_FILES['life_img2']);
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    $post['life_img2']  = $info['savepath'] . $info['savename'];
                    $img_s = $this->shuiyin($post['life_img2']);
                    if($img_s){
                        $post['life_img2'] = $img_s;
                    }
                    $unlink[] = $nurse_info['life_img2'];
                    $del_s = explode('_s',$nurse_info['life_img2']);
                    $unlink[] = $del_s[0].$del_s[1];
                }
            }
            if($_FILES['id_img']['tmp_name']) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/id_img/';// 设置附件上传目录// 上传文件
                $info = $upload->uploadOne($_FILES['id_img']);
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    $post['id_img']  = $info['savepath'] . $info['savename'];
                    $img_s = $this->shuiyin($post['id_img']);
                    if($img_s){
                        $post['id_img'] = $img_s;
                    }
                    $unlink[] = $nurse_info['id_img'];
                    $del_s = explode('_s',$nurse_info['id_img']);
                    $unlink[] = $del_s[0].$del_s[1];
                }
            }
            if($_FILES['test_img']['tmp_name']) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/test_img/';// 设置附件上传目录// 上传文件
                $info = $upload->uploadOne($_FILES['test_img']);
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    $post['test_img']  = $info['savepath'] . $info['savename'];
                    $img_s = $this->shuiyin($post['test_img']);
                    if($img_s){
                        $post['test_img'] = $img_s;
                    }
                    $unlink[] = $nurse_info['test_img'];
                    $del_s = explode('_s',$nurse_info['test_img']);
                    $unlink[] = $del_s[0].$del_s[1];
                }
            }
            if($_FILES['zs_img']['tmp_name']) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/zs_img/';// 设置附件上传目录// 上传文件
                $info = $upload->uploadOne($_FILES['zs_img']);
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    $post['zs_img']  = $info['savepath'] . $info['savename'];
                    $img_s = $this->shuiyin($post['zs_img']);
                    if($img_s){
                        $post['zs_img'] = $img_s;
                    }
                    $unlink[] = $nurse_info['zs_img'];
                    $del_s = explode('_s',$nurse_info['zs_img']);
                    $unlink[] = $del_s[0].$del_s[1];
                }
            }
            $post['imgs'] = $nurse_info['imgs']!=''?($nurse_info['imgs'].','):'';
            if($_FILES['imgs']['tmp_name'][0]) {
                $upload = new \Think\Upload();// 实例化上传类
                $upload->maxSize = 2145728;// 设置附件上传大小
                $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
                $upload->savePath = '/nurse/imgs/';// 设置附件上传目录// 上传文件
                $info = $upload->upload(array($_FILES['imgs']));
                if (!$info) {// 上传错误提示错误信息
                    $this->error($upload->getError());
                } else {//上传成功 获取上传文件信息
                    foreach($info as $v){
                        $img_g =  $v['savepath'] . $v['savename'];
                        $img_s = $this->shuiyin($img_g);
                        if($img_s){
                            $post['imgs'] .= $img_s.',';
                        }else{
                            $post['imgs'] .= $img_g.',';
                        }
                    }
                }
            }
            $post['imgs'] = substr($post['imgs'],0,-1);


            if($post['imgs_del']&&$post['imgs_del']!=''){
                $del_imgs_a = explode(',',substr($post['imgs_del'],0,-1));
                foreach($del_imgs_a as $v){
                    $post['imgs'] = str_replace($v.',','',$post['imgs']);
                    $post['imgs'] = str_replace(','.$v,'', $post['imgs']);
                    $post['imgs'] = str_replace($v,'', $post['imgs']);
                    $unlink[] = $v;
                    $del_s = explode('_s',$v);
                    $unlink[] = $del_s[0].$del_s[1];
                }
            }

            //性格
            if($post['character_type']!=$nurse_info['character_type']){
                $rand = rand(1,4);
                $post['character_img'] =  '/character_type/'.(($post['character_type']-1)*4+$rand).'.jpg';
            }

            //定薪
            $post['price_level'] = 0;
            $level_name = ['','小田螺1.1','小田螺1.2','小田螺1.3','小田螺1.4','小田螺1.5','大田螺2.1','大田螺2.2','大田螺2.3','大田螺2.4','大田螺2.5','超级田螺3.1','超级田螺3.2','超级田螺3.3','超级田螺3.4','超级田螺3.5','金牌田螺'];
            if($post['agreement_type'] == 1){
                $price_arr = ['','3000','3200','3400','3600','4000','5000','5200','5400','5600','6000','7000','7200','7400','7600','8000','9000'];
                $price_level = array_keys($level_name,$post['price_name']);
                $post['price_level'] = $price_level[0];
                $post['price'] = $price_arr[$post['price_level']];
            }
            //等级
            for($i=1;$i<=11;$i++){
                $post['skills'] .= ($post['skills'.$i]?$post['skills'.$i]:0).',';
            }
            $post['skills'] = substr($post['skills'],0,-1);

            //家庭成员
            for($i=1;$i<=5;$i++){
                $post['family_1'] .= ($post['family_1_'.$i]?$post['family_1_'.$i]:' ').'||';
            }
            $post['family_urgent'] == 1?( $post['family_1'].=1):( $post['family_1'].=0);

            for($i=1;$i<=5;$i++){
                $post['family_2'] .= ($post['family_2_'.$i]?$post['family_2_'.$i]:' ').'||';
            }
            $post['family_urgent'] == 2?( $post['family_2'].=1):( $post['family_2'].=0);
            for($i=1;$i<=5;$i++){
                $post['family_3'] .= ($post['family_3_'.$i]?$post['family_3_'.$i]:' ').'||';
            }
            $post['family_urgent'] == 3?( $post['family_3'].=1):( $post['family_3'].=0);

            //擅长菜系处理
            for($i=1;$i<=9;$i++){
                $post['good_cuisine'] .= ($post['good_cuisine'.$i]?$post['good_cuisine'.$i]:' ').',';
            }
            $post['good_cuisine'] = substr($post['good_cuisine'],0,-1);

            //擅长口味处理
            for($i=1;$i<=5;$i++){
                $post['good_flavor'] .=($post['good_flavor'.$i]?$post['good_flavor'.$i]:' ').',';
            }
            $post['good_flavor'] = substr($post['good_flavor'],0,-1);



            //个人经历成员
            for($i=1;$i<=8;$i++){
                $post['experience_own1'] .= ($post['experience_own1_'.$i]?$post['experience_own1_'.$i]:' ').'||';
            }
            $post['experience_own1'] = substr($post['experience_own1'],0,-2);

            for($i=1;$i<=8;$i++){
                $post['experience_own2'] .= ($post['experience_own2_'.$i]?$post['experience_own2_'.$i]:' ').'||';
            }
            $post['experience_own2'] = substr($post['experience_own2'],0,-2);
            for($i=1;$i<=8;$i++){
                $post['experience_own3'] .= ($post['experience_own3_'.$i]?$post['experience_own3_'.$i]:' ').'||';
            }
            $post['experience_own3'] = substr($post['experience_own3'],0,-2);

            $post['status'] = 2;



            $nurse_id = M('nurse')->where('id='.$post['id'].'')->save($post);
            if($nurse_id!==false) {
                foreach($unlink as $v){
                    unlink('Uploads/'.$v);
                }
                $save['number'] = 'TLAY-' . str_pad($post['id'], 6, 0, STR_PAD_LEFT);  //6位数不足补0
                $save_mod = M('nurse')->where('id=' . $post['id'] . '')->save($save);
                if ($save_mod === false) {
                    M('nurse')->where('id=' . $post['id'] . '')->save($save);
                }
                echo "<script>alert('成功'); window.location.href='".__MODULE__."/Student/studentInfo_1/id/".$post['id']."'</script>";
                exit;
            }else{
                echo "<script>alert('失败');window.onload=function(){window.history.go(-1);return false;}</script>";
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
            if($info['status']==22){
                echo "<script>alert('已淘汰');window.onload=function(){window.history.go(-1);return false;}</script>";
                exit;
            }
            if($info['type']!=1 || $info['status']<2){
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

            $this->assign('info',$info);
            $this->display();
        }
    }
    //结业操作
    public function status_2(){
        if(!I('post.id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('nurse')->where('id='.I('post.id').'')->find();
        if(!$info){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        if($info['type']!=1 || $info['status']!=1){
            echo "<script>alert('不是学员或者已经结业');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $post = I('post.');
        $post['study_day_true'] = (strtotime($post['finish_time_s'])-strtotime($info['come_time']))/86400;
        $post['status'] = 2;
        $save = M('nurse')->where('id='.I('post.id').'')->save($post);
        if($save!==false){
            echo "<script>alert('结业成功，请完善信息'); window.location.href='".__MODULE__."/Student/doneNurse/id/".$post['id']."'</script>";
            exit;
        }else{
            echo "<script>alert('结业失败，请重新结业');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
    }

    //淘汰
    public function status_24(){
        if(!I('post.id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('nurse')->where('id='.I('post.id').'')->find();
        if(!$info){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $post = I('post.');
        $post['status']=24;
        $save = M('nurse')->where('id='.I('post.id').'')->save($post);
        if($save!==false){
            echo "<script>alert('淘汰成功'); window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }else{
            echo "<script>alert('淘汰失败，请重新操作');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }

    }


    //结业学员
    public function studentInfo_1(){

        if(!I('get.id')){
            echo "<script>alert('地址异常');window.onload=function(){window.history.go(-1);return false;}</script>";
            exit;
        }
        $info = M('nurse')->where('id='.I('get.id').'')->find();
        if(!$info){
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





        $this->assign('info',$info);
        $this->display();
    }




}