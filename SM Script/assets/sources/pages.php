<?php
if($mobile == true){
	$sm['content'] = getMobilePage($folder.'/'.$page);
} else {
	$sm['content'] = getPage($folder.'/'.$page);
}



