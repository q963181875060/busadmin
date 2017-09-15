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
						<h2 class="title">订单查询</h2>
						<p class="title-description">
							填写需要筛选的字段进行订单搜索
						</p>
					</header>
					<hr>
				</section>
				<ul class="flex flex-wrap flex-col-4">
				<?php
					include 'adminLogicController.php';
					$book_template = get_admin_filter_books_template();
					
					foreach($book_template as $key=>$value){
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
								<button class="btn btn-primary radius" onclick="filter_books()">搜索</button>
							</div>
						</div>
					</li>
				</ul>
				
				<table class="table mb-15">
					<thead>
						<tr>
						<?php
							foreach($book_template as $key=>$value){
								echo '<th>'.$value.'</th>';
							}
						?>
							<th>操作</th>
						</tr>
					</thead>
					<tbody id="route_tbody">
					<?php
					
					$books = get_admin_filter_books();
					
						
					
					//print_r($books);
					$i = 0;
					foreach($books as $book){
						echo ' <tr class="cen"> ';
						foreach($book as $key=>$value){
							echo '<td id="'.$key.'_'.$i.'">'.$value.'</td>';
						}
						echo '
							<td>
								<a class="mr-5" onclick="refund_book('.$book['book_id'].')">退票</a>
							</td>
						</tr>
						
						';
						$i++;
					}
					
					echo ' <tr class="cen" id="add_tr" style="display:none;"> ';
					foreach($book_template as $key=>$value){
						echo '<td><textarea class="new_textarea" style="height:100px" class="form-control form-boxed" id="'.$key.'"></textarea></td>';
					}
					echo '
						<td>
							<a class="mr-5" onclick="save_book(-1)">保存</a>
							<a class="mr-5" onclick="cancel_save(-1)">取消</a>
						</td>
					</tr>
					
					';
					?>
					
					</tbody>
				</table>
				<a class="mr-5" onclick="add_book()">新增</a>
				<!--开始::结束-->
			</div>
		</main>
		
		<script type="text/javascript">
			var tmp_req_url = 'http://139.199.105.54/busadmin/view/adminController.php';
			var AJAX_TIMEOUT = 2000;
		
			
			function add_book(){
				$("#add_tr").show();
			}
			
			function cancel_save(){
				$("#add_tr").hide();
			}
			
			function refund_book(id){
				if(window.confirm('【敏感操作】你确定要为订单编码：'+id+' 进行退票吗？')){
					var book_ids = {};
					book_ids[0] = id;
					var post_data = {'action': 'refund_books','book_ids':book_ids};
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
			}
			
			function save_book(id){
				var params = {};
				var data = $(".new_textarea");
				for(var i=0;i<data.length;i++){
					params[data[i].id] = data[i].value;
				}
				
				var post_data = {'action': 'save_book','params':params};
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
			
			function filter_books(){
				var params = {};
				var data = $(".filter_input");
				for(var i=0;i<data.length;i++){
					if(data[i].value.trim() != ""){
						params[data[i].id] = data[i].value;
					}
				}
				var post_data = {'action': 'filter_book','params':params};
				
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
