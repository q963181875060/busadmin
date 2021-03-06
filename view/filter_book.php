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
								<a class="mr-5" onclick="refund_book(\'0,'.$book['user_id'].','.$book['book_id'].'\')">退半票</a>
								<a class="mr-5" onclick="refund_book(\'1,'.$book['user_id'].','.$book['book_id'].'\')">退全票</a>
							</td>
						</tr>
						
						';
						$i++;
					}
					//输出user_ids，为发送模板消息提供数据
					$user_ids = "";
					foreach($books as $book){
						$user_ids = $user_ids . $book['user_id'] . ">";
					}
					echo '<input id="user_ids" style="display:none" value="'.$user_ids.'"/>';
					
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
				<a class="mr-5" onclick="admin_template()">发送通知</a>
				<!--开始::结束-->
			</div>
		</main>
		
		<script type="text/javascript">
			var tmp_req_url = 'adminController.php';
			var AJAX_TIMEOUT = 2000;
		
			
			function add_book(){
				$("#add_tr").show();
			}
			
			function cancel_save(){
				$("#add_tr").hide();
			}
			
			function refund_book(param){
				var is_refund_full = parseInt(param.split(",")[0].trim());
				var user_id = param.split(",")[1].trim();
				var book_id = param.split(",")[2].trim();
				if(window.confirm('【敏感操作】你确定要为订单编码：'+book_id+' 退'+(is_refund_full==1?'全':'半')+'票吗？')){
					var params = {};
					params['user_id'] = user_id;
					params['book_id'] = book_id;
					params['is_refund_full'] = is_refund_full;
					var post_data = {'action': 'refund_books','params':params};
					$.ajax({
						type        : 'post',
						url         : tmp_req_url,
						async		: false,
						data        : {'request':JSON.stringify(post_data)},
						dataType    : 'json',
						success     : function(data) {
							if(data['suc'] == 1){
								window.location.href=data['url'];
							}else{
								alert(data['msg']);
							}
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
			
			//重新跳转到本页面，根据筛选字段的内容重新加载数据
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
			
			//跳转到发送模板消息的页面
			function admin_template(){
				var user_ids = "";
				var tmp = $("#user_ids").val().split('>');
				var map = {};
				for(var i=0; i<tmp.length; i++){
					var user_id = tmp[i].trim(); 
					if(user_id != "" && !map.hasOwnProperty(user_id)){
						map[user_id] = user_id;
						user_ids = user_ids + user_id + "\n";
					}
				}
				var post_data = {'action': 'admin_template','user_ids':user_ids};
				
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
