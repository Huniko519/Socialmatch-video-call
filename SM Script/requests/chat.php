<?php
/* Social Match By Xohan - xohansosa@gmail.com */
header('Content-Type: application/json');
require_once('../assets/includes/core.php');

switch ($_POST['action']) {
	case 'load':
		$uid = secureEncode($_POST['user']);
		getUserInfo($uid,3);
		$mysqli->query("UPDATE chat set seen = 1 where s_id = '".$sm['chat']['id']."' and r_id = '".$sm['user']['id']."'");
		$arr =array();
		$arr['id']=$sm['chat']['id'];
		$arr['name']=$sm['chat']['name'];
		$arr['chat'] = getChat($sm['user']['id'],$sm['chat']['id']);
		echo json_encode($arr);		
	break;		
	
	case 'send':
		$message = secureEncode($_POST['message']);
		$r_id = secureEncode($_POST['r_id']);
		$mobile = $_POST['mobile'];		
		$time = time();		
		$mysqli->query("INSERT INTO chat (s_id,r_id,time,message) VALUES ('".$sm['user']['id']."','".$r_id."','".$time."','".$message."')");
		$arr = array();
		
		$sm['profile_notifications'] = userNotifications($sm['profile']['id']);
		if($sm['profile']['last_access']+300 >= time() && $sm['profile_notifications']['message'] == 1){
			chatMailNotification($r_id,$message);
		} 

		$arr['message'] = cleanMessage($message);
		if($mobile == 1){
			$arr['chat'] = getLastChatMobile($sm['user']['id'],$r_id);
		} else {
			$arr['chat'] = getLastChat($sm['user']['id'],$r_id);	
		}
		echo json_encode($arr);			
	break;	
	
	case 'access':
		$time = time();		
		$access = secureEncode($_POST['access']);
		$r_id = secureEncode($_POST['r_id']);		
		if($access == 1) {
			$mysqli->query("INSERT INTO chat (s_id,r_id,time,message) VALUES 
											  ('".$sm['user']['id']."','".$r_id."','".$time."','".$sm['lang'][189]['text']."')");	
			$mysqli->query("UPDATE chat SET access = 2 WHERE s_id = '".$r_id."' AND r_id = '".$sm['user']['id']."' AND access = 1");
			$mysqli->query("INSERT INTO blocked_photos (u1,u2) VALUES ('".$r_id."','".$sm['user']['id']."')");	
		} else {
			$mysqli->query("INSERT INTO chat (s_id,r_id,time,message) VALUES
											  ('".$sm['user']['id']."','".$r_id."','".$time."','".$sm['lang'][190]['text']."')");	
			$mysqli->query("UPDATE chat SET access = 2 WHERE s_id = '".$r_id."' AND r_id = '".$sm['user']['id']."' AND access = 1");
		}
	break;		
	
	case 'current':
		$uid = secureEncode($_POST['uid']);
		$mob = secureEncode($_POST['mobile']);
		getUserInfo($uid,3);
		$arr = array();	
		$arr['result'] = 0;	
		$results = $mysqli->query("SELECT s_id,id,photo FROM chat WHERE r_id = '".$sm['user']['id']."' AND s_id = '".$uid."' AND seen = 0  order by id asc");
		if($results->num_rows > 0){
			$re = $results->fetch_object();
			$arr['result'] = 1;
			$arr['photo'] = $re->photo;
			if($mob == 1){
				$arr['chat'] = getLastChatMobile2($sm['user']['id'],$uid);
			} else {
				$arr['chat'] = getLastMessage($sm['user']['id'],$uid);	
			}
			$arr['message'] = cleanMessageById($re->id);			
			$mysqli->query("UPDATE chat set seen = 1 where r_id = '".$sm['user']['id']."' and s_id = '".$uid."'");				
		}
		echo json_encode($arr);			
	break;
	
	case 'notification':
		$user = secureEncode($_POST['user']);
		$time = time();			
		$mysqli->query("UPDATE users set last_access = '".$time."' where id = '".$sm['user']['id']."'");		
		$arr = array();	
		if($user == 0){
			$results = $mysqli->query("SELECT DISTINCT s_id FROM chat WHERE r_id = '".$sm['user']['id']."' AND seen = 0 AND notification = 0 order by id desc");
		} else {
			$results = $mysqli->query("SELECT DISTINCT s_id FROM chat WHERE r_id = '".$sm['user']['id']."' AND s_id <> '".$user."' AND seen = 0 AND notification = 0 order by id desc");			
		}
		if($results->num_rows > 0){		
			while($use = $results->fetch_object()){
				$arr[] = $use->s_id;	
			}		
			$mysqli->query("UPDATE chat set notification = 1 where r_id = '".$sm['user']['id']."'");				
		}
		echo json_encode($arr);			
	break;	
	
	case 'new':
		echo getUserFriends($sm['user']['id']);			
	break;	
	
	case 'today':
		$uid = secureEncode($sm['user']['id']);
		$time = time();
		$date = date('m/d/Y', time());
		$mysqli->query("INSERT INTO users_chat (uid,date,count,last_chat) VALUES ('".$uid."','".$date."',1,'".$time."') 
						ON DUPLICATE KEY UPDATE count=count+1");	
	break;	
	
	case 'chat_limit':
		$uid = secureEncode($sm['user']['id']);
		$date = date('m/d/Y', time());
		$mysqli->query("DELETE FROM users_chat WHERE uid = '".$uid."' AND date = '".$date."'");	
	break;		
}

//CLOSE DB CONNECTION
$mysqli->close();
