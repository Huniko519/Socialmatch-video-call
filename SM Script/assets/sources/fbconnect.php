<?php
require_once("assets/includes/core.php");
require_once("assets/social/facebook/facebook.php");

$facebook = new Facebook(array(
  'appId'  => siteConfig('fb_app_id'), 
  'secret' => siteConfig('fb_app_secret'), 
  'default_graph_version' => 'v2.5'
));
$fbuser = $facebook->getUser();

if ($fbuser) {

  try {  
    $user_profile = $facebook->api('/me?fields=id,name,email,gender', 'GET');
	
	$location = json_decode(file_get_contents('http://freegeoip.net/json/'.$_SERVER['REMOTE_ADDR']));		
  	$fbid = $user_profile['id'];         
	$name = $user_profile['name']; 
	$email = $user_profile['email']; 
	$gender = $user_profile['gender'];	
	fbconnect($fbid,$name,$email,$gender,$location);   
  } catch (FacebookApiException $e) {
    error_log($e);
   $fbuser = null;
  }
}
if ($fbuser) {
	if($sm['mobile'] == 1){
		header('Location: ' . smoothLink('mobile.php?page=meet'));
	} else {
		header('Location: ' . smoothLink('index.php?page=meet'));
	}
} else {
	$loginUrl = $facebook->getLoginUrl(array(
		'scope'		=> 'email' // Permissions to request from the user
		));
	header("Location: ".$loginUrl);
}

