<?php
require_once('../includes/core.php');
require_once("../social/instagram/Instagram.php");

use MetzWeb\Instagram\Instagram;

$instagram = new Instagram(array(
    'apiKey' => siteConfig('instagram_key'),
    'apiSecret' => siteConfig('instagram_secret'),
    'apiCallback' => $sm['config']['site_url'].'assets/sources/instagram.php'
));

$code = $_GET['code'];

if (isset($code)) {
    // receive OAuth token object
    $data = $instagram->getOAuthToken($code);
    $username = $data->user->username;
    // store user access token
    $instagram->setAccessToken($data);
	$photo = $data->user->profile_picture;
	$location = json_decode(file_get_contents('http://freegeoip.net/json/'.$_SERVER['REMOTE_ADDR']));		
  	$fbid = $data->user->id;         
	$name = $data->user->name; 
	$email = $data->user->email; 
	$gender = 'male';	
	$uid = instaconnect($fbid,$username,$email,$gender,$photo,$location);
    $result = $instagram->getUserMedia();
	
	if($uid != 1){
		foreach ($result->data as $media) {
			if ($media->type === 'video') {
			} else {
				$photo = $media->images->low_resolution->url;
				$mysqli->query("INSERT INTO users_photos (u_id,photo,profile,thumb,approved) VALUES ('".$uid."','".$photo."',1,'".$photo."',1)");
			}
		}	
	}
	header('Location:'.$sm['config']['site_url']);
} else {
    // check whether an error occurred
    if (isset($_GET['error'])) {
        echo 'An error occurred: ' . $_GET['error_description'];
		echo '<br> <a href="'.$sm['config']['site_url'].'"> Go back</a>';
    }
}
