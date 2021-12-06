<?php
require_once("assets/includes/core.php");
require_once("assets/social/instagram/Instagram.php");

use MetzWeb\Instagram\Instagram;

$instagram = new Instagram(array(
    'apiKey' => siteConfig('instagram_key'),
    'apiSecret' => siteConfig('instagram_secret'),
    'apiCallback' => $sm['config']['site_url'].'assets/sources/instagram.php'
));
$loginUrl = $instagram->getLoginUrl();
header("Location: ".$loginUrl);