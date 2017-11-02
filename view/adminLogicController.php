<?php

$DBSTR = 'mysql:host=localhost;port=3306;dbname=bus';
$user='root';      //数据库连接用户名
$pass='920208';          //对应的密码

date_default_timezone_set('Asia/Shanghai');
if (!isset($_SESSION['user_account'])) 
{ 
	Header("HTTP/1.1 303 See Other"); 
	Header("Location: login.php"); 
	exit; //from www.w3sky.com 
} 

function get_admin_routes(){
	
	try {
		global $DBSTR, $user, $pass;
		$dbh = new PDO ($DBSTR,$user,$pass); 
		$sth = $dbh->prepare('select route_id, from_city, to_city, from_times, from_stops, to_stops, special_price, price, special_ticket_num, special_must_share, ticket_num, 
					contact_mobile, available_dates from route_table order by from_city asc, to_city asc');
		
		$sth->execute();
		
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		error_log($e->getMessage());
	}finally{
		$dbh = null;
	}
	return $result;
}

function get_admin_routes_template(){
	return array('route_id'=>'班次编号','from_city'=>'起始城市','to_city'=>'目的城市','from_times'=>'上车时间','from_stops'=>'上车点',
			'to_stops'=>'下车点','special_price'=>'特价','price'=>'正常价','special_ticket_num'=>'特价票数','special_must_share'=>'是否必须分享','ticket_num'=>'总票数','contact_mobile'=>'领队电话', 'available_dates'=>'可售日期');
}


function get_admin_verify_users(){
	
	try {
		global $DBSTR, $user, $pass;
		$dbh = new PDO ($DBSTR,$user,$pass); 
		$sth = $dbh->prepare('select user_id, name, mobile, role, state from user_table order by register_time desc');
		
		$sth->execute();
		
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
	} catch (PDOException $e) {
		error_log($e->getMessage());
	}finally{
		$dbh = null;
	}
	return $result;
}

function get_admin_verify_users_template(){
	return array('user_id'=>'人员编号','name'=>'姓名','mobile'=>'手机号','role'=>'角色','state'=>'当前状态');
}

function get_admin_coupons(){
	try {
		global $DBSTR, $user, $pass;
		$dbh = new PDO ($DBSTR,$user,$pass); 
		$sth = $dbh->prepare('select coupon_id, coupon_price, start_time, end_time, coupon_describe, is_special_available, route_ids from coupon_table order by coupon_id desc');
		
		$sth->execute();
		
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		
	} catch (PDOException $e) {
		error_log($e->getMessage());
	}finally{
		$dbh = null;
	}
	return $result;
}

function get_admin_coupons_template(){
	return array('coupon_id'=>'代金券编号','coupon_price'=>'代金券金额','start_time'=>'起始可用日期','end_time'=>'过期日期（当天不过期）','coupon_describe'=>'描述','is_special_available'=>'特价票是否可用','route_ids'=>'可用班次');
}
function get_admin_books(){
	try{
		$sql = "
			select start_date, tab1.route_id, from_city, to_city, contact_mobile, CONCAT(book_num, '/', ticket_num) as book_num from (
				select start_date, route_id, from_city, to_city, contact_mobile, sum(ticket_num) book_num from book_table where state in ('正常', '待验票', '已过期', '已验票')";
		$exe_params = array();
		if(isset($_SESSION['book_params'])){
			foreach($_SESSION['book_params'] as $key=>$value){
				$sql = $sql . ' and '. $key . '=:' . $key;
			}
			foreach($_SESSION['book_params'] as $key=>$value){
				$exe_params[':'.$key]=$value;
			}
			unset($_SESSION['book_params']);
				
		}
		$sql = $sql . ' group by start_date, route_id, from_city, to_city, contact_mobile
			)tab1 left join (
				select route_id, ticket_num from route_table
			) tab2 on (tab1.route_id = tab2.route_id)
			order by start_date desc, from_city desc, to_city desc';
		
		global $DBSTR, $user, $pass;
		$dbh = new PDO ($DBSTR,$user,$pass);
		$sth = $dbh->prepare($sql);
		$sth->execute($exe_params);
							
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
			
	}catch (PDOException $e) {
		error_log($e->getMessage());
	}finally{
		$dbh = null;
	}
	
	return $result;
	
	
	
}

function get_admin_books_template(){
	return array('start_date'=>'日期','route_id'=>'班次编号','from_city'=>'起始城市','to_city'=>'目的城市','contact_mobile'=>'领队电话','book_num'=>'订票人数');
}

function get_admin_filter_books(){
	try{
		$sql = "select book_id, start_date, route_id, from_time, from_city, to_city, from_stop, to_stop, user_id, customer_name, customer_id_card, submit_time, buy_time, is_special_ticket, 
			ticket_num, verify_code, verify_time, refund_time, price, contact_mobile, state from book_table where state!='已取消' ";
		
		$exe_params = array();
		if(isset($_SESSION['filter_book_params'])){
			$i = 0;
			foreach($_SESSION['filter_book_params'] as $key=>$value){
				$sql = $sql . ' and ' . $key . '=:' . $key;
				$i++;
			}
			foreach($_SESSION['filter_book_params'] as $key=>$value){
				$exe_params[':'.$key]=$value;
			}
		}
		
		$sql = $sql . " order by book_id desc";

		global $DBSTR, $user, $pass;
		$dbh = new PDO ($DBSTR,$user,$pass);
		$sth = $dbh->prepare($sql);
		$sth->execute($exe_params);
							
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
			
	}catch (PDOException $e) {
		error_log($e->getMessage());
	}finally{
		$dbh = null;
	}
	
	return $result;
}

function get_admin_filter_books_template(){
	return array('book_id'=>'订单编号','start_date'=>'日期','route_id'=>'班次编号','from_time'=>'上车时间','from_city'=>'起始城市','to_city'=>'目的城市',
						'from_stop'=>'上车点','to_stop'=>'下车点','user_id'=>'购票人id','customer_name'=>'乘客姓名','customer_id_card'=>'乘客身份证号','submit_time'=>'提交时间','buy_time'=>'支付时间','is_special_ticket'=>'是否特价票','ticket_num'=>'购票数量',
						'verify_code'=>'验票码','verify_time'=>'验票时间','refund_time'=>'退款时间',
							'price'=>'价格','contact_mobile'=>'领队电话','state'=>'当前状态');
}

function get_admin_filter_coupons(){
	try{
		$sql = "select user_coupon_id, user_id, coupon_id, state, get_time, use_time from user_coupon_table ";
		
		$exe_params = array();
		if(isset($_SESSION['filter_coupons_params'])){
			$i = 0;
			foreach($_SESSION['filter_coupons_params'] as $key=>$value){
				if($i == 0){
					$sql = $sql . ' where ';
				}else{
					$sql = $sql . ' and ';
				}
				$sql = $sql . $key . '=:' . $key;
				$i++;
			}
			foreach($_SESSION['filter_coupons_params'] as $key=>$value){
				$exe_params[':'.$key]=$value;
			}
		}
		
		global $DBSTR, $user, $pass;
		$dbh = new PDO ($DBSTR,$user,$pass);
		$sth = $dbh->prepare($sql);
		$sth->execute($exe_params);
							
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
			
	}catch (PDOException $e) {
		error_log($e->getMessage());
	}finally{
		$dbh = null;
	}
	
	return $result;
}

function get_admin_filter_coupons_template(){
	return array('user_coupon_id'=>'领券编号','user_id'=>'用户编号','coupon_id'=>'代金券编号','state'=>'状态','get_time'=>'获取时间','use_time'=>'使用时间');
}

//得到存储的用户的地理位置用于对停靠点做决策
function get_admin_positions(){
	try{
		$sql = "select * from position_table";
		global $DBSTR, $user, $pass;
		$dbh = new PDO ($DBSTR,$user,$pass);
		$sth = $dbh->prepare($sql);
		$sth->execute();
							
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
			
	}catch (PDOException $e) {
		error_log($e->getMessage());
	}finally{
		$dbh = null;
	}
	
	return $result;
	
}

?>