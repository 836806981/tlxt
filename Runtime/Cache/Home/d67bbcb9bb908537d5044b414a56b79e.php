<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>[田螺阿姨]后台</title>

</head>

<body>

<?php
$press_number = M('order')->where('press_status=1 and status>1 and status<10')->count(); $receive_number = M('order')->where('press_status=2 and status>1 and status<10')->count(); $nursePay_number = M('order_nurse')->where('is_through=1  and true_s_time!="" and order_id!=0')->count(); $nursePayCheck_number = M('order_nurse')->where('is_through=0  and true_s_time!="" and order_id!=0')->count(); ?>




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
				$('.left_menu li').eq(6).addClass('called');
			</script>
			<!--main-->
			<div class="col-md-10 main">
				<ul class="nav nav-tabs">
					<li <?php if($belong == 1): ?>class="active"<?php endif; ?>>
						 <a href="/index.php/Finance/press.html">财务催款</a>
						 <?php if($press_number > 0): ?><em class="new"></em><?php endif; ?>
					</li>
					<li <?php if($belong == 2): ?>class="active"<?php endif; ?>>
						 <a href="/index.php/Finance/receive.html">财务收款</a>
						 <?php if($receive_number > 0): ?><em class="new"></em><?php endif; ?>
					</li>
					<li <?php if($belong == 3): ?>class="active"<?php endif; ?>>
						 <a href="/index.php/Finance/nursePay.html">工资发放</a>
						 <?php if($nursePay_number > 0): ?><em class="new"></em><?php endif; ?>
					</li>
					<li <?php if($belong == 4): ?>class="active"<?php endif; ?>>
						 <a href="/index.php/Finance/nursePayCheck.html">工资审核</a>
						 <?php if($nursePayCheck_number > 0): ?><em class="new"></em><?php endif; ?>
					</li>

					<!--<li <?php if($belong == 5): ?>class="active"<?php endif; ?>>-->
					<!--<a href="/index.php/Finance/studentPay.html">实习工资发放</a>-->
					<!--</li>-->

					<!--<li <?php if($belong == 6): ?>class="active"<?php endif; ?>>-->
					<!--<a href="/index.php/Finance/studentPayCheck.html">实习工资审核</a>-->
					<!--</li>-->
				</ul>
				<div class="form-inline">
					<input type="hidden" id="belong" value="<?php echo ($belong); ?>"/>
					<?php if($belong < 5): ?><div class="form-group">
						 <label>客户姓名</label><input type="text" class="form-control" id="order_name"/>
					</div>
					<div class="form-group">
						 <label>阿姨姓名</label><input type="text" class="form-control" id="nurse_name"/>
					</div><?php endif; ?>


					<?php if($belong > 4): ?><div class="form-group">
							<label>学员姓名</label><input type="text" class="form-control" id="student_name"/>
						</div><?php endif; ?>


					<?php if($belong < 3): ?><div class="form-group">
						 <label>催款状态</label>
						 <select class="form-control" id="press_status">
					         <option value="0">全部</option>
					         <option value="1">未催款</option>
					         <option value="3">已催款</option>
					     </select>
					</div><?php endif; ?>
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
						<?php if($belong == 1): ?><tr class="info_name">
								<td width="8%">客户姓名</td>
								<td width="8%">阿姨姓名</td>
								<td width="8%">订单金额</td>
								<td width="8%">上户时间</td>
								<td width="8%">下户时间</td>
								<td width="8%">是否催款</td>
								<td width="8%">已收款</td>
								<td width="8%">操作</td>
							</tr>

						<?php elseif($belong == 2): ?>
							<tr class="info_name">
								<td width="8%">客户姓名</td>
								<td width="8%">阿姨姓名</td>
								<td width="8%">订单金额</td>
								<td width="8%">上户时间</td>
								<td width="8%">下户时间</td>
								<td width="8%">是否催款</td>
								<td width="8%">已收款</td>
								<td width="8%">操作</td>
							</tr>

						<?php elseif($belong == 3): ?>
							<tr class="info_name">
								<td width="8%">客户姓名</td>
								<td width="8%">阿姨姓名</td>
								<td width="8%">订单金额</td>
								<td width="8%">上户时间</td>
								<td width="8%">下户时间</td>
								<td width="8%">上单时长</td>
								<td width="8%">阿姨工资</td>
								<td width="8%">实发工资</td>
								<td width="8%">通过</td>
								<td width="8%">支付</td>
								<td width="8%">操作</td>
							</tr>
						<?php elseif($belong == 4): ?>
							<tr class="info_name">
								<td width="8%">客户姓名</td>
								<td width="8%">阿姨姓名</td>
								<td width="8%">订单金额</td>
								<td width="8%">上户时间</td>
								<td width="8%">下户时间</td>
								<td width="8%">上单时长</td>
								<td width="8%">阿姨工资</td>
								<td width="8%">实发工资</td>
								<td width="8%">通过</td>
								<td width="8%">支付</td>
								<td width="8%">操作</td>
							</tr>
							<?php elseif($belong == 5): ?>
							<tr class="info_name">
								<td width="8%">学员姓名</td>
								<td width="8%">上户日期</td>
								<td width="8%">下户日期</td>
								<td width="8%">上单时长</td>
								<td width="8%">学员工资</td>
								<td width="8%">实发工资</td>
								<td width="8%">通过</td>
								<td width="8%">支付</td>
								<td width="8%">操作</td>
							</tr>
							<?php elseif($belong == 6): ?>
							<tr class="info_name">
								<td width="8%">学员姓名</td>
								<td width="8%">上户日期</td>
								<td width="8%">下户日期</td>
								<td width="8%">上单时长</td>
								<td width="8%">学员工资</td>
								<td width="8%">实发工资</td>
								<td width="8%">通过</td>
								<td width="8%">支付</td>
								<td width="8%">操作</td>
							</tr><?php endif; ?>

						<!--<tr>-->
							<!--<td>李翰宇</td>-->
							<!--<td>李翰宇</td>-->
							<!--<td>512323</td>-->
							<!--<td>2011.1.1</td>-->
							<!--<td>2011.1.1</td>-->
							<!--<td>是</td>-->
							<!--<td>111</td>-->
							<!--<td><a onclick="addRecord()">催款记录</a><a href="detailsInfo.html">查看</a></td>-->
						<!--</tr>-->
						<tr class="last_tr">
							<td colspan="14" class="clear">
								<div class="page" id="pagenum" style="margin:0 auto;width: 80%; text-align: left;display: inline-block;">

								</div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>


			<script src="/Home/View/Public/js/press.js"></script>

			<!--main end-->
			<script>
				var a=/^[0-9]*(\.[0-9]{1,2})?$/;
				var reg=/^(?:(?!0000)[0-9]{4}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-8])|(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[0-9]{2}(?:0[48]|[2468][048]|[13579][26])|(?:0[48]|[2468][048]|[13579][26])00)-02-29)$/;

				//					新增催款记录
						$('.add_record').live('click',function(){
						var submit = "javascript:return window.confirm('确认新增催款记录？')";
						layer.open({
						  	type: 1,
						  	title: '新增催款记录',
							area: ['auto', 'auto'], //宽高
							content: '<div class="lay_cnt">'
							+   '<form action="/index.php/Finance/addPress" method="post" id="form_press" class="form" onsubmit="'+submit+'"  enctype="multipart/form-data">'
									+	'<div class="opn_div clear">'
									+		'<label>催款结果：</label><input type="text" class="form-control" value="" name="remark" id="remark"/><input type="hidden" value="'+$(this).attr('order_id')+'" name="order_id" id="order_id"/>'
									+	'</div>'
									+	'<div class="opn_div clear">'
									+		'<label>催款时间：</label><input type="text" class="form-control" value="" name="press_time" id="press_time" placeholder="2016-01-01"/>'
									+	'</div>'
									+	'<div class="btn_div clear">'
									+		'<button type="button" class="default_btn" id="sub_press">添加</button>'
									+		'<button type="button" class="default_btn" onclick="close_lay()">取消</button>'
									+	'</div>'
									+	'</form>'
									+'</div>'
						});
					});
//提交催款记录
					$('#sub_press').live('click',function(){

						if($('#remark').val()==''){
							layer.msg('请输入催款结果！');
							return false;
						}
						if($('#order_id').val()==''){
							layer.msg('异常请重新新增！');
							return false;
						}
						if(!reg.test($('#press_time').val()) || $('#press_time').val()==''){
							layer.msg('请输入催款时间！');
							return false;
						}
						$('#form_press').submit();
					});



				//新增收款记录

				$('.add_get').live('click',function(){
					var submit = "javascript:return window.confirm('确认新增收款记录？')";
					layer.open({
						type: 1,
						title: '新增收款记录',
						area: ['auto', 'auto'], //宽高
						content: '<div class="lay_cnt">'
						+   '<form action="/index.php/Finance/addGet" method="post" id="form_get" class="form" onsubmit="'+submit+'"  enctype="multipart/form-data">'
						+	'<div class="opn_div clear">'
						+		'<label>付款客户：</label><input type="text" class="form-control" value="'+$(this).attr('order_name')+'" name="name" />'
						+	'</div>'
						+	'<div class="opn_div clear">'
						+		'<label>收款金额：</label><input type="text" class="form-control" value="" name="get_money" id="get_money"/><input type="hidden" value="'+$(this).attr('order_id')+'" name="order_id" id="order_id"/>'
						+	'</div>'
						+	'<div class="opn_div clear">'
						+		'<label>收款时间：</label><input type="text" class="form-control" value="" name="get_time" id="get_time" placeholder="2016-01-01"/>'
						+	'</div>'
						+	'<div class="opn_div clear">'
						+		'<label>收款备注：</label><input type="text" class="form-control" value="" name="remark" id="remark" />'
						+	'</div>'
						+	'<div class="btn_div clear">'
						+		'<button type="button" class="default_btn" id="sub_get">添加</button>'
						+		'<button type="button" class="default_btn" onclick="close_lay()">取消</button>'
						+	'</div>'
						+	'</form>'
						+'</div>'
					});
				});
				$('#sub_get').live('click',function(){
					if(!a.test($('#get_money').val()) || $('#get_money').val()=='') {
						layer.msg('请输入正确的收款金额！');
						return false;
					}
					if($('#order_id').val()==''){
						layer.msg('异常请重新新增！');
						return false;
					}
					if(!reg.test($('#get_time').val()) || $('#get_time').val()==''){
						layer.msg('请输入收款时间！');
						return false;
					}
					if($('#remark').val()==''){
						layer.msg('请填写收款备注！');
						return false;
					}
					$('#form_get').submit();
				});

				//发工资

				$('.pay_status').live('click',function(){
					var submit = "javascript:return window.confirm('确认发放工资？')";
					layer.open({
						type: 1,
						title: '发放工资',
						area: ['auto', 'auto'], //宽高
						content: '<div class="lay_cnt">'
						+   '<form action="/index.php/Finance/payStatus" method="post" id="form_pay_status" class="form" onsubmit="'+submit+'"  enctype="multipart/form-data">'
						+	'<div class="opn_div clear">'
						+		'<label>客户：</label><input type="text" class="form-control" value="'+$(this).attr('order_name')+'" readonly/><input type="hidden" name="id" value="'+$(this).attr('id')+'"/>'
						+	'</div>'
						+	'<div class="opn_div clear">'
						+		'<label>阿姨：</label><input type="text" class="form-control" value="'+$(this).attr('nurse_name')+'" readonly/>'
						+	'</div>'
						+	'<div class="opn_div clear">'
						+		'<label>银行卡：</label><input placeholder="请填写银行卡信息" type="text" class="form-control" value="'+($(this).attr('bank_card')?$(this).attr('bank_card'):'')+'" id="bank_card" name="bank_card"/>'
						+	'</div>'
						+	'<div class="opn_div clear">'
						+		'<label>实发工资：</label><input type="text" class="form-control" value="'+$(this).attr('nurse_pay_true')+'" name="nurse_pay_true" id="nurse_pay_true" readonly/>'
						+	'</div>'
						+	'<div class="opn_div clear">'
						+		'<label>下户备注：</label><input type="text" class="form-control" value="'+$(this).attr('remark')+'" name="remark" id="remark" />'
						+	'</div>'
						+	'<div class="btn_div clear">'
						+		'<button type="button" class="default_btn" id="sub_pay_status">已发</button>'
						+		'<button type="button" class="default_btn" onclick="close_lay()">取消</button>'
						+	'</div>'
						+	'</form>'
						+'</div>'
					});
				});
				$('#sub_pay_status').live('click',function(){
					if($('#remark').val()==''){
						layer.msg('请填写下户备注！');
						return false;
					}
					if($('#bank_card').val()==''){
						layer.msg('请填写打款银行卡信息！');
						return false;
					}
					$('#form_pay_status').submit();
				});



				//审核

				$('.do_through').live('click',function(){
					var submit = "javascript:return window.confirm('确认审核工资？')";
					layer.open({
						type: 1,
						title: '审核阿姨工资',
						area: ['auto', 'auto'], //宽高
						content: '<div class="lay_cnt">'
						+   '<form action="/index.php/Finance/doThrough" method="post" id="form_through" class="form" onsubmit="'+submit+'"  enctype="multipart/form-data">'
						+	'<div class="opn_div clear">'
						+		'<label>客户：</label><input type="text" class="form-control" value="'+$(this).attr('order_name')+'" readonly/><input type="hidden" name="id" value="'+$(this).attr('id')+'"/>'
						+	'</div>'
						+	'<div class="opn_div clear">'
						+		'<label>阿姨：</label><input type="text" class="form-control" value="'+$(this).attr('nurse_name')+'" readonly/>'
						+	'</div>'
						+	'<div class="opn_div clear">'
						+		'<label>阿姨工资：</label><input type="text" class="form-control" value="'+$(this).attr('nurse_pay')+'" readonly/>'
						+	'</div>'
						+	'<div class="opn_div clear">'
						+		'<label>上单时长：</label><input type="text" class="form-control" value="'+$(this).attr('do_time')+'" readonly/>'
						+	'</div>'
						+	'<div class="opn_div clear">'
						+		'<label>工资审核：</label><input type="text" class="form-control" value="'+$(this).attr('nurse_pay_true')+'" name="nurse_pay_true" id="nurse_pay_true" />'
						+	'</div>'
						+	'<div class="opn_div clear">'
						+		'<label>下户备注：</label><input type="text" class="form-control" value="'+$(this).attr('remark')+'" name="remark" id="remark" />'
						+	'</div>'
						+	'<div class="btn_div clear">'
						+		'<button type="button" class="default_btn" id="sub_through">审核</button>'
						+		'<button type="button" class="default_btn" onclick="close_lay()">取消</button>'
						+	'</div>'
						+	'</form>'
						+'</div>'
					});
				});
				$('#sub_through').live('click',function(){
					if(!a.test($('#nurse_pay_true').val()) || $('#nurse_pay_true').val()=='') {
						layer.msg('请输入正确的工资金额！');
						return false;
					}
					if($('#remark').val()==''){
						layer.msg('请填写下户备注！');
						return false;
					}
					$('#form_through').submit();
				});





				//学员发工资

				$('.student_pay_status').live('click',function(){
					var submit = "javascript:return window.confirm('确认发放实习工资？')";
					layer.open({
						type: 1,
						title: '发放工资',
						area: ['auto', 'auto'], //宽高
						content: '<div class="lay_cnt">'
						+   '<form action="/index.php/Finance/payStudentStatus" method="post" id="form_student_pay_status" class="form" onsubmit="'+submit+'"  enctype="multipart/form-data">'
						+	'<div class="opn_div clear">'
						+		'<label>学员姓名：</label><input type="text" class="form-control" value="'+$(this).attr('student_name')+'" readonly/><input type="hidden" name="id" value="'+$(this).attr('id')+'"/>'
						+	'</div>'
						+	'<div class="opn_div clear">'
						+		'<label>上户日期：</label><input type="text" class="form-control" value="'+$(this).attr('true_b_time')+'" readonly/>'
						+	'</div>'
						+	'<div class="opn_div clear">'
						+		'<label>下户日期：</label><input type="text" class="form-control" value="'+$(this).attr('true_s_time')+'" readonly/>'
						+	'</div>'
						+	'<div class="opn_div clear">'
						+		'<label>上单时长：</label><input type="text" class="form-control" value="'+$(this).attr('do_time')+'" readonly/>'
						+	'</div>'
						+	'<div class="opn_div clear">'
						+		'<label>需发工资：</label><input type="text" class="form-control" value="'+$(this).attr('student_pay')+'" readonly/>'
						+	'</div>'
						+	'<div class="opn_div clear">'
						+		'<label>实发工资：</label><input type="number" class="form-control" value="'+$(this).attr('student_pay_true')+'" readonly />'
						+	'</div>'
						+	'<div class="btn_div clear">'
						+		'<button type="button" class="default_btn" id="sub_student_pay_status">已发</button>'
						+		'<button type="button" class="default_btn" onclick="close_lay()">取消</button>'
						+	'</div>'
						+	'</form>'
						+'</div>'
					});
				});
				$('#sub_student_pay_status').live('click',function(){
					if($('#bank_card').val()==''){
						layer.msg('请填写打款银行卡信息！');
						return false;
					}
					$('#form_student_pay_status').submit();
				});


				//学员工资审核

				$('.student_do_through').live('click',function(){
					var submit = "javascript:return window.confirm('确认审核学员工资？')";
					layer.open({
						type: 1,
						title: '审核学员工资',
						area: ['auto', 'auto'], //宽高
						content: '<div class="lay_cnt">'
						+   '<form action="/index.php/Finance/doStudentThrough" method="post" id="form_student_through" class="form" onsubmit="'+submit+'"  enctype="multipart/form-data">'
						+	'<div class="opn_div clear">'
						+		'<label>学员姓名：</label><input type="text" class="form-control" value="'+$(this).attr('student_name')+'" readonly/><input type="hidden" name="id" value="'+$(this).attr('id')+'"/>'
						+	'</div>'
						+	'<div class="opn_div clear">'
						+		'<label>上户日期：</label><input type="text" class="form-control" value="'+$(this).attr('true_b_time')+'" readonly/>'
						+	'</div>'
						+	'<div class="opn_div clear">'
						+		'<label>下户日期：</label><input type="text" class="form-control" value="'+$(this).attr('true_s_time')+'" readonly/>'
						+	'</div>'
						+	'<div class="opn_div clear">'
						+		'<label>上单时长：</label><input type="text" class="form-control" value="'+$(this).attr('do_time')+'" readonly/>'
						+	'</div>'
						+	'<div class="opn_div clear">'
						+		'<label>需发工资：</label><input type="text" class="form-control" value="'+$(this).attr('student_pay')+'" readonly/>'
						+	'</div>'
						+	'<div class="opn_div clear">'
						+		'<label>实发工资：</label><input type="number" class="form-control" value="" name="student_pay_true" id="student_pay_true" />'
						+	'</div>'
						+	'<div class="btn_div clear">'
						+		'<button type="button" class="default_btn" id="sub_student_through">审核</button>'
						+		'<button type="button" class="default_btn" onclick="close_lay()">取消</button>'
						+	'</div>'
						+	'</form>'
						+'</div>'
					});
				});
				$('#sub_student_through').live('click',function(){
					if(!a.test($('#student_pay_true').val()) || $('#student_pay_true').val()=='') {
						layer.msg('请输入正确的实发工资金额！');
						return false;
					}
					$('#form_student_through').submit();
				});

					function close_lay(){
						layer.closeAll();
					}



			</script>
</body>

</html>