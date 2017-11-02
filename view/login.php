<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<title>登录-后台管理系统</title>
<meta name="keywords"  content="设置关键词..." />
<meta name="description" content="设置描述..." />
<meta name="author" content="DeathGhost" />
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name='apple-touch-fullscreen' content='yes'>
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="format-detection" content="telephone=no">
<meta name="format-detection" content="address=no">
<link rel="icon" href="images/icon/favicon.ico" type="image/x-icon">
<link rel="stylesheet" type="text/css" href="css/style.css" />
<script src="javascript/jquery.js"></script>
<script src="javascript/public.js"></script>
<script src="javascript/plug-ins/customScrollbar.min.js"></script>
</head>
<body class="login-page">
	<section class="login-contain">
		<header>
			<h1>巴士管理系统</h1>
			<p>management system</p>
		</header>
		<div class="form-content">
			<ul>
				<li>
					<div class="form-group">
						<label class="control-label">管理员账号：</label>
						<input type="text" placeholder="" class="form-control form-underlined" id="adminName"/>
					</div>
				</li>
				<li>
					<div class="form-group">
						<label class="control-label">管理员密码：</label>
						<input type="password" placeholder="" class="form-control form-underlined" id="adminPwd"/>
					</div>
				</li>
				<!--
				<li>
					<label class="check-box">
						<input type="checkbox" name="remember"/>
						<span>记住账号密码</span>
					</label>
				</li>-->
				<li>
					<button class="btn btn-lg btn-block" id="entry">立即登录</button>
				</li>
			</ul>
		</div>
	</section>
<div class="mask"></div>
<script type="text/javascript">

	var tmp_req_url = 'adminController.php';
	var AJAX_TIMEOUT = 2000;
	
	$('#dialog_button').click(function(){
		$('.mask,.dialog').hide();
	});
	
	
	$('#entry').click(function(){
		if($('#adminName').val()==''){
			$('.mask,.dialog').show();
			$('.dialog .dialog-bd p').html('请输入管理员账号');
		}else if($('#adminPwd').val()==''){
			$('.mask,.dialog').show();
			$('.dialog .dialog-bd p').html('请输入管理员密码');
		}else{
			//$('.mask,.dialog').hide();
			var params = {'user_account':$('#adminName').val(), 'user_passwd':$('#adminPwd').val()};
			var post_data = {'action': 'admin_login','params':params};
			
			$.ajax({
				type        : 'post',
				url         : tmp_req_url,
				async		: false,
				data        : {'request':JSON.stringify(post_data)},
				dataType    : 'json',
				success     : function(data) {
					//alert(data['suc']);
					if(data['suc'] == 1){
						window.location.href=data['url'];
					}else{
						$('.mask,.dialog').show();
						$('.dialog .dialog-bd p').html('用户名或密码错误');
					}
					
				}
			})
		}
	});
</script>
<div class="dialog">
	<div class="dialog-hd">
		<strong class="lt-title">提示</strong>
		<!--<a class="rt-operate icon-remove JclosePanel" title="关闭"></a>-->
	</div>
	<div class="dialog-bd">
		<!--start::-->
		<p>这里是基础弹窗,可以定义文本信息，HTML信息这里是基础弹窗,可以定义文本信息，HTML信息。</p>
		<!--end::-->
	</div>
	<div class="dialog-ft">
		<button class="btn btn-info JyesBtn" id="dialog_button">确认</button>
	</div>
</div>
</body>
</html>
