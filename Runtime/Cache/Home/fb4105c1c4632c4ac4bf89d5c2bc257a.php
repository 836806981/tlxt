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
				$('.left_menu li').eq(4).addClass('called');
			</script>
			<!--main-->
			<div class="col-md-10 main">
				<div class="tab_tle">
					<span class="int_s"></span>阿姨列表
				</div>
				<div class="form-inline">
					<div class="form-group">
						 <label>姓名</label><input type="text" class="form-control" id="name"/>
					</div>
					<div class="form-group">
						<label>年龄</label><input type="number" class="form-control little" id="age1"/>&nbsp;-&nbsp;<input type="number" class="form-control little" id="age2"/>
					</div>
					<div class="form-group">
						 <label>类型</label>
						 <select class="form-control" id="other">
					         <option value="0" >全部</option>
					         <option value="月嫂">月嫂</option>
					         <option value="育儿嫂">育儿嫂</option>
					         <option value="保姆">保姆</option>
					         <option value="钟点工">钟点工</option>
					     </select>
					</div>
					<div class="form-group">
						 <label>上户状态</label>
						 <select class="form-control" id="status_sh">
					         <option value="0" >全部</option>
					         <option value="1">已上户</option>
					         <option value="2">未上户</option>
					     </select>
					</div>
					<div class="form-group">
						 <label>等级</label>
						 <select class="form-control" id="level_name">
					         <option value="0">全部</option>
							 <option value="1">小田螺1.1</option>
							 <option value="2">小田螺1.2</option>
							 <option value="3">小田螺1.3</option>
							 <option value="4">小田螺1.4</option>
							 <option value="5">小田螺1.5</option>
							 <option value="6">大田螺1.1</option>
							 <option value="7">大田螺1.2</option>
							 <option value="8">大田螺1.3</option>
							 <option value="9">大田螺1.4</option>
							 <option value="10">大田螺1.5</option>
							 <option value="11">超级田螺1.1</option>
							 <option value="12">超级田螺1.2</option>
							 <option value="13">超级田螺1.3</option>
							 <option value="14">超级田螺1.4</option>
							 <option value="15">超级田螺1.5</option>
							 <option value="16">金牌田螺</option>
					     </select>
					</div>
					<div class="form-group">
						 <button type="submit" class="subform" id="search">搜索</button>
					</div>

				</div>
				<table class="tab_info">
					<tbody>
						<tr class="present_num">
							<td colspan="14">
								当前列表数据为：<span id="show_number_str"></span>条
							</td>
						</tr>
						<tr class="info_name">
							<td width="8%">阿姨编号</td>
							<td width="8%">阿姨姓名</td>
							<td width="8%">类型</td>
							<td width="8%">年龄</td>
							<td width="15%">联系方式</td>
							<td width="8%">上户状态</td>
							<td width="8%">等级</td>
							<td width="15%">身份证</td>
							<td width="15%">操作</td>
						</tr>

						<tr class="last_tr">
							<td colspan="14" class="clear">
								<div class="page" id="pagenum" style="margin:0 auto;width: 80%; text-align: left;display: inline-block;">

								</div>
								<div class="btn_modular">
									<button type="button" class="btnform" onclick="location.href='/index.php/Nurse/addNurseInfo.html'">新增阿姨</button>
								</div>
								
							</td>
						</tr>
					</tbody>
				</table>
			</div>

			<script src="/Home/View/Public/js/nurse.js"></script>
			<!--main end-->
</body>

</html>