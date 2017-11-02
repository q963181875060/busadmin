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
	<div class="side-nav"><?php include 'nav.php'; ?></div>
	<div class="content-wrap">
		<header class="top-hd"><?php include 'header.php'; ?></header>
		<main class="main-cont content mCustomScrollbar" id="route_list_main">
			<div class="page-wrap">
				<!--开始::内容-->
				<section class="page-hd">
					<header>
						<h2 class="title">人员管理</h2>
						<p class="title-description">
							添加验票员流程：1.只填姓名、手机号（必填，且不可改）、角色（填验票员） 2.让验票员通过微信发送自己手机号给合力巴士公众号，则状态自动变更为已绑定
						</p>
						<p class="title-description">
							状态有三种：（空），已绑定，已解绑。若要解绑验票员，设置其状态为“已解绑”即可，若开启，则设置状态为空，并进入上面的流程。
						</p>
					</header>
					<hr>
				</section>
				<table class="table mb-15">
					<thead>
						<tr>
							<th><input type="checkbox"/></th>
							<?php
								include 'adminLogicController.php';
								$template = get_admin_verify_users_template();
								foreach($template as $key=>$value){
									echo '<th>'.$value.'</th>';
								}
							?>
							<th>操作</th>
						</tr>
					</thead>
					<tbody id="route_tbody">
					<?php
					
					$units = get_admin_verify_users();
					foreach($units as $key_out=>$unit){
						
						echo '<tr class="cen" id="uneditable_tr_'.$unit['mobile'].'">
								<td><input type="checkbox"/></td>';
						
						foreach($unit as $key=>$value){
							echo '<td>'.$value.'</td>';
						}
						echo '
							<td>
								<a title="编辑" class="mr-5" onclick="edit('.$unit['mobile'].')">编辑</a>
								<a title="删除" class="mr-5" onclick="delete_('.$unit['mobile'].')">删除</a>
							</td>
						</tr>';
						
						echo '<tr class="cen" id="editable_tr_'.$unit['mobile'].'"  style="display:none">
								<td><input type="checkbox"/></td>';
						foreach($unit as $key=>$value){
							echo '<td><textarea class="new_textarea_'.$unit['mobile'].'" style="height:300px" class="form-control form-boxed" id="'.$key.'">'.$value.'</textarea></td>';
						}
						echo '<td>
								<a title="保存" class="mr-5" onclick="save('.$unit['mobile'].')">保存</a>
								<a title="取消" class="mr-5" onclick="cancel('.$unit['mobile'].')">取消</a>
							</td>
						</tr>';
					}
					
					echo '<tr class="cen" id="editable_tr_-1"  style="display:none">
							<td><input type="checkbox"/></td>';
					foreach($template as $key=>$value){
						echo '<td><textarea class="new_textarea_-1" style="height:300px" class="form-control form-boxed" id="'.$key.'"></textarea></td>';
					}
					echo '	<td>
								<a title="保存" class="mr-5" onclick="save(-1)">保存</a>
								<a title="取消" class="mr-5" onclick="cancel(-1)">取消</a>
							</td>
						</tr>';
					?>
						
							
					
					</tbody>
				</table>
				<a class="mr-5" onclick="add()">新增</a>
				<!--开始::结束-->
			</div>
		</main>
		
		<script type="text/javascript">
			var tmp_req_url = 'adminController.php';
			var AJAX_TIMEOUT = 2000;
		
			/*function delete_(mobile){
				if(window.confirm('【敏感操作】你确定要删除吗？')){
					var post_data = {'action': 'delete_user','mobile':mobile};
					$.ajax({
						type        : 'post',
						url         : tmp_req_url,
						async		: false,
						data        : {'request':JSON.stringify(post_data)},
						dataType    : 'json',
						success     : function(data) {
							if(data['suc'] == 1){
								alert("删除成功");
								window.location.href=data['url'];
							}else{
								alert();
							}
							
						}
					})
				}
			}*/
			
			function edit(mobile){
				
				$("#uneditable_tr_"+mobile).hide();	
				$("#editable_tr_"+mobile).show();
			}
			
			function save(mobile){
				if(mobile != -1){
					if(!window.confirm('【敏感操作】确定要修改吗？')){
						return;
					}
				}
				
				var params = {};
				var data = $(".new_textarea_"+mobile);
				for(var i=0;i<data.length;i++){
					params[data[i].id] = data[i].value;
				}				
				
				var post_data = {'action': 'save_user','mobile':mobile,'params':params};
				$.ajax({
					type        : 'post',
					url         : tmp_req_url,
					async		: false,
					data        : {'request':JSON.stringify(post_data)},
					dataType    : 'json',
					success     : function(data) {
						window.location.href=data['url'];
					}
				})
			}
			
			function cancel(id){
				$("#uneditable_tr_"+id).show();	
				$("#editable_tr_"+id).hide();
			}
			
			function add(){
				$("#editable_tr_-1").show();
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
