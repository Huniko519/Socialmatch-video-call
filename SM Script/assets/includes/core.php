<?php
/* Social Match By Xohan - xohansosa@gmail.com */
header('Content-Type: text/html; charset=UTF-8');
require_once('connect.php');


function getIdInterest($name) {
	global $mysqli,$sm;
	
	$name = secureEncode($name);
	$user = $mysqli->query("SELECT id FROM interest WHERE name = '".$name."'");
	$u = $user->fetch_object();
	
	return 	$u->id;
}

function checkInterestExist($name) {
	global $mysqli;
	
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM interest WHERE name = '".$name."'");
	$total = $query->fetch_assoc();
	$result = $total['total'];
	
	return $result;
}


function profilePhoto($uid) {
	global $mysqli,$sm;
	
	$uid = secureEncode($uid);
	$photo = $mysqli->query("SELECT photo,thumb FROM users_photos where u_id = '".$uid."' and approved = 1 and profile = 1  LIMIT 1");
	if($photo->num_rows == 1) {
		$profile = $photo->fetch_object();	
		if (file_exists($profile->thumb)) {	
			$profile_photo = $profile->thumb;
		} else {
			$profile_photo = $profile->photo;
		}
	} else {
		$profile_photo = $sm['config']['theme_url']."/images/no_user.jpg";
	}
	return $profile_photo;
}

function profilePhotoThumb($uid) {
	global $mysqli,$sm;
	
	$uid = secureEncode($uid);
	$photo = $mysqli->query("SELECT thumb FROM users_photos where u_id = '".$uid."' and approved = 1 and profile = 1 LIMIT 1");
	if($photo->num_rows == 1) {
		$profile = $photo->fetch_object();
		$profile_photo = $profile->thumb;
	} else {
		$profile_photo = $sm['config']['theme_url']."/images/no_user.jpg";
	}
	return $profile_photo;
}

function userStatus($uid) {
	global $mysqli,$sm;
	
	$uid = secureEncode($uid);
	$status = $mysqli->query("SELECT last_access FROM users where id = '".$uid."'");
	$st = $status->fetch_object();	
	if($st->last_access+300 >= time()){
		$s = "y";
	} else {
		$s = "n";
	}
	return $s;
}


function distance($lat1, $lon1, $lat2, $lon2){
  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $kilometer = $miles * 1.609344;
  $kilometer = round($kilometer);
  return $kilometer; 
}

function userStatusIcon($uid) {
	global $mysqli,$sm;
	
	$uid = secureEncode($uid);
	$status = $mysqli->query("SELECT last_access,fake,online_day FROM users where id = '".$uid."'");
	$st = $status->fetch_object();	
	$today = date('w');
	if($st->last_access+300 >= time() || $st->fake == 1 && $st->online_day == $today){
		$s = '<i class="mdi-image-brightness-1" style="color:#17d425"></i>';
	} else {
		$s = '';
	}
	return $s;
}

function userStatusSpotlight($uid) {
	global $mysqli,$sm;
	
	$uid = secureEncode($uid);
	$status = $mysqli->query("SELECT last_access FROM users where id = '".$uid."'");
	$st = $status->fetch_object();	
	if($st->last_access+300 >= time()){
		$s = '<i class="mdi-image-brightness-1 online" ></i>';
	} else {
		$s = '';
	}
	return $s;
}

function userStatusSpotlightMobile($uid) {
	global $mysqli,$sm;
	
	$uid = secureEncode($uid);
	$status = $mysqli->query("SELECT last_access FROM users where id = '".$uid."'");
	$st = $status->fetch_object();	
	if($st->last_access+300 >= time()){
		$s = '<div class="online"></div>';
	} else {
		$s = '';
	}
	return $s;
}

function userStatusMessagesMobile($uid) {
	global $mysqli,$sm;
	
	$uid = secureEncode($uid);
	$status = $mysqli->query("SELECT last_access FROM users where id = '".$uid."'");
	$st = $status->fetch_object();	
	if($st->last_access+300 >= time()){
		$s = '<div style="	position:absolute;top:16px;left:48px;width:12px;height:12px;border-radius:50%;background:#2acf2a;border: 2px solid #fff;z-index:99999;"></div>';
	} else {
		$s = '';
	}
	return $s;
}

function userFilterStatus($uid) {
	global $mysqli,$sm;
	$today = date('w');
	$uid = secureEncode($uid);
	$status = $mysqli->query("SELECT last_access FROM users where id = '".$uid."'");
	$st = $status->fetch_object();	
	if($st->last_access+300 >= time() || $st->fake == 1 && $st->online_day == $today){
		$s = 1;
	} else {
		$s = 0;
	}
	return $s;
}




function searchUser($q) {
	global $mysqli,$sm;
	$search = '';
	$query = $mysqli->query("SELECT id FROM users where id = '".$q."' OR name LIKE '%$q%' OR email LIKE '%$q%' LIMIT 10");

	if ($query->num_rows > 0) {
		while($user = $query->fetch_object()){
			getUserInfo($user->id,6);
			$search .= ' <tr>
							  <td class="man-photos"><div class="profile-photo" data-src="'.$sm['search']['profile_photo'].'"></td>						
							  <td>'.$sm['search']['id'].'</td>				  
							  <td>'.$sm['search']['name'].' , '.$sm['search']['age'].'
							  '; if($sm['search']['last_access'] >= $time_now) {
							  	$search .= ' <i class="fa fa-circle text-success" style="font-size:8px;"></i>';
							  }
							  
							  $search .= '
							  </td>						  
							  <td>'.$sm['search']['email'].'</td>
							  <td>'.$sm['search']['city'].'</td>
							  <td>'.$sm['search']['country'].'</td>					  
							  <td>'.$sm['search']['credits'].'</td>
							  <td>'.$sm['search']['total_photos'].'</td>
							  <td>'.$sm['search']['join_date'].'</td>					  
							  <td><a href="index.php?page=profile&id='.$sm['search']['id'].'" target="_blank" class="label label-info">View</a> 
							  <a href="index.php?page=admin&p=user&id='.$sm['search']['id'].'" target="_blank" class="label label-primary">Edit</a></td>					
							</tr>';
		}
	}
	return $search;			
}

function getOnlineUsers() {
	global $mysqli,$sm;
	
	$search = '';
	$time_now = time()-300;
	$query = $mysqli->query("SELECT id FROM users WHERE last_access >='".$time_now."' LIMIT 10");
	if ($query->num_rows > 0) {
		while($user = $query->fetch_object()){
			getUserInfo($user->id,6);
			$search.='<li style="width:100px;height:100px;">
                      <div class="profile-photo" style="border-radius:50%;" data-src="'.$sm['search']['profile_photo'].'" ></div>
                      <a class="users-list-name" href="index.php?page=admin&p=user&id='.$sm['search']['id'].'">'.$sm['search']['first_name'].'</a>
                    </li>';
		}
	}
	return $search;
}

function getLatestUsersProfilePhoto() {
	global $mysqli,$sm;
	
	$search = '';
	$i=0;
	$query = $mysqli->query("SELECT thumb,u_id FROM users_photos where profile = 1 ORDER BY id DESC LIMIT 6");
	if ($query->num_rows > 0) {
		while($user = $query->fetch_object()){
			getUserInfo($user->u_id,6);
			$search.='<div class="photo" style="background-image: url('.$sm['search']['profile_photo'].')"></div>';
		}
	}
	return $search;
}

function getLatestUsers() {
	global $mysqli,$sm;
	
	$search = '';
	$time_now = time()-300;
	$query = $mysqli->query("SELECT id FROM users ORDER BY id DESC LIMIT 10");
	if ($query->num_rows > 0) {
		while($user = $query->fetch_object()){
			getUserInfo($user->id,6);
			$search.='<li style="width:100px;height:100px;">
                      <div class="profile-photo" style="border-radius:50%;" data-src="'.$sm['search']['profile_photo'].'" ></div>
                      <a class="users-list-name" href="index.php?page=admin&p=user&id='.$sm['search']['id'].'">'.$sm['search']['first_name'].'</a>
                    </li>';
		}
	}
	return $search;
}

function getTotalUsers($value) {
	global $mysqli;
	
	if($value == 1) {
		$add = "";
	}
	if($value == 2) {
		$add = "WHERE last_access >=1";
	}
	if($value == 3) {
		$time_now = time()-300;
		$add = "WHERE last_access >=".$time_now;
	}	
	$query = $mysqli->query("SELECT count(*) as total FROM users $add");
	$total = $query->fetch_assoc();
	
	return $total['total'];
}

function getTotalPhotos($value) {
	global $mysqli;
	
	if($value == 1) {
		$add = "";
	}
	if($value == 2) {
		$add = "WHERE aprovada = 0";
	}	
	
	$query = $mysqli->query("SELECT count(*) as total FROM users_photos $add");
	$total = $query->fetch_assoc();
	
	return $total['total'];
}

function getTotalPhotosReview() {
	global $mysqli;
	
	$query = $mysqli->query("SELECT count(*) as total FROM users_photos where approved = 0");
	if($query->num_rows > 0){
	$total = $query->fetch_assoc();
	} else {
		$total['total'] = 0;	
	}
	return $total['total'];
}

function getTotalPhotosPrivate() {
	global $mysqli;
	
	$query = $mysqli->query("SELECT count(*) as total FROM users_photos where blocked = 1");
	if($query->num_rows > 0){
	$total = $query->fetch_assoc();
	} else {
		$total['total'] = 0;	
	}
	return $total['total'];
}

function getPhotosReview() {
	global $mysqli;
	
	$r = '';
	$query = $mysqli->query("SELECT photo,id,thumb FROM users_photos where approved = 0 ORDER BY id DESC LIMIT 20");
	if($query->num_rows > 0){
		while($result = $query->fetch_object()){
			$r .= '<li style="width:10%;height:100px;">
		   			  <div class="profile-photo" data-src="'.$result->thumb.'" data-psrc="'.$result->photo.'" data-review="'.$result->id.'"></div>
                    </li>';	
		}
	}
	
	return $r;
}

function getPhotosUser($uid) {
	global $mysqli;
	
	$r = '';
	$query = $mysqli->query("SELECT photo,id,thumb FROM users_photos where approved = 1 and u_id = '".$uid."' ORDER BY id DESC LIMIT 20");
	if($query->num_rows > 0){
		while($result = $query->fetch_object()){
			$r .= '<li style="width:20%;height:100px;">
		   			  <div class="profile-photo" data-src="'.$result->thumb.'" data-psrc="'.$result->photo.'" data-review="'.$result->id.'"></div>
                    </li>';	
		}
	}
	
	return $r;
}

function getAdminPhotos($f,$p) {
	global $mysqli;
	
	$r = '';
	if($f == 1){
	$query = $mysqli->query("SELECT photo,id,thumb FROM users_photos where approved <> 2 ORDER BY id DESC LIMIT $p,50");
	}
	if($f == 2){
	$query = $mysqli->query("SELECT photo,id,thumb FROM users_photos where approved = 0 ORDER BY id DESC LIMIT 50");
	}
	if($f == 3){
	$query = $mysqli->query("SELECT photo,id,thumb FROM users_photos where blocked = 1 and approved <> 2 ORDER BY id DESC LIMIT 50");
	}	
	if($query->num_rows > 0){
		while($result = $query->fetch_object()){
			$r .= '<li style="width:20%;height:200px;">
		   			  <div class="profile-photo" data-src="'.$result->thumb.'" data-psrc="'.$result->photo.'" data-review="'.$result->id.'"></div>
                    </li>';	
		}
	}
	
	return $r;
}

function getAdminThemesLanding() {
	global $mysqli;
	
	$r = '';
	$current = siteConfig('theme_landing');
	$query = $mysqli->query("SELECT * FROM config_themes where type = 2");
	if($query->num_rows > 0){
		while($result = $query->fetch_object()){
			$r .= '<div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">'.$result->name.'</h3>
                </div><!-- /.box-header -->
                <img src="'.$result->screenshot.'" width="100%"/>
                  <div class="box-footer">';
				  if($result->folder == $current){
                    $r.='<button class="btn btn-success">Selected</button>';
				  } else {
					  $r.='<button class="btn btn-primary" data-theme="'.$result->folder.'" data-type="theme_landing">Select</button>';
				  }
				  $r.='
                  </div>			
              </div>';	
		}
	}
	return $r;
}

function getAdminThemesFrontEnd() {
	global $mysqli;
	
	$r = '';
	$current = siteConfig('theme');
	$query = $mysqli->query("SELECT * FROM config_themes where type = 1");
	if($query->num_rows > 0){
		while($result = $query->fetch_object()){
			$r .= '<div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">'.$result->name.'</h3>
                </div><!-- /.box-header -->
                <img src="'.$result->screenshot.'" width="100%"/>
                  <div class="box-footer">';
				  if($result->folder == $current){
                    $r.='<button class="btn btn-success">Selected</button>';
				  } else {
					  $r.='<button class="btn btn-primary" data-theme="'.$result->folder.'" data-type="theme">Select</button>';
				  }
				  $r.='
                  </div>			
              </div>';	
		}
	}
	return $r;
}

function getAdminThemesMobile() {
	global $mysqli;
	
	$r = '';
	$current = siteConfig('theme_mobile');
	$query = $mysqli->query("SELECT * FROM config_themes where type = 3");
	if($query->num_rows > 0){
		while($result = $query->fetch_object()){
			$r .= '<div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">'.$result->name.'</h3>
                </div><!-- /.box-header -->
                <img src="'.$result->screenshot.'" width="100%"/>
                  <div class="box-footer">';
				  if($result->folder == $current){
                    $r.='<button class="btn btn-success">Selected</button>';
				  } else {
					  $r.='<button class="btn btn-primary" data-theme="'.$result->folder.'" data-type="theme_mobile">Select</button>';
				  }
				  $r.='
                  </div>			
              </div>';	
		}
	}
	return $r;
}

function getAdminThemesEmail() {
	global $mysqli;
	
	$r = '';
	$current = siteConfig('theme_email');
	$query = $mysqli->query("SELECT * FROM config_themes where type = 4");
	if($query->num_rows > 0){
		while($result = $query->fetch_object()){
			$r .= '<div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">'.$result->name.'</h3>
                </div><!-- /.box-header -->
                <img src="'.$result->screenshot.'" width="100%"/>
                  <div class="box-footer">';
				  if($result->folder == $current){
                    $r.='<button class="btn btn-success">Selected</button>';
				  } else {
					  $r.='<button class="btn btn-primary" data-theme="'.$result->folder.'" data-type="theme_email">Select</button>';
				  }
				  $r.='
                  </div>			
              </div>';	
		}
	}
	return $r;
}


function getTotalVideocalls($value) {
	global $mysqli;
	$query = $mysqli->query("SELECT count(*) as total FROM videocall");
	$total = $query->fetch_assoc();
	
	return $total['total'];
}

function getActiveVideocalls() {
	global $mysqli;
		
	$query = $mysqli->query("SELECT count(*) as total FROM users_videocall where status = 2 ");
	$total = $query->fetch_assoc();
	
	return $total['total'];
}

function checkMessageRead($uid1,$uid2) {
	global $mysqli,$sm;
	
	$query = $mysqli->query("SELECT * FROM chat where s_id = '".$uid1."' and r_id = '".$uid2."' order by id desc LIMIT 1");
	$read = $query->fetch_object();
	return $read->seen;
}

function checkUserLang($lang){
	global $mysqli,$sm;
	
	$query = $mysqli->query("SELECT id FROM languages where prefix = '".$lang."'");
	if($query->num_rows >= 1) {
		$result = $query->fetch_object();
		$ret = $result->id;
	} else {
		$ret = $sm['config']['lang'];	
	}
	return $ret;	
}

function getOldLang($lang){
	global $mysqli,$sm;
	
	$query = $mysqli->query("SELECT name FROM languages where id = '".$lang."'");
	if($query->num_rows >= 1) {
		$result = $query->fetch_object();
		$ret = $result->name;
	} else {
		$ret = 'error';	
	}
	return $ret;	
}

function getSiteLangs($no){
	global $mysqli,$sm;
	$ret = '';
	$query = $mysqli->query("SELECT * FROM languages WHERE id <> '$no' and visible = 1 ORDER BY id ASC");
	if($query->num_rows >= 1) {
		while($result = $query->fetch_object()){
		$ret.= '<option value="'.$result->id.'">'.$result->name.'</option>';
		}
	}
	return $ret;	
}

function getSiteLangsIndex($start,$finish){
	global $mysqli,$sm;
	$ret = '';
	$query = $mysqli->query("SELECT * FROM languages where visible = 1 ORDER BY id ASC LIMIT $start,$finish");
	if($query->num_rows >= 1) {
		while($result = $query->fetch_object()){
		$ret.= '<td data-lang="'.$result->id.'">'.$result->name.'</td>';
		}
	}
	return $ret;	
}

function getUserId($tid) {
	global $mysqli,$sm;
	
	$query = $mysqli->query("SELECT id FROM users where twitter_id = '".$tid."'");
	$user = $query->fetch_object();
	return $user->id;
}

function getCurrentLang($id) {
	global $mysqli,$sm;
	
	$query = $mysqli->query("SELECT name FROM languages where id = '".$id."'");
	$lang = $query->fetch_object();
	return $lang->name;
}

function getUserInfo($uid,$value=0) {
	global $mysqli,$sm;
	
	$uid = secureEncode($uid);
	$user = $mysqli->query("SELECT * FROM users WHERE id = '".$uid."'");
	$u = $user->fetch_object();	
	$first_name = explode(' ',trim($u->name));	
	$first_name = explode('_',trim($first_name[0]));
	//CURRENT USER
	$current_user['id'] = $u->id;
	$current_user['name'] = $u->name;
	$current_user['email'] = $u->email;	
	$current_user['gender'] = $u->gender;		
	$current_user['facebook_id'] = $u->facebook_id;	
	$current_user['first_name'] = $first_name[0];
	$current_user['profile_photo'] = profilePhoto($u->id);
	$current_user['profile_photo_thumb'] = profilePhotoThumb($u->id);	
	$current_user['total_photos'] = getUserTotalPhotos($u->id);	
	$current_user['total_photos_public'] = getUserTotalPhotosPublic($u->id);		
	$current_user['total_photos_private'] = getUserTotalPhotosPrivate($u->id);		
	$current_user['total_likers'] = getUserTotalLikers($u->id);
	$current_user['total_likes'] = getUserTotalLikes($u->id);	
	$current_user['extended'] = userExtended($u->id);
	$current_user['interest'] = userInterest($u->id);	
	$current_user['status_info'] = userFilterStatus($u->id);
	//$current_user['status'] = userStatus($u->id);	
	$current_user['city'] = $u->city;
	$current_user['country'] = $u->country;	
	$current_user['age'] = $u->age;
	$current_user['lat'] = $u->lat;
	$current_user['lng'] = $u->lng;	
	$current_user['birthday'] = $u->birthday;		
	$current_user['last_access'] = $u->last_access;	
	$current_user['admin'] = $u->admin;		
	$current_user['lang'] = $u->lang;	
	$current_user['looking'] = $u->looking;	
	$current_user['premium'] = $u->premium;	
	$current_user['premium_check'] = adminCheckUserPremium($uid);		
	$current_user['verified'] = $u->verified;	
	$current_user['popular'] = $u->popular;
	$current_user['credits'] = $u->credits;	
	$current_user['link'] = clean($first_name[0]);		
	$current_user['status'] = userStatus($u->id);
	$current_user['online'] = userStatusIcon($u->id);	
	$current_user['fake'] = $u->fake;
	$current_user['join_date'] = $u->join_date;	
	$current_user['bio'] = $u->bio;
	$current_user['s_gender'] = $u->s_gender;	
	$current_user['s_radius'] = $u->s_radious;
	$current_user['s_age'] = $u->s_age;	
	$current_user['online_day'] = $u->online_day;			
	if($value == 1){
		$sm['profile'] = $current_user; 
	} else if($value == 2){
		$sm['meet'] = $current_user;	
	} else if($value == 3){
		$sm['chat'] = $current_user;	
	}	else if($value == 4){
		$sm['friend'] = $current_user;	
	} else if($value == 5){
		$sm['videocall'] = $current_user;	
	} else if($value == 6){
		$sm['search'] = $current_user;	
	} else if($value == 7){
		$sm['manage'] = $current_user;	
	} else if($value == 8){
		$sm['edit'] = $current_user;	
	} else if($value == 9){
		$sm['comment'] = $current_user;	
	} else if($value == 10){
		$sm['suggest'] = $current_user;	
	}  else if($value == 11){
		$sm['gift'] = $current_user;	
	}  else if($value == 12){
		$sm['mail'] = $current_user;	
	} else{
		$sm['user'] = $current_user;	
	}
}

function testMailNotification(){
	global $mysqli,$sm,$lang;
	
	$message = file_get_contents($sm['config']['site_url'].'/themes/' . $sm['config']['theme_email'] . '/layout/test.phtml');
	preg_match_all('~{lang}([^<]*){/lang}~', $message, $matches);
	foreach($matches[0] as $match){
		$parsed = get_string_between($match, '{lang}', '{/lang}');	
		$message = str_replace('{lang}'.$parsed.'{/lang}', $sm['lang'][$parsed]['text'], $message);	
	}		
	$mail = new PHPMailer;
	$mail->IsSMTP();
	$mail->Host = $sm['config_email']['host']; 
	$mail->Port = $sm['config_email']['port']; 
	$mail->SMTPSecure = 'tls';	
	$mail->SMTPAuth = true;                              
	$mail->Username = $sm['config_email']['user'];                 
	$mail->Password = $sm['config_email']['password'];                         	
	$mail->setFrom($sm['config_email']['user'], $sm['config']['name']);
	$mail->addAddress($sm['config']['email'],$sm['config']['name']);
	$mail->Subject = "Test smtp email";
	$mail->msgHTML($message);
	if($mail->send()) {
		return 'Test Email sended to '. $sm['config']['email'];
	}else {
		return 'Error something went wrong, please verify the information';
	}
}

function fanMailNotification($sexy_id){
	global $mysqli,$sm,$lang;
	
	getUserInfo($sexy_id,12);
	$sm['lang'] = siteLang($sm['mail']['lang']);
	$message = file_get_contents($sm['config']['site_url'].'/themes/' . $sm['config']['theme_email'] . '/layout/crush.phtml');
	$message = str_replace('$sexy_name', $sm['mail']['name'], $message);
	$message = str_replace('$sexy_email', $sm['mail']['email'], $message);
	$message = str_replace('$user_name', $sm['user']['name'], $message);
	$message = str_replace('$user_age', $sm['user']['age'], $message);
	$message = str_replace('$user_photo', $sm['user']['profile_photo'], $message);	
	$message = str_replace('$user_url', $sm['config']['site_url']."index.php?page=profile&id=".$sm['user']['id'], $message);	
	$message = str_replace('$site_url', $sm['config']['site_url'], $message);	
	$message = str_replace('$site_name', $sm['config']['name'], $message);
	$message = str_replace('$site_email', $sm['config']['theme_url_email'], $message);
	preg_match_all('~{lang}([^<]*){/lang}~', $message, $matches);
	foreach($matches[0] as $match){
		$parsed = get_string_between($match, '{lang}', '{/lang}');	
		$message = str_replace('{lang}'.$parsed.'{/lang}', $sm['lang'][$parsed]['text'], $message);	
	}
	$mail = new PHPMailer;
	$mail->IsSMTP();
	$mail->Host = $sm['config_email']['host']; 
	$mail->Port = $sm['config_email']['port']; 
	$mail->SMTPSecure = 'tls';	
	$mail->SMTPAuth = true;                              
	$mail->Username = $sm['config_email']['user'];                 
	$mail->Password = $sm['config_email']['password'];                         	
	$mail->setFrom($sm['config']['email'], $sm['config']['name']);
	$mail->addAddress($sm['mail']['email'],$sm['mail']['name']);
	$mail->CharSet = 'UTF-8';	
	$mail->Subject = $sm['user']['name']." ".$sm['lang'][343]['text'];
	$mail->msgHTML($message);
	if($mail->send()) {
	}
}

function matchMailNotification($sexy_id){
	global $mysqli,$sm,$lang;
	
	getUserInfo($sexy_id,12);
	$sm['lang'] = siteLang($sm['mail']['lang']);	
	$message = file_get_contents($sm['config']['site_url'].'/themes/' . $sm['config']['theme_email'] . '/layout/match.phtml');
	$message = str_replace('$sexy_name', $sm['mail']['name'], $message);
	$message = str_replace('$sexy_email', $sm['mail']['email'], $message);
	$message = str_replace('$user_name', $sm['user']['name'], $message);
	$message = str_replace('$user_age', $sm['user']['age'], $message);
	$message = str_replace('$user_photo', $sm['user']['profile_photo'], $message);	
	$message = str_replace('$user_url', $sm['config']['site_url']."index.php?page=profile&id=".$sm['user']['id'], $message);	
	$message = str_replace('$site_url', $sm['config']['site_url'], $message);	
	$message = str_replace('$site_name', $sm['config']['name'], $message);
	$message = str_replace('$site_email', $sm['config']['theme_url_email'], $message);	
	preg_match_all('~{lang}([^<]*){/lang}~', $message, $matches);
	foreach($matches[0] as $match){
		$parsed = get_string_between($match, '{lang}', '{/lang}');	
		$message = str_replace('{lang}'.$parsed.'{/lang}', $sm['lang'][$parsed]['text'], $message);	
	}	
	$mail = new PHPMailer;
	$mail->IsSMTP();
	$mail->Host = $sm['config_email']['host']; 
	$mail->Port = $sm['config_email']['port']; 
	$mail->SMTPSecure = 'tls';	
	$mail->SMTPAuth = true;                              
	$mail->Username = $sm['config_email']['user'];                 
	$mail->Password = $sm['config_email']['password'];                         	
	$mail->setFrom($sm['config']['email'], $sm['config']['name']);
	$mail->addAddress($sm['mail']['email'],$sm['mail']['name']);
	$mail->CharSet = 'UTF-8';	
	$mail->Subject = $sm['user']['first_name']." ".$sm['lang'][343]['text']." ".$sm['config']['name'];
	$mail->msgHTML($message);
	if($mail->send()) {
	}
}

function nearMailNotification($sexy_id,$photo){
	global $mysqli,$sm,$lang;
	
	getUserInfo($sexy_id,12);
	$sm['lang'] = siteLang($sm['mail']['lang']);	
	$message = file_get_contents($sm['config']['site_url'].'/themes/' . $sm['config']['theme_email'] . '/layout/near.phtml');
	$message = str_replace('$sexy_name', $sm['mail']['name'], $message);
	$message = str_replace('$sexy_email', $sm['mail']['email'], $message);
	$message = str_replace('$user_name', $sm['user']['name'], $message);
	$message = str_replace('$user_age', $sm['user']['age'], $message);
	$message = str_replace('$user_photo', $photo, $message);	
	$message = str_replace('$user_url', $sm['config']['site_url']."index.php?page=profile&id=".$sm['user']['id'], $message);	
	$message = str_replace('$site_url', $sm['config']['site_url'], $message);	
	$message = str_replace('$site_name', $sm['config']['name'], $message);
	$message = str_replace('$site_email', $sm['config']['theme_url_email'], $message);	
	preg_match_all('~{lang}([^<]*){/lang}~', $message, $matches);
	foreach($matches[0] as $match){
		$parsed = get_string_between($match, '{lang}', '{/lang}');	
		$message = str_replace('{lang}'.$parsed.'{/lang}', $sm['lang'][$parsed]['text'], $message);	
	}	
	$mail = new PHPMailer;
	$mail->IsSMTP();
	$mail->Host = $sm['config_email']['host']; 
	$mail->Port = $sm['config_email']['port']; 
	$mail->SMTPSecure = 'tls';	
	$mail->SMTPAuth = true;                              
	$mail->Username = $sm['config_email']['user'];                 
	$mail->Password = $sm['config_email']['password'];                         	
	$mail->setFrom($sm['config']['email'], $sm['config']['name']);
	$mail->addAddress($sm['mail']['email'],$sm['mail']['name']);
	$mail->CharSet = 'UTF-8';	
	$mail->Subject = $sm['user']['name']." ".$sm['lang'][357]['text'];
	$mail->msgHTML($message);
	if($mail->send()) {
	}
}


function welcomeMailNotification($user_name,$user_email,$user_password){
	global $mysqli,$sm,$lang;
	
	$message = file_get_contents($sm['config']['site_url'].'/themes/' . $sm['config']['theme_email'] . '/layout/welcome.phtml');
	$message = str_replace('$user_name', $user_name, $message);
	$message = str_replace('$user_email', $user_email, $message);
	$message = str_replace('$user_password', $user_password, $message);	
	$message = str_replace('$site_url', $sm['config']['site_url'], $message);	
	$message = str_replace('$site_name', $sm['config']['name'], $message);
	$message = str_replace('$site_email', $sm['config']['theme_url_email'], $message);
	preg_match_all('~{lang}([^<]*){/lang}~', $message, $matches);
	foreach($matches[0] as $match){
		$parsed = get_string_between($match, '{lang}', '{/lang}');	
		$message = str_replace('{lang}'.$parsed.'{/lang}', $sm['lang'][$parsed]['text'], $message);	
	}	
	$mail = new PHPMailer;
	$mail->IsSMTP();
	$mail->Host = $sm['config_email']['host']; 
	$mail->Port = $sm['config_email']['port']; 
	$mail->SMTPSecure = 'tls';	
	$mail->SMTPAuth = true;                              
	$mail->Username = $sm['config_email']['user'];                 
	$mail->Password = $sm['config_email']['password'];                          	
	$mail->setFrom($sm['config']['email'], $sm['config']['name']);
	$mail->addAddress($user_email,$user_name);
	$mail->CharSet = 'UTF-8';	
	$mail->Subject = $sm['lang'][380]['text']." ".$sm['config']['name'];
	$mail->msgHTML($message);
	if($mail->send()) {
	}
}

function welcomeMailVerification($user_name,$user_id,$user_email,$user_password){
	global $mysqli,$sm,$lang;

	$message = file_get_contents($sm['config']['site_url'].'/themes/' . $sm['config']['theme_email'] . '/layout/verification.phtml');
	$message = str_replace('$user_name', $user_name, $message);
	$message = str_replace('$user_id', $user_id, $message);	
	$message = str_replace('$user_email', $user_email, $message);
	$message = str_replace('$user_password', $user_password, $message);	
	$message = str_replace('$site_url', $sm['config']['site_url'], $message);	
	$message = str_replace('$site_name', $sm['config']['name'], $message);
	$message = str_replace('$site_email', $sm['config']['theme_url_email'], $message);
	preg_match_all('~{lang}([^<]*){/lang}~', $message, $matches);
	foreach($matches[0] as $match){
		$parsed = get_string_between($match, '{lang}', '{/lang}');	
		$message = str_replace('{lang}'.$parsed.'{/lang}', $sm['lang'][$parsed]['text'], $message);	
	}	
	$mail = new PHPMailer;
	$mail->IsSMTP();
	$mail->Host = $sm['config_email']['host']; 
	$mail->Port = $sm['config_email']['port']; 
	$mail->SMTPSecure = 'tls';	
	$mail->SMTPAuth = true;                              
	$mail->Username = $sm['config_email']['user'];                 
	$mail->Password = $sm['config_email']['password'];                         	
	$mail->setFrom($sm['config']['email'], $sm['config']['name']);
	$mail->addAddress($user_email,$user_name);
	$mail->CharSet = 'UTF-8';	
	$mail->Subject = $sm['lang'][380]['text']." ".$sm['config']['name'];
	$mail->msgHTML($message);
	if($mail->send()) {
	}
}



function siteConfig($val) {
	global $mysqli,$sm;
	
	$config = $mysqli->query("SELECT * FROM config");
	$result = $config->fetch_object();
	return $result->$val;
}

/*
function countryInfo($id) {
	global $mysqli,$sm;
	
	$config = $mysqli->query("SELECT * FROM country WHERE id = '".$id."'");
	$result = $config->fetch_assoc();
	
	$sm['country'] = $result;
}
*/

function sitePrices() {
	global $mysqli,$sm;
	
	$config = $mysqli->query("SELECT * FROM config_prices");
	$result = $config->fetch_assoc();
	
	return $result;
}

function userExtended($id) {
	global $mysqli,$sm;
	
	$config = $mysqli->query("SELECT * FROM users_extended where uid = '".$id."'");
	$result = $config->fetch_assoc();
	
	return $result;
}

function userInterest($id) {
	global $mysqli,$sm;
	$result=array();
	$config = $mysqli->query("SELECT * FROM users_interest where u_id = '".$id."'");
	if($config->num_rows > 0 ){
		while($row = $config->fetch_assoc()){
			$result[] = array(
				"id" => $row['i_id'], 
				"name" => $row['name']
			);
		}
	}
	return $result;	
}

function availableLanguages() {
	global $mysqli,$sm;
	$result=array();
	$config = $mysqli->query("SELECT * FROM languages where visible = 1");
	if($config->num_rows > 0 ){
		while($row = $config->fetch_assoc()){
			$result[] = $row['prefix'];
		}
	}
	return $result;	
}

function configEmail() {
	global $mysqli,$sm;
	
	$config = $mysqli->query("SELECT * FROM config_email");
	$result = $config->fetch_assoc();
	
	return $result;
}

function userNotifications($uid) {
	global $mysqli,$sm;
	
	$config = $mysqli->query("SELECT * FROM users_notifications where uid = '".$uid."'");
	$result = $config->fetch_assoc();
	
	return $result;
}


function siteAccountsBasic() {
	global $mysqli,$sm;
	
	$config = $mysqli->query("SELECT * FROM config_accounts where type = 1");
	$result = $config->fetch_assoc();
	
	return $result;
}

function siteAccountsPremium() {
	global $mysqli,$sm;
	
	$config = $mysqli->query("SELECT * FROM config_accounts where type = 2");
	$result = $config->fetch_assoc();
	
	return $result;
}

function updateUserFilter($looking,$age,$radius,$uid){
	global $mysqli;	
$mysqli->query("UPDATE users SET s_gender = '".$looking."', s_age = '".$age."', s_radious = '".$radius."' where id = '".$uid."'");	
}

function siteLang($lang) {
	global $mysqli,$sm;
	$result=array();
	$lang = secureEncode($lang);	
	$query = $mysqli->query("SELECT * FROM site_lang where lang_id = '".$lang."' ORDER BY id ASC");
	while($row = $query->fetch_assoc()){
		$result[] = array(
			"id" => $row['id'], 
			"text" => $row['text']
		);
	}	
	return $result;
}

function totalFans($uid1) {
	global $mysqli;
	
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM users_likes where u2 = '".$uid1."'");
	$total = $query->fetch_assoc();
	$result = $total['total'];
	return $result;
}

function totalVisits($uid1) {
	global $mysqli;
	
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM users_visits where u1 = '".$uid1."'");
	$total = $query->fetch_assoc();
	$result = $total['total'];
	return $result;
}


function getUserVisitors($uid) {
	global $mysqli,$sm;
	
	$search = '';
	$count = $limit;
	$time_now = time()-300;
	$query2 = $mysqli->query("SELECT u2,timeago FROM users_visits where u1 = '$uid' and u2 <> '$uid' order by timeago desc");
	if($query2->num_rows > 0){
		while($result2 = $query2->fetch_object()){
			getUserInfo($result2->u2,6);
			$time = $result2->timeago;

			$search.= '<div class="search"><div class="photos-count" ><i class="mdi-image-camera-alt"></i><span>'.$sm['search']['total_photos'].'</span></div> 
<a href="#" onclick="goToProfile('.$sm['search']['id'].')"><div class="profile-photo" data-src="'.$sm['search']['profile_photo'].'" ></div></a>';
			if($sm['search']['last_access'] >= $time_now){
				$search.= '<div class="name" data-tooltip="'.$sm['lang'][151]['text'].'"><h1><a href="#" onClick="goToProfile('.$sm['search']['id'].');" ><b>'.$sm['search']['first_name'].'</b> , '.$sm['search']['age'].' <i class="mdi-image-brightness-1 online"></i></a></h1><div class="loc">visited '.time_elapsed_string($time).'</span></div></div>';
			} else {
				$search.= '<div class="name" ><h1><a href="#" onClick="goToProfile('.$sm['search']['id'].');" ><b>'.$sm['search']['first_name'].'</b> , '.$sm['search']['age'].'</a></h1><div class="loc">'.$sm['lang'][302]['text'].' '.time_elapsed_string($time).'</span></div></div>';
			}
			
			$search.= '<div class="footer"><div class="like" data-tooltip="'.$sm['search']['total_likers'].' '.$sm['lang'][278]['text'].'"><i class="mdi-action-favorite"></i></div>';
			
			if($sm['search']['premium'] == 1){	
				$search.= '<div class="premium" data-tooltip="Premium"><i class="mdi-editor-attach-money"></i></div>';
			}
			if($sm['search']['verified'] == 1){	
				$search.= '<div class="verified" data-tooltip="'.$sm['lang'][152]['text'].'"><i class="mdi-action-verified-user"></i></div>';
			}
			if($sm['search']['popular'] > 2999){	
				$search.= '<div class="popular" data-tooltip="Popular"><i class="mdi-social-whatshot"></i></div>';
			}			
			$search.= '</div></div>';
		}
	} else {
		$search.= '<center><h5 style="cursor:pointer;padding-top:65px;color:#666666;"><i class="mdi-image-remove-red-eye" style="font-size:166px;"></i><br><br></h5><br><h5>'.$sm['lang'][301]['text'].'</h5></center>';
	}
	
	return $search;
}

function checkUserPremium($uid){
	global $mysqli,$sm;
	
	$time = time();
	$query = $mysqli->query("SELECT premium FROM users_premium WHERE uid = '".$uid."'");
	$user = $query->fetch_object();
	
	if($user->premium > $time){
		$mysqli->query("UPDATE users SET premium = 1 where id = '".$uid."'");
	} else {
		$mysqli->query("UPDATE users SET premium = 0 where id = '".$uid."'");	
	}
}


function adminCheckUserPremium($uid){
	global $mysqli,$sm;
	
	$time = time();
	$query = $mysqli->query("SELECT premium FROM users_premium WHERE uid = '".$uid."'");
	$user = $query->fetch_object();
	
	if($user->premium > $time){
		$mysqli->query("UPDATE users SET premium = 1 where id = '".$uid."'");
		return $user->premium;
	} else {
		$mysqli->query("UPDATE users SET premium = 0 where id = '".$uid."'");
		return 0;
	}

}

function adminCheckDaysLeft($premium){
	return $date2 = date("Y-m-d",$premium);	
}

function likeSuggest($uid,$lang,$looking,$limit){
	global $mysqli,$sm;	
	
	$suggest = '';
	$i = 0;
	$query = $mysqli->query("SELECT id
	FROM users
	WHERE gender = '".$looking."'
	AND id <> '".$sm['user']['id']."'
	ORDER BY RAND()
	LIMIT $limit");

	if ($query->num_rows > 0) {
		while($row = $query->fetch_object()){
			getUserInfo($row->id,10);
			$time = time();
			$i++;
			if($i > 3 ) {
			$suggest.= '<div class="follow-suggest" style="display:none;"><div class="profile-photo" data-src="'.$sm['suggest']['profile_photo'].'"></div> <h2 data-uid="'.$sm['suggest']['id'].'" data-ssurl="profile" style="cursor:pointer">'.$sm['suggest']['name'].'</h2><span><b>'.$sm['suggest']['total_likers'].'</b> '.$sm['lang'][278]['text'].'</span><a class="waves-effect  btn-flat" id="sl'.$time.'" data-uid="'.$sm['suggest']['id'].'" data-action="like" style="padding-left:10px; padding-right:10px;"><i class="mdi-action-favorite"></i></a></div>';				
			} else {
			$suggest.= '<div class="follow-suggest"><div class="profile-photo" data-src="'.$sm['suggest']['profile_photo'].'"></div> <h2 data-uid="'.$sm['suggest']['id'].'" data-ssurl="profile" style="cursor:pointer">'.$sm['suggest']['name'].'</h2><span><b>'.$sm['suggest']['total_likers'].'</b> '.$sm['lang'][278]['text'].'</span><a class="waves-effect  btn-flat" href="javascript:;" id="sl'.$time.'" data-uid="'.$sm['suggest']['id'].'" data-action="like" style="padding-left:10px; padding-right:10px;"><i class="mdi-action-favorite" ></i></a></div>';				
			}

		}	
	}
	
	return $suggest;
}


function blockedUser($u1,$u2){
	global $mysqli,$sm;
	$u1 = secureEncode($u1);
	$u2 = secureEncode($u2);
	
	$return = 0;
	$check = $mysqli->query("SELECT * FROM users_blocks where uid1 = '".$u1."' AND uid2 = '".$u2."' || uid2 = '".$u1."' AND uid1 = '".$u2."'");
	if($check->num_rows >= 1){
		$return = 1;
	}	
	return $return;
}

function visit($u1,$u2){
	global $mysqli;
	$time = time();		
	$mysqli->query("INSERT INTO users_visits (u2,u1,timeago) VALUES ('".$u1."','".$u2."','".$time."') ON DUPLICATE KEY UPDATE timeago = '".$time."'");	
	$mysqli->query("UPDATE users set popular = popular+1 where id = '".$u2."'");
	
}

function meetFilter($uid,$lang,$looking,$age,$radius,$status=0,$l=0){
	global $mysqli,$sm;
	
	$search = '';
	$time_now = time()-300;
	$lat = $sm['user']['lat'];
	$lng = $sm['user']['lng'];
	$today = date('w');
	$limit = $l * 15;
	$ages = "18,30";
	if($age == 1){$age1 = 18; $age2 = 23; $ages= "18,23,1";}
	if($age == 2){$age1 = 24; $age2 = 30; $ages= "24,30,2";}
	if($age == 3){$age1 = 31; $age2 = 50; $ages= "31,50,3";}
	if($age == 4){$age1 = 51; $age2 = 100; $ages= "51,100,4";}
	
	updateUserFilter($looking,$ages,$radius,$uid);	
	
	if($status == 0){
		$status_filter = "";	
	} else {
		$time_now = time()-300;
		if($looking == 3) {
		$status_filter = "AND last_access >=".$time_now." OR fake = 1 AND online_day = ".$today." AND age BETWEEN '".$age1."' AND '".$age2."'";			
		} else {
		$status_filter = "AND last_access >=".$time_now." OR fake = 1 AND online_day = ".$today." AND age BETWEEN '".$age1."' AND '".$age2."' AND gender = '".$looking."'";			
		}

	}	
	
	if($looking == 3) {
		$query = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
		* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
		FROM users
		WHERE age BETWEEN '".$age1."' AND '".$age2."'
		AND id <> '".$sm['user']['id']."'	
		$status_filter	
		HAVING distance < $radius
		ORDER BY last_access
		LIMIT ".$limit.", 15";	
		
		$query2 = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
		* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
		FROM users
		WHERE age BETWEEN '".$age1."' AND '".$age2."'
		AND id <> '".$sm['user']['id']."'	
		$status_filter	
		HAVING distance < $radius
		ORDER BY last_access";			
	} else {
		$query = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
		* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
		FROM users
		WHERE gender = '".$looking."'
		AND age BETWEEN '".$age1."' AND '".$age2."'
		AND id <> '".$sm['user']['id']."'	
		$status_filter	
		HAVING distance < $radius
		ORDER BY last_access
		LIMIT ".$limit.", 15";	
		
		$query2 = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
		* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
		FROM users
		WHERE gender = '".$looking."'
		AND age BETWEEN '".$age1."' AND '".$age2."'
		AND id <> '".$sm['user']['id']."'	
		$status_filter	
		HAVING distance < $radius
		ORDER BY last_access";			
	}
	$result = $mysqli->query($query);
	$result2 = $mysqli->query($query2);
	$sm['meet_result'] = $result2->num_rows;
	if ($result->num_rows > 0) {
		
		while($row = $result->fetch_object()){
			getUserInfo($row->id,6);
			$search.= '<div class="search"><div class="photos-count" ><i class="mdi-image-camera-alt"></i><span>'.$sm['search']['total_photos'].'</span></div> 
<a href="#" onclick="goToProfile('.$sm['search']['id'].')"><div class="profile-photo" data-src="'.$sm['search']['profile_photo'].'" ></div></a>';
			if($sm['search']['last_access'] >= $time_now || $sm['search']['fake'] == 1 && $sm['search']['online_day'] == $today){
				$search.= '<div class="name" data-tooltip="'.$sm['lang'][151]['text'].'"><h1><a href="'.$sm['config']['site_url'].'profile/'.$sm['search']['id'].'/'.$sm['search']['first_name'].'" ><b>'.$sm['search']['first_name'].'</b> , '.$sm['search']['age'].' <i class="mdi-image-brightness-1 online"></i></a></h1><div class="loc"><i class="mdi-maps-place"></i><span>'.$sm['search']['city'].'</span></div></div>';
			} else {
				$search.= '<div class="name" ><h1><a href="'.$sm['config']['site_url'].'profile/'.$sm['search']['id'].'/'.$sm['search']['first_name'].'"><b>'.$sm['search']['first_name'].'</b> , '.$sm['search']['age'].'</a></h1><div class="loc"><i class="mdi-maps-place"></i><span>'.$sm['search']['city'].'</span></div></div>';
			}
			
			$search.= '<div class="footer"><div class="like" data-tooltip="'.$sm['search']['total_likers'].' '.$sm['lang'][278]['text'].'"><i class="mdi-action-favorite"></i></div>';
			
			if($sm['search']['premium'] == 1){	
				$search.= '<div class="premium" data-tooltip="Premium"><i class="mdi-editor-attach-money"></i></div>';
			}
			if($sm['search']['verified'] == 1){	
				$search.= '<div class="verified" data-tooltip="'.$sm['lang'][152]['text'].'"><i class="mdi-action-verified-user"></i></div>';
			}
			if($sm['search']['popular'] > 2999){	
				$search.= '<div class="popular" data-tooltip="Popular"><i class="mdi-social-whatshot"></i></div>';
			}			
			$search.= '</div></div>';
		}
	} else {
		$search.= '<center><h5 style="cursor:pointer;padding-top:65px;color:#666666;"><i class="mdi-social-public" style="font-size:166px;"></i><br><br>'.$sm['lang'][153]['text'].'</h5></center>';
	}
	$totalPages = ceil($sm['meet_result'] / 15);
	$totalp = $totalPages-1;
	$search.= '<script>meet_pages = '.$totalp.'</script>';
	$search.= '<br><div class="row"><div class="col s12"><br><center><p><span class="mdi-social-public"></span> '.$sm['meet_result'].' '.$sm['lang'][154]['text'].' </p></center><ul class="pagination center-align">';	

	for ($i=$l; $i<=$totalp; $i++) { 
		$d = $i-$l;
		$x = $i-1;
		if($d >10){
			break;	
		}
		if($l == $i){
			if($l >= 1){
				$search.= '<li data-meet="'.$x.'" class="disabled "><a href="#!"><span class="mdi-navigation-chevron-left"></span></a></li>';				
			}
			$search.= '<li data-meet="'.$i.'" class="active  blue lighten-2"><a href="#!"><span class="mdi-image-adjust"></span></a></li>';				
		} else {
			$search.= '<li data-meet="'.$i.'"><a href="#!">'.$i.'</a></li>';		
		}

	};	

	$search.= '</ul><br><br></div></div>';	
	return $search;
	
}

function meetFilterMobile($uid,$lang,$looking,$age,$radius,$status=0,$limi=0){
	global $mysqli,$sm;
	
	$search = '';
	$time_now = time()-300;
	$lat = $sm['user']['lat'];
	$lng = $sm['user']['lng'];
	$today = date('w');
	$limit = $limi * 15;
	$x=0;
	
	$e_age = explode( ',', $age );
	$age1 = $e_age[0];
	$age2 = $e_age[1];
	$world = 0;
	$age = $age.',1';
	if($radius > 10000){
		$world = 1;
	}
	updateUserFilter($looking,$age,$radius,$uid);
	if($status == 0){
		$status_filter = "";	
	} else {
		$time_now = time()-300;
		if($looking == 3) {
		$status_filter = "AND last_access >=".$time_now." OR fake = 1 AND online_day = ".$today." AND age BETWEEN '".$age1."' AND '".$age2."'";			
		} else {
		$status_filter = "AND last_access >=".$time_now." OR fake = 1 AND online_day = ".$today." AND age BETWEEN '".$age1."' AND '".$age2."' AND gender = '".$looking."'";			
		}

	}	
	
	$country_filter = '';
	if($radius < 3000){
		$country_filter = "AND country = '".$sm['user']['country']."'";			
	}
	
	if($looking == 3) {
		
		if($world == 1){
			$query = "SELECT id
			FROM users
			WHERE age BETWEEN '".$age1."' AND '".$age2."'
			AND id <> '".$sm['user']['id']."'
			$country_filter			
			$status_filter	
			ORDER BY last_access
			LIMIT ".$limit.", 16";	
			
			$query2 = "SELECT id
			FROM users
			WHERE age BETWEEN '".$age1."' AND '".$age2."'
			AND id <> '".$sm['user']['id']."'	
			$country_filter
			$status_filter	
			ORDER BY last_access";			
			
		} else {
			
			$query = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
			* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
			FROM users
			WHERE age BETWEEN '".$age1."' AND '".$age2."'
			AND id <> '".$sm['user']['id']."'	
			$country_filter
			$status_filter	
			HAVING distance < $radius
			ORDER BY last_access
			LIMIT ".$limit.", 16";	
			
			$query2 = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
			* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
			FROM users
			WHERE age BETWEEN '".$age1."' AND '".$age2."'
			AND id <> '".$sm['user']['id']."'	
			$country_filter
			$status_filter	
			HAVING distance < $radius
			ORDER BY last_access";			
			
		}
			
	} else {
		
		if($world == 1){
			
			$query = "SELECT id
			FROM users
			WHERE gender = '".$looking."'
			AND age BETWEEN '".$age1."' AND '".$age2."'
			AND id <> '".$sm['user']['id']."'	
			$country_filter
			$status_filter	
			ORDER BY last_access
			LIMIT ".$limit.", 16";	
			
			$query2 = "SELECT id
			FROM users
			WHERE gender = '".$looking."'
			AND age BETWEEN '".$age1."' AND '".$age2."'
			AND id <> '".$sm['user']['id']."'
			$country_filter			
			$status_filter	
			ORDER BY last_access";			
			
		} else {
			
			$query = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
			* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
			FROM users
			WHERE gender = '".$looking."'
			AND age BETWEEN '".$age1."' AND '".$age2."'
			AND id <> '".$sm['user']['id']."'
			$country_filter			
			$status_filter	
			HAVING distance < $radius
			ORDER BY last_access
			LIMIT ".$limit.", 16";	
			
			$query2 = "SELECT id, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) )
			* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin( radians( lat ) ) ) ) AS distance 
			FROM users
			WHERE gender = '".$looking."'
			AND age BETWEEN '".$age1."' AND '".$age2."'
			AND id <> '".$sm['user']['id']."'	
			$country_filter
			$status_filter	
			HAVING distance < $radius
			ORDER BY last_access";			
		}
			
	}
	$result = $mysqli->query($query);
	$result2 = $mysqli->query($query2);
	$sm['meet_result'] = $result2->num_rows;
	if ($result->num_rows > 0) {
		
		while($row = $result->fetch_object()){
			getUserInfo($row->id,6);
			$x++;
			if($sm['user']['premium'] == 0 && $sm['basic']['mobile_ads'] == 1 && $x==7){
				$search.= '<center><div style="width:100%;background:#f3f3f3;color:#488ad8;padding:10px;">'.siteConfig('ads2').'<br><span style="cursor:pointer" data-premium="1">Upgrade premium for remove ads</span></div></center>';
			}
			if($sm['user']['premium'] == 0 && $sm['basic']['mobile_ads'] == 1 && $x==13){
				$search.= '<center><div style="width:100%;background:#f3f3f3;color:#488ad8;padding:10px;">'.siteConfig('ads2').'<br><span style="cursor:pointer" data-premium="1">Upgrade premium for remove ads</span></div></center>';
			}			
			$search.= '<div class="search"><div class="photos-count" ><i class="mdi-image-camera-alt"></i><span>'.$sm['search']['total_photos'].'</span></div> 
<a href="mobile.php?page=profile&id='.$sm['search']['id'].'" ><div class="profile-photo" data-src="'.$sm['search']['profile_photo'].'" ></div></a>';
			if($sm['search']['last_access'] >= $time_now || $sm['search']['fake'] == 1 && $sm['search']['online_day'] == $today){
				$search.= '<div class="name" data-tooltip="'.$sm['lang'][151]['text'].'"><h1><a href="mobile.php?page=profile&id='.$sm['search']['id'].'" ><b>'.$sm['search']['first_name'].'</b> , '.$sm['search']['age'].' <i class="mdi-image-brightness-1 online"></i></a></h1><div class="loc"><i class="mdi-maps-place"></i> <span>'.$sm['search']['city'].'</span></div></div>';
			} else {
				$search.= '<div class="name" ><h1><a href="'.$sm['config']['site_url'].'profile&id='.$sm['search']['id'].'"><b>'.$sm['search']['first_name'].'</b> , '.$sm['search']['age'].'</a></h1><div class="loc"><i class="mdi-maps-place"></i> <span>'.$sm['search']['city'].' </span></div></div>';
			}
			
			$search.= '<div class="footer"><div class="like" data-tooltip="'.$sm['search']['total_likers'].' '.$sm['lang'][278]['text'].'"><i class="mdi-action-favorite"></i></div>';
			
			if($sm['search']['premium'] == 1){	
				$search.= '<div class="premium" data-tooltip="Premium"><i class="mdi-editor-attach-money"></i></div>';
			}
			if($sm['search']['verified'] == 1){	
				$search.= '<div class="verified" data-tooltip="'.$sm['lang'][152]['text'].'"><i class="mdi-action-verified-user"></i></div>';
			}
			if($sm['search']['popular'] > 2999){	
				$search.= '<div class="popular" data-tooltip="Popular"><i class="mdi-social-whatshot"></i></div>';
			}			
			$search.= '</div></div>';
		}
	} else {
		$search.= '<center><h5 style="cursor:pointer;padding-top:65px;color:#666666;"><i class="mdi-social-public" style="font-size:166px;"></i><br><br>'.$sm['lang'][153]['text'].'</h5></center>';
	}
	$totalPages = ceil($sm['meet_result'] / 16);
	$totalp = $totalPages-1;
	$limi = $limi + 1;
	if($totalp > 0 ){
		$search.='<a href="mobile.php?page=meet&status='.$status.'&l='.$limi.'"><div
		style="width:80%;height:45px;margin-left:10%;margin-right:10%;margin-bottom:20px;background:#fff;color:#488ad8;border:1px solid #488ad8;padding:10px;font-size:14px;line-height:18px;text-align:center;border-radius:25px;">Load more</div></a>';	
	}
	$search.= '<script>meet_pages = '.$totalp.'</script>';
	return $search;
	
}

function popularUsers(){
	global $mysqli,$sm;
	
	$search = '';
	$time_now = time()-300;
	$x=0;
	if($sm['user']['s_gender'] == 3) {
		$query = $mysqli->query("SELECT id
		FROM users
		WHERE id <> '".$sm['user']['id']."'
		ORDER BY popular desc, last_access desc
		LIMIT 18
		");		
	} else {
		$query = $mysqli->query("SELECT id
		FROM users
		WHERE gender = '".$sm['user']['s_gender']."'
		AND id <> '".$sm['user']['id']."'	
		ORDER BY popular desc, last_access desc
		LIMIT 18
		");			
	}
	if ($query->num_rows > 0) {
		while($row = $query->fetch_object()){
			$x++;
			if($sm['user']['premium'] == 0 && $sm['basic']['mobile_ads'] == 1 && $x==7 && $sm['mobile'] == 1){
				$search.= '<center><div style="width:100%;background:#f3f3f3;color:#488ad8;padding:10px;">'.siteConfig('ads2').'<br><span style="cursor:pointer" data-premium="1">Upgrade premium for remove ads</span></div></center>';
			}
			if($sm['user']['premium'] == 0 && $sm['basic']['mobile_ads'] == 1 && $x==13 && $sm['mobile'] == 1){
				$search.= '<center><div style="width:100%;background:#f3f3f3;color:#488ad8;padding:10px;">'.siteConfig('ads2').'<br><span style="cursor:pointer" data-premium="1">Upgrade premium for remove ads</span></div></center>';
			}			
			getUserInfo($row->id,6);
			$search.= '<div class="search"><div class="photos-count" ><i class="mdi-image-camera-alt"></i><span>'.$sm['search']['total_photos'].'</span></div> 
<a href="#" onClick="goToProfile('.$sm['search']['id'].');"><div class="profile-photo" data-src="'.$sm['search']['profile_photo'].'" ></div></a>';
			if($sm['search']['last_access'] >= $time_now){
				$search.= '<div class="name" data-tooltip="'.$sm['lang'][151]['text'].'"><h1><a  href="#" onClick="goToProfile('.$sm['search']['id'].');"><b>'.$sm['search']['first_name'].'</b> , '.$sm['search']['age'].' <i class="mdi-image-brightness-1 online"></i></a></h1><div class="loc"><i class="mdi-maps-place"></i><span>'.$sm['search']['city'].'</span></div></div>';
			} else {
				$search.= '<div class="name" ><h1><a  href="#" onClick="goToProfile('.$sm['search']['id'].');" ><b>'.$sm['search']['first_name'].'</b> , '.$sm['search']['age'].'</a></h1><div class="loc"><i class="mdi-maps-place"></i><span>'.$sm['search']['city'].'</span></div></div>';
			}
			
			$search.= '<div class="footer"><div class="like" data-tooltip="'.$sm['search']['total_likers'].' '.$sm['lang'][278]['text'].'"><i class="mdi-action-favorite"></i></div>';
			
			if($sm['search']['premium'] == 1){	
				$search.= '<div class="premium" data-tooltip="Premium"><i class="mdi-editor-attach-money"></i></div>';
			}
			if($sm['search']['verified'] == 1){	
				$search.= '<div class="verified" data-tooltip="'.$sm['lang'][152]['text'].'"><i class="mdi-action-verified-user"></i></div>';
			}	
			$search.= '<div class="popular" data-tooltip="Popular"><i class="mdi-social-whatshot"></i></div>';		
			$search.= '</div></div>';
		}
	} else {
		$search.= '<center><h5 style="cursor:pointer;padding-top:65px;color:#666666;"><i class="mdi-social-whatshot" style="font-size:166px;"></i><br><br>'.$sm['lang'][155]['text'].'</h5></center>';
	}
	
	return $search;
	
}

function userFans(){
	global $mysqli,$sm;
	
	$search = '';
	$time_now = time()-300;

	$query = $mysqli->query("SELECT u1 FROM users_likes WHERE u2 = '".$sm['user']['id']."' AND love = 1");			
	
	if ($query->num_rows > 0) {
		while($row = $query->fetch_object()){
			getUserInfo($row->u1,6);
			$search.= '<div class="search"><div class="photos-count" ><i class="mdi-image-camera-alt"></i><span>'.$sm['search']['total_photos'].'</span></div> 
<a  href="#" onClick="goToProfile('.$sm['search']['id'].');"><div class="profile-photo" data-src="'.$sm['search']['profile_photo'].'" ></div></a>';
			if($sm['search']['last_access'] >= $time_now){
				$search.= '<div class="name" data-tooltip="'.$sm['lang'][151]['text'].'"><h1><a href="#" onClick="goToProfile('.$sm['search']['id'].');" ><b>'.$sm['search']['first_name'].'</b> , '.$sm['search']['age'].' <i class="mdi-image-brightness-1 online"></i></a></h1><div class="loc"><i class="mdi-maps-place"></i><span>'.$sm['search']['city'].'</span></div></div>';
			} else {
				$search.= '<div class="name" ><h1><a href="#" onClick="goToProfile('.$sm['search']['id'].');" ><b>'.$sm['search']['first_name'].'</b> , '.$sm['search']['age'].'</a></h1><div class="loc"><i class="mdi-maps-place"></i><span>'.$sm['search']['city'].'</span></div></div>';
			}
			
			$search.= '<div class="footer"><div class="like" data-tooltip="'.$sm['search']['total_likers'].' '.$sm['lang'][278]['text'].'"><i class="mdi-action-favorite"></i></div>';
			
			if($sm['search']['premium'] == 1){	
				$search.= '<div class="premium" data-tooltip="Premium"><i class="mdi-editor-attach-money"></i></div>';
			}
			if($sm['search']['verified'] == 1){	
				$search.= '<div class="verified" data-tooltip="'.$sm['lang'][152]['text'].'"><i class="mdi-action-verified-user"></i></div>';
			}
			if($sm['search']['popular'] > 2999){	
				$search.= '<div class="popular" data-tooltip="Popular"><i class="mdi-social-whatshot"></i></div>';
			}			
			$search.= '</div></div>';
		}
	} else {
		$search.= '<center><h5 style="cursor:pointer;padding-top:65px;color:#666666;"><i class="mdi-action-stars" style="font-size:166px;"></i><br><br></h5><br><h5>'.$sm['lang'][156]['text'].'</h5></center>';
	}
	
	return $search;
	
}

function myLikes(){
	global $mysqli,$sm;
	
	$search = '';
	$time_now = time()-300;

	$query = $mysqli->query("SELECT u2 FROM users_likes WHERE u1 = '".$sm['user']['id']."' AND love = 1");			
	
	if ($query->num_rows > 0) {
		while($row = $query->fetch_object()){
			getUserInfo($row->u2,6);
			$search.= '<div class="search"><div class="photos-count" ><i class="mdi-image-camera-alt"></i><span>'.$sm['search']['total_photos'].'</span></div> 
<a  href="#" onClick="goToProfile('.$sm['search']['id'].');"><div class="profile-photo" data-src="'.$sm['search']['profile_photo'].'" ></div></a>';
			if($sm['search']['last_access'] >= $time_now){
				$search.= '<div class="name" data-tooltip="'.$sm['lang'][151]['text'].'"><h1><a href="#" onClick="goToProfile('.$sm['search']['id'].');" ><b>'.$sm['search']['first_name'].'</b> , '.$sm['search']['age'].' <i class="mdi-image-brightness-1 online"></i></a></h1><div class="loc"><i class="mdi-maps-place"></i><span>'.$sm['search']['city'].'</span></div></div>';
			} else {
				$search.= '<div class="name" ><h1><a href="#" onClick="goToProfile('.$sm['search']['id'].');" ><b>'.$sm['search']['first_name'].'</b> , '.$sm['search']['age'].'</a></h1><div class="loc"><i class="mdi-maps-place"></i><span>'.$sm['search']['city'].'</span></div></div>';
			}
			
			$search.= '<div class="footer"><div class="like" data-tooltip="'.$sm['search']['total_likers'].' '.$sm['lang'][278]['text'].'"><i class="mdi-action-favorite"></i></div>';
			
			if($sm['search']['premium'] == 1){	
				$search.= '<div class="premium" data-tooltip="Premium"><i class="mdi-editor-attach-money"></i></div>';
			}
			if($sm['search']['verified'] == 1){	
				$search.= '<div class="verified" data-tooltip="'.$sm['lang'][152]['text'].'"><i class="mdi-action-verified-user"></i></div>';
			}
			if($sm['search']['popular'] > 2999){	
				$search.= '<div class="popular" data-tooltip="Popular"><i class="mdi-social-whatshot"></i></div>';
			}			
			$search.= '</div></div>';
		}
	} else {
		$search.= '<center><h5 style="cursor:pointer;padding-top:65px;color:#666666;"><i class="mdi-action-favorite" style="font-size:166px;"></i><br><br></h5><br><h5>'.$sm['lang'][303]['text'].'</h5></center>';
	}
	
	return $search;
	
}

function userMatches(){
	global $mysqli,$sm;
	
	$search = '';
	$time_now = time()-300;
	$i= 0;
	$query = $mysqli->query("SELECT u1 FROM users_likes WHERE u2 = '".$sm['user']['id']."' AND love = 1");			
	
	if ($query->num_rows > 0) {
		while($row = $query->fetch_object()){
			getUserInfo($row->u1,6);
			if(isFan($sm['user']['id'],$sm['search']['id']) == 1){
				$i++;	
				$search.= '<div class="search"><div class="photos-count" ><i class="mdi-image-camera-alt"></i><span>'.$sm['search']['total_photos'].'</span></div> 
	<a href="#" onclick="goToProfile('.$sm['search']['id'].')"><div class="profile-photo" data-src="'.$sm['search']['profile_photo'].'" ></div></a>';
				if($sm['search']['last_access'] >= $time_now){
					$search.= '<div class="name" data-tooltip="'.$sm['lang'][151]['text'].'"><h1><a href="#" onClick="goToProfile('.$sm['search']['id'].');" ><b>'.$sm['search']['first_name'].'</b> , '.$sm['search']['age'].' <i class="mdi-image-brightness-1 online"></i></a></h1><div class="loc"><i class="mdi-maps-place"></i><span>'.$sm['search']['city'].'</span></div></div>';
				} else {
					$search.= '<div class="name" ><h1><a href="#" onClick="goToProfile('.$sm['search']['id'].');" ><b>'.$sm['search']['first_name'].'</b> , '.$sm['search']['age'].'</a></h1><div class="loc"><i class="mdi-maps-place"></i><span>'.$sm['search']['city'].'</span></div></div>';
				}
				
				$search.= '<div class="footer"><div class="like" data-tooltip="'.$sm['search']['total_likers'].' '.$sm['lang'][278]['text'].'"><i class="mdi-action-favorite"></i></div>';
				
				if($sm['search']['premium'] == 1){	
					$search.= '<div class="premium" data-tooltip="Premium"><i class="mdi-editor-attach-money"></i></div>';
				}
				if($sm['search']['verified'] == 1){	
					$search.= '<div class="verified" data-tooltip="'.$sm['lang'][152]['text'].'"><i class="mdi-action-verified-user"></i></div>';
				}
				if($sm['search']['popular'] > 2999){	
					$search.= '<div class="popular" data-tooltip="Popular"><i class="mdi-social-whatshot"></i></div>';
				}			
				$search.= '</div></div>';					
			}
		}
	}
	if($i == 0){
		$search.= '<center><h5 style="cursor:pointer;padding-top:65px;color:#666666;"><i class="mdi-action-favorite" style="font-size:166px;"></i><br><br></h5><br><h5>'.$sm['lang'][157]['text'].'</h5></center>';
	}
	return $search;
	
}

function date_dropdown($d=0,$m=0,$y=0,$year_limit = 19){
	global $sm;
	if($d==1){
		for ($day = 1; $day <= 31; $day++) {
			$html_output .= '<option style="color:#000;" value="' . $day . '">' . $day . '</option>'."\n";
		}
	}
	
	if($m==1){	
		$months = array("", $sm['lang'][158]['text'],$sm['lang'][159]['text'],$sm['lang'][160]['text'],$sm['lang'][161]['text'],$sm['lang'][162]['text'],$sm['lang'][163]['text'],$sm['lang'][164]['text'],$sm['lang'][165]['text'],$sm['lang'][166]['text'],$sm['lang'][167]['text'],$sm['lang'][168]['text'],$sm['lang'][169]['text']);
		for ($month = 1; $month <= 12; $month++) {
			$html_output .= '<option value="' . $month . '" style="color:#000;">' . $months[$month] . '</option>'."\n";
		}
	}
	
	if($y==1){
		for ($year = 1925; $year <= (date("Y") - $year_limit); $year++) {
			$html_output .= '<option style="color:#000;" value="' . $year . '">' . $year . '</option>'."\n";
		}
		}
    return $html_output;
}


function fbconnect($fuid,$name,$email,$gender,$location){
	global $mysqli,$sm;
	$city = $location->city; 	
	$country = $location->country_name; 	
	$lat = $location->latitude; 	
	$lng = $location->longitude; 	

    $check = $mysqli->query("select id from users where facebook_id = '".$fuid."'");
	$photo = "https://graph.facebook.com/".$fuid."/picture?type=large";
	$pswd = $fuid;
	$name = secureEncode($name);
	if ($check->num_rows == 1){	
		$su = $check->fetch_object();
		$query = "UPDATE users SET verified = 1 WHERE id = '".$su->id."'";
		$mysqli->query($query);
		$_SESSION['user'] = $su->id;	
		
	} else {
		if($gender == 'male'){
			$gender = 1;
			$looking = 2;
		} else {
			$gender = 2;
			$looking = 1;
		}

		$query = "INSERT INTO users (name,email,pass,age,gender,city,country,lat,lng,looking,lang,join_date,s_gender,s_age,verified,facebook_id,credits)
								VALUES ('".$name."', '".$email."','".crypt($pswd)."','20','".$gender."','".$city."','".$country."','".$lat."','".$lng."','".$looking."','".$_SESSION['lang']."','".$date."','".$looking."','18,30,1',1,'".$fuid."','".$sm['config']['free_credits']."')";	
		if ($mysqli->query($query) === TRUE) {
			$last_id = $mysqli->insert_id;
			$_SESSION['user'] = $last_id;	
			$mysqli->query("INSERT INTO users_videocall (u_id) VALUES ('".$last_id."')");	
			$free_premium = $sm['config']['free_premium'];
			$time = time();	
			$extra = 86400 * $free_premium;
			$premium = $time + $extra;
			$mysqli->query("INSERT INTO users_premium (uid,premium) VALUES ('".$last_id."','".$premium."')");
			$query2 = "INSERT INTO users_photos (u_id,photo,profile,thumb,approved) VALUES ('".$last_id."','".$photo."',1,'".$photo."',1)";
			$mysqli->query($query2);				
			$mysqli->query("INSERT INTO users_notifications (uid) VALUES ('".$last_id."')");
			$mysqli->query("INSERT INTO users_extended (uid) VALUES ('".$last_id."')");	
		}							 
	}
}

function instaconnect($id,$name,$email,$gender,$photo,$location){
	global $mysqli,$sm;
	$city = $location->city; 	
	$country = $location->country_name; 	
	$lat = $location->latitude; 	
	$lng = $location->longitude; 	
	$return = 1;
    $check = $mysqli->query("select id from users where instagram_id = '".$id."'");
	$pswd = $id;
	$name = secureEncode($name);
	if ($check->num_rows == 1){	
		$su = $check->fetch_object();
		$query = "UPDATE users SET verified = 1 WHERE id = '".$su->id."'";
		$mysqli->query($query);
		$_SESSION['user'] = $su->id;	
		return $return;
	} else {
		if($gender == 'male'){
			$gender = 1;
			$looking = 2;
		} else {
			$gender = 2;
			$looking = 1;
		}

		$query = "INSERT INTO users (name,email,pass,age,gender,city,country,lat,lng,looking,lang,join_date,s_gender,s_age,verified,instagram_id,credits)
								VALUES ('".$name."', '".$email."','".crypt($pswd)."','20','".$gender."','".$city."','".$country."','".$lat."','".$lng."','".$looking."','".$_SESSION['lang']."','".$date."','".$looking."','18,30,1',1,'".$id."','".$sm['config']['free_credits']."')";	
		if ($mysqli->query($query) === TRUE) {
			$last_id = $mysqli->insert_id;
			$result = $last_id;
			$_SESSION['user'] = $last_id;	
			$mysqli->query("INSERT INTO users_videocall (u_id) VALUES ('".$last_id."')");	
			$free_premium = $sm['config']['free_premium'];
			$time = time();	
			$extra = 86400 * $free_premium;
			$premium = $time + $extra;
			$mysqli->query("INSERT INTO users_premium (uid,premium) VALUES ('".$last_id."','".$premium."')");
			$query2 = "INSERT INTO users_photos (u_id,photo,profile,thumb,approved) VALUES ('".$last_id."','".$photo."',1,'".$photo."',1)";
			$mysqli->query($query2);				
			$mysqli->query("INSERT INTO users_notifications (uid) VALUES ('".$last_id."')");
			$mysqli->query("INSERT INTO users_extended (uid) VALUES ('".$last_id."')");
			return $result;
		}							 
	}
}

function googleconnect($id,$name,$email,$gender,$photo,$location){
	global $mysqli,$sm;
	$city = $location->city; 	
	$country = $location->country_name; 	
	$lat = $location->latitude; 	
	$lng = $location->longitude; 	
    $check = $mysqli->query("select id from users where google_id = '".$id."'");
	$pswd = $id;
	$name = secureEncode($name);
	if ($check->num_rows == 1){	
		$su = $check->fetch_object();
		$query = "UPDATE users SET verified = 1 WHERE id = '".$su->id."'";
		$mysqli->query($query);
		$_SESSION['user'] = $su->id;	
	} else {
		if($gender == 'male'){
			$gender = 1;
			$looking = 2;
		} else {
			$gender = 2;
			$looking = 1;
		}

		$query = "INSERT INTO users (name,email,pass,age,gender,city,country,lat,lng,looking,lang,join_date,s_gender,s_age,verified,google_id,credits)
								VALUES ('".$name."', '".$email."','".crypt($pswd)."','20','".$gender."','".$city."','".$country."','".$lat."','".$lng."','".$looking."','".$_SESSION['lang']."','".$date."','".$looking."','18,30,1',1,'".$id."','".$sm['config']['free_credits']."')";	
		if ($mysqli->query($query) === TRUE) {
			$last_id = $mysqli->insert_id;
			$result = $last_id;
			$_SESSION['user'] = $last_id;	
			$mysqli->query("INSERT INTO users_videocall (u_id) VALUES ('".$last_id."')");	
			$free_premium = $sm['config']['free_premium'];
			$time = time();	
			$extra = 86400 * $free_premium;
			$premium = $time + $extra;
			$mysqli->query("INSERT INTO users_premium (uid,premium) VALUES ('".$last_id."','".$premium."')");
			$query2 = "INSERT INTO users_photos (u_id,photo,profile,thumb,approved) VALUES ('".$last_id."','".$photo."',1,'".$photo."',1)";
			$mysqli->query($query2);				
			$mysqli->query("INSERT INTO users_notifications (uid) VALUES ('".$last_id."')");
			$mysqli->query("INSERT INTO users_extended (uid) VALUES ('".$last_id."')");
		}							 
	}
}

function twitterconnect($id,$name,$email,$gender,$photo,$location){
	global $mysqli,$sm;
	$city = $location->city; 	
	$country = $location->country_name; 	
	$lat = $location->latitude; 	
	$lng = $location->longitude; 	
	$return = 1;
    $check = $mysqli->query("select id from users where twitter_id = '".$id."'");
	$pswd = $id;
	$name = secureEncode($name);
	if ($check->num_rows == 1){	
		$su = $check->fetch_object();
		$query = "UPDATE users SET verified = 1 WHERE id = '".$su->id."'";
		$mysqli->query($query);
		$_SESSION['user'] = $su->id;	
		return $return;
	} else {
		if($gender == 'male'){
			$gender = 1;
			$looking = 2;
		} else {
			$gender = 2;
			$looking = 1;
		}

		$query = "INSERT INTO users (name,email,pass,age,gender,city,country,lat,lng,looking,lang,join_date,s_gender,s_age,verified,twitter_id,credits)
								VALUES ('".$name."', '".$email."','".crypt($pswd)."','20','".$gender."','".$city."','".$country."','".$lat."','".$lng."','".$looking."','".$_SESSION['lang']."','".$date."','".$looking."','18,30,1',1,'".$id."','".$sm['config']['free_credits']."')";	
		if ($mysqli->query($query) === TRUE) {
			$last_id = $mysqli->insert_id;
			$result = $last_id;
			$_SESSION['user'] = $last_id;	
			$mysqli->query("INSERT INTO users_videocall (u_id) VALUES ('".$last_id."')");	
			$free_premium = $sm['config']['free_premium'];
			$time = time();	
			$extra = 86400 * $free_premium;
			$premium = $time + $extra;
			$mysqli->query("INSERT INTO users_premium (uid,premium) VALUES ('".$last_id."','".$premium."')");
			$query2 = "INSERT INTO users_photos (u_id,photo,profile,thumb,approved) VALUES ('".$last_id."','".$photo."',1,'".$photo."',1)";
			$mysqli->query($query2);				
			$mysqli->query("INSERT INTO users_notifications (uid) VALUES ('".$last_id."')");
			$mysqli->query("INSERT INTO users_extended (uid) VALUES ('".$last_id."')");
			return $result;
		}							 
	}
}


function quitar_tildes($cadena) {
$no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","À","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹");
$permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E");
$texto = str_replace($no_permitidas, $permitidas ,$cadena);
return $texto;
}

function getimg($url) {         
    $headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg, image/png';              
    $headers[] = 'Connection: Keep-Alive';         
    $headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';         
    $user_agent = 'php';         
    $process = curl_init($url);         
    curl_setopt($process, CURLOPT_HTTPHEADER, $headers);         
    curl_setopt($process, CURLOPT_HEADER, 0);         
    curl_setopt($process, CURLOPT_USERAGENT, $useragent);         
    curl_setopt($process, CURLOPT_TIMEOUT, 30);         
    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);         
    curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);         
    $return = curl_exec($process);         
    curl_close($process);         
    return $return;     
}

/* Check Functions */
function isLogged() {
    global $mysqli;
    
    if (!empty($_SESSION['user']) && is_numeric($_SESSION['user']) && $_SESSION['user'] > 0) {
        $user_id = secureEncode($_SESSION['user']);
        $query = "SELECT COUNT(id) AS count FROM usuarios WHERE id=$user_id";
        $sql_query = $mysqli->query($query);
        $sql_fetch = mysqli_fetch_assoc($sql_query);
        
        return $sql_fetch['count'];
    }
}




function getPage($page_url='') {
    global $sm, $lang;
    
    $page = './themes/' . $sm['config']['theme'] . '/layout/' . $page_url . '.phtml';
    $page_content = '';
    
    ob_start();
    include($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    
    return $page_content;
}

function getLandingPage($page_url='') {
    global $sm, $lang;
    
    $page = './themes/' . $sm['config']['theme_landing'] . '/layout/' . $page_url . '.phtml';
    $page_content = '';
    
    ob_start();
    include($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    
    return $page_content;
}


function requestPage($page_url='') {
    global $sm, $lang;
    
    $page = '../themes/' . $sm['config']['theme'] . '/layout/' . $page_url . '.phtml';
    $page_content = '';
    
    ob_start();
    include($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    
    return $page_content;
}

function getMailPage($page_url='') {
    global $sm, $lang;
    
    $page = './themes/' . $sm['config']['theme_email'] . '/layout/' . $page_url . '.phtml';
    $page_content = '';
    
    ob_start();
    include($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    
    return $page_content;
}

function getMobilePage($page_url='') {
    global $sm, $lang;
    
    $page = './themes/' . $sm['config']['theme_mobile'] . '/layout/' . $page_url . '.phtml';
    $page_content = '';
    
    ob_start();
    include($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    
    return $page_content;
}

function getAdminPage($page_url='') {
    global $sm, $lang;
    
    $page = './themes/default/layout/admin/' . $page_url . '.phtml';
    $page_content = '';
    
    ob_start();
    include($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    
    return $page_content;
}
function requestAdminPage($page_url='') {
    global $sm, $lang;
    
    $page = '../themes/' . $sm['config']['theme'] . '/layout/admin/' . $page_url . '.phtml';
    $page_content = '';
    
    ob_start();
    include($page);
    $page_content = ob_get_contents();
    ob_end_clean();
    
    return $page_content;
}


function pixelateS3($image, $output, $pixelate_x = 55, $pixelate_y = 55){
	global $sm;
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    if($ext == "jpg" || $ext == "jpeg")
        $img = imagecreatefromjpeg($image);
    elseif($ext == "png")
        $img = imagecreatefrompng($image);
    elseif($ext == "gif")
        $img = imagecreatefromgif($image);
    else
        echo 'Unsupported file extension';
		
    $size = getimagesize($image);
    $height = $size[1];
    $width = $size[0];

    for($y = 0;$y < $height;$y += $pixelate_y+1)
    {

        for($x = 0;$x < $width;$x += $pixelate_x+1)
        {
            $rgb = imagecolorsforindex($img, imagecolorat($img, $x, $y));

            $color = imagecolorclosest($img, $rgb['red'], $rgb['green'], $rgb['blue']);
            imagefilledrectangle($img, $x, $y, $x+$pixelate_x, $y+$pixelate_y, $color);

        }       
    }


    $output_name = $sm['config']['site_url'].'/assets/sources/uploads/'.$output .'.jpg';
    $output_src = 'uploads/'.$output .'.jpg';	

    imagejpeg($img, $output_src);
    imagedestroy($img); 
	return $output_src;
}

function pixelate($image, $output, $pixelate_x = 55, $pixelate_y = 55){
	global $sm;
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    if($ext == "jpg" || $ext == "jpeg")
        $img = imagecreatefromjpeg($image);
    elseif($ext == "png")
        $img = imagecreatefrompng($image);
    elseif($ext == "gif")
        $img = imagecreatefromgif($image);
    else
        echo 'Unsupported file extension';
		
    $size = getimagesize($image);
    $height = $size[1];
    $width = $size[0];

    for($y = 0;$y < $height;$y += $pixelate_y+1)
    {

        for($x = 0;$x < $width;$x += $pixelate_x+1)
        {
            $rgb = imagecolorsforindex($img, imagecolorat($img, $x, $y));

            $color = imagecolorclosest($img, $rgb['red'], $rgb['green'], $rgb['blue']);
            imagefilledrectangle($img, $x, $y, $x+$pixelate_x, $y+$pixelate_y, $color);

        }       
    }


    $output_name = $sm['config']['site_url'].'/assets/sources/uploads/'.$output .'.jpg';
    $output_src = 'uploads/'.$output .'.jpg';	

    imagejpeg($img, $output_src);
    imagedestroy($img); 
	return $output_name;
}


function spotLight($lat,$lng,$r=0){
	global $mysqli,$sm;	
	$time = time()-86400;
	$time_now = time()-300;
	$i = 0;
		$spotlight = $mysqli->query("SELECT u_id,photo,time, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) ) 
		* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin(radians(lat)) ) ) AS distance 
		FROM spotlight
		where country = '".$sm['user']['country']."'		
		ORDER BY time desc
		LIMIT 25
		");	
	if ($spotlight->num_rows < 3) {
		$spotlight = $mysqli->query("SELECT u_id,photo,time, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) ) 
		* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin(radians(lat)) ) ) AS distance 
		FROM spotlight

		ORDER BY distance
		LIMIT 25
		");				
	}
	if ($spotlight->num_rows > 0) { 
		while($spotl = $spotlight->fetch_object()){		
			echo'<div data-surl="profile"  data-uid="'.$spotl->u_id.'" class="profile-photo" data-src="'.$spotl->photo.'" >'.userStatusSpotlight($spotl->u_id).'</div>';
		}	
	}
}

function spotLightMobile($lat,$lng){
	global $mysqli,$sm;	
	$time = time()-86400;
	$time_now = time()-300;
	$i = 0;
		$spotlight = $mysqli->query("SELECT u_id,photo,time, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) ) 
		* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin(radians(lat)) ) ) AS distance 
		FROM spotlight
		where country = '".$sm['user']['country']."'		
		ORDER BY time desc
		LIMIT 15
		");	
	if ($spotlight->num_rows < 3) {
		$spotlight = $mysqli->query("SELECT u_id,photo,time, ( 6371 * acos( cos( radians($lat) ) * cos( radians( lat ) ) 
		* cos( radians( lng ) - radians($lng) ) + sin( radians($lat) ) * sin(radians(lat)) ) ) AS distance 
		FROM spotlight

		ORDER BY distance
		LIMIT 15
		");				
	}
	if ($spotlight->num_rows > 0) { 
		while($spotl = $spotlight->fetch_object()){		
			echo '<a href="#" onClick="goToProfile('.$spotl->u_id.')" data-show="1"><div class="user-in" style="background-image:url('.$spotl->photo.');">'.userStatusSpotlightMobile($spotl->u_id).'</div></a>';
		}	
	}
}



function getGifts() {
	global $mysqli,$sm;
	$gifts = '';
	$gift = $mysqli->query("SELECT * FROM gifts order by price ASC limit 100");

	if ($gift->num_rows > 0) { 
		while($gi = $gift->fetch_object()){
			if($gi->price <= $sm['user']['credits']) 
			{
			 $gifts .= '<div class="card-panel photo send-gift" data-tooltip="'.$sm['lang'][304]['text'].' '.$gi->price.'" data-gprice="'.$gi->price.'" data-src="'.$sm['config']['site_url'].'/'.$gi->icon.'">	 			
						</div>';	
			} else {
			 $gifts .= '<div class="card-panel photo grayScale send-gift" data-tooltip="'.$sm['lang'][304]['text'].' '.$gi->price.'"  data-gprice="'.$gi->price.'" data-src="'.$sm['config']['site_url'].'/'.$gi->icon.'">		
						</div>';
			}
		}
	}
	return $gifts;			
}

function getCreditsPackages() {
	global $mysqli,$sm;
	$return = '';
	$i=0;
	$query = $mysqli->query("SELECT * FROM config_credits order by credits ASC limit 100");

	if ($query->num_rows > 0) { 
		while($cre = $query->fetch_object()){
			$i++;
			if($i==1){
				$return .= '<option data-price="'.$cre->price.'" data-quantity="'.$cre->credits.'" selected>'.$cre->credits.' '.$sm['lang'][305]['text'].''.$cre->price.' '.$sm['config']['currency'].'</option>';
			} else {
				$return .= '<option data-price="'.$cre->price.'" data-quantity="'.$cre->credits.'">'.$cre->credits.' '.$sm['lang'][305]['text'].''.$cre->price.' '.$sm['config']['currency'].'</option>';				
			}
		}
	}
	return $return;			
}

function getAdminCreditsPackages() {
	global $mysqli,$sm;
	$return = '';
	$query = $mysqli->query("SELECT * FROM config_credits order by credits ASC limit 100");

	if ($query->num_rows > 0) { 
		while($cre = $query->fetch_object()){
				$return .= '<div class="row">
						<div class="col-xs-8">
							<label>Price in '.$sm['config']['currency'].' for '.number_format($cre->credits).' credits </label>
						</div>
						<div class="col-xs-1"></div>								
						<div class="col-xs-3">
						  <input type="text" class="form-control" value="'.$cre->price.'" name="credits'.$cre->id.'"> 
						</div>
				
					  </div>';				
		}
	}
	return $return;			
}

function getAdminUsersReported($limit=0) {
	global $mysqli,$sm;
	$return = '';
	$order = secureEncode($order);
	$time_now = time()-300;
	$query = $mysqli->query("SELECT reported,reported_by FROM reports order by id DESC");
	if ($query->num_rows > 0) { 
		while($cre = $query->fetch_object()){
				getUserInfo($cre->reported,6);
				getUserInfo($cre->reported_by,7);
				$return .= ' <tr>
							  <td class="man-photos"><div class="profile-photo" data-src="'.$sm['search']['profile_photo'].'"></td>						
							  <td>'.$sm['search']['id'].'</td>				  
							  <td>'.$sm['search']['name'].' , '.$sm['search']['age'].'
							  '; if($sm['search']['last_access'] >= $time_now) {
							  	$return .= ' <i class="fa fa-circle text-success" style="font-size:8px;"></i>';
							  }
							  
							  $return .= '
							  </td>						  
							  <td>'.$sm['search']['email'].'</td>
							  <td>'.$sm['search']['city'].'</td>
							  <td>'.$sm['search']['country'].'</td>					  
							  <td>'.$sm['search']['credits'].'</td>
							  <td>'.$sm['search']['total_photos'].'</td>
					  
							  <td><a href="'.$sm['config']['site_url'].'profile/'.$sm['search']['id'].'/'.$sm['search']['first_name'].'" target="_blank" class="label label-info">View</a> 
							  <a href="'.$sm['config']['site_url'].'index.php?page=admin&p=user&id='.$sm['search']['id'].'" target="_blank" class="label label-primary">Edit</a></td>		
							  <td><a href="'.$sm['config']['site_url'].'profile/'.$sm['manage']['id'].'/'.$sm['manage']['first_name'].'" target="_blank">'.$sm['manage']['name'].'</a></td>							  
							</tr>';				
		}
	}
	return $return;			
}

function getAdminPremiumPackages() {
	global $mysqli,$sm;
	$return = '';
	$query = $mysqli->query("SELECT * FROM config_premium order by days ASC limit 100");

	if ($query->num_rows > 0) { 
		while($cre = $query->fetch_object()){
				$return .= '<div class="row">
						<div class="col-xs-9">
							<label>Price in '.$sm['config']['currency'].' for '.$cre->days.' days of premium </label>
						</div>							
						<div class="col-xs-3">
						  <input type="text" class="form-control"  value="'.$cre->price.'" name="premium'.$cre->id.'"> 
						</div>
				
					  </div>';				
		}
	}
	return $return;			
}

function getAdminUsers($f,$order,$limit=0) {
	global $mysqli,$sm;
	$return = '';
	$order = secureEncode($order);
	$time_now = time()-300;
	if($f == 1) {
		$query = $mysqli->query("SELECT id FROM users order by $order DESC limit $limit,50");
	}
	if($f == 2) {
		$query = $mysqli->query("SELECT id FROM users where last_access >= '".$time_now."' order by $order DESC limit $limit,50");
	}	
	if ($query->num_rows > 0) { 
		while($cre = $query->fetch_object()){
				getUserInfo($cre->id,6);
				$return .= ' <tr>
							  <td class="man-photos"><div class="profile-photo" data-src="'.$sm['search']['profile_photo'].'"></td>						
							  <td>'.$sm['search']['id'].'</td>				  
							  <td>'.$sm['search']['name'].' , '.$sm['search']['age'].'
							  '; if($sm['search']['last_access'] >= $time_now) {
							  	$return .= ' <i class="fa fa-circle text-success" style="font-size:8px;"></i>';
							  }
							  
							  $return .= '
							  </td>						  
							  <td>'.$sm['search']['email'].'</td>
							  <td>'.$sm['search']['city'].'</td>
							  <td>'.$sm['search']['country'].'</td>					  
							  <td>'.$sm['search']['credits'].'</td>
							  <td>'.$sm['search']['total_photos'].'</td>
							  <td>'.$sm['search']['join_date'].'</td>					  
							  <td><a href="'.$sm['config']['site_url'].'/profile/'.$sm['search']['id'].'/'.$sm['search']['first_name'].'" target="_blank" class="label label-info">View</a> 
							  <a href="'.$sm['config']['site_url'].'index.php?page=admin&p=user&id='.$sm['search']['id'].'" target="_blank" class="label label-primary">Edit</a></td>					
							</tr>';				
		}
	}
	return $return;			
}

function getAdminGifts() {
	global $mysqli,$sm;
	$return = '';
	$query = $mysqli->query("SELECT * FROM gifts order by price ASC limit 100");

	if ($query->num_rows > 0) { 
		while($cre = $query->fetch_object()){
				$return .= '<div class="col-md-2">
								<div class="row">
									<div class="col-xs-12">
										<img src="'.$sm['config']['site_url'].'/'.$cre->icon.'" />
									</div>							
									<div class="col-xs-12">
										<input type="text" class="form-control" data-gift="'.$cre->id.'" value="'.$cre->price.'"> 
									</div>					
								</div>
							</div>';				
		}
	}
	return $return;			
}


function getPremiumPackages() {
	global $mysqli,$sm;
	$return = '';
	$i=0;
	$query = $mysqli->query("SELECT * FROM config_premium order by price ASC limit 100");

	if ($query->num_rows > 0) { 
		while($cre = $query->fetch_object()){
			$return .= '<button class="donate" data-premium-send="'.$cre->days.'" data-price="'.$cre->price.'" style="margin-bottom:10px;">
						'.$sm['lang'][170]['text'].' '.$cre->days.' '.$sm['lang'][171]['text'].' $'.$cre->price.' '.$sm['config']['currency'].'</button>';				
		}
	}
	return $return;			
}


function getUserPhotosHeaderChat($uid){	
	global $mysqli,$sm;	
	$photo = "";
	$photos = $mysqli->query("SELECT thumb FROM users_photos WHERE approved = 1 and profile = 0 and blocked = 0 and u_id = '".$uid."' order by id desc LIMIT 200");
	if ($photos->num_rows > 0) { 
		while($up = $photos->fetch_object()){
			
			$photo .= '<a data-purl="profile" data-uid="'.$uid.'" href="javascript:;"><img src="'.$up->thumb.'" /></a>';
		}	
	}
	
	return $photo;
}

function getUserPhotosHeader($uid){	
	global $mysqli,$sm;	
	$photo = "";
	$photos = $mysqli->query("SELECT thumb FROM users_photos WHERE approved = 1 and profile = 0 and blocked = 0 and u_id = '".$uid."' order by id desc LIMIT 200");
	if ($photos->num_rows > 0) { 
		while($up = $photos->fetch_object()){
			
			$photo .= '<a data-url="photos" data-uid="'.$uid.'" href="javascript:;"><img src="'.$up->thumb.'" /></a>';
		}	
	}
	
	return $photo;
}



function getUserPublicPhotosMobile($uid){	
	global $mysqli,$sm;	
	$photo = "";
	$photos = $mysqli->query("SELECT photo,id,thumb FROM users_photos WHERE approved = 1 and blocked = 0 and u_id = '".$uid."' order by id desc LIMIT 200");
	if ($photos->num_rows > 0) { 
		while($up = $photos->fetch_object()){
			
			$photo .= '<div class="group-block" data-div="'.$up->id.'"><div class="group-image" style="background-image:url('.$up->thumb.');"></div>
			<a href="#" data-set-profile="true" data-pid="'.$up->id.'" style="font-size:22px;margin-right:10px;margin-top:5px;"><i class="mdi-image-portrait"></i></a>
			<a href="#" data-delete-photo="true" data-pid="'.$up->id.'" data-tooltip="Delete" style="font-size:22px;margin-right:10px;margin-top:5px;"><i class="mdi-action-delete"></i></a></div>';
		}	
	}
	
	return $photo;
}

function getUserPrivatePhotosMobile($uid){	
	global $mysqli,$sm;	
	$photo = "";
	$photos = $mysqli->query("SELECT photo,id.thumb FROM users_photos WHERE approved = 1 and blocked = 1 and u_id = '".$uid."' order by id desc LIMIT 200");
	if ($photos->num_rows > 0) { 
		while($up = $photos->fetch_object()){
			
			$photo .= '<div class="group-block" data-div="'.$up->id.'"><div class="group-image" style="background-image:url('.$up->thumb.');"></div>
			<a href="#" data-delete-photo="true" data-pid="'.$up->id.'" data-tooltip="Delete" style="font-size:22px;margin-right:10px;margin-top:5px;"><i class="mdi-action-delete"></i></a></div>';
		}	
	}
	
	return $photo;
}


function getUserPhotosAll($uid){	
	global $mysqli,$sm;	
	$array  = array();
	$photos = $mysqli->query("SELECT photo FROM users_photos WHERE approved = 1 and blocked = 0 and u_id = '".$uid."' order by id desc LIMIT 3");
	if ($photos->num_rows > 0) { 
		while($up = $photos->fetch_object()){
			$array[] = array(
				"image" => $up->photo, 
			);
		}	
	}
	return $array;
}

function getUserPhotosAllProfile($uid){	
	global $mysqli,$sm;	
	$array  = array();
	$photos = $mysqli->query("SELECT photo,blocked,private FROM users_photos WHERE approved = 1 and u_id = '".$uid."' order by id desc LIMIT 200");
	$view = unblockedUser($sm['user']['id'],$uid);
	if ($photos->num_rows > 0) { 
		while($up = $photos->fetch_object()){
			if($up->blocked == 1){
				if($view == 0){
					$array[] = array(
						"image" => $up->private, 
					);
				} else {
					$array[] = array(
						"image" => $up->photo, 
					);
					
				}
			} else {
				$array[] = array(
					"image" => $up->photo, 
				);					
			}			

		}	
	}
	return $array;
}


function getUserPhotos($uid,$i=0,$x=0,$y=0,$b=0){	
	global $mysqli,$sm;	
	$photo = "";
	if($i == 1){
		$photos = $mysqli->query("SELECT * FROM users_photos WHERE u_id = '".$uid."' and approved = 1 order by id desc LIMIT 200");
	} else if($b == 1){
		$photos = $mysqli->query("SELECT * FROM users_photos WHERE approved = 1 and u_id = '".$uid."' and blocked = 1 order by id desc LIMIT 200");		
	} else {
		$photos = $mysqli->query("SELECT * FROM users_photos WHERE approved = 1 and u_id = '".$uid."' and blocked = 0 order by id desc LIMIT 200");
	}	
	if ($photos->num_rows > 0) { 
		while($up = $photos->fetch_object()){
			getUserInfo($uid,1);
			$photoData['id'] = $up->id;
			$photoData['src'] = $up->photo;
			$photoData['desc'] = $up->desc;
			$photoData['user'] = $sm['profile']['name'];
			$photoData['photo'] = $sm['profile']['profile_photo'];
			$photoData['blocked'] = $up->blocked;			
			$photoData['like'] = checkPhotoLike($sm['user']['id'],$up->id);
			$photoData['likes'] = getPhotoLikes($up->id);			
			$photoData['comments'] = getPhotoComments($up->id);
			$sm['photo'] = $photoData;
			if($i == 1 && $x == 0){
				$photo .= getPage('profile/mphoto');
			} else if($x == 1){
				$photo .= requestPage('profile/mphoto');		
			} else {
				$photo .= requestPage('profile/photo');
			}
		}	
	}
	return $photo;
}

function getUserPhotosSpotlight($uid){	
	global $mysqli,$sm;	
	$photo = "";
	$photos = $mysqli->query("SELECT * FROM users_photos WHERE u_id = '".$uid."' and approved = 1 order by id desc LIMIT 200");
	if ($photos->num_rows > 0) { 
		while($up = $photos->fetch_object()){
			getUserInfo($uid,1);
			$photoData['id'] = $up->id;
			$photoData['src'] = $up->photo;
			$photoData['desc'] = $up->desc;
			$photoData['user'] = $sm['profile']['name'];
			$photoData['photo'] = $sm['profile']['profile_photo'];
			$photoData['blocked'] = $up->blocked;			
			$photoData['like'] = checkPhotoLike($sm['user']['id'],$up->id);
			$photoData['likes'] = getPhotoLikes($up->id);			
			$photoData['comments'] = getPhotoComments($up->id);
			$sm['photo'] = $photoData;
			$photo .= getPage('profile/sphoto');
		}	
	}
	return $photo;
}

function getUserPrivatePhotos($u1,$u2){	
	global $mysqli,$sm;	
	$photo = "";
	$view = unblockedUser($u1,$u2);
	$photos = $mysqli->query("SELECT * FROM users_photos WHERE approved = 1 and u_id = '".$u2."' and blocked = 1 order by id desc LIMIT 200");	
	if ($photos->num_rows > 0) { 
		while($up = $photos->fetch_object()){
			getUserInfo($u2,1);
			$photoData['id'] = $up->id;
			$photoData['src'] = $up->photo;
			$photoData['psrc'] = $up->private;			
			$photoData['desc'] = $up->desc;
			$photoData['user'] = $sm['profile']['name'];
			$photoData['photo'] = $sm['profile']['profile_photo'];
			$photoData['blocked'] = $up->blocked;			
			$photoData['like'] = checkPhotoLike($sm['user']['id'],$up->id);
			$photoData['likes'] = getPhotoLikes($up->id);			
			$photoData['comments'] = getPhotoComments($up->id);
			$sm['photo'] = $photoData;
			if($view == 0){
				$photo .= requestPage('profile/p_photo');
			} else {
				$photo .= requestPage('profile/photo');
			}
		}	
	}
	return $photo;
}

function unblockedUser($u1,$u2){
	global $mysqli,$sm;
	$u1 = secureEncode($u1);
	$u2 = secureEncode($u2);
	
	$return = 0;
	$check = $mysqli->query("SELECT * FROM blocked_photos where u1 = '".$u1."' AND u2 = '".$u2."'");
	if($check->num_rows == 1){
		$return = 1;
	}
	if($u1 == $u2){
		$return = 2;
	}
	if($sm['user']['premium'] == 1 && $sm['premium']['private'] == 1){
		$return = 1;
	}
	if($sm['basic']['private'] == 1){
		$return = 1;
	}	
	return $return;
}

function getPhotoComments($pid){	
	global $mysqli,$sm;	
	$photo = "";
	$photos = $mysqli->query("SELECT * FROM photos WHERE pid = '".$pid."' order by id asc LIMIT 200");
	if ($photos->num_rows > 0) { 
		while($up = $photos->fetch_object()){
			getUserInfo($up->cid,9);
			$photo .= '<li><div class="profile-photo" data-src="'.$sm['comment']['profile_photo'].'"></div><h3><a href="'.$sm['config']['site_url'].'profile/'.$sm['comment']['id'].'/comments">'.$sm['comment']['first_name'].'</a></h3><div class="comment">'.$up->comment.'</div></li>';
		}	
	}
	return $photo;
}

function getPhotoCommentsAjax($pid){	
	global $mysqli,$sm;	
	$photo = "";
	$photos = $mysqli->query("SELECT * FROM photos WHERE pid = '".$pid."' order by id asc LIMIT 200");
	if ($photos->num_rows > 0) { 
		while($up = $photos->fetch_object()){
			getUserInfo($up->cid,9);
			$photo .= '<li><div class="profile-photo" data-src="'.$sm['comment']['profile_photo'].'"></div><h3><a href="'.$sm['config']['site_url'].'profile/'.$sm['comment']['id'].'/comment">'.$sm['comment']['first_name'].'</a> '.$sm['comment']['online'].'</h3><div class="comment">'.$up->comment.'</div></li>';
		}	
	}
	echo $photo;
}

function getPhotoLikes($pid){	
	global $mysqli,$sm;	
	$likes = "";
	$names = "";
	$total = getPhotoLikesTotal($pid);
	$photos = $mysqli->query("SELECT * FROM photos_likes WHERE pid = '".$pid."' order by RAND() LIMIT 1");
	if ($photos->num_rows > 0) { 
		while($up = $photos->fetch_object()){
			$names.= "<b>".$up->name."</b> ";
		}
		if($names != ""){
			if($total <= 1){
				$likes = '<i class="mdi-action-favorite"></i><span>'.$names.' '.$sm['lang'][173]['text'].'</span>';				
			} else {
				$total = $total-1;
				$likes = '<i class="mdi-action-favorite"></i><span>'.$names.' '.$sm['lang'][44]['text'].' <b>'.$total.'</b> '.$sm['lang'][172]['text'].'</span>';
			}
		}
	}
	return $likes;
}

function getPhotoLikesTotal($pid) {
	global $mysqli;
	
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM photos_likes WHERE pid = '".$pid."'");
	$total = $query->fetch_assoc();
	$result = $total['total'];
	
	return $result;
}

function checkPhotoLike($uid,$pid) {
	global $mysqli;
	
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM photos_likes WHERE pid = '".$pid."'  AND uid = '".$uid."'");
	$total = $query->fetch_assoc();
	$result = $total['total'];
	
	return $result;
}

function checkRecoverCode($uid,$code) {
	global $mysqli;
	
	$uid = secureEncode($uid);
	$code = secureEncode($code);	
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM emails WHERE uid = '".$uid."'  AND code = '".$code."'");
	if($query->num_rows > 0){
		$total = $query->fetch_assoc();
		$result = $total['total'];
	}
	
	return $result;
}

function getUserPhotosChat($uid,$name){	
	global $mysqli,$sm;
	$photos = '';
	$name = secureEncode($name);
	$spotlight = $mysqli->query("SELECT photo FROM users_photos WHERE approved = 1 and u_id = '".$uid."' order by id desc LIMIT 100");
	if ($spotlight->num_rows > 0) { 

		while($spotl = $spotlight->fetch_object()){
			
			$photos.='<img data-name="'.$name.'" src="'.$spotl->photo.'" />';
		}	
	}
	
	return $photos;
}

function getVideocallStatus($uid) {
	global $mysqli,$sm;
	
	$uid = secureEncode($uid);
	$user = $mysqli->query("SELECT status FROM users_videocall WHERE u_id = '".$uid."'");
	$u = $user->fetch_object();
	
	return 	$u->status;
}

function getPeerId($uid) {
	global $mysqli,$sm;
	
	$uid = secureEncode($uid);
	$user = $mysqli->query("SELECT peer_id FROM users_videocall WHERE u_id = '".$uid."'");
	$u = $user->fetch_object();
	
	return 	$u->peer_id;
}

function getIdPeer($uid) {
	global $mysqli,$sm;
	
	$uid = secureEncode($uid);
	$user = $mysqli->query("SELECT u_id FROM users_videocall WHERE peer_id = '".$uid."'");
	$u = $user->fetch_object();
	
	return 	$u->u_id;
}



function getUserFriends($uid){	
	global $mysqli,$sm;
	$friends = '';
	$query = $mysqli->query("SELECT u1 FROM users_likes WHERE u2 = '".$uid."' and love = 1 LIMIT 1500");
	if ($query->num_rows > 0) { 
		while($result = $query->fetch_object()){
			getUserInfo($result->u1,4);
			
			$friends.='<div class="sidebar-friends" data-name="'.$sm['friend']['name'].'" data-uid="'.$sm['friend']['id'].'" data-chat="'.$sm['friend']['id'].'"
			data-all="1" data-fan="1" data-conv="'.checkConv($uid,$sm['friend']['id']).'" data-message="'.getNewMessages($uid,$sm['friend']['id']).'" data-status="'.userFilterStatus($sm['friend']['id']).'">
			<a href="javascript:;" data-uid="'.$sm['friend']['id'].'" ><div class="friend-list"><div class="profile-photo" data-src="'.$sm['friend']['profile_photo'].'"></div><h3>'.$sm['friend']['name'].'</h3><span class="'.$sm['friend']['status'].'"></span></div></a></div> ';
		}	
	}
	
	$query2 = $mysqli->query("SELECT DISTINCT s_id FROM chat WHERE r_id = '".$uid."'  order by id DESC");
	if ($query2->num_rows > 0) { 
		while($result2 = $query2->fetch_object()){
			getUserInfo($result2->s_id,4);
			if(isFan($sm['friend']['id'],$uid) == 0){
				$friends.='<div class="sidebar-friends" data-name="'.$sm['friend']['name'].'" data-uid="'.$sm['friend']['id'].'" 
				data-chat="'.$sm['friend']['id'].'" data-all="1" data-fan="0" data-conv="1"  data-message="'.getNewMessages($uid,$sm['friend']['id']).'"
				data-status="'.userFilterStatus($sm['friend']['id']).'">
				<a href="javascript:;" data-uid="'.$sm['friend']['id'].'"><div class="friend-list"><div class="profile-photo" data-src="'.$sm['friend']['profile_photo'].'"></div><h3>'.$sm['friend']['name'].'</h3><span class="'.$sm['friend']['status'].'"></span></div></a></div> ';
			}
		}	
	}
	
	return $friends;
}
function getUserFriendsMobile($uid){	
	global $mysqli,$sm;
	$friends = '';
	$arr = array();
	$arr[] = $uid;
	$query2 = $mysqli->query("SELECT s_id,r_id,seen,notification FROM chat WHERE r_id = '".$uid."' || s_id = '".$uid."' order by id desc");
	if ($query2->num_rows > 0) { 
		while($result2 = $query2->fetch_object()){
			if (!in_array($result2->s_id, $arr)){
				$arr[] = $result2->s_id;			  
				getUserInfo($result2->s_id,4);
				if($result2->r_id == $uid && $result2->seen == 2){				

				} else {				
					$friends.='
					<li class="list-message" data-ix="list-item">
					<a class="w-clearfix w-inline-block" href="mobile.php?page=chat&id='.$sm['friend']['id'].'">
					  '.userStatusMessagesMobile($sm['friend']['id']).'
					  <div class="w-clearfix column-left">
						<div class="image-message profile-photo" data-src="'.$sm['friend']['profile_photo'].'"><img src="'.$sm['friend']['profile_photo'].'">
	
						</div>
					  </div>
					  <div class="column-right">
	<div style="position:absolute;right:10px;top:15px;font-size:10px;color:#a0a0a0">'.time_elapsed_string(getLastMessageMobileTime($sm['user']['id'],$sm['friend']['id'])).'</div>
	<div style="position:absolute;right:10px;top:42px;font-size:12px;">'.getLastMessageMobileSeen($sm['user']['id'],$sm['friend']['id']).'</div>	

						<div class="message-title">'.$sm['friend']['first_name'].'</div>
						<div class="message-text">'.getLastMessageMobile($sm['user']['id'],$sm['friend']['id']).'</div>
					  </div>
					</a>
				  </li> ';	
				}				  
			}
			
			if (!in_array($result2->r_id, $arr)){
				$arr[] = $result2->r_id;			  
				getUserInfo($result2->r_id,4);
				if($result2->s_id == $uid && $result2->notification == 2){				

				} else {				
					$friends.='
					<li class="list-message" data-ix="list-item">
					<a class="w-clearfix w-inline-block" href="mobile.php?page=chat&id='.$sm['friend']['id'].'">
					  '.userStatusMessagesMobile($sm['friend']['id']).'
					  <div class="w-clearfix column-left">
						<div class="image-message profile-photo" data-src="'.$sm['friend']['profile_photo'].'"><img src="'.$sm['friend']['profile_photo'].'">
	
						</div>
					  </div>
					  <div class="column-right">
	<div style="position:absolute;right:10px;top:15px;font-size:10px;color:#a0a0a0">'.time_elapsed_string(getLastMessageMobileTime($sm['user']['id'],$sm['friend']['id'])).'</div>	
	<div style="position:absolute;right:10px;top:42px;font-size:12px;">'.getLastMessageMobileSeen($sm['user']['id'],$sm['friend']['id']).'</div>	
						<div class="message-title">'.$sm['friend']['first_name'].'</div>
						<div class="message-text">'.getLastMessageMobile($sm['user']['id'],$sm['friend']['id']).'</div>
					  </div>
					</a>
				  </li> ';		
				}				  
			}			
		}	
	}
	
	return $friends;
}

function getUserNewFriends($uid){	
	global $mysqli,$sm;
	$friends = '';
	$query = $mysqli->query("SELECT id FROM users WHERE id = '".$uid."'");
	if ($query->num_rows > 0) { 
		while($result = $query->fetch_object()){
			getUserInfo($result->u1,4);
			$friends.='<div class="sidebar-friends" data-name="'.$sm['friend']['name'].'" data-uid="'.$sm['friend']['id'].'" data-chat="'.$sm['friend']['id'].'"
			data-all="1" data-fan="1" data-conv="'.checkConv($uid,$sm['friend']['id']).'" data-message="'.getNewMessages($uid,$sm['friend']['id']).'" data-status="'.userFilterStatus($sm['friend']['id']).'">
			<a href="javascript:;" data-uid="'.$sm['friend']['id'].'" ><div class="friend-list"><div class="profile-photo" data-src="'.$sm['friend']['profile_photo'].'"></div><h3>'.$sm['friend']['name'].'</h3><span class="'.$sm['friend']['status'].'"></span></div></a></div> ';
		}	
	}
	return $friends;
}

function searchFriends($uid,$filter){	
	global $mysqli,$sm;
	$search = '';
	$query = $mysqli->query("SELECT id FROM users WHERE name LIKE '%$filter%' OR screen_name LIKE '%$filter%' order by id DESC LIMIT 1500");
	if ($query->num_rows > 0) { 
		while($result = $query->fetch_object()){
			getUserInfo($result->id,6);
			
			if(isFriend($uid,$sm['search']['id']) == 0){
				$search.='<div class="sidebar-friends" data-name="'.$sm['search']['name'].'" data-uid="'.$sm['search']['id'].'" 
				data-twitter="'.isFriend($uid,$sm['search']['id']).'" data-friend="'.isFriend($sm['search']['id'],$uid).'" data-conv="'.checkConv($uid,$sm['search']['id']).'" data-message="'.getNewMessages($uid,$sm['search']['id']).'"  data-status="'.userFilterStatus($sm['search']['id']).'">
				<a href="javascript:;" data-id="'.$sm['search']['screen_name'].'" data-uid="'.$sm['search']['id'].'" data-invite="'.$sm['search']['invite'].'"><div class="friend-list"><img src="'.$sm['search']['profile_photo'].'" alt="" /><h3>'.$sm['search']['name'].'</h3><span class="'.$sm['search']['status'].'"></span></div></a></div> ';
			}
		}	
	}	
	
	return $search;
}




function isFan($uid1,$uid2) {
	global $mysqli;
	
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM users_likes where u1 = '".$uid1."' and u2 = '".$uid2."'");
	$total = $query->fetch_assoc();
	if($total['total'] >= 1){
		$result = 1;
	}
	return $result;
}

function isBlocked($uid1,$uid2) {
	global $mysqli;
	
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM black_list where u2 = '".$uid1."' and u1 = '".$uid2."'");
	$total = $query->fetch_assoc();
	if($total['total'] >= 1){
		$result = 1;
	}
	return $result;
}

function getChatMobile($uid1,$uid2){	
	global $mysqli,$sm;
	$chat = '';
	$text = "";
	$next = 0;
	$last = 0;
	$mysqli->query("UPDATE chat set seen = 1 where s_id = '".$uid2."' and r_id = '".$uid1."'");	
	$spotlight = $mysqli->query("SELECT * FROM chat WHERE s_id = '".$uid1."' and r_id = '".$uid2."'
								OR r_id = '".$uid1."' and s_id = '".$uid2."' ORDER BY id ASC");
	if ($spotlight->num_rows > 0) { 

		while($spotl = $spotlight->fetch_object()){
				
			$message = $spotl->message;
			$continue = true;
			if($spotl->photo == 1){
				$continue = true;
			}
			if($spotl->access == 1){
				$continue = true;
			}			
			if($spotl->seen == 1){
				$seen = '<i class="mdi-navigation-check" title="Seen"></i>';
			} else {
				$seen = '<i class="mdi-device-access-time" title="Unseen"></i>';
			}
			if($continue == true){
				if($uid1 == $spotl->s_id) {
					$chat.='<li class="list-chat right" data-ix="list-item" id="me">
					<div class="w-clearfix column-right chat right">
					<div class="arrow-globe right"></div>
					<div class="chat-text right">';
					if($spotl->photo == 1){
						$chat.='<img src="'.$message.'"></div><div class="chat-time right">'.time_elapsed_string($spotl->time).'</div></div></li>';
					}else if($spotl->access == 1){
						$chat.= $text.' '.$sm['lang'][174]['text'].'</p></div><div class="chat-time right">'.time_elapsed_string($spotl->time).'</div></div></li>';					
					} else if($spotl->access == 2){
						$chat.= $text.' '.$sm['lang'][174]['text'].'</p></div><div class="chat-time right">'.time_elapsed_string($spotl->time).'</div></div></li>';					
					}  else {
						$chat.= $text.$message.' </p></div><div class="chat-time right">'.time_elapsed_string($spotl->time).'</div></div></li>';
					}
					$text = "";
				}else {
					$chat.='<li class="w-clearfix list-chat" data-ix="list-item" id="you">
					<div class="column-left chat"><div class="image-message chat">
					<img src="'.$sm['profile']['profile_photo'].'"></div></div>
	<div class="w-clearfix column-right chat">
	  <div class="arrow-globe"></div>
	  <div class="chat-text">';
					if($spotl->photo == 1){
						$chat.='<img src="'.$message.'"></div><div class="chat-time">'.time_elapsed_string($spotl->time).'</div></div></li>';
					}else if($spotl->access == 1){
						$chat.=$text.' '.$sm['lang'][174]['text'].' <a href="javascript:;" data-tooltip="'.$sm['lang'][175]['text'].'" class="access center-align" data-access="yes"><i class="mdi-action-done"></i></a><a data-tooltip="'.$sm['lang'][176]['text'].'" href="javascript:;" data-access="no" class="access center-align"> <i class="mdi-content-clear"></i></a></p></div><div class="chat-time">'.time_elapsed_string($spotl->time).'</div></div></li>';					
					} else if($spotl->access == 2){
						$chat.= $text.' '.$sm['lang'][174]['text'].'</p></div><div class="chat-time">'.time_elapsed_string($spotl->time).'</div></div></li>';					
					} else {
						$chat.= $text.$message.' </p></div><div class="chat-time">'.time_elapsed_string($spotl->time).'</div></div></li>';
					}
					$text = "";
				}
			}
		}	
	}
	
	return $chat;
}


function getChat($uid1,$uid2){	
	global $mysqli,$sm;
	$chat = '';
	$text = "";
	$next = 0;
	$last = 0;
	$mysqli->query("UPDATE chat set seen = 1 where s_id = '".$uid2."' and r_id = '".$uid1."'");	
	$spotlight = $mysqli->query("SELECT * FROM chat WHERE s_id = '".$uid1."' and r_id = '".$uid2."'
								OR r_id = '".$uid1."' and s_id = '".$uid2."' ORDER BY id ASC");
	if ($spotlight->num_rows > 0) { 

		while($spotl = $spotlight->fetch_object()){
				
			$message = $spotl->message;
			$next_query = $mysqli->query("SELECT id FROM chat WHERE s_id = '".$uid1."' and r_id = '".$uid2."' and id > '".$spotl->id."'
								OR r_id = '".$uid1."' and s_id = '".$uid2."' and id > '".$spotl->id."' ORDER BY id ASC LIMIT 1");
			if ($next_query->num_rows > 0) {
				$next_result = $next_query->fetch_object();
				$next_id = $next_result->id;
			}
			$prev = $spotl->s_id;
			$current = $spotl->id;
			$next_men = $mysqli->query("SELECT s_id,photo FROM chat WHERE id = '".$next_id."'");			
			$last_men = $mysqli->query("SELECT id FROM chat WHERE s_id = '".$uid1."' and r_id = '".$uid2."'
								OR r_id = '".$uid1."' and s_id = '".$uid2."' ORDER BY id DESC LIMIT 1");
			if ($last_men->num_rows > 0) {
				$last_m = $last_men->fetch_object();
				$last = $last_m->id;
			}
			$continue = true;
			
			if ($next_men->num_rows > 0) {
				$next_m = $next_men->fetch_object();
				$next = $next_m->s_id;
			}

			if (preg_match_all('/(?<!\w)*(\w+)/', $message, $matches))
			{
				$icons = $matches[1];
				foreach ($icons as $icon)
				{
					$message = str_replace( '*'.$icon, '<i class="emoticon '.$icon.' title="cool"></i>', $message );
				}
			}

			if($next == $prev){
				if($current == $last){
					$continue = true;
				} else if($next_m->photo == 1){
					$continue = true;
				} else {
					$continue = false;	
					if($spotl->photo == 0 && $spotl->access == 0){
						$text = $text." ".$message."<br>";		
					}
				}
			}
			if($spotl->photo == 1){
				$continue = true;
			}
			if($spotl->access == 1){
				$continue = true;
			}			
			if($spotl->seen == 1){
				$seen = '<i class="mdi-navigation-check" title="Seen"></i>';
			} else {
				$seen = '<i class="mdi-device-access-time" title="Unseen"></i>';
			}
			if($continue == true){
				if($uid1 == $spotl->s_id) {
					$chat.='<div class="post"><div class="left" id="me"><div class="profile-photo" data-src="'.$sm['user']['profile_photo'].'"></div>
					<h1><a href="javascript:;" data-purl="profile" data-uid="'.$sm['user']['id'].'">'.$sm['user']['name'].'</a></h1>';
					if($spotl->photo == 1){
						$chat.='<div class="photos1"><div class="post-photo1" data-instance="photo'.$spotl->time.'" src="'.$message.'" data-src="'.$message.'"></div></div></div></div>';
					}else if($spotl->access == 1){
						$chat.='<p class="me">'.$text.' '.$sm['lang'][174]['text'].'</p></div></div>';					
					} else if($spotl->access == 2){
						$chat.='<p class="me">'.$text.' '.$sm['lang'][174]['text'].'</p></div></div>';					
					}  else {
						$chat.='<p class="me">'.$text.$message.' </p></div></div>';
					}
					$text = "";
				}else {
					$chat.='<div class="post"><div class="left" id="you"><div class="profile-photo" data-src="'.$sm['profile']['profile_photo'].'"></div>
					<h1><a href="javascript:;" data-purl="profile" data-uid="'.$sm['profile']['id'].'">'.$sm['profile']['name'].'</a></h1>';
					if($spotl->photo == 1){
						$chat.='<div class="photos1"><div class="post-photo1" data-instance="photo'.$spotl->time.'" src="'.$message.'" data-src="'.$message.'"></div></div></div></div>';
					}else if($spotl->access == 1){
						$chat.='<p class="you">'.$text.' '.$sm['lang'][174]['text'].' <a href="javascript:;" data-tooltip="'.$sm['lang'][175]['text'].'" class="access center-align" data-access="yes"><i class="mdi-action-done"></i></a><a data-tooltip="'.$sm['lang'][176]['text'].'" href="javascript:;" data-access="no" class="access center-align"> <i class="mdi-content-clear"></i></a></p></div></div>';					
					} else if($spotl->access == 2){
						$chat.='<p class="you">'.$text.' '.$sm['lang'][174]['text'].'</p></div></div>';					
					} else {
						$chat.='<p class="you">'.$text.$message.' </p></div></div>';
					}
					$text = "";
				}
			}
		}	
	}
	
	return $chat;
}

function getLastChat($uid1,$uid2){	
	global $mysqli,$sm;
	$chat = '';
	$spotlight = $mysqli->query("SELECT * FROM chat WHERE s_id = '".$uid1."' and r_id = '".$uid2."' ORDER BY id DESC LIMIT 1");
	if ($spotlight->num_rows > 0) { 

		while($spotl = $spotlight->fetch_object()){
			$message = $spotl->message;
			if (preg_match_all('/(?<!\w)*(\w+)/', $message, $matches))
			{
				$icons = $matches[1];
				foreach ($icons as $icon)
				{
					$message = str_replace( '*'.$icon, '<i class="emoticon '.$icon.' title="cool"></i>', $message );
				}
			}		
			if($uid1 == $spotl->s_id) {
				$chat.='<div class="post"><div class="left" id="me"><div class="profile-photo" data-src="'.$sm['user']['profile_photo'].'"></div>
				<h1>'.$sm['user']['name'].'</h1> <p class="me">'.$message.' </p></div></div></div>';
			}else {
				$chat.='<div class="post"><div class="left" id="you"><div class="profile-photo" data-src="'.$sm['chat']['profile_photo'].'"></div>
				<h1>'.$sm['chat']['name'].'</h1> <p class="you">'.$message.'</p></div></div></div>';	
			}

		}	
	}
	
	return $chat;
}

function getLastChatMobile($uid1,$uid2){	
	global $mysqli,$sm;
	$chat = '';
	$spotlight = $mysqli->query("SELECT * FROM chat WHERE s_id = '".$uid1."' and r_id = '".$uid2."' ORDER BY id DESC LIMIT 1");
	if ($spotlight->num_rows > 0) { 

		while($spotl = $spotlight->fetch_object()){
			$message = $spotl->message;
			if (preg_match_all('/(?<!\w)*(\w+)/', $message, $matches))
			{
				$icons = $matches[1];
				foreach ($icons as $icon)
				{
					$message = str_replace( '*'.$icon, '<i class="emoticon '.$icon.' title="cool"></i>', $message );
				}
			}		
			if($uid1 == $spotl->s_id) {
				$chat.='<li class="list-chat right" data-ix="list-item" id="me" style="opacity: 1; transform: translateX(0px) translateY(0px); transition: opacity 500ms cubic-bezier(0.23, 1, 0.32, 1), transform 500ms cubic-bezier(0.23, 1, 0.32, 1);">
						<div class="w-clearfix column-right chat right">
						  <div class="arrow-globe right"></div>
						  <div class="chat-text right">'.$message.'</div>
						</div>
					  </li>';
			}else {
				$chat.='<li class="w-clearfix list-chat" data-ix="list-item" id="you" style="opacity: 1; transform: translateX(0px) translateY(0px); transition: opacity 500ms cubic-bezier(0.23, 1, 0.32, 1), transform 500ms cubic-bezier(0.23, 1, 0.32, 1);">
				<div class="column-left chat"><div class="image-message chat">
				<img src="'.$sm['chat']['profile_photo'].'"></div></div>
<div class="w-clearfix column-right chat">
  <div class="arrow-globe"></div>
  <div class="chat-text">'.$message.' </p></div></div></li>';	
			}

		}	
	}
	
	return $chat;
}

function getLastChatMobile2($uid1,$uid2){	
	global $mysqli,$sm;
	$chat = '';
	$spotlight = $mysqli->query("SELECT * FROM chat WHERE r_id = '".$uid1."' and s_id = '".$uid2."' and seen = 0 ORDER BY id ASC");
	if ($spotlight->num_rows > 0) { 

		while($spotl = $spotlight->fetch_object()){
			$message = $spotl->message;
			if (preg_match_all('/(?<!\w)*(\w+)/', $message, $matches))
			{
				$icons = $matches[1];
				foreach ($icons as $icon)
				{
					$message = str_replace( '*'.$icon, '<i class="emoticon '.$icon.' title="cool"></i>', $message );
				}
			}		
			if($uid1 == $spotl->s_id) {
				$chat.='<li class="list-chat right" data-ix="list-item" id="me" style="opacity: 1; transform: translateX(0px) translateY(0px); transition: opacity 500ms cubic-bezier(0.23, 1, 0.32, 1), transform 500ms cubic-bezier(0.23, 1, 0.32, 1);">
						<div class="w-clearfix column-right chat right">
						  <div class="arrow-globe right"></div>
						  <div class="chat-text right">'.$message.'</div>
						</div>
					  </li>';
			}else {
				$chat.='<li class="w-clearfix list-chat" data-ix="list-item" id="you" style="opacity: 1; transform: translateX(0px) translateY(0px); transition: opacity 500ms cubic-bezier(0.23, 1, 0.32, 1), transform 500ms cubic-bezier(0.23, 1, 0.32, 1);">
				<div class="column-left chat"><div class="image-message chat">
				<img src="'.$sm['chat']['profile_photo'].'"></div></div>
<div class="w-clearfix column-right chat">
  <div class="arrow-globe"></div>
  <div class="chat-text">'.$message.' </p></div></div></li>';	
			}

		}	
	}
	
	return $chat;
}



function getLastMessage($uid1,$uid2){	
	global $mysqli,$sm;
	$chat = '';
	$spotlight = $mysqli->query("SELECT * FROM chat WHERE r_id = '".$uid1."' and s_id = '".$uid2."' and seen = 0 ORDER BY id ASC");
	if ($spotlight->num_rows > 0) { 
		while($spotl = $spotlight->fetch_object()){
			$message = $spotl->message;
			if (preg_match_all('/(?<!\w)*(\w+)/', $message, $matches))
			{
				$icons = $matches[1];
				foreach ($icons as $icon)
				{
					$message = str_replace( '*'.$icon, '<i class="emoticon '.$icon.' title="cool"></i>', $message );
				}
			}		
			if($uid1 == $spotl->s_id) {
				$chat.='<div class="post"><div class="left" id="me"><div class="profile-photo" data-src="'.$sm['user']['profile_photo'].'"></div>
				<h1>'.$sm['user']['name'].'</h1>';
				if($spotl->photo == 1){
					$chat.='<div class="photos1"><div class="post-photo1" data-instance="photo'.$spotl->time.'" src="'.$message.'" data-src="'.$message.'"></div></div></div></div>';
				}else {
					$chat.='<p class="me">'.$text.$message.' </p></div></div>';
				}				
			}else {
				$chat.='<div class="post"><div class="left" id="you"><div class="profile-photo" data-src="'.$sm['chat']['profile_photo'].'"></div>
				<h1>'.$sm['chat']['name'].'</h1> ';
				if($spotl->photo == 1){
					$chat.='<div class="photos1"><div class="post-photo1" data-instance="photo'.$spotl->time.'" src="'.$message.'" data-src="'.$message.'"></div></div></div></div>';
				} else {
					$chat.='<p class="you">'.$text.$message.' </p></div></div>';
				}
			}

		}	
	}
	return $chat;
}



function getLastMessageMobile($uid1,$uid2){	
	global $mysqli,$sm;
	$chat = '';
	$spotlight = $mysqli->query("SELECT * FROM chat WHERE r_id = '".$uid1."' and s_id = '".$uid2."' OR s_id = '".$uid1."' and r_id = '".$uid2."' ORDER BY id DESC LIMIT 1");
	if ($spotlight->num_rows > 0) { 
		while($spotl = $spotlight->fetch_object()){
			if($spotl->photo == 1 ){
				$message = '<i class="mdi-image-photo-camera"></i>';		
			}
			else if($spotl->access == 1 ){
				$message = $sm['lang'][174]['text'];		
			}	else {
				$message = $spotl->message;			
			}
			
		}	
	}
	return $message;
}

function getLastMessageMobileTime($uid1,$uid2){	
	global $mysqli,$sm;
	$chat = '';
	$spotlight = $mysqli->query("SELECT time FROM chat WHERE r_id = '".$uid1."' and s_id = '".$uid2."' OR s_id = '".$uid1."' and r_id = '".$uid2."' ORDER BY id DESC LIMIT 1");
	if ($spotlight->num_rows > 0) { 
		while($spotl = $spotlight->fetch_object()){
			$message = $spotl->time;
		}	
	}
	return $message;
}

function getLastMessageMobileSeen($uid1,$uid2){	
	global $mysqli,$sm;
	$message = '';
	$spotlight = $mysqli->query("SELECT seen,s_id FROM chat WHERE r_id = '".$uid1."' and s_id = '".$uid2."' OR s_id = '".$uid1."' and r_id = '".$uid2."' ORDER BY id DESC LIMIT 1");
	if ($spotlight->num_rows > 0) { 
		while($spotl = $spotlight->fetch_object()){
			if($spotl->s_id == $uid1 && $spotl->seen == 0){
				$message = '<i class="mdi-navigation-check" style="color:#488ad8"></i>';
			}
			if($spotl->s_id == $uid1 && $spotl->seen == 1){
				$message = '<i class="mdi-navigation-check" style="color:#13d213"></i><i class="mdi-navigation-check" style="color:#13d213"></i>';
			}
			if($spotl->s_id != $uid1 && $spotl->seen == 0){
				$message = '<i class="mdi-image-brightness-1" style="color:#13d213"></i>';
			}
			if($spotl->s_id != $uid1 && $spotl->seen == 1){
				$message = '<i class="mdi-image-brightness-1" style="color:#488ad8"></i>';
			}			
		}	
	}
	return $message;
}

function cleanMessage($message){	
	global $mysqli,$sm;
	$chat = '';
	
	if (preg_match_all('/(?<!\w)*(\w+)/', $message, $matches))
	{
		$icons = $matches[1];
		foreach ($icons as $icon)
		{
			$message = str_replace( '*'.$icon, '<i class="emoticon '.$icon.' title="cool"></i>', $message );
		}
	}		
	$chat = $message;				
	
	return $chat;
}

function cleanMessageById($id){	
	global $mysqli,$sm;
	$chat = '';
	
	$cm = $mysqli->query("SELECT message FROM chat WHERE id = '".$id."'");
	$men = $cm->fetch_object();
	$message = $men->message;
	if (preg_match_all('/(?<!\w)*(\w+)/', $message, $matches))
	{
		$icons = $matches[1];
		foreach ($icons as $icon)
		{
			$message = str_replace( '*'.$icon, '<i class="emoticon '.$icon.' title="cool"></i>', $message );
		}
	}		
	$chat = $message;				
	
	return $chat;
}

function getAdminLang(){	
	global $mysqli,$sm;
	$lang = '';
	$query = $mysqli->query("SELECT * FROM languages ORDER BY id ASC");
	if ($query->num_rows > 0) { 
		while($result = $query->fetch_object()){
			$lang.= '<div class="col-md-3" style="height:50px;">
						<a href="index.php?page=admin&p=editlang&id='.$result->id.'" class="btn btn-primary" style="padding:10px;">
							'.$result->name.'
						</a>';
						if($result->visible == 1 ){ 
						$lang.='
							<div class="onoffswitch">
								<input type="checkbox" data-lang-switch="1" data-lang-id="'.$result->id.'" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch'.$result->id.'" checked>
								<label class="onoffswitch-label" for="myonoffswitch'.$result->id.'"></label>
							</div>';
						} else {
						$lang.='
							<div class="onoffswitch">
								<input type="checkbox" name="onoffswitch" data-lang-switch="0"  data-lang-id="'.$result->id.'" class="onoffswitch-checkbox" id="myonoffswitch'.$result->id.'">
								<label class="onoffswitch-label" for="myonoffswitch'.$result->id.'"></label>
							</div>';
						}						
					$lang.=' </div>';
		}	
	}
	return $lang;
}

function getAdminLangEdit($id){	
	global $mysqli,$sm;
	$lang = '';
	$query = $mysqli->query("SELECT * FROM site_lang where lang_id = '$id' and id <= 341 ORDER BY id ASC");
	if ($query->num_rows > 0) { 
		while($result = $query->fetch_object()){
			$lang.= '<div class="col-md-2" style="height:50px;"><input class="form-control" value="'.$result->text.'"  data-langid="'.$result->lang_id.'" data-lid="'.$result->id.'" /></div>';
		}	
	}
	return $lang;
}

function getAdminLangEditEmail($id){	
	global $mysqli,$sm;
	$lang = '';
	$query = $mysqli->query("SELECT * FROM site_lang where lang_id = '$id' and id > 341 ORDER BY id ASC");
	if ($query->num_rows > 0) { 
		while($result = $query->fetch_object()){
			$lang.= '<div class="col-md-2" style="height:50px;"><input class="form-control" value="'.$result->text.'"  data-langid="'.$result->lang_id.'" data-lid="'.$result->id.'" /></div>';
		}	
	}
	return $lang;
}


function getUserTotalPhotos($uid) {
	global $mysqli;
		
	$query = $mysqli->query("SELECT count(*) as total FROM users_photos where u_id = '".$uid."' and approved = 1");
	if($query->num_rows > 0){
		$total = $query->fetch_assoc();
	}else {
		$total['total'] = 0;	
	}
	return $total['total'];
}


function getUserTotalConv($u1,$u2) {
	global $mysqli;
		
	$query = $mysqli->query("SELECT count(*) as total FROM chat where r_id = '".$u1."' AND s_id = '".$u2."' OR r_id = '".$u2."' AND s_id = '".$u1."'");
	$total = $query->fetch_assoc();
	
	return $total['total'];
}

function getUserTodayConv($uid) {
	global $mysqli;
	
	$date = date('m/d/Y', time());
	$count = 0;
	$query = $mysqli->query("SELECT count FROM users_chat where uid = '".$uid."' and date = '".$date."' ");

	if($query->num_rows > 0){
		$total = $query->fetch_object();
		$count = $total->count;
	}
	return $count;
}

function getUserTotalPhotosPublic($uid) {
	global $mysqli;
		
	$query = $mysqli->query("SELECT count(*) as total FROM users_photos where u_id = '".$uid."' and blocked = 0 and approved = 1");
	if($query->num_rows > 0){
		$total = $query->fetch_assoc();
	}else {
		$total['total'] = 0;	
	}
	return $total['total'];
}

function getUserTotalPhotosPrivate($uid) {
	global $mysqli;
		
	$query = $mysqli->query("SELECT count(*) as total FROM users_photos where u_id = '".$uid."' and blocked = 1");
	$total = $query->fetch_assoc();
	
	return $total['total'];
}

function getUserTotalLikers($uid) {
	global $mysqli;
		
	$query = $mysqli->query("SELECT count(*) as total FROM users_likes where u2 = '".$uid."' and love = 1");
	$total = $query->fetch_assoc();
	
	return $total['total'];
}

function getUserTotalLikes($uid) {
	global $mysqli;
		
	$query = $mysqli->query("SELECT count(*) as total FROM users_likes where u1 = '".$uid."' and love = 1");
	$total = $query->fetch_assoc();
	
	return $total['total'];
}

function getNewMessages($uid1,$uid2) {
	global $mysqli;
	
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM chat where r_id = '".$uid1."' and s_id = '".$uid2."' and seen = 0");
	$total = $query->fetch_assoc();
	if($total['total'] >= 1){
		$result = 1;
	}
	return $result;
}

function checkConv($uid1,$uid2) {
	global $mysqli;
	
	$result = 0;
	$query = $mysqli->query("SELECT count(*) as total FROM chat where s_id = '".$uid1."' and r_id = '".$uid2."'");
	$total = $query->fetch_assoc();
	if($total['total'] >= 1){
		$result = 1;
	}
	return $result;
}

/* Other functions */
function smoothLink($query='') {
    global $sm;
	
    $query = $sm['config']['site_url'] . $query;
	
    return $query;
}

function secureEncode($string) {
    global $mysqli;
    $string = trim($string);
    $string = mysqli_real_escape_string($mysqli, $string);
    $string = htmlspecialchars($string, ENT_QUOTES);
    $string = str_replace('\\r\\n', '<br>',$string);
    $string = str_replace('\\r', '<br>',$string);
    $string = str_replace('\\n\\n', '<br>',$string);
    $string = str_replace('\\n', '<br>',$string);
    $string = str_replace('\\n', '<br>',$string);
    $string = stripslashes($string);
    $string = str_replace('&amp;#', '&#',$string);
    
    return $string;
}

function callInstagram($url){
	$ch = curl_init();
	curl_setopt_array($ch, array(
	CURLOPT_URL => $url,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_SSL_VERIFYPEER => false,
	CURLOPT_SSL_VERIFYHOST => 2
	));

	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}


function time_elapsed_string($ptime)
{
	global $sm;
    $etime = time() - $ptime;
	
    if ($etime < 1)
    {
        return $sm['lang'][306]['text'];
    }

    $a = array( 365 * 24 * 60 * 60  =>  $sm['lang'][307]['text'],
                 30 * 24 * 60 * 60  =>  $sm['lang'][308]['text'],
                      24 * 60 * 60  =>  $sm['lang'][309]['text'],
                           60 * 60  =>  $sm['lang'][310]['text'],
                                60  =>  $sm['lang'][311]['text'],
                                 1  =>  $sm['lang'][312]['text']
                );
    $a_plural = array( $sm['lang'][307]['text']   => $sm['lang'][313]['text'],
                       $sm['lang'][308]['text']  => $sm['lang'][314]['text'],
                       $sm['lang'][309]['text']    => $sm['lang'][315]['text'],
                       $sm['lang'][310]['text']   => $sm['lang'][316]['text'],
                       $sm['lang'][311]['text'] => $sm['lang'][317]['text'],
                       $sm['lang'][312]['text'] => $sm['lang'][318]['text']
                );

    foreach ($a as $secs => $str)
    {
        $d = $etime / $secs;
        if ($d >= 1)
        {
            $r = round($d);
            return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' '.$sm['lang'][319]['text'];
        }
    }
}

function clean($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

   return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

function prefered_language(array $available_languages, $http_accept_language) {

    $available_languages = array_flip($available_languages);

    $langs;
    preg_match_all('~([\w-]+)(?:[^,\d]+([\d.]+))?~', strtolower($http_accept_language), $matches, PREG_SET_ORDER);
    foreach($matches as $match) {

        list($a, $b) = explode('-', $match[1]) + array('', '');
        $value = isset($match[2]) ? (float) $match[2] : 1.0;

        if(isset($available_languages[$match[1]])) {
            $langs[$match[1]] = $value;
            continue;
        }

        if(isset($available_languages[$a])) {
            $langs[$a] = $value - 0.1;
        }

    }
    arsort($langs);

    return $langs;
}

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}