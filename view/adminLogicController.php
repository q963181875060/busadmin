<?php

$DBSTR = 'mysql:host=localhost;port=3306;dbname=bus';
$user='root';      //数据库连接用户名
$pass='920208';          //对应的密码

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
		$sth = $dbh->prepare('select route_id, from_city, to_city, from_times, from_stops, to_stops, special_price, price, special_ticket_num, ticket_num, 
					contact_mobile, available_dates from route_table order by from_city asc, to_city asc');
		
		$sth->execute();
		
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		$dbh = null;
		
	} catch (PDOException $e) {
		error_log($e->getMessage());
	}
	return $result;
}

function get_admin_routes_template(){
	return array('route_id'=>'班次编号','from_city'=>'起始城市','to_city'=>'目的城市','from_times'=>'上车时间','from_stops'=>'上车点',
			'to_stops'=>'下车点','special_price'=>'特价','price'=>'正常价','special_ticket_num'=>'特价票数','ticket_num'=>'总票数','contact_mobile'=>'领队电话', 'available_dates'=>'可售日期');
}

function get_admin_books(){
	try{
		$sql = "select start_date, route_id, from_city, to_city, contact_mobile, sum(ticket_num) book_num from book_table where state!='已退票' ";
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
		$sql = $sql . ' group by start_date, route_id, from_city, to_city, contact_mobile order by start_date, from_city, to_city';
		
		global $DBSTR, $user, $pass;
		$dbh = new PDO ($DBSTR,$user,$pass);
		$sth = $dbh->prepare($sql);
		$sth->execute($exe_params);
							
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
			
		$dbh = null;
	}catch (PDOException $e) {
		error_log($e->getMessage());
	}
	
	return $result;
	
	
	
}

function get_admin_books_template(){
	return array('start_date'=>'日期','route_id'=>'班次编号','from_city'=>'起始城市','to_city'=>'目的城市','contact_mobile'=>'领队电话','book_num'=>'订票人数');
}

function get_admin_filter_books(){
	try{
		$sql = "select book_id, start_date, route_id, from_time, from_city, to_city, from_stop, to_stop, user_id, buy_time, is_special_ticket, 
			ticket_num, verify_code, verify_time, refund_time, price, contact_mobile, state from book_table ";
		
		$exe_params = array();
		if(isset($_SESSION['filter_book_params'])){
			$i = 0;
			foreach($_SESSION['filter_book_params'] as $key=>$value){
				if($i == 0){
					$sql = $sql . ' where ';
				}else{
					$sql = $sql . ' and ';
				}
				$sql = $sql . $key . '=:' . $key;
				$i++;
			}
			foreach($_SESSION['filter_book_params'] as $key=>$value){
				$exe_params[':'.$key]=$value;
			}
			unset($_SESSION['filter_book_params']);
		}

		global $DBSTR, $user, $pass;
		$dbh = new PDO ($DBSTR,$user,$pass);
		$sth = $dbh->prepare($sql);
		$sth->execute($exe_params);
							
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
			
		$dbh = null;
	}catch (PDOException $e) {
		error_log($e->getMessage());
	}
	
	return $result;
}

function get_admin_filter_books_template(){
	return array('book_id'=>'订单编号','start_date'=>'日期','route_id'=>'班次编号','from_time'=>'上车时间','from_city'=>'起始城市','to_city'=>'目的城市',
						'from_stop'=>'上车点','to_stop'=>'下车点','user_id'=>'购票人','buy_time'=>'购票时间','is_special_ticket'=>'是否特价票','ticket_num'=>'购票数量',
						'verify_code'=>'验票码','verify_time'=>'验票时间','refund_time'=>'退款时间',
							'price'=>'价格','contact_mobile'=>'领队电话','state'=>'当前状态');
}


?>