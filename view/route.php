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
						<h2 class="title">班次管理</h2>
						<p class="title-description">
							注意：“上车时间”指的是一趟客运在不同上车点的停靠时间
						</p>
						<p class="title-description">
							注意：不同时间、上车点、下车点、可售日期，用>进行间隔
						</p>
						<p class="title-description">
							注意：若班次信息发生任何变化，线路加盟商须提前3天告知合力巴士进行信息变更；若未及时告知，线路加盟商须按原班次信息进行运营。
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
								$route_template = get_admin_routes_template();
								foreach($route_template as $key=>$value){
									echo '<th>'.$value.'</th>';
								}
								if($_SESSION['user_role'] == '平台管理员'){
									echo '<th>操作</th>';
								}
							?>
						</tr>
					</thead>
					<tbody id="route_tbody">
					<?php
					
					$routes = get_admin_routes();
					//echo 'routes size='.count($routes);
					foreach($routes as $key_out=>$route){
						
						echo '<tr class="cen" id="uneditable_tr_'.$route['route_id'].'">
								<td><input type="checkbox"/></td>';
						//echo 'size='.count($route);
						//print_r($route);
						foreach($route as $key=>$value){
							echo '<td>'.$value.'</td>';
						}
						if($_SESSION['user_role'] == '平台管理员'){
							echo '
								<td>
									<a title="编辑" class="mr-5" onclick="edit_route('.$route['route_id'].')">编辑</a>
									<a title="删除" class="mr-5" onclick="delete_route('.$route['route_id'].')">删除</a>
								</td>
							';
						}						
						
						echo '</tr><tr class="cen" id="editable_tr_'.$route['route_id'].'"  style="display:none">
								<td><input type="checkbox"/></td>';
						foreach($route as $key=>$value){
							echo '<td><textarea class="new_textarea_'.$route['route_id'].'" style="height:300px" class="form-control form-boxed" id="'.$key.'">'.$value.'</textarea></td>';
						}
						echo '<td>
								<a title="保存" class="mr-5" onclick="save_route('.$route['route_id'].')">保存</a>
								<a title="取消" class="mr-5" onclick="cancel_route('.$route['route_id'].')">取消</a>
							</td>
						</tr>';
					}
					
					echo '<tr class="cen" id="editable_tr_-1"  style="display:none">
							<td><input type="checkbox"/></td>';
					foreach($route_template as $key=>$value){
						echo '<td><textarea class="new_textarea_-1" style="height:300px" class="form-control form-boxed" id="'.$key.'"></textarea></td>';
					}
					echo '	<td>
								<a title="保存" class="mr-5" onclick="save_route(-1)">保存</a>
								<a title="取消" class="mr-5" onclick="cancel_route(-1)">取消</a>
							</td>
						</tr>';
					?>
						
							
					
					</tbody>
				</table>
				<?php
				if($_SESSION['user_role'] == '平台管理员'){
					echo '<a class="mr-5" onclick="add_route()">新增</a>';
				}
				?>
				
				<!--开始::结束-->
			</div>
		</main>
		
		<script type="text/javascript">
			var tmp_req_url = 'adminController.php';
			var AJAX_TIMEOUT = 2000;
		
			function delete_route(route_id){
				if(window.confirm('【敏感操作】你确定要删除此条路线吗？')){
					var post_data = {'action': 'delete_route','route_id':route_id};
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
			}
			
			function edit_route(route_id){
				
				$("#uneditable_tr_"+route_id).hide();	
				$("#editable_tr_"+route_id).show();
			}
			
			function save_route(route_id){
				if(route_id != -1){
					if(!window.confirm('【敏感操作】确定要修改此条路线吗？')){
						return;
					}
				}
				
				var params = {};
				var data = $(".new_textarea_"+route_id);
				for(var i=0;i<data.length;i++){
					params[data[i].id] = data[i].value;
				}				
				
				var post_data = {'action': 'save_route','route_id':route_id,'params':params};
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
			
			function cancel_route(route_id){
				$("#uneditable_tr_"+route_id).show();	
				$("#editable_tr_"+route_id).hide();
			}
			
			function add_route(){
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
