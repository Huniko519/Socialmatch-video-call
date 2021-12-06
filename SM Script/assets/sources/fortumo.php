<?php
require('../includes/core.php');
$secret = siteConfig('fortumo_secret'); // insert your secret between ''

$amount = $_GET['amount'];
$cuid = $_GET['cuid'];
$status = $_GET['status'];
if($status == 'completed'){
	$mysqli->query("UPDATE users SET credits = credits+'$amount' WHERE id = '$cuid'");
}

function check_signature($params_array, $secret) {
ksort($params_array);

$str = '';
foreach ($params_array as $k=>$v) {
  if($k != 'sig') {
	$str .= "$k=$v";
  }
}
$str .= $secret;
$signature = md5($str);

return ($params_array['sig'] == $signature);
}
