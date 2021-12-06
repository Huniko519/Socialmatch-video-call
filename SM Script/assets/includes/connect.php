<?php
/* Social Match By Xohan - xohansosa@gmail.com */
date_default_timezone_set('America/Chicago');
session_cache_limiter('none');
session_start();


require('config.php');
require 'mail/PHPMailerAutoload.php';

// Connect to SQL Server
$mysqli = new mysqli($db_host, $db_username, $db_password,$db_name);

// Check connection
if (mysqli_connect_errno($mysqli)) {
    exit(mysqli_connect_error());
}

$protocol = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";

if (substr($_SERVER['HTTP_HOST'], 0, 4) !== 'www.') {
    header('Location: '.$protocol.'www.'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    exit;
}

// Stores site configurations to variables for later use
$sm = array();

$sm['price'] = sitePrices();
$sm['basic'] = siteAccountsBasic();
$sm['premium'] = siteAccountsPremium();
$sm['config_email'] = configEmail();

$mobile = false;

$sm['mobile']=0;
$sm['config']['videocall'] = siteConfig('videocall');
$sm['config']['name'] = siteConfig('name');
$check_bar = substr($site_url, -1);
if($check_bar == '/'){
	$sm['config']['site_url'] = $site_url;	
} else {
	$sm['config']['site_url'] = $site_url.'/';	
}
$sm['config']['theme'] = siteConfig('theme');
$sm['config']['theme_mobile'] = siteConfig('theme_mobile');
$sm['config']['theme_email'] = siteConfig('theme_email');
$sm['config']['theme_landing'] = siteConfig('theme_landing');
$sm['config']['logo'] = siteConfig('logo');
$sm['config']['title'] = siteConfig('title');
$sm['config']['description'] = siteConfig('description');
$sm['config']['keywords'] = siteConfig('keywords');
$sm['config']['lang'] = siteConfig('lang');
$sm['config']['photo_review'] = siteConfig('photo_review');
$sm['config']['paypal'] = siteConfig('paypal');
$sm['config']['paygol'] = siteConfig('paygol');
$sm['config']['stripe'] = siteConfig('stripe_pub');
$sm['config']['fortumo'] = siteConfig('fortumo_service');
$sm['config']['currency'] = siteConfig('currency');
$sm['config']['free_credits'] = siteConfig('free_credits');
$sm['config']['free_premium'] = siteConfig('free_premium');
$sm['config']['email'] = siteConfig('email');
$sm['config']['email_verification'] = siteConfig('email_verification');
$sm['config']['theme_url'] = $site_url . '/themes/default';
$sm['config']['theme_url_landing'] = $site_url . '/themes/' . $sm['config']['theme_landing'];
$sm['config']['theme_url_mobile'] = $site_url . '/themes/' . $sm['config']['theme_mobile'];
$sm['config']['theme_url_email'] = $site_url . '/themes/' . $sm['config']['theme_email'];
$sm['config']['ajax_path'] = $site_url . '/requests';
$mp = 4;
$logged = false;
$user = array();
$available_languages = availableLanguages();
$langs = prefered_language($available_languages, $_SERVER["HTTP_ACCEPT_LANGUAGE"]);	
$lang = key($langs);
if($lang != ''){
	$_SESSION['lang'] = checkUserLang($lang);
	$sm['lang'] = siteLang(checkUserLang($lang));
} else{
	$sm['lang'] = siteLang($sm['config']['lang']);
}
if (!empty($_SESSION['user']) && is_numeric($_SESSION['user']) && $_SESSION['user'] > 0) {
	$user_id = secureEncode($_SESSION['user']);
	$logged = true;
	getUserInfo($user_id,0);
	checkUserPremium($user_id);
	$sm['user_notifications'] = userNotifications($user_id);
	$sm['lang'] = siteLang($sm['user']['lang']);	
}

$sm['logged'] = $logged;

if (!empty($_GET['lang'])) {
	$slang = secureEncode($_GET['lang']);
	$_SESSION['lang'] = $slang;
	$sm['lang'] = siteLang($_SESSION['lang']);
	if ($logged == true) {
	   $mysqli->query("UPDATE users SET lang = '".$slang."' WHERE id = '".$user_id."'"); 
	}
}

if ($logged == false) {
    unset($_SESSION['user']);
    unset($user);
}

foreach(glob("assets/includes/custom/*.php") as $file){
	if (file_exists($file)) {
		require $file;
	}
}
