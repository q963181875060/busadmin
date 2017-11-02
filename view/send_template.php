<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<title>后台管理系统-HTML5后台管理系统</title>
<meta name="keywords"  content="设置关键词..." />
<meta name="description" content="设置描述..." />
<meta name="author" content="DeathGhost" />
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
<link rel="icon" href="images/icon/favicon.ico" type="image/x-icon">
<link rel="stylesheet" type="text/css" href="css/style.css" />
<script src="javascript/jquery.js"></script>
<script src="javascript/plug-ins/customScrollbar.min.js"></script>
<script src="javascript/plug-ins/echarts.min.js"></script>
<script src="javascript/plug-ins/layerUi/layer.js"></script>
<script src="editor/ueditor.config.js"></script>
<script src="editor/ueditor.all.js"></script>
<script src="javascript/plug-ins/pagination.js"></script>
<script src="javascript/public.js"></script>
</head>
<body>
<div class="main-wrap">
	<div class="side-nav">
		<?php include 'nav.php'; ?>
	</div>
	<div class="content-wrap">
		<header class="top-hd">
			<?php include 'header.php'; ?>
		</header>
		<main class="main-cont content mCustomScrollbar" id="route_list_main">
			<div class="page-wrap">
				<!--开始::内容-->
				<section class="page-hd">
					<header>
						<h2 class="title">消息通知</h2>
						<p class="title-description">
							通知以公众号模板消息的形式发送给用户，用户列表一行一个，无分隔符。
						</p>
					</header>
					<hr>
					<?php
						echo '<div id="user_ids" style="display:none">'.$_SESSION['user_ids'].'</div>';
						unset($_SESSION['user_ids']);
					?>
				</section>
							
							
	
				<div class="form-group-col-2">
					<div class="form-label">用户列表：</div>
					<div class="form-cont">
						<textarea id="user_list" class="form-control" contenteditable="true" style="overflow-y:scroll;min-height:300px">
						</textarea>	
					</div>
				</div>
									
				<div class="form-group-col-2">
					<div class="form-label">标题：</div>
					<div class="form-cont">
						<input id="first" type="text" placeholder="" class="form-control form-boxed">
					</div>
				</div>
				<div class="form-group-col-2">
					<div class="form-label"></div>
					<div class="form-cont">
						<input onclick='send_template()' type="submit" class="btn btn-primary" value="发送" />
					</div>
					
				</div>
				<!--开始::结束-->
			</div>
		</main>
		
		<script type="text/javascript">
			var tmp_req_url = 'adminController.php';
			var AJAX_TIMEOUT = 2000;
		
			$('#user_list').text($('#user_ids').html().trim());
			
			function send_template(){
				var tmp_userList = $('#user_list').val().split('\n',-1);
				var user_ids = new Array();
				for(var i=0;i<tmp_userList.length; i++){
					var user = tmp_userList[i];
					if(user.trim() != ""){
						
						user_ids.push(user.trim());
					}
				}
				
				if(window.confirm('【敏感操作】你确定要为这'+user_ids.length+'个人发送消息吗？')){
					var params = {};
					params['first'] = $('#first').val();
					var post_data = {'action': 'send_template', 'user_ids':user_ids, 'params':params};
					$.ajax({
						type        : 'post',
						url         : tmp_req_url,
						async		: false,
						data        : {'request':JSON.stringify(post_data)},
						dataType    : 'json',
						success     : function(data) {
							if(data['suc'] == 1){
								alert('发送成功！');
							}
						}
					})
				}
			}
			
		</script>
		
		
		<footer class="btm-ft">
			<?php
				include 'footer.php';
			?>
		</footer>
	</div>
</div>

</body>
</html>
