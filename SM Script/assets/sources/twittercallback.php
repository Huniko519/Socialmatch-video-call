<?php
require_once("../includes/core.php");
require_once('../social/twitter/twitter_classs.php');
require_once('../social/twitter/twitteroauth.php');
if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
  $_SESSION['oauth_status'] = 'oldtoken';
	session_destroy();
	header('Location: '.$sm['config']['site_url']);
}else{
	$key = siteConfig('twitter_key');
	$secret = siteConfig('twitter_secret');
	$callback= $sm['config']['site_url'].'assets/sources/twittercallback.php';
	$objTwitterApi = new TwitterLoginAPI($key,$secret,$callback);
	$connection = $objTwitterApi->twitter_callback();
	if( $connection == 'connected'){
			$return = $objTwitterApi->view();
			$photo = $return['profile_image_url'];
			$location = json_decode(file_get_contents('http://freegeoip.net/json/'.$_SERVER['REMOTE_ADDR']));		
			$id = $return['id'];         
			$name = $return['name']; 
			$email = ' '; 
			$gender = 'male';
			twitterconnect($id,$name,$email,$gender,$photo,$location); 
			header('Location:'.$sm['config']['site_url']);
	}else{
		header('Location:'.$sm['config']['site_url']);
		exit;
	}
}
