<?php
/* Social Match By Xohan - xohansosa@gmail.com */
header('Content-Type: application/json');
require_once('../assets/includes/core.php');

$uid = $sm['user']['id'];

switch ($_POST['action']) {

	case 'fortumo':
		$encode = secureEncode($_POST['encode']);	
		$secret = siteConfig('fortumo_secret');
		$result = md5($encode.$secret);
		echo $result;
	break;
	
	case 'interest':
		$result=array();
		$interests = $mysqli->query("SELECT * FROM interest order by count desc LIMIT 200");
		if ($interests->num_rows > 0) { 
			while($up = $interests->fetch_object()){
				$result[] = $up->name;
			}	
		}		
		echo json_encode($result);
	break;
	
	case 'add_interest':
		$name = secureEncode($_POST['name']);
		$check = checkInterestExist($name);		
		$query = "INSERT INTO interest (name) VALUES ('".$name."') ON DUPLICATE KEY UPDATE count = count+1";		
		if ($mysqli->query($query) === TRUE) {
			$last_id = $mysqli->insert_id;		
			$mysqli->query("INSERT INTO users_interest (i_id,u_id,name) VALUES ('$last_id','$uid','$name')");
		}
		
		if($check >= 1){
			$i_id =	getIdInterest($name);
			$mysqli->query("INSERT INTO users_interest (i_id,u_id,name) VALUES ('$i_id','$uid','$name')");		
		}
	break;

	case 'del_interest':
		$id = secureEncode($_POST['id']);
		$mysqli->query("DELETE FROM users_interest where u_id = '".$uid."' and i_id = '".$id."'");		
	break;	
	
	case 'likephoto':
		$pid = secureEncode($_POST['pid']);
		$name = secureEncode($_POST['uname']);		
		$mysqli->query("INSERT INTO photos_likes (pid,uid,name) VALUES ('$pid','$uid','$name')");					   
	break;
	
	case 'photocomments':
		$pid = secureEncode($_POST['pid']);
		$comments = getPhotoCommentsAjax($pid);		
		echo $comments;
	break;
	
	case 'photocomment':
		$pid = secureEncode($_POST['pid']);
		$message = secureEncode($_POST['message']);		
		$mysqli->query("INSERT INTO photos (pid,cid,comment) VALUES ('$pid','$uid','$message')");
		$arr =array();
		$arr['comment'] = $message;
		echo json_encode($arr);		
	break;	
	
	case 'block':
		$id = secureEncode($_POST['id']);
		$query = "INSERT INTO users_blocks (uid1,uid2) VALUES ('".$uid."', '".$id."')";
		$mysqli->query($query);		
		$query2 = "DELETE FROM chat where s_id = '".$uid."' AND r_id = '".$id."' || r_id = '".$uid."' AND s_id = '".$id."'";
		$mysqli->query($query2);				
	break;	
	case 'report':
		$id = secureEncode($_POST['id']);
		$query = "INSERT INTO reports (reported,reported_by) VALUES ('".$id."', '".$uid."')";
		$mysqli->query($query);				
	break;		
	
	case 'photos':
		$id = secureEncode($_POST['id']);
		getUserInfo($id,1);	
		$sm['content'] = requestPage('profile/photos');
		echo $sm['content'];		
	break;
	
	case 'update':	
		$name = secureEncode($_POST['name']);
		$email = secureEncode($_POST['email']);	
		$month = secureEncode($_POST['month']);
		$day = secureEncode($_POST['day']);
		$year = secureEncode($_POST['year']);			
		$birthday = date('F', mktime(0, 0, 0, $month, 10)).' '.secureEncode($_POST['day']).', '.secureEncode($_POST['year']);	
		$gender = secureEncode($_POST['gender']);
		$city = secureEncode($_POST['city']);
		$country = secureEncode($_POST['country']);
		$lat = secureEncode($_POST['lat']);
		$lng = secureEncode($_POST['lng']);
		$lang = secureEncode($_POST['lang']);
		$bio = secureEncode($_POST['bio']);		
					
		$from = new DateTime($year.'-'.$month.'-'.$day);
		$to   = new DateTime('today');
		$age = $from->diff($to)->y;	
		
		if($city == "" || $city == NULL){
			$city = $country;	
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			echo 'Error'.$sm['lang'][181]['text'];	
			exit;	
		}		
			
		if($lat == "" || $lat == NULL){
			echo 'Error'.$sm['lang'][186]['text'];
			exit;	
		}	
		
		if($name == "" || $name == NULL || $email == "" || $email == NULL){
			echo 'Error'.$sm['lang'][182]['text'];
			exit;	
		}
	
		$query = "UPDATE users SET name = '".$name."', email = '".$email."', age = '".$age."', birthday = '".$birthday."',
				  gender = '".$gender."', city = '".$city."', country = '".$country."', lat = '".$lat."', lng = '".$lng."', lang = '".$lang."',
				  bio = '".$bio."'
				  WHERE id = '".$sm['user']['id']."'";
		$mysqli->query($query);
		
		$f1 = secureEncode($_POST['extended1']);
		$f2 = secureEncode($_POST['extended2']);
		$f3 = secureEncode($_POST['extended3']);
		$f4 = secureEncode($_POST['extended4']);
		$f5 = secureEncode($_POST['extended5']);
		$f6 = secureEncode($_POST['extended6']);
		$f7 = secureEncode($_POST['extended7']);
		$f8 = secureEncode($_POST['extended8']);
		$f9 = secureEncode($_POST['extended9']);
		$f10 = secureEncode($_POST['extended10']);
		
		
		
		$query2 = "UPDATE users_extended SET field1 = '".$f1."', field2 = '".$f2."', field3 = '".$f3."', field4 = '".$f4."',
				  field5 = '".$f5."', field6 = '".$f6."', field7 = '".$f7."', field8 = '".$f8."', field9 = '".$f9."', field10 = '".$f10."' 
				  WHERE uid = '".$sm['user']['id']."'";
		$mysqli->query($query2);	
		
		
	break;	
	
	case 'register':	
		$name = secureEncode($_POST['name']);
		$email = secureEncode($_POST['email']);	
		$password = secureEncode($_POST['pass']);
		$month = secureEncode($_POST['month']);
		$day = secureEncode($_POST['day']);
		$year = secureEncode($_POST['year']);			
		if(!is_numeric($day) || !is_numeric($year) || !is_numeric($month))
		{
			echo 'Error'.$sm['lang'][185]['text'];	
			exit;			
		}		
		$birthday = date('F', mktime(0, 0, 0, $month, 10)).' '.secureEncode($_POST['day']).', '.secureEncode($_POST['year']);	
		$gender = secureEncode($_POST['gender']);
		$city = secureEncode($_POST['city']);
		$country = secureEncode($_POST['country']);
		$lat = secureEncode($_POST['lat']);
		$lng = secureEncode($_POST['lng']);
		$looking = secureEncode($_POST['looking']);
		$date = date('m/d/Y', time());
		$from = new DateTime($year.'-'.$month.'-'.$day);
		$to   = new DateTime('today');
		$age = $from->diff($to)->y;	
		
		if($city == "" || $city == NULL){
			$city = $country;	
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			echo 'Error'.$sm['lang'][181]['text'];	
			exit;	
		}		
		
		if (strlen($password) < 4) {
			echo 'Error'.$sm['lang'][187]['text'];
			exit;	
		}		
		if($lat == "" || $lat == NULL){
			echo 'Error'.$sm['lang'][186]['text'];
			exit;	
		}	
		
		if($name == "" || $name == NULL || $email == "" || $email == NULL || $password == "" || $password == NULL || $year == "" || $year == NULL){
			echo 'Error'.$sm['lang'][182]['text'];	
			exit;	
		}			
		
		$bio = $sm['lang'][322]['text']." ".$name.", ".$age." ".$sm['lang'][323]['text']." ".$city." ".$country;
		//CHECK IF USER EXIST
		$email_check = $mysqli->query("SELECT email FROM users WHERE email = '".$email."'");	
		if($email_check->num_rows == 1 ){
			echo 'Error'.$sm['lang'][188]['text'];
			exit;
		} else {
			$query = "INSERT INTO users (name,email,pass,age,birthday,gender,city,country,lat,lng,looking,lang,join_date,bio,s_gender,s_age,credits)
									VALUES ('".$name."', '".$email."','".crypt($password)."','".$age."','".$birthday."','".$gender."','".$city."','".$country."','".$lat."','".$lng."','".$looking."','".$_SESSION['lang']."','".$date."','".$bio."','".$looking."','18,30,1','".$sm['config']['free_credits']."')";		
			if ($mysqli->query($query) === TRUE) {
				$last_id = $mysqli->insert_id;

				if($sm['config']['email_verification'] == 1){
					welcomeMailVerification($name,$last_id,$email,$password);
					echo 'Error'.$sm['lang'][324]['text'];
					exit;					
				} else {
					welcomeMailNotification($name,$email,$password);
				}
				$mysqli->query("INSERT INTO users_videocall (u_id) VALUES ('".$last_id."')");	
				$free_premium = $sm['config']['free_premium'];
				$time = time();	
				$extra = 86400 * $free_premium;
				$premium = $time + $extra;
				$mysqli->query("INSERT INTO users_premium (uid,premium) VALUES ('".$last_id."','".$premium."')");	
				$mysqli->query("INSERT INTO users_notifications (uid) VALUES ('".$last_id."')");
				$mysqli->query("INSERT INTO users_extended (uid) VALUES ('".$last_id."')");				
				$_SESSION['user'] = $last_id;
			}							 
		}
	break;
	
	case 'changep':
		$password = secureEncode($_POST['new_password']);
		if (strlen($password) < 4) {
			echo 'Error - Password too short!';	
			exit;	
		}		
		$query = "UPDATE users SET pass = '".crypt($password)."' WHERE id = '".$sm['user']['id']."'";
		$mysqli->query($query);	

	break;
	
	case 'spotlight':
		$id = secureEncode($_POST['s_uid']);
		$time = time();
		$lat = secureEncode($_POST['s_lat']);
		$lng = secureEncode($_POST['s_lng']);
		$photo = secureEncode($_POST['s_photo']);
		$lang = secureEncode($_POST['s_lang']);	
		$price = $sm['price']['spotlight'];
		$query = "INSERT INTO spotlight (u_id,time,lat,lng,photo,lang,country) VALUES ('".$id."', '".$time."', '".$lat."', '".$lng."', '".$photo."', '".$lang."', '".$sm['user']['country']."')";
		$mysqli->query($query);	
		$query2 = "UPDATE users SET credits = credits-'".$price."' WHERE id= '".$id."'";
		$mysqli->query($query2);			
	break;	
	
	case 'gift':
		$id = secureEncode($_POST['g_id']);
		$gift = secureEncode($_POST['g_src']);
		$price = secureEncode($_POST['g_price']);
		getUserInfo($uid,11);
		if($sm['gift']['credits'] < $price){
		 echo 'error';
		 exit;
		}
		$message = '<img src="'.$gift.'"/>';
		$query = "INSERT INTO chat (s_id,r_id,message,time) VALUES ('".$uid."', '".$id."', '".$message."','".$time."')";
		$mysqli->query($query);	
		$query2 = "UPDATE users SET credits = credits-'".$price."' WHERE id= '".$uid."'";
		$mysqli->query($query2);			
	break;	
	
	case 'p_access':
		$id = secureEncode($_POST['id']);
		$query = "INSERT INTO blocked_photos (u1,u2) VALUES ('".$uid."', '".$id."')";
		$mysqli->query($query);			
	break;
	
	case 'delete_profile':
		$mysqli->query("DELETE FROM users WHERE id = '".$uid."'");
		$mysqli->query("DELETE FROM spotlight WHERE u_id = '".$uid."'");
		$mysqli->query("DELETE FROM chat WHERE s_id = '".$uid."'");	
		$mysqli->query("DELETE FROM chat WHERE r_id = '".$uid."'");
		$mysqli->query("DELETE FROM users_visits WHERE u1 = '".$uid."'");	
		$mysqli->query("DELETE FROM users_visits WHERE u2 = '".$uid."'");			
		$mysqli->query("DELETE FROM users_likes WHERE u1 = '".$uid."'");
		$mysqli->query("DELETE FROM users_likes WHERE u2 = '".$uid."'");	
		$mysqli->query("DELETE FROM photos WHERE cid = '".$uid."'");		
		if (isset($_SESSION['user'])) {
			unset($_SESSION['user']);
		}
		if (isset($_SESSION['new_user'])) {
			unset($_SESSION['new_user']);
		}
	break;	
	
	case 'login':
		/*
			if (!empty($_SESSION['lang'])) {
				$sm['lang'] = siteLang($_SESSION['lang']);
			}
		*/		
		$email = secureEncode($_POST['login_email']);	
		$password = secureEncode($_POST['login_pass']);	
		$dID = secureEncode($_POST['login_dID']);	
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			echo 'Error'.$sm['lang'][181]['text'];	
			exit;	
		}		
		
		if($email == "" || $email == NULL || $password == "" || $password == NULL ){
			echo 'Error'.$sm['lang'][182]['text'];
			exit;	
		}			
		
		$email_check = $mysqli->query("SELECT email,id,pass,verified,name FROM users WHERE email = '".$email."'");	
		if($email_check->num_rows == 0 ){
			echo 'Error'.$sm['lang'][183]['text'];	
			exit;
		} else {
			$pass = $email_check->fetch_object();
			if(crypt($password, $pass->pass) == $pass->pass) {
				if($sm['config']['email_verification'] == 1 && $pass->verified == 0){
					echo 'Error'.$sm['lang'][183]['text'].' '.$pass->email;
					welcomeMailVerification($pass->name,$pass->id,$pass->email,'*******');
					exit;	
				}
				$mysqli->query("UPDATE users SET app_id = '".$dID."' where email = '".$email."'");	
				$_SESSION['user'] = $pass->id;
				exit;	
			} else {
				echo 'Error'.$sm['lang'][184]['text'];	
				exit;		
			}			
		}
	break;	



	case 'recover':
		if (!empty($_SESSION['lang'])) {
			$sm['lang'] = siteLang($_SESSION['lang']);
		}	
		$email = secureEncode($_POST['recover_email']);	
		
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			echo 'Error'.$sm['lang'][181]['text'].'!';	
			exit;	
		}		
		
		if($email == "" || $email == NULL ){
			echo 'Error'.$sm['lang'][182]['text'].'!';	
			exit;	
		}			
		
		$email_check = $mysqli->query("SELECT email,id,name FROM users WHERE email = '".$email."'");	
		if($email_check->num_rows == 0 ){
			echo 'Error'.$sm['lang'][183]['text'];	
			exit;
		} else {
			$user = $email_check->fetch_object();
			$time = time();
			$code = md5($time);
			$mysqli->query("INSERT INTO emails (type,uid,code) VALUES (1,'".$user->id."', '".$code."')");			
			$msg = " ".$sm['lang'][177]['text']." ".$user->name." ".$sm['lang'][178]['text']."<br><br><a href='".$sm['config']['site_url']."/index.php?page=recover&code=".$code."&id=".$user->id."'>".$sm['lang'][179]['text']."</a>";
			
			$headers = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			$headers .= 'From: <'.$sm['config']['email'].'>' . "\r\n";			
			$subject = $sm['config']['name'].' - '.$sm['lang'][180]['text'];
			
			mail($email,$subject,$msg,$headers);			
		}
	break;

	
	case 'register_mobile':	
		$name = secureEncode($_POST['name']);
		$email = secureEncode($_POST['email']);	
		$password = secureEncode($_POST['pass']);
		$month = secureEncode($_POST['month']);
		$day = secureEncode($_POST['day']);
		$dID = secureEncode($_POST['dID']);		
		$year = secureEncode($_POST['year']);			
		if(!is_numeric($day) || !is_numeric($year) || !is_numeric($month))
		{
			echo 'Error'.$sm['lang'][185]['text'];	
			exit;			
		}		
		$birthday = date('F', mktime(0, 0, 0, $month, 10)).' '.secureEncode($_POST['day']).', '.secureEncode($_POST['year']);	
		$gender = secureEncode($_POST['gender']);
		$city = secureEncode($_POST['city']);
		$country = secureEncode($_POST['country']);
		$lat = secureEncode($_POST['lat']);
		$lng = secureEncode($_POST['lng']);
		$looking = secureEncode($_POST['looking']);
		$date = date('m/d/Y', time());
		$from = new DateTime($year.'-'.$month.'-'.$day);
		$to   = new DateTime('today');
		$age = $from->diff($to)->y;	
		$photo = secureEncode($_POST['photo']);
		$thumb = secureEncode($_POST['thumb']);	
		$private = secureEncode($_POST['private']);			
		
		if($city == "" || $city == NULL){
			$city = $country;	
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			echo 'Error'.$sm['lang'][181]['text'];	
			exit;	
		}		
		
		if (strlen($password) < 4) {
			echo 'Error'.$sm['lang'][187]['text'];
			exit;	
		}		
		if($lat == "" || $lat == NULL){
			echo 'Error'.$sm['lang'][186]['text'];
			exit;	
		}	
		if($photo == "" || $photo == NULL){
			echo 'Error'.$sm['lang'][321]['text'];
			exit;	
		}					
		if($name == "" || $name == NULL || $email == "" || $email == NULL || $password == "" || $password == NULL || $year == "" || $year == NULL){
			echo 'Error'.$sm['lang'][182]['text'];	
			exit;	
		}			
		$bio = $sm['lang'][322]['text']." ".$name.", ".$age." ".$sm['lang'][323]['text']." ".$city." ".$country;
		//CHECK IF USER EXIST
		$email_check = $mysqli->query("SELECT email FROM users WHERE email = '".$email."'");	
		if($email_check->num_rows == 1 ){
			echo 'Error - '.$sm['lang'][188]['text'];
			exit;
		} else {
			$query = "INSERT INTO users (name,email,pass,age,birthday,gender,city,country,lat,lng,looking,lang,join_date,bio,s_gender,s_age,credits,app_id)
									VALUES ('".$name."', '".$email."','".crypt($password)."','".$age."','".$birthday."','".$gender."','".$city."','".$country."','".$lat."','".$lng."','".$looking."','".$_SESSION['lang']."','".$date."','".$bio."','".$looking."','18,30,1','".$sm['config']['free_credits']."','".$dID."')";	
			if ($mysqli->query($query) === TRUE) {
				$last_id = $mysqli->insert_id;
				if($sm['config']['email_verification'] == 1){
					welcomeMailVerification($name,$last_id,$email,$password);
					echo 'Error - We sended you a email for verify your account';
					exit;					
				} else {
					welcomeMailNotification($name,$email,$password);
				}				
				$_SESSION['user'] = $last_id;	
				$mysqli->query("INSERT INTO users_videocall (u_id) VALUES ('".$last_id."')");	
				$mysqli->query("INSERT INTO users_notifications (uid) VALUES ('".$last_id."')");	
				$free_premium = $sm['config']['free_premium'];
				$time = time();	
				$extra = 86400 * $free_premium;
				$premium = $time + $extra;
				$mysqli->query("INSERT INTO users_premium (uid,premium) VALUES ('".$last_id."','".$premium."')");				
				$mysqli->query("INSERT INTO users_extended (uid) VALUES ('".$last_id."')");	
				$query2 = "INSERT INTO users_photos (u_id,photo,profile,thumb,approved,private) VALUES ('".$last_id."','".$photo."',1,'".$thumb."',1,'".$private."')";
				$mysqli->query($query2);
				$query3 = "SELECT id,s_gender,s_age
				FROM users
				WHERE city = '".$city."'
				AND id <> '".$last_id."'
				ORDER BY last_access";
				$result = $mysqli->query($query3);
				getUserInfo($last_id,0);
				if ($result->num_rows > 0) {
					while($row = $result->fetch_object()){
						$e_age = explode( ',', $row->s_age );
						$age1 = $e_age[0];
						$age2 = $e_age[1];
						$sm['profile_notifications'] = userNotifications($row->id);
						if($row->s_gender == $gender && $sm['profile_notifications']['near_me'] == 1 &&
							$age >= $age1 && $age <= $age2){
								nearMailNotification($row->id,$photo);
						}
					}
				}				
			}							 
		}
	break;
	
	case 'p_photos':
		$id = secureEncode($_POST['id']);
		getUserInfo($id,1);	
		$sm['content'] = requestPage('profile/p_photos');
		echo $sm['content'];		
	break;	
	
	case 'photo':	
		echo getUserPhotos($uid,1,1);
	break;	
	
	case 'wall':
		$id = secureEncode($_POST['id']);
		getUserInfo($id,1);	
		$check = blockedUser($sm['user']['id'],$sm['profile']['id']);
		if($check == 1){
			echo '<script>alert("'.$sm['profile']['name'].' '.$sm['lang'][325]['text'].'");</script>';
			getUserInfo($uid,1);	
			$sm['content'] = requestPage('profile/content');
			echo $sm['content'];				
			exit;
		}		
		$sm['content'] = requestPage('profile/content');
		echo $sm['content'];		
	break;	
	
	case 'chat':
		$id = secureEncode($_POST['id']);
		getUserInfo($id,1);
		$count = getUserTodayConv($uid);	
		$new = getUserTotalConv($uid,$sm['profile']['id']);
		$check = blockedUser($sm['user']['id'],$sm['profile']['id']);
		if($check == 1){
			echo '<script>alert("'.$sm['profile']['name'].' '.$sm['lang'][325]['text'].'");</script>';
			echo '<script>window.location.href="'.$sm['config']['site_url'].'";</script>';
			exit;
		}		
		if($new == 0 && $count >= $sm['basic']['chat'] && $sm['user']['premium'] == 0 || $new == 0 && $count >= $sm['premium']['chat']){
			$sm['content'] = requestPage('chat/premium');
		} else {
			$sm['content'] = requestPage('chat/content');
		}
		echo $sm['content'];		
	break;
	
	case 'chat_p':
		$id = secureEncode($_POST['id']);
		getUserInfo($id,1);	
		$time = time();		
		$mysqli->query("INSERT INTO chat (s_id,r_id,time,access) VALUES ('".$sm['user']['id']."','".$id."','".$time."',1)");		
		$sm['content'] = requestPage('chat/content');
		echo $sm['content'];		
	break;	

	case 'meet':	
		$sm['content'] = requestPage('meet/content');
		echo $sm['content'];		
	break;
	
	case 'meet_back':	
		$sm['content'] = requestPage('meet/content_back');
		echo $sm['content'];		
	break;	
	
	case 'popular':	
		$sm['content'] = requestPage('popular/content');
		echo $sm['content'];		
	break;	
	
	case 'fans':	
		$sm['content'] = requestPage('fans/content');
		echo $sm['content'];		
	break;	
	
	case 'visits':	
		$sm['content'] = requestPage('visits/content');
		echo $sm['content'];		
	break;		
	
	case 'matches':	
		$sm['content'] = requestPage('matches/content');
		echo $sm['content'];		
	break;	
	
	case 'settings':	
		$sm['content'] = requestPage('profile/settings');
		echo $sm['content'];		
	break;		
	
	case 'discover':	
		$sm['content'] = requestPage('discover/content');
		echo $sm['content'];		
	break;	
	
	case 'game':
		$e_age = explode( ',', $sm['user']['s_age'] );
		$age1 = $e_age[0];
		$age2 = $e_age[1];
		$gender = $sm['user']['s_gender'];
		if($gender == 3){
			$u_total = $mysqli->query("SELECT id, ( 6371 * acos( cos( radians('".$sm['user']['lat']."') ) * cos( radians( lat ) ) * 
					  cos( radians( lng ) - radians('".$sm['user']['lng']."') ) + sin( radians('".$sm['user']['lat']."') ) * sin(radians(lat)) ) )
					  AS distance 
					  FROM users
					  WHERE age BETWEEN '".$age1."' AND '".$age2."'				  
					  AND id <> '".$uid."'
					  ORDER BY distance DESC, last_access DESC");
		}else{
			$u_total = $mysqli->query("SELECT id, ( 6371 * acos( cos( radians('".$sm['user']['lat']."') ) * cos( radians( lat ) ) * 
					  cos( radians( lng ) - radians('".$sm['user']['lng']."') ) + sin( radians('".$sm['user']['lat']."') ) * sin(radians(lat)) ) )
					  AS distance 
					  FROM users
					  WHERE age BETWEEN '".$age1."' AND '".$age2."'
					  AND gender = '".$sm['user']['s_gender']."'					  
					  AND id <> '".$uid."'
					  ORDER BY distance DESC, last_access DESC");			
		}
		$array1  = array();
		if ($u_total->num_rows > 0) { 
			while($u_t= $u_total->fetch_object()){
				$array1[] = $u_t->id;						
			}
		}		
		$u_total2 = $mysqli->query("SELECT u2 FROM users_likes where u1 = '".$uid."'");
		$array2  = array();
		
		if ($u_total2->num_rows > 0) {
			while($u_t2 = $u_total2->fetch_object()) {
				$array2[] = $u_t2->u2;						
			}
		}
			
		$resultado = array_diff($array1, $array2);
		$resultado = array_slice($resultado, 0, 1);
		$user_g = array_shift($resultado);
		
		if($user_g == 0){
			if($gender == 3){
				$user_game = $mysqli->query("SELECT id, ( 6371 * acos( cos( radians('".$sm['user']['lat']."') ) * cos( radians( lat ) ) * 
						  cos( radians( lng ) - radians('".$sm['user']['lng']."') ) + sin( radians('".$sm['user']['lat']."') ) * sin(radians(lat)) ) )
						  AS distance 
						  FROM users
						  WHERE age BETWEEN '".$age1."' AND '".$age2."'				  
						  AND id <> '".$uid."'
						  ORDER BY distance DESC, last_access DESC
						  LIMIT 1");
			}else{
				$user_game = $mysqli->query("SELECT id, ( 6371 * acos( cos( radians('".$sm['user']['lat']."') ) * cos( radians( lat ) ) * 
						  cos( radians( lng ) - radians('".$sm['user']['lng']."') ) + sin( radians('".$sm['user']['lat']."') ) * sin(radians(lat)) ) )
						  AS distance 
						  FROM users
						  WHERE age BETWEEN '".$age1."' AND '".$age2."'
						  AND gender = '".$sm['user']['s_gender']."'					  
						  AND id <> '".$uid."'
						  ORDER BY distance DESC, last_access DESC
						  LIMIT 1");			
			}			
		} else {
			$user_game = $mysqli->query("SELECT * FROM users WHERE id = '".$user_g."'");
		}
		if($user_game->num_rows == 1) {
			$sexy_game = $user_game->fetch_object();
			$info = array(
				  "id" => $sexy_game->id,
				  "name" => $sexy_game->name,
				  "status" => userStatusIcon($sexy_game->id),
				  "distance" => distance($sm['user']['lat'],$sm['user']['lng'],$sexy_game->lat,$sexy_game->lng),				  
				  "age" => $sexy_game->age,
				  "city" => $sexy_game->city,				  
				  "photos" => getUserPhotosAll($sexy_game->id),	  
				  "total" => getUserTotalLikers($sexy_game->id),
				  "photo" => profilePhoto($sexy_game->id)
			);	
			echo json_encode($info);
		}
	break;
	
	case 'game_like':
		$id = secureEncode($_POST['id']);
		$action = secureEncode($_POST['like']);		
		$mysqli->query("INSERT INTO users_likes (u1,u2,love) VALUES ('$uid','$id','$action')");
		$sm['profile_notifications'] = userNotifications($id);
		if($sm['profile_notifications']['fan'] == 1){
			fanMailNotification($id);
		}
		if(isFan($id,$sm['user']['id']) == 1 && $sm['profile_notifications']['match_m'] == 1){
			matchMailNotification($id);														   
		}
	break;
	
	case 'instagram':
		$insta = secureEncode($_POST['insta']);
		$url = "https://www.instagram.com/".$insta."/media/";
		$inst_stream = callInstagram($url);
		$results = json_decode($inst_stream, true);
		$i=0;
		foreach($results['items'] as $item){
			$image_link = $item['images']['standard_resolution']['url'];
			if($i == 0){
				$u_foto = $mysqli->query("SELECT * FROM users_photos where u_id = '".$uid."' and profile = 1");		
				if ($u_foto->num_rows == 0) {		
					$mysqli->query("INSERT INTO users_photos(u_id,photo,thumb,profile,approved,private) 
															   VALUES('$uid','$image_link', '$image_link',1,'".$sm['config']['photo_review']."','$image_link')");			
				} else {
				$mysqli->query("INSERT INTO users_photos(u_id,photo,thumb,approved,private)
															   VALUES ('$uid','$image_link', '$image_link','".$sm['config']['photo_review']."','$image_link')");	
				}
			} else {
			$mysqli->query("INSERT INTO users_photos(u_id,photo,thumb,approved,private)
														   VALUES ('$uid','$image_link', '$image_link','".$sm['config']['photo_review']."','$image_link')");	
			}
		}
		$i++;

	break;	
	
	case 'user_notifications':
		$val = secureEncode($_POST['val']);
		$col = secureEncode($_POST['col']);		
		$mysqli->query("UPDATE users_notifications set $col = $val where uid = '".$sm['user']['id']."'");		
	break;	
	
	case 'meet_filter':
		$sm['filter']['age'] = secureEncode($_POST['age']);
		$sm['filter']['gender'] = secureEncode($_POST['gender']);
		$sm['filter']['radius'] = secureEncode($_POST['radius']);
		$sm['filter']['online'] = secureEncode($_POST['online']);
		$sm['filter']['limit'] = secureEncode($_POST['limit']);		
		echo meetFilter($sm['user']['id'],$sm['user']['lang'],$sm['filter']['gender'],$sm['filter']['age'],$sm['filter']['radius'],$sm['filter']['online'],$sm['filter']['limit']);		
	break;	
	
	case 'meet_filter_mobile':
		$sm['filter']['age'] = secureEncode($_POST['age']);
		$sm['filter']['gender'] = secureEncode($_POST['gender']);
		$sm['filter']['radius'] = secureEncode($_POST['radius']);
		$sm['filter']['online'] = secureEncode($_POST['online']);
		$sm['filter']['limit'] = secureEncode($_POST['limit']);		
		echo meetFilterMobile($sm['user']['id'],$sm['user']['lang'],$sm['filter']['gender'],$sm['filter']['age'],$sm['filter']['radius'],$sm['filter']['online'],$sm['filter']['limit']);		
	break;	


	case 'like':
		$id = secureEncode($_POST['id']);
		$mysqli->query("insert into users_likes (u1,u2,love) values ('".$uid."','".$id."',1)");	
	break;		
	
	case 'delete':
		$photo = $_POST['photo'];
		$query = "DELETE FROM users_photos where photo = '$photo'";
		$query2 = "DELETE FROM users_profile_photo where photo = '$photo'";		
		$mysqli->query($query);	
		$mysqli->query($query2);			
	break;
	
	case 'manage':
		$photo = secureEncode($_POST['pid']);
		$profile = secureEncode($_POST['profile']);
		$block = secureEncode($_POST['block']);
		$unblock = secureEncode($_POST['unblock']);
		$del = secureEncode($_POST['del']);
		
		if($profile == 1) {
			$query = "UPDATE users_photos set profile = 0 where u_id = '$uid'";	
			$mysqli->query($query);
			$query2 = "UPDATE users_photos set profile = 1,blocked = 0 where id = '$photo'";	
			$mysqli->query($query2);			
		}
		
		if($block == 1) {
			$query = "UPDATE users_photos set blocked = 1 where id = '$photo'";	
			$mysqli->query($query);				
		}
		
		if($unblock == 1) {
			$query = "UPDATE users_photos set blocked = 0 where id = '$photo'";	
			$mysqli->query($query);				
		}
		
		if($del == 1) {
			$query = "DELETE FROM users_photos where id = '$photo'";	
			$mysqli->query($query);				
		}		
	break;	
	
	case 'search':
		$data = secureEncode($_POST['data']);
		$arr =array();
		$arr['result'] = searchFriends($uid,$data);
		echo json_encode($arr);			
	break;	
	
	case 'lang':
		$lang = secureEncode($_POST['lang']);
		$query = "UPDATE users SET lang = '$lang' WHERE id = '$uid'";		
		$mysqli->query($query);			
	break;	
	
	case 'block':
		$u1 = secureEncode($_POST['u1']);
		$u2 = secureEncode($_POST['u2']);		
		$mysqli->query("INSERT INTO black_list (u1,u2) VALUES ('$u1','$u2')");
		$mysqli->query("DELETE FROM users_friends WHERE u_id ='$u1' AND friend_id = '$u2'");	
		$mysqli->query("DELETE FROM chat WHERE s_id ='$u1' AND r_id = '$u2' OR r_id ='$u1' AND s_id = '$u2'");						   
	break;		
}
$mysqli->close();
