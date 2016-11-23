<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>[田螺阿姨]后台</title>



</head>

<body>
			<!--top-->
			<link href="/Home/View/Public/css/bootstrap.min.css" rel="stylesheet">
<link href="/Home/View/Public/css/style.css" rel="stylesheet">
<script type="text/javascript" src="/Home/View/Public/js/jquery-1.7.1.min.js" ></script>
<script type="text/javascript" src="/Home/View/Public/js/bootstrap.min.js" ></script>
<script type="text/javascript" src="/Home/View/Public/layer/layer.js" ></script>
<script src="/Home/View/Public/js/common.js"></script>
<script src="/Home/View/Public/js/jquery.longyuJs.js"></script>


<div class="page-header">
    <div class="top_right">
        <a href="/index.php/Index/login_out"><button type="button" class="out">退出系统</button></a>
    </div>
</div>
<!--top end-->
<!--left-->





<div class="col-md-2 column mune">
    <div class="head_img">
        <a href="/index.php/User/userInfo/id/<?php echo ($_SESSION[C('USER_AUTH_KEY')]['id']); ?>.html"><img src="/Home/View/Public/img/user_img.jpg" /></a>
    </div>
    <div class="user_info">
        <p class="name"><?php echo ($_SESSION[C('USER_AUTH_KEY')]['real_name']); ?></p>
        <p class="job"><?php echo ($_SESSION[C('USER_AUTH_KEY')]['permission_name']); ?></p>
    </div>
    <ul class="left_menu">
        <li><a href="/index.php/"><span class="dingdan">首<i></i>页</span></a></li>

        <li style="<?php if(!in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(11,1))){echo 'display:none;';}?>"><a href="/index.php/Order/orderList.html"><span class="dingdan">派单列表 <?php if($count1 > 0): ?><em class="new"></em><?php endif; ?></span></a></li>
        <li style="<?php if(!in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(11,2))){echo 'display:none;';}?>"><a href="/index.php/Order/fileList.html"><span class="dingdan">接单列表<?php if($count2 > 0): ?><em class="new"></em><?php endif; ?></span></a></li>
        <li style="<?php if(!in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(11,2,3))){echo 'display:none;';}?>"><a href="/index.php/OrderShow/orderList.html"><span class="dingdan">订单管理</span></a></li>
        <li style="<?php if(!in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(11,2,3))){echo 'display:none;';}?>"><a href="/index.php/Nurse/nurseList.html"><span class="dingdan">阿姨管理</span></a></li>
        <li style="<?php if(!in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(11,3))){echo 'display:none;';}?>"><a href="/index.php/Insurance/index.html"><span class="dingdan">保险列表</span></a></li>
        <li style="<?php if(!in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(11,2,4))){echo 'display:none;';}?>"><a href="/index.php/Finance/press.html"><span class="dingdan">财务统计</span></a></li>
        <li style="<?php if(!in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(11))){echo 'display:none;';}?>"><a href="/index.php/User/userList.html"><span class="dingdan">员工管理</span></a></li>
        <li style="display:none;<?php if(!in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(11,5))){echo 'display:none;';}?>"><a href="/index.php/Student/studentList.html"><span class="dingdan">学员管理</span></a></li>
        <li style="<?php if(!in_array($_SESSION[C('USER_AUTH_KEY')]['permission'],array(11))){echo 'display:none;';}?>"><a href="/index.php/Nurse/nurseUpList.html"><span class="dingdan">阿姨升级</span></a></li>
    </ul>
</div>


<script>
    var MODULE  =  '/index.php';
    var ROOT  =  '';
    var UPLOADS  =  '/Uploads';

</script>
			<!--left end-->
			<script>
				$('.left_menu li').eq(7).addClass('called');
			</script>
			<!--main-->
			<div class="col-md-10 main">
				<div class="tab_tle">
					<span class="int_s"></span>添加员工
				</div>
				<form action="/index.php/User/addUser" method="post" id="form" onsubmit="javascript:return window.confirm('确定添加么？')"  enctype="multipart/form-data">
					<div class="filesInfo form">
						<div class="form-group">
							 <label>用户名：</label><input type="text" class="form-control" name="username" value=""/>
						</div>
						<div class="form-group">
							 <label>密&emsp;码：</label>系统默认:tlay123
						</div>
						<div class="form-group">
							 <label>昵&emsp;称：</label><input type="text" class="form-control" name="real_name" value=""/>
						</div>
						<div class="form-group">
							 <label>权&emsp;限：</label>
							 <span class="spa_cek"><input type="radio" name="permission"  value="1"/>客服</span>
							 <span class="spa_cek"><input type="radio"  checked="checked" name="permission" value="2"/>顾问</span>
							 <span class="spa_cek"><input type="radio"  name="permission" value="3"/>内勤管理</span>
							 <span class="spa_cek"><input type="radio"  name="permission" value="4"/>财务</span>
							 <span class="spa_cek"><input type="radio"  name="permission" value="5"/>学员管理</span>
						</div>
					</div>
					<div class="form-group" style="margin-left: 19px;">
						<button type="button" class="default_btn magrin0" id="sub">提交</button>
						<button type="button" class="default_btn" onclick="javascript:history.go(-1);">返回</button>
					</div>
				</form>
				
			</div>


			<script>
				$('#sub').live('click',function(){
					if($('input[name=username]').val() == ''){
						layer.msg('请输入用户名！');
						return false;
					}else if($('input[name=real_name]').val() == ''){
						layer.msg('请输入昵称！');
						return false;
					}

					$('#form').submit();
				});



			</script>

			<!--main end-->
</body>

</html>