<?php
require_once("assets/includes/core.php");
require_once('assets/social/twitter/twitteroauth.php');
require_once('assets/social/twitter/twitter_classs.php');
$key = siteConfig('twitter_key');
$secret = siteConfig('twitter_secret');
$callback= $sm['config']['site_url'].'assets/sources/twittercallback.php';
$objTwitterApi = new TwitterLoginAPI($key,$secret,$callback);
$return = $objTwitterApi->login_twitter('twitter');
if($return['error']){
	echo $return['error'];
}else{
	header('location:'.$return['url']);
	exit;
}
	
