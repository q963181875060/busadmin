<?php
$VIEW_URL = 'http://139.199.105.54/busadmin/view/'; 

$DBSTR = 'mysql:host=localhost;port=3306;dbname=bus';
$user='root';      //数据库连接用户名
$pass='920208';          //对应的密码


$req = json_decode( $_POST['request'],1);
if(isset($req['action'])){
		$suc = false;
		error_log('have action');
		//检查是否登录
		if(!isset($_SESSION['user_account']) && $req['action']!='admin_login'){
			$req['action'] = 'admin_logout';
		}
		
        switch ($req['action']){
			case "admin_login":
				if(isset($req['params']) && $req['params']['user_account'] == 'owen' && $req['params']['user_passwd'] == '920208'){
					$res  = array('url'=>$VIEW_URL.'book.php', 'suc'=>1);
					$_SESSION['user_account'] = $req['params']['user_account'];
				}else{
					$res  = array('url'=>$VIEW_URL.'book.php', 'suc'=>0);
				}
				echo json_encode($res);	
				break;
			case "admin_logout":
				unset($_SESSION['user_account']);
				$res  = array('url'=>$VIEW_URL.'login.php', 'suc'=>1);
				echo json_encode($res);	
				break;
			case "save_route": 
				error_log($req['route_id']);
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
						
						$dbh = null;
					} catch (PDOException $e) {
						//die ("Error!: " . $e->getMessage() . "<br/>");
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
						$exe_params['route_id']=$req['route_id'];
						$suc = $sth->execute($exe_params);
						
						$dbh = null;
					} catch (PDOException $e) {
						//die ("Error!: " . $e->getMessage() . "<br/>");
					}
				}
				$res  = array('url'=>$VIEW_URL.'route.php', 'suc'=>$suc);
				echo json_encode($res);		
				break;
			case "delete_route":
				if(isset($req['route_id'])){
					try {
						$dbh = new PDO ($DBSTR,$user,$pass); 
						$sth = $dbh->prepare('delete from route_table where route_id=:route_id');
						$suc = $sth->execute(array(':route_id'=>$req['route_id']));
						
						$dbh = null;
					} catch (PDOException $e) {
						//die ("Error!: " . $e->getMessage() . "<br/>");
					}
				}
				$res  = array('url'=>$VIEW_URL.'route.php', 'suc'=>$suc);
				echo json_encode($res);
				break;
			case "filter_book"://获取订单详情操作
				$_SESSION['filter_book_params'] = $req['params'];
				$res = array('url'=>$VIEW_URL.'filter_book.php', 'suc'=>$suc);
				echo json_encode($res);
				break;
			case "refund_books"://退票操作
				$res = array('url'=>$VIEW_URL.'filter_book.php', 'suc'=>$suc);
				echo json_encode($res);
				break;
			case "save_book":
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
				$res  = array('url'=>$VIEW_URL.'filter_book.php', 'suc'=>$suc);
				echo json_encode($res);	
				break;
		}
}else{
        error_log('no action');
}


?>