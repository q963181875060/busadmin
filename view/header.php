<!DOCTYPE html>

<div class="hd-lt">
	<a class="icon-reorder"></a>
</div>
<div class="hd-rt">
	<ul>
		<!--<li>
			<a href="#" target="_blank"><i class="icon-home"></i>前台访问</a>
		</li>
		<li>
			<a><i class="icon-random"></i>清除缓存</a>
		</li>
		<li>
			<a><i class="icon-user"></i>管理员:<em>DeathGhost</em></a>
		</li>
		<li>
			<a><i class="icon-bell-alt"></i>系统消息</a>
		</li>-->
		<li>
			<a href="javascript:void(0)" id="JsSignOut"><i class="icon-signout"></i>安全退出</a>
		</li>
	</ul>
</div>

<script type="text/javascript">
	var tmp_req_url = 'http://139.199.105.54/busadmin/view/adminController.php';
	var AJAX_TIMEOUT = 2000;
	
	//安全退出
	$('#JsSignOut').click(function(){
		layer.confirm('确定登出管理中心？', {
		  title:'系统提示',
		  btn: ['确定','取消']
		}, function(){
			var post_data = {'action': 'admin_logout'};
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
					}
				}
			})
		});
	});
	
</script>
