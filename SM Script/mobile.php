<?php
/* Social Match By Xohan - xohansosa@gmail.com */
require_once('assets/includes/core.php');

if (!isset($_GET['page']) && !isset($_GET['social'])) {
    $_GET['page'] = 'login';
}
$mobile = true;
$sm['mobile'] = 1;
switch ($_GET['page']) {
	
	case 'index':
		if ($logged !== true ) {
			echo getMobilePage('index/content');
			exit;
		} else {
			$folder = 'discover';
			$page = 'content';
			include('assets/sources/pages.php');		
		}
	break;
	
	case 'login':
		if ($logged !== true ) {
			echo getMobilePage('index/content');
			exit;
		} else {
			$folder = 'discover';
			$page = 'content';
			include('assets/sources/pages.php');		
		}
	break;
	
	case 'connect':
		$uID = secureEncode($_GET['uID']);
		$dID = secureEncode($_GET['dID']);
		$device_check = $mysqli->query("SELECT id FROM users WHERE app_id = '".$dID."' and id = '".$uID."' ");	
		if($device_check->num_rows == 0 ){
			echo getMobilePage('index/content');
			exit;
		} else {
			$pass = $device_check->fetch_object();
			getUserInfo($pass->id,0);
			$_SESSION['user'] = $pass->id;
			$folder = 'discover';
			$page = 'content';
			include('assets/sources/pages.php');
		}								  

	break;	
	
	case 'recover':
		if ($logged !== true ) {
			echo getMobilePage('index/recover');
			exit;
		} else {
			$folder = 'discover';
			$page = 'content';
			include('assets/sources/pages.php');		
		}
	break;
	
	case 'register':
		if ($logged !== true ) {
			echo getMobilePage('index/register');
			exit;
		} else {
			$folder = 'discover';
			$page = 'content';
			include('assets/sources/pages.php');		
		}
	break;		
	
	case 'fb':
		if($_SESSION['new_user'] == 1){
			echo getMobilePage('index/fb');
			exit;
		} else {
			echo getMobilePage('index/content');
			exit;				
		}
	break;	
	
	case 'profile':
		if ($logged !== true ) {
			echo getMobilePage('index/content');
			exit;
		}	
		$folder = 'profile';
		$page = 'content';
		include('assets/sources/pages.php');
	break;	
	
	case 'photos':
		if ($logged !== true ) {
			echo getMobilePage('index/content');
			exit;
		}	
		$folder = 'profile';
		$page = 'photos';
		include('assets/sources/pages.php');
	break;		

	case 'meet':
		if ($logged !== true ) {
			echo getMobilePage('index/content');
			exit;
		}	
		$folder = 'meet';
		$page = 'content';
		include('assets/sources/pages.php');
		$_SESSION['prev'] = 'meet'; 
	break;
	
	case 'messages':
		if ($logged !== true ) {
			echo getMobilePage('index/content');
			exit;
		}	
		$folder = 'chat';
		$page = 'messages';
		include('assets/sources/pages.php');
	break;	
	case 'filter':
		if ($logged !== true ) {
			echo getMobilePage('index/content');
			exit;
		}	
		$folder = 'filter';
		$page = 'content';
		include('assets/sources/pages.php');
	break;		
	
	case 'popular':
		if ($logged !== true ) {
			echo getMobilePage('index/content');
			exit;
		}		
		$folder = 'popular';
		$page = 'content';
		include('assets/sources/pages.php');
	break;	
	
	case 'recover':
		if($_GET['id'] != '' && $_GET['code'] != ''){
		$check = checkRecoverCode($_GET['id'],$_GET['code']);
			if($check > 0){
				$_SESSION['user'] = $_GET['id'];
				header('Location:'.$sm['config']['site_url']);
			} else {
				echo getMobilePage('index/content');
				exit;						
			}
		} else {
			echo getMobilePage('index/content');
			exit;			
		}
	break;		
	
	case 'fans':
		if ($logged !== true ) {
			echo getMobilePage('index/content');
			exit;
		}		
		$folder = 'fans';
		$page = 'content';
		include('assets/sources/pages.php');
	break;	
	
	case 'credits-ok':
		if ($logged !== true ) {
			echo getMobilePage('index/content');
			exit;
		}		
		$folder = 'profile';
		$page = 'credits';
		include('assets/sources/pages.php');
	break;	
	
	case 'matches':
		if ($logged !== true ) {
			echo getMobilePage('index/content');
			exit;
		}		
		$folder = 'matches';
		$page = 'content';
		include('assets/sources/pages.php');
	break;
	
	case 'mylikes':
		if ($logged !== true ) {
			echo getMobilePage('index/content');
			exit;
		}		
		$folder = 'matches';
		$page = 'mylikes';
		include('assets/sources/pages.php');
	break;	
	
	case 'settings':
		if ($logged !== true ) {
			echo getMobilePage('index/content');
			exit;
		}		
		$folder = 'profile';
		$page = 'settings';
		include('assets/sources/pages.php');
	break;
	
	case 'terms':
		if ($logged !== true ) {
			echo getMobilePage('index/content');
			exit;
		}		
		$folder = 'terms';
		$page = 'terms';
		include('assets/sources/pages.php');
	break;
	
	case 'tac':
		echo getMobilePage('index/tac');
		exit;	
	break;
	
	case 'pp':
		echo getMobilePage('index/pp');
		exit;	
	break;	
	
	case 'privacy':
		if ($logged !== true ) {
			echo getMobilePage('index/content');
			exit;
		}		
		$folder = 'terms';
		$page = 'privacy';
		include('assets/sources/pages.php');
	break;	
	
	case 'discover':
		if ($logged !== true ) {
			echo getMobilePage('index/content');
			exit;
		}
		$age = $sm['user']['s_age'];
		if($_GET['age1'] != NULL ){
			$age = secureEncode($_GET['age1']);
			$age = $age.','.secureEncode($_GET['age2']);			
		}
		$radius = $sm['user']['s_radius'];
		if($_GET['radius'] != NULL ){
			$radius = secureEncode($_GET['radius']);
		}
		$looking = $sm['user']['s_gender'];
		if($_GET['looking'] != NULL ){
			$looking = secureEncode($_GET['looking']);
		}
		updateUserFilter($looking,$age,$radius,$sm['user']['id']);
		$folder = 'discover';
		$page = 'content';
		include('assets/sources/pages.php');
		$_SESSION['prev'] = 'discover'; 
	break;	
	
	case 'visits':
		if ($logged !== true ) {
			echo getMobilePage('index/content');
			exit;
		}		
		$folder = 'visits';
		$page = 'content';
		include('assets/sources/pages.php');
	break;	
	
	case 'chat':
		if ($logged !== true ) {
			echo getMobilePage('index/content');
			exit;
		}	
		$folder = 'chat';
		$count = getUserTodayConv($sm['user']['id']);
		$new = getUserTotalConv($sm['user']['id'],$_GET['id']);
		if($new == 0 && $count >= $sm['basic']['chat'] && $sm['user']['premium'] == 0){
			$page = 'premium';
		} else {
			$page = 'content';
		}		
		
		include('assets/sources/pages.php');
	break;	
	
	case 'logout':
		include('assets/sources/logout_m.php');
	break;			
}

switch ($_GET['social']) {
				
	case 'fb':
		include('assets/sources/fbconnect.php');
		exit;
	break;		
	
}	

echo getMobilePage('container');
$mysqli->close();