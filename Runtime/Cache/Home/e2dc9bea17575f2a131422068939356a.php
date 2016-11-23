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


<?php
 $count1 = M('order')->where('is_read=0 and status=1')->count(); if($_SESSION[C('USER_AUTH_KEY')]['permission'] == 2){ $count2 = M('order')->where('is_read=0 and status=1 and employee_id = '.$_SESSION[C('USER_AUTH_KEY')]['id'].'')->count(); }else{ $count2 = M('order')->where('is_read=0 and status=1')->count(); } ?>



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
	$('.left_menu li').eq(1).addClass('called');
</script>
<!--main-->
<div class="col-md-10 main">
	<div class="tab_tle">
		<span class="int_s"></span>新增派单
	</div>
	<form action="/index.php/Order/addOrder" method="post" id="form" class="form" onsubmit="javascript:return window.confirm('确定派单么？')"  enctype="multipart/form-data">
		<div class="form-group">
			<label>客户姓名：</label><input type="text" class="form-control" name="name" id="name"/>
		</div>
		<div class="form-group">
			<label>联系方式：</label><input type="text" class="form-control"  name="phone" id="phone"/>
		</div>

		<div class="form-group">
			<label>派单类型：</label>

			<select class="form-control"  name="other" id="other">
					<option>月嫂</option>
					<option>育儿嫂</option>
					<option>保姆</option>
					<option>钟点工</option>
				<!--<option>黄显均</option>-->
				<!--<option>黄显均2</option>-->
			</select>
		</div>



		<div class="form-group">
			<label>客户需求：</label>
			<textarea type="text" class="form-control w_240"  name="ask" id="ask"></textarea>
		</div>
		<div class="form-group">
			<label>顾问姓名：</label>

			<select class="form-control"  name="employee_id" id="employee_id">
				<!--<option value="0">全部</option>-->
				<?php if(is_array($employee)): $i = 0; $__LIST__ = $employee;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["real_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>

				<!--<option>黄显均</option>-->
				<!--<option>黄显均2</option>-->
			</select>
		</div>
		<div class="form-group">
			<button type="button" class="default_btn magrin0" id="sub">提交</button>
			<button type="button" class="default_btn" onclick="javascript:history.back(-1);">返回</button>
		</div>
	</form>
</div>
<script>

	var reg=/^(?:(?!0000)[0-9]{4}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-8])|(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[0-9]{2}(?:0[48]|[2468][048]|[13579][26])|(?:0[48]|[2468][048]|[13579][26])00)-02-29)$/;
	//				reg.test(add_time_b);

	$('#sub').live('click',function(){
		if($('#name').val() == ''){
			layer.msg('请输入姓名！');
			return false;
		}
		if($('#phone').val() == ''){
			layer.msg('请输入联系方式！');
			return false;
		}
		if($('#ask').val() == ''){
			layer.msg('请输入客户需求！');
			return false;
		}
		$('#form').submit();
	});


</script>

<!--main end-->
</body>

</html>