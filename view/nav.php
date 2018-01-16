<!DOCTYPE html>



<div class="side-logo">
	<div class="logo">
		<span class="logo-ico">
			<i class="i-l-1"></i>
			<i class="i-l-2"></i>
			<i class="i-l-3"></i>
		</span>
		<strong>合力巴士管理系统</strong>
	</div>
</div>

<nav class="side-menu content mCustomScrollbar" data-mcs-theme="minimal-dark">
	<h2>
		<a href="route.php" class="InitialPage"><i class="icon-dashboard"></i>班次管理</a>
	</h2>
	<h2>
		<a href="book.php" class="InitialPage"><i class="icon-dashboard"></i>班次订单</a>
	</h2>

	<?php
	if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == '平台管理员'){
			echo '<h2>
						<a href="company.php" class="InitialPage"><i class="icon-dashboard"></i>企业管理</a>
				  </h2>
				  <h2>
						<a href="filter_book.php" class="InitialPage"><i class="icon-dashboard"></i>订单查询</a>
				  </h2>
				  <h2>
						<a href="coupon.php" class="InitialPage"><i class="icon-dashboard"></i>代金券管理</a>
					</h2>
					<h2>
						<a href="filter_coupon.php" class="InitialPage"><i class="icon-dashboard"></i>代金券查询</a>
					</h2>
					<h2>
						<a href="send_template.php" class="InitialPage"><i class="icon-dashboard"></i>消息通知</a>
					</h2>
					<h2>
						<a href="user.php" class="InitialPage"><i class="icon-dashboard"></i>人员管理</a>
					</h2>
					<h2>
						<a href="map.php" target="_Blank" class="InitialPage"><i class="icon-dashboard"></i>位置地图</a>
					</h2>
				  
				  ';
	}
	?>

	
	
</nav>


<footer class="side-footer">© 合力巴士 版权所有</footer>
	


