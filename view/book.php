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
		<header class="top-hd"><?php include 'header.php'; ?></header>
		<main class="main-cont content mCustomScrollbar" id="route_list_main">
			<div class="page-wrap">
				<!--开始::内容-->
				<section class="page-hd">
					<header>
						<h2 class="title">班次订单</h2>
						<p class="title-description">
							查询各个班次的订票情况
						</p>
					</header>
					<hr>
				</section>
				<table class="table mb-15">
					<thead>
						<tr>
						<?php
							include 'adminLogicController.php';
							$book_template = get_admin_books_template();
							foreach($book_template as $key=>$value){
								echo '<th>'.$value.'</th>';
							}
						?>
							<th>操作</th>
						</tr>
					</thead>
					<tbody id="route_tbody">
					<?php
					$books = get_admin_books();
					$i = 0;
					foreach($books as $book){
						echo '<tr class="cen">';
						foreach($book as $key=>$value){
							echo '<td id="'.$key.'_'.$i.'">'.$value.'</td>';
						}
						echo '
							<td>
								<a class="mr-5" onclick="filter_books('.$i.')">详情</a>
							</td>
						</tr>';
						$i++;
						
					}?>
					
					</tbody>
				</table>
				<!--开始::结束-->
			</div>
		</main>
		
		<script type="text/javascript">
			var tmp_req_url = 'http://139.199.105.54/busadmin/view/adminController.php';
			var AJAX_TIMEOUT = 2000;
		
			
			function filter_books(id){
				var tmp = $('#start_date_'+id);
				//alert($("#start_date_"+id).html());
				var params = {'start_date':$('#start_date_'+id).html(), 
							  'route_id':$('#route_id_'+id).html()};
				 
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
