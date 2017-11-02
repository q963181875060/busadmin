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
		<main class="main-cont content mCustomScrollbar">
			<div class="page-wrap">
				<!--开始::内容-->
				<section class="page-hd">
					<header>
						<h2 class="title">代金券查询</h2>
						<p class="title-description">
							填写需要筛选的字段进行搜索
						</p>
					</header>
					<hr>
				</section>
				<ul class="flex flex-wrap flex-col-4">
				<?php
					include 'adminLogicController.php';
					$coupon_template = get_admin_filter_coupons_template();
					
					foreach($coupon_template as $key=>$value){
						echo '<li class="box-child">
								<div class="form-group-col-2">
									<div class="form-label">'.$value.'：</div>
									<div class="form-cont">
										<input class="filter_input" id="'.$key.'" type="text" placeholder="" class="form-control form-boxed ">
									</div>
								</div>
							</li>';
					}
				?>
					<li class="box-child">
						<div class="form-group-col-2">
							<div class="form-cont">
								<button class="btn btn-primary radius" onclick="filter_user_coupon()">搜索</button>
							</div>
						</div>
					</li>
				</ul>
				
				<table class="table mb-15">
					<thead>
						<tr>
						<?php
							foreach($coupon_template as $key=>$value){
								echo '<th>'.$value.'</th>';
							}
						?>
						</tr>
					</thead>
					<tbody id="route_tbody">
					<?php
					
					$coupons = get_admin_filter_coupons();
						
					$i = 0;
					foreach($coupons as $coupon){
						echo ' <tr class="cen"> ';
						foreach($coupon as $key=>$value){
							echo '<td id="'.$key.'_'.$i.'">'.$value.'</td>';
						}
						echo '
						</tr>
						';
						$i++;
					}
					
					echo ' <tr class="cen" id="add_tr" style="display:none;"> ';
					foreach($coupon_template as $key=>$value){
						echo '<td><textarea class="new_textarea" style="height:100px" class="form-control form-boxed" id="'.$key.'"></textarea></td>';
					}
					echo '
						<td>
							<a class="mr-5" onclick="save_user_coupon(-1)">保存</a>
							<a class="mr-5" onclick="cancel_save(-1)">取消</a>
						</td>
					</tr>
					
					';
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
		
			
			function add_coupon(){
				$("#add_tr").show();
			}
			
			function cancel_save(){
				$("#add_tr").hide();
			}
		
			
			function save_user_coupon(id){
				var params = {};
				var data = $(".new_textarea");
				for(var i=0;i<data.length;i++){
					params[data[i].id] = data[i].value;
				}
				
				var post_data = {'action': 'save_user_coupon','params':params};
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
			
			function filter_user_coupon(){
				var params = {};
				var data = $(".filter_input");
				for(var i=0;i<data.length;i++){
					if(data[i].value.trim() != ""){
						params[data[i].id] = data[i].value;
					}
				}
				var post_data = {'action': 'filter_user_coupon','params':params};
				
				//alert(JSON.stringify(post_data));
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
