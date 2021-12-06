<?php
/* Social Match By Xohan - xohansosa@gmail.com */
header('Content-Type: application/json');
require_once('../assets/includes/core.php');

$uid = $sm['user']['id'];

switch ($_POST['action']) {
	case 'update':
		$peer = secureEncode($_POST['peer']);
		$mysqli->query("UPDATE users_videocall set peer_id = '".$peer."',status=1 where u_id = '".$uid."'");
	break;
	
	case 'check':
		$id = secureEncode($_POST['id']);
		echo isFan($id,$sm['user']['id']);
	break;
	case 'income':
		$peer = secureEncode($_POST['peer']);
		$peerid = getIdPeer($peer);
		getUserInfo($peerid,5);
		$info = array(
			  "name" => $sm['videocall']['name'],
			  "id" => $sm['videocall']['id'],	  
			  "peer" => $peerid,	  
			  "photo" => profilePhoto($sm['videocall']['id']), 
		);	
		echo json_encode($info);
	break;
	
	case 'invideocall':
		$mysqli->query("UPDATE users_videocall set status=2 where u_id = '".$uid."'");
	break;
	
	case 'log':
		$min = secureEncode($_POST['min']);
		$sec = secureEncode($_POST['sec']);		
		$user = secureEncode($_POST['user']);
		$time = $min.":".$sec;
		$date = date("Y-m-d H:i:s", time());
		$mysqli->query("INSERT INTO videocall (c_id,r_id,time,date) VALUES ('".$uid."','".$user."','".$time."','".$date."')");
	break;	
	
	case 'getpeerid':
		$peer = secureEncode($_POST['id']);
		$peerid = getPeerId($peer);
		$status = getVideocallStatus($peer);
		getUserInfo($peer,5);	
		if ($sm['videocall']['last_access']+300 >= time() && $status == 1) {
			$status = 1;
		}
		$info = array(
			  "name" => $sm['videocall']['name'],
			  "id" => $sm['videocall']['id'],	  
			  "peer" => $peerid,	
			  "status" => $status,		  
			  "photo" => profilePhoto($sm['videocall']['id']), 
		);	
		echo json_encode($info);
	break;
}

$mysqli->close();
