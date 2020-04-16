<?php
error_reporting(0);
set_time_limit(0);
require_once('color.php');

function friendlist($token){
	$a = json_decode(file_get_contents('https://graph.facebook.com/me/friends?access_token='.$token), true);
	return $a['data'];
}

function last_active($id, $tok){
	$a = json_decode(file_get_contents('https://graph.facebook.com/'.$id.'/feed?access_token='.$tok.'&limit=1'), true);
	$date = $a['data'][0]['created_time'];
	$aa = strtotime($date);
	return date('Y', $aa);
}

function simpan($text){
	$myfile = fopen("inactive_id.txt", "a+") or die("Unable to open file!");
	fwrite($myfile, $text."|");
	fclose($myfile);
}

echo Console::blue("     Facebook Auto Unfriend\n");
echo Console::blue("        Inactive Users\n\n");

//INPUT
echo "Facebook token : ";
$fbtoken = trim(fgets(STDIN));
echo "Year : ";
$year = trim(fgets(STDIN));
echo "\n";

$count = 0;
$delay = 10;
$FL = friendlist($fbtoken);
$totalFL = count($FL);
foreach($FL as $list){
	if($delay == 0){
		echo Console::yellow('=== Berhenti Sejenak ===')."\n";
		sleep(10);
		$delay = 10;
	} else {
		$name = $list['name'];
		$id = $list['id'];
		$date = last_active($id, $fbtoken);
		if($date != "1970"){
			$count++;
			echo Console::cyan("(" .$count. "/" .$totalFL. ")");
			if($date < $year){
				echo Console::red('[INACTIVE]').' '.$name.' ~ '.$date;//.' '.unfriend($id, $fbtoken);
				echo "\r\n";
				simpan($id);
			}else{
				echo Console::green('[ACTIVE]').' '.$name.' ~ '.$date;
				echo "\r\n";
			}
			$delay--;
		}
	}
}
