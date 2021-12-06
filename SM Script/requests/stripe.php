<?php
require_once('../assets/includes/core.php');
require_once('../assets/sources/stripe.php');
$stripe_key = siteConfig('stripe_secret');
\Stripe\Stripe::setApiKey($stripe_key);

$token = $_POST['token'];
$price = $_POST['price'];
$de = $_POST['de'];
$id = $_POST['uid'];
$credits = $_POST['quantity'];
$app = $_POST['app'];
$live = '';
try {
  $charge = \Stripe\Charge::create(array(
    "amount" => $price,
    "currency" => $sm['config']['currency'],
    "source" => $token,
    "description" => $de
    ));
  	
	if($app == 1){
		$mysqli->query("UPDATE users SET credits = credits+'".$credits."' WHERE id = '".$id."'");	
	}else{
		
	}  
} catch(\Stripe\Error\Card $e) {
}

