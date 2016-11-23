<?php if (!defined('THINK_PATH')) exit();?> <!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>[田螺阿姨]后台</title>
		<link rel="stylesheet" href="/Home/View/Public/css/login.css" />
		<script type="text/javascript" src="/Home/View/Public/js/jquery-1.7.1.min.js" ></script>
		<script type="text/javascript" src="/Home/View/Public/layer/layer.js" ></script>
		<script src="/Home/View/Public/js/jquery.longyuJs.js"></script>
	</head>
	<body>
		<div class="page">
			<div class="content">
				<form action="/index.php/Index/login" method="post" id="form" class="form"   enctype="multipart/form-data">
				<div class="ipt">
					<input type="text" class="txt" value="" placeholder="账号" name="username" id="username"/>
					<input type="password" class="txt" value="" placeholder="密码" name="pwd" id="pwd"/>
					<input type="button" class="btn" value="登录" id="sub_login"/>
				</div>
				</form>
			</div>
			<script>
				$('#sub_login').live('click',function(){
					if($('#username').val() == ''){
						layer.msg('请输入账号！');
						return false;
					}
					if($('#pwd').val() == ''){
						layer.msg('请输入密码！');
						return false;
					}


					$('#form').submit();
				});
				$("body").keydown(function() {
		             if (event.keyCode == "13") {
		                 $('#form').submit();
		             }
		        });
			</script>
		</div>
	</body>
</html>