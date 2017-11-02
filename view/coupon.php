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
		<main class="main-cont content mCustomScrollbar">
			<div class="page-wrap">
				<!--开始::内容-->
				<section class="page-hd">
					<header>
						<h2 class="title">代金券管理</h2>
						<p class="title-description">
							注意：可用班次请填写班次编号，多个班次编号用>间隔，且以>开头和结尾，若全部可用，则填写“>全部>”
						</p>
						<p class="title-description">
							注意：“特价票是否可用”中，1代表可用，0代表不可用。
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
								$coupon_template = get_admin_coupons_template();
								foreach($coupon_template as $key=>$value){
									echo '<th>'.$value.'</th>';
								}
							?>
							<th>操作</th>
						</tr>
					</thead>
					<tbody>
					<?php
					
					$coupons = get_admin_coupons();
					foreach($coupons as $key_out=>$coupon){
						
						echo '<tr class="cen" id="uneditable_tr_'.$coupon['coupon_id'].'">
								<td><input type="checkbox"/></td>';
						foreach($coupon as $key=>$value){
							echo '<td>'.$value.'</td>';
						}
						echo '
							<td>
								<a title="编辑" class="mr-5" onclick="edit_coupon('.$coupon['coupon_id'].')">编辑</a>
								<a title="删除" class="mr-5" onclick="delete_coupon('.$coupon['coupon_id'].')">删除</a>
							</td>
						</tr>';
						
						echo '<tr class="cen" id="editable_tr_'.$coupon['coupon_id'].'"  style="display:none">
								<td><input type="checkbox"/></td>';
						foreach($coupon as $key=>$value){
							echo '<td><textarea class="new_textarea_'.$coupon['coupon_id'].'" style="height:300px" class="form-control form-boxed" id="'.$key.'">'.$value.'</textarea></td>';
						}
						echo '<td>
								<a title="保存" class="mr-5" onclick="save_coupon('.$coupon['coupon_id'].')">保存</a>
								<a title="取消" class="mr-5" onclick="cancel_coupon('.$coupon['coupon_id'].')">取消</a>
							</td>
						</tr>';
					}
					
					echo '<tr class="cen" id="editable_tr_-1"  style="display:none">
							<td><input type="checkbox"/></td>';
					foreach($coupon_template as $key=>$value){
						echo '<td><textarea class="new_textarea_-1" style="height:300px" class="form-control form-boxed" id="'.$key.'"></textarea></td>';
					}
					echo '	<td>
								<a title="保存" class="mr-5" onclick="save_coupon(-1)">保存</a>
								<a title="取消" class="mr-5" onclick="cancel_coupon(-1)">取消</a>
							</td>
						</tr>';
					?>
						
							
					
					</tbody>
				</table>
				<a class="mr-5" onclick="add_coupon()">新增</a>
				<!--开始::结束-->
			</div>
		</main>
		
		<script type="text/javascript">
			var tmp_req_url = 'adminController.php';
			var AJAX_TIMEOUT = 2000;
		
			function delete_coupon(coupon_id){
				if(window.confirm('【敏感操作】你确定要删除此代金券吗？')){
					var post_data = {'action': 'delete_coupon','coupon_id':coupon_id};
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
			
			function edit_coupon(coupon_id){
				
				$("#uneditable_tr_"+coupon_id).hide();	
				$("#editable_tr_"+coupon_id).show();
			}
			
			function save_coupon(coupon_id){
				if(coupon_id != -1){
					if(!window.confirm('【敏感操作】确定要修改此代金券吗？')){
						return;
					}
				}
				
				var params = {};
				var data = $(".new_textarea_"+coupon_id);
				
				for(var i=0;i<data.length;i++){
					params[data[i].id] = data[i].value;
					
					if(data[i].id == 'route_ids' && (data[i].value.charAt(0) != '>' || data[i].value.charAt(data[i].value.length-1) != '>')){
						alert("可用班次填写错误，应以>开头和结尾");
						return;
					}
				}				
				
				var post_data = {'action': 'save_coupon','coupon_id':coupon_id,'params':params};
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
			
			function cancel_coupon(coupon_id){
				$("#uneditable_tr_"+coupon_id).show();	
				$("#editable_tr_"+coupon_id).hide();
			}
			
			function add_coupon(){
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
