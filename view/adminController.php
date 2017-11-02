<?php
require_once '../../bus/view/wechat_poster.php';
require_once '../../bus/view/wxpay/lib/WxPay.Api.php';
require_once '../../bus/view/common.php';

date_default_timezone_set('Asia/Shanghai');
$req = json_decode( $_POST['request'],1);

if(isset($req['action'])){
		$suc = false;
		//检查是否登录
		if(!isset($_SESSION['user_account']) && $req['action']!='admin_login'){
			$req['action'] = 'admin_logout';
		}
		
        switch ($req['action']){
			case "admin_login":
				if(isset($req['params']) && $req['params']['user_account'] == 'owen' && $req['params']['user_passwd'] == '920208'){
					$res  = array('url'=>'route.php', 'suc'=>1);
					$_SESSION['user_account'] = $req['params']['user_account'];
				}else{
					$res  = array('url'=>'route.php', 'suc'=>0);
				}
				echo json_encode($res);	
				break;
			case "admin_logout":
				unset($_SESSION['user_account']);
				$res  = array('url'=>'login.php', 'suc'=>1);
				echo json_encode($res);	
				break;
			case "save_user": 
				if($req['user_id'] == '-1'){
					//add new		
					try {
						$dbh = new PDO ($DBSTR,$user,$pass);
						
						$sql = 'insert into user_table (';
						$i = 0;
						foreach($req['params'] as $key=>$value){
							if($i != 0){
								$sql = $sql . ',';
							}
							$sql = $sql.$key;
							$i++;
						}
						$sql = $sql . ') values (';
						$i = 0;
						foreach($req['params'] as $key=>$value){
							if($i != 0){
								$sql = $sql . ',';
							}
							$sql = $sql . ':' .$key;
							$i++;
						}
						$sql = $sql . ')';
						$sth = $dbh->prepare($sql);
						$exe_params = array();
						foreach($req['params'] as $key=>$value){
							$exe_params[':'.$key]=$value;
						}
						//error_log(print_r($exe_params));
						$suc = $sth->execute($exe_params);
						
					} catch (PDOException $e) {
						
					}finally{
						$dbh = null;
					}
				}else{
					//update
					try {
						$dbh = new PDO ($DBSTR,$user,$pass); 
						$sql = 'update user_table set ';
						$i = 0;
						foreach($req['params'] as $key=>$value){
							if($i != 0){
								$sql = $sql . ',';
							}
							$sql = $sql.$key.'=:'.$key;
							$i++;
						}
						$sql = $sql . ' where mobile=:mobile';
						$sth = $dbh->prepare($sql);
						$exe_params = array();
						foreach($req['params'] as $key=>$value){
							$exe_params[':'.$key]=$value;
						}
						$exe_params[':mobile']=$req['mobile'];
						$suc = $sth->execute($exe_params);
						
					} catch (PDOException $e) {
						//die ("Error!: " . $e->getMessage() . "<br/>");
					}finally{
						$dbh = null;
					}
				}
				$res  = array('url'=>'user.php', 'suc'=>$suc);
				echo json_encode($res);		
				break;
			case "save_route": 
				if($req['route_id'] == '-1'){
					//add new route				
					try {
						$dbh = new PDO ($DBSTR,$user,$pass);
						
						$sql = 'insert into route_table (';
						$i = 0;
						foreach($req['params'] as $key=>$value){
							if($i != 0){
								$sql = $sql . ',';
							}
							$sql = $sql.$key;
							$i++;
						}
						$sql = $sql . ') values (';
						$i = 0;
						foreach($req['params'] as $key=>$value){
							if($i != 0){
								$sql = $sql . ',';
							}
							$sql = $sql . ':' .$key;
							$i++;
						}
						$sql = $sql . ')';
						$sth = $dbh->prepare($sql);
						$exe_params = array();
						foreach($req['params'] as $key=>$value){
							$exe_params[':'.$key]=$value;
						}
						//error_log(print_r($exe_params));
						$suc = $sth->execute($exe_params);
						
					} catch (PDOException $e) {
						
					}finally{
						$dbh = null;
					}
				}else{
					//update route
					try {
						$dbh = new PDO ($DBSTR,$user,$pass); 
						$sql = 'update route_table set ';
						$i = 0;
						foreach($req['params'] as $key=>$value){
							if($i != 0){
								$sql = $sql . ',';
							}
							$sql = $sql.$key.'=:'.$key;
							$i++;
						}
						$sql = $sql . ' where route_id=:route_id';
						$sth = $dbh->prepare($sql);
						$exe_params = array();
						foreach($req['params'] as $key=>$value){
							$exe_params[':'.$key]=$value;
						}
						$exe_params[':route_id']=$req['route_id'];
						$suc = $sth->execute($exe_params);
						
					} catch (PDOException $e) {
						//die ("Error!: " . $e->getMessage() . "<br/>");
					}finally{
						$dbh = null;
					}
				}
				$res  = array('url'=>'route.php', 'suc'=>$suc);
				echo json_encode($res);		
				break;
			case "delete_route":
				$suc = 0;
				$msg = '';
				if(isset($req['route_id'])){
					try {
						//如果有人预定了这个班次的订单，则不能删除
						$dbh = new PDO ($DBSTR,$user,$pass); 
						$exe_params = array();
						$sql = "select start_date, user_id from book_table where route_id=:route_id and (status='正常' or status='待验票' or status='待支付')";
						$sth = $dbh->prepare($sql);
						$exe_params[':route_id'] = trim($req['route_id']);
						$sth->execute($exe_params);
						$result = $sth->fetchAll(PDO::FETCH_ASSOC);
						
						if(count($result) != 0){
							foreach($result as $unit){
								$msg += $unit['start_date'] . ' 用户：' . $unit['user_id'] . ';';
							}
							$msg += '已经预定了此班次的车，因此无法删除此班次';
						}else{
							$sth = $dbh->prepare('delete from route_table where route_id=:route_id');
							$exe_params = array();
							$exe_params[':route_id']=trim($req['route_id']);
							$suc = $sth->execute($exe_params);
							if($suc == 0){
								$msg = '删除班次失败，未知错误，请联系管理员';
							}
						}
						
						$dbh = null;
					} catch (Exception $e) {
						error_log($e->getMessage());
					}
				}
				$res  = array('url'=>'route.php', 'suc'=>$suc,'msg'=>$msg);
				echo json_encode($res);
				break;
			case "save_coupon": 
				//error_log($req['coupon_id']);
				if($req['coupon_id'] == '-1'){
					//add new coupon				
					try {
						$dbh = new PDO ($DBSTR,$user,$pass);
						
						$sql = 'insert into coupon_table (';
						$i = 0;
						foreach($req['params'] as $key=>$value){
							if($i != 0){
								$sql = $sql . ',';
							}
							$sql = $sql.$key;
							$i++;
						}
						$sql = $sql . ') values (';
						$i = 0;
						foreach($req['params'] as $key=>$value){
							if($i != 0){
								$sql = $sql . ',';
							}
							$sql = $sql . ':' .$key;
							$i++;
						}
						$sql = $sql . ')';
						$sth = $dbh->prepare($sql);
						$exe_params = array();
						foreach($req['params'] as $key=>$value){
							$exe_params[':'.$key]=$value;
						}
						//error_log(print_r($exe_params));
						$suc = $sth->execute($exe_params);
						
						$dbh = null;
					} catch (PDOException $e) {
						//die ("Error!: " . $e->getMessage() . "<br/>");
					}
				}else{
					//update coupon
					try {
						$dbh = new PDO ($DBSTR,$user,$pass); 
						$sql = 'update coupon_table set ';
						$i = 0;
						foreach($req['params'] as $key=>$value){
							if($i != 0){
								$sql = $sql . ',';
							}
							$sql = $sql.$key.'=:'.$key;
							$i++;
						}
						$sql = $sql . ' where coupon_id=:coupon_id';
						$sth = $dbh->prepare($sql);
						$exe_params = array();
						foreach($req['params'] as $key=>$value){
							$exe_params[':'.$key]=$value;
						}
						$exe_params[':coupon_id']=$req['coupon_id'];
						$suc = $sth->execute($exe_params);
						
						$dbh = null;
					} catch (PDOException $e) {
						//die ("Error!: " . $e->getMessage() . "<br/>");
					}
				}
				$res  = array('url'=>'coupon.php', 'suc'=>$suc);
				echo json_encode($res);		
				break;
			case "delete_coupon":
				$suc = 0;
				$msg = '';
				if(isset($req['coupon_id'])){
					try {
						//如果有人有有效的代金券，则不能删除
						$dbh = new PDO ($DBSTR,$user,$pass); 
						$exe_params = array();
						$sql = "select user_id from coupon_table where coupon_id=:coupon_id and status='正常'";
						$sth = $dbh->prepare($sql);
						$exe_params[':coupon_id'] = trim($req['coupon_id']);
						$sth->execute($exe_params);
						$result = $sth->fetchAll(PDO::FETCH_ASSOC);
						
						if(count($result) != 0){
							foreach($result as $unit){
								$msg += $unit['user_id'] . ';';
							}
							$msg += '有可使用的此代金券，因此无法删除';
						}else{
							$sth = $dbh->prepare('delete from coupon_table where coupon_id=:coupon_id');
							$exe_params = array();
							$exe_params[':coupon_id']=trim($req['coupon_id']);
							$suc = $sth->execute($exe_params);
							if($suc == 0){
								$msg = '删除失败，未知错误，请联系管理员';
							}
						}
						
						$dbh = null;
					} catch (Exception $e) {
						error_log($e->getMessage());
					}
				}
				$res  = array('url'=>'coupon.php', 'suc'=>$suc,'msg'=>$msg);
				echo json_encode($res);
				break;
			case "filter_user_coupon"://获取代金券领取操作
				unset($_SESSION['filter_coupons_params']);
				$_SESSION['filter_coupons_params'] = $req['params'];
				$res = array('url'=>'filter_coupon.php', 'suc'=>$suc);
				echo json_encode($res);
				break;
			
			case "save_user_coupon"://保存&新增领取的代金券
				try {
					$dbh = new PDO ($DBSTR,$user,$pass); 
					
					$sql = 'insert into user_coupon_table (';
					
					$i = 0;
					foreach($req['params'] as $key=>$value){
						if($i != 0){
							$sql = $sql . ',';
						}
						$sql = $sql.$key;
						$i++;
					}
					$sql = $sql . ') values (';
					$i = 0;
					foreach($req['params'] as $key=>$value){
						if($i != 0){
							$sql = $sql . ',';
						}
						$sql = $sql . ':' .$key;
						$i++;
					}
					$sql = $sql . ')';
					error_log($sql);
					$sth = $dbh->prepare($sql);
					$exe_params = array();
					foreach($req['params'] as $key=>$value){
						$exe_params[':'.$key]=$value;
					}
					
					$suc = $sth->execute($exe_params);
					
					$dbh = null;
				} catch (PDOException $e) {
					//die ("Error!: " . $e->getMessage() . "<br/>");
				}
				$res  = array('url'=>'filter_coupon.php', 'suc'=>$suc);
				echo json_encode($res);	
				break;
			case "filter_book"://获取订单详情操作
				unset($_SESSION['filter_book_params']);
				$_SESSION['filter_book_params'] = $req['params'];
				$res = array('url'=>'filter_book.php', 'suc'=>$suc);
				echo json_encode($res);
				break;
			case "refund_books"://退票操作
				try{					
					
					$dbh = new PDO ($DBSTR,$user,$pass); 
					$sql = "update book_table set state='已退票', refund_time=:refund_time where user_id=:user_id and book_id=:book_id";
					$exe_params = array();
					$exe_params[':user_id']=$req['params']['user_id'];
					$exe_params[':refund_time']=date("Y-m-d H:i:s");
					$exe_params[':book_id']=$req['params']['book_id'];
					$sth = $dbh->prepare($sql);
					$sth->execute($exe_params);
					$affected_rows = $sth->rowCount();
					if($affected_rows == 0){
						throw new Exception("更新数据库失败，请稍后再试");
					}
					
					$sql = "select * from book_table where book_id=:book_id";
					$exe_params = array();
					$exe_params[':book_id']=$req['params']['book_id'];
					$sth = $dbh->prepare($sql);
					$sth->execute($exe_params);
					$result = $sth->fetchAll(PDO::FETCH_ASSOC);
					if(count($result) == 0){
						throw new Exception("获取数据库信息失败，请稍后再试");
					}
					
					//对用户的账户发起退款
					$out_trade_no = $result[0]['book_id'];
					$total_fee = (int)(((float)$result[0]['price'] * (int)$result[0]['ticket_num'] - (float)$result[0]['coupon_price'])*100);
					if($req['params']['is_refund_full'] != 1){
						$refund_fee = round($total_fee*0.95, 0);
					}else{
						$refund_fee = $total_fee;
					}
					$input = new WxPayRefund();
					$input->SetOut_trade_no($out_trade_no);
					$input->SetTotal_fee($total_fee);
					$input->SetRefund_fee($refund_fee);
					$input->SetOut_refund_no(WxPayConfig::MCHID.date("YmdHis"));
					$input->SetOp_user_id(WxPayConfig::MCHID);
					$input->SetRefund_account('REFUND_SOURCE_UNSETTLED_FUNDS');//REFUND_SOURCE_RECHARGE_FUNDS
					$res = WxPayApi::refund($input);
					//error_log(print_r($res));
					
					//如果未结算余额不足，使用账户余额退款
					if(isset($res['err_code']) && $res['err_code'] == 'NOTENOUGH'){
						$input->SetRefund_account('REFUND_SOURCE_RECHARGE_FUNDS');
						$res = WxPayApi::refund($input);						
					}
					
					if(!(isset($res['result_code']) && $res['result_code'] == 'SUCCESS')){
						if(isset($res['err_code'])){
							throw new Exception("退票失败".$res['err_code'].$res['err_code_des']);
						}else{
							throw new Exception("退票失败".$res['return_msg']);
						}
					}
					
					//发送成功退票模板消息
					$data[] = array();
					$data['user_id'] = $result[0]['user_id'];
					$data['first'] = '取消订单成功！';
					$data['keyword1'] = $result[0]['book_id'];
					$data['keyword2'] = '官方退票';
					$data['keyword3'] = '￥' . round(((float)$refund_fee)/100,2);
					$data['remark'] = '有疑问欢迎随时联系我们，欢迎下次乘坐合力巴士！';
					send_cancel_ticket_template($data);
				
					$suc = 1;
					$msg = '';
				} catch (Exception $e) {
					//error_log($e->getMessage());
					$suc = 0;
					$msg = $e->getMessage();
				}finally{
					$dbh = null;
				}
			
				$res = array('url'=>'filter_book.php', 'suc'=>$suc, 'msg'=>$msg);
				echo json_encode($res);
				break;
			case "save_book"://保存&新增订单
				try {
					$dbh = new PDO ($DBSTR,$user,$pass); 
					
					$sql = 'insert into book_table (';
					
					$i = 0;
					foreach($req['params'] as $key=>$value){
						if($i != 0){
							$sql = $sql . ',';
						}
						$sql = $sql.$key;
						$i++;
					}
					$sql = $sql . ') values (';
					$i = 0;
					foreach($req['params'] as $key=>$value){
						if($i != 0){
							$sql = $sql . ',';
						}
						$sql = $sql . ':' .$key;
						$i++;
					}
					$sql = $sql . ')';
					error_log($sql);
					$sth = $dbh->prepare($sql);
					$exe_params = array();
					foreach($req['params'] as $key=>$value){
						$exe_params[':'.$key]=$value;
					}
					//error_log(print_r($exe_params));
					$suc = $sth->execute($exe_params);
					error_log("save_book ".$suc);
					$dbh = null;
				} catch (PDOException $e) {
					//die ("Error!: " . $e->getMessage() . "<br/>");
				}
				$res  = array('url'=>'filter_book.php', 'suc'=>$suc);
				echo json_encode($res);	
				break;
			case "send_template"://发送模板消息给用户
				$user_ids = $req['user_ids'];
				$data = $req['params'];
				send_admin_template($user_ids, $data);
				$res  = array('suc'=>1);
				echo json_encode($res);	
				break;
			case "admin_template"://跳转到模板消息页面
				$_SESSION['user_ids'] = $req['user_ids'];
				$res = array('url'=>'send_template.php', 'suc'=>1);
				echo json_encode($res);
				break;
			
		}
}else{
        error_log('no action');
}


?>