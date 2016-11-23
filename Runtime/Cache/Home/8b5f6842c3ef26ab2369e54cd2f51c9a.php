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
				$('.left_menu li').eq(3).addClass('called');
			</script>
			<!--main-->
			<div class="col-md-10 main">
				<ul class="nav nav-tabs">
					<li <?php if($belong == 1): ?>class="active"<?php endif; ?>>
						 <a href="/index.php/OrderShow/orderList.html">订单列表</a>
					</li>
					<li <?php if($belong == 2): ?>class="active"<?php endif; ?>>
						 <a href="/index.php/OrderShow/onOrderList.html">上单列表</a>
					</li>
					<li <?php if($belong == 3): ?>class="active"<?php endif; ?>>
						 <a href="/index.php/OrderShow/downOrderList.html">下单列表</a>
					</li>

					<li <?php if($belong == 4): ?>class="active"<?php endif; ?>>
					<a href="/index.php/OrderShow/onOrderDoList.html">上户提醒</a>
					</li>

					<li <?php if($belong == 5): ?>class="active"<?php endif; ?>>
					<a href="/index.php/OrderShow/downOrderDoList.html">下户提醒</a>
					</li>
				</ul>
				<div class="form-inline">
					<div class="form-group">
						 <label>客户姓名</label><input type="text" class="form-control" id="name" />
						<input type="hidden"  id="belong" value="<?php echo ($belong); ?>"/>
					</div>
					<?php if($_SESSION[C('USER_AUTH_KEY')]['permission'] == 11): ?><div class="form-group">
						 <label>顾问</label>
						<select class="form-control" id="employee_id">
							<option value="0">全部</option>
							<?php if(is_array($employee)): $i = 0; $__LIST__ = $employee;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["real_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
							<!--<option>黄显均</option>-->
							<!--<option>黄显均2</option>-->
						</select>
					</div><?php endif; ?>
					<div class="form-group">
						 <label>类型</label>
						 <select class="form-control"  id="other" >
					         <option value="0">全部</option>
					         <option>月嫂</option>
					         <option>育儿嫂</option>
					         <option>保姆</option>
					         <option>钟点工</option>
					     </select>
					</div>
					<div class="form-group">
						 <label>联系方式</label><input type="text" class="form-control" id="phone"/>
					</div>
					<div class="form-group">
						 <button type="submit" class="subform" id="search">搜索</button>
					</div>
					
				</div>
				<table class="tab_info">
					<tbody>
						<tr class="present_num">
							<td colspan="14">
								当前搜索订单：<span id="show_number_str"></span>条
							</td>
						</tr>

						<?php if($belong == 1): ?><tr class="info_name">
								<td >客户姓名</td>
								<td >类型</td>
								<td >联系方式</td>
								<td >阿姨姓名</td>
								<td >顾问</td>
								<td >上户时间</td>
								<td >下户时间</td>
								<td >状态</td>
								<td >订单金额</td>
								<td >阿姨工资</td>
								<td >操作</td>
							</tr>
							<?php elseif($belong == 2): ?>
							<tr class="info_name">
								<td >客户姓名</td>
								<td >类型</td>
								<td >联系方式</td>
								<td >阿姨姓名</td>
								<td >顾问</td>
								<td >上户时间</td>
								<td >下户时间</td>
								<td >订单金额</td>
								<td >阿姨工资</td>
								<td >操作</td>
							</tr>


							<?php elseif($belong == 3): ?>
							<tr class="info_name">
								<td >客户姓名</td>
								<td >类型</td>
								<td >联系方式</td>
								<td >阿姨姓名</td>
								<td >顾问</td>
								<td >上户时间</td>
								<td >下户时间</td>
								<td >订单金额</td>
								<td >阿姨工资</td>
								<td >操作</td>
							</tr>



							<?php elseif($belong == 4): ?>

							<tr class="info_name">
								<td >客户姓名</td>
								<td >类型</td>
								<td >联系方式</td>
								<td >阿姨姓名</td>
								<td >顾问</td>
								<td >上户时间</td>
								<td >下户时间</td>
								<td >订单金额</td>
								<td >阿姨工资</td>
								<td >操作</td>
							</tr>


							<?php elseif($belong == 5): ?>

							<tr class="info_name">
								<td >客户姓名</td>
								<td >类型</td>
								<td >联系方式</td>
								<td >阿姨姓名</td>
								<td >顾问</td>
								<td >上户时间</td>
								<td >下户时间</td>
								<td >订单金额</td>
								<td >阿姨工资</td>
								<td >操作</td>
							</tr><?php endif; ?>


						<!--<tr>-->
							<!--<td>李翰宇</td>-->
							<!--<td>李翰宇</td>-->
							<!--<td>18382389676</td>-->
							<!--<td>无李翰宇c</td>-->
							<!--<td>2016-7-21 15:50:49</td>-->
							<!--<td>2016-7-21 15:50:49</td>-->
							<!--<td>201</td>-->
							<!--<td>201</td>-->
							<!--<td><a href="orderInfo.html">查看</a><a href="#">删除</a></td>-->
						<!--</tr>-->
						<tr class="last_tr">
							<td colspan="14" class="clear">
								<div class="page" id="pagenum" style="margin:0 auto;width: 80%; text-align: left;display: inline-block;">

								</div>
								<!--<div class="btn_modular">
									<button type="button" class="btnform" onclick="location.href='addOrder.html'">新增订单</button>
								</div>-->
								
							</td>
						</tr>
					</tbody>
				</table>
			</div>


			<script src="/Home/View/Public/js/orderShow.js"></script>

			<!--main end-->
</body>

</html>