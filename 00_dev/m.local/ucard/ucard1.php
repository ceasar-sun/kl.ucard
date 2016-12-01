<?php
require_once("libucard.php");
require_once('ucard_config.php');
$debug = 1;
$db=$UCARD_CFG->dbname;
$username=$UCARD_CFG->dbuser;
$password=$UCARD_CFG->dbpass;

$token = $UCARD_CFG->token;
$server = $UCARD_CFG->server;
$dir = $UCARD_CFG->dir;

$ucard = new UCard($db, $username, $password);
$Jcourse=array();

$cardid = "";
$location = "";
$cardid = $_GET['rfid_key16'];
$location = $_GET['location'];
$debug = $_GET['debug'];
//$cardid = filter_var($cardid, FILTER_FLAG_ENCODE_HIGH);
//$location = filter_var($location, FILTER_SANITIZE_SPECIAL_CHARS);

if ($cardid === "" || $location === ""){
    exit;
}

$ucard->init_moodle($token, $server, $dir);
if ($debug == 1){
    $cardlogs = $ucard->listCardLogs();
}
$sid = $ucard->getStudentID($cardid); // for moodle idnumber
$rfid_keyout = $ucard->getRFIDKeyOut($cardid);
$level = $ucard->getStudentLevel($ucard->getID($cardid));
$status=0;
if ($sid != NULL){
    $status=1;
}

if ($level == NULL){
    if ($debug == 1){
	echo "sid:$sid, rfid_keyout:$rfid_keyout, level:$level\n";
    }
    $status=0;
}
if($status === 0 ){
    echo "{\"status\":\"$status\"}";
    exit(); 
}
$moodleuser = $ucard->getMoodleUserbyStudentID($sid);
$moodleid = $moodleuser['id'];
$ucard->logCardID($moodleid, $cardid, $location);
$courseids = array();
$usercourses = $ucard->getRunningCourse($moodleid, $location, false);
$userrunningcourses = $ucard->getRunningCourse($moodleid, $location, true);
if ((count($userrunningcourses) == 0) && (count($usercourses) == 0)){
    $levelcourseids = $ucard->getCoursesbyLevelLocation($level, $location);
    $courseids_a = array_merge($levelcourseids, $userrunningcourses);
    $courseids = array_unique($courseids_a);
}else{
    $courseids_a = array_merge($userrunningcourses, $usercourses);
    $courseids = array_unique($courseids_a);
}

if ($debug == 1){
    ?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>

	<body>
	<p>
	卡號：<?= $cardid ?> <br>
	學號：<?= $sid ?> <br>
	moodle帳號id：<?= $moodleid ?><br>
	學年：<?= $level ?><br>
	活動場館：<?= $location ?><br>
	<?= $courses_message_html ?>
	</p>
	<p>最新10筆場館打卡資訊</p>
	<table width="700" border="1">
	<tr>
	<td>id</td>
	<td>rfid_key16</td>
	<td>location</td>
	<td>timestamp</td>
	</tr>
	<?php
	for($i=0;$i<count($cardlogs);$i++){
	    ?>
		<tr>
		<td><?= $cardlogs[$i]['id'] ?></td>
		<td><?= $cardlogs[$i]['rfid_key16'] ?></td>
		<td><?= $cardlogs[$i]['location'] ?></td>
		<td><?= $cardlogs[$i]['dtime'] ?></td>
		</tr>
		<?php
	}
    ?>
	</table>
	<p>課程相關資訊</p>
	<?php
} // end of if debug
foreach($courseids as $courseid){
    $courseStatus=$ucard->getCompletionStatus($courseid, $moodleid);
    $courseName = $ucard->getNameofCourse($courseid);
    if($debug==1){echo "running course ".$courseName." [$courseid]";}
    if($debug==1){echo " and level: ".$ucard->getLevelbyCourse($courseid)."\n";}
    if($debug==1){echo " ";}
    if ($courseStatus === TRUE){
        $Jcourse[$courseName]="YES";
	if($debug==1){echo "completion: TRUE\n";}
	$newcourseid = $ucard->upgradeCourse($moodleid, $courseid, $location);
	if(empty($newcourseid)){
	    // release all course for this track
	    $track = $ucard->getTrackbyCourse($courseid);
	    $lastcourses = $ucard->getLastCourse($level, $track);
	    foreach ($lastcourses as $cid){
		$lastcourseid = $cid['id'];
		$lastcoursestatus = $ucard->getCompletionStatus($lastcourseid, $moodleid);
		if ($lastcoursestatus === False){
		    $lastcourselevel = $ucard->getLevelbyCourse($lastcourseid);
		    $ucard->registCourse($moodleid, $lastcourseid);
		    $ucard->logRunningCourse($moodleid, $rfid_keyout, $location, $lastcourseid, $lastcourselevel);
		    $lcname = $ucard->getNameofCourse($lastcourseid);
		    if($debug==1){echo "\t\tlast course $lcname completion: FALSE\n";}
		    $JUPcourse[$lcame]="UP";
		}
	    }
	}else{
	    $courselevel = $ucard->getLevelbyCourse($newcourseid);
	    $ucard->logRunningCourse($moodleid, $rfid_keyout, $location, $newcourseid, $courselevel);
	    $newcoursename = $ucard->getNameofCourse($newcourseid);
	    $JUPcourse[$newcoursename]="UP";
	}
	$ucard->upgradeRunningCourse($moodleid, $courseid);
	if($debug==1){echo "upgrade done\n";}
    } else if ($courseStatus === FALSE) {
	if($debug==1){echo "completion: FALSE\n";}
        $Jcourse[$courseName]="NO";
	// nice
    } else {
	if($debug==1){echo "completion: no data/ error\n";}
	// regist course
	if($debug==1){echo "regist $moodleid, $courseid";}
	$ucard->registCourse($moodleid, $courseid);
	$courselevel = $ucard->getLevelbyCourse($courseid);
	$ucard->logRunningCourse($moodleid, $rfid_keyout, $location, $courseid, $courselevel);
    }
    if($debug==1){echo "<br>\n";}

}

foreach ($JUPcourse as $name => $value){
    if (array_key_exists($name, $Jcourse)){
	    if ($Jcourse[$name] == "NO"){
		$Jcourse[$name] = "NO";
	    }
	}else{
	    $Jcourse[$name] = "NO";
	}
}

if($debug==1){echo "<p>JSON:</p>";}
if($debug==1){echo "<pre>";}
if ($status === 1){
    echo "{\"status\":\"$status\",\"result\":{\"moodleid\":\"$moodleid\",\"sid\":\"$sid\",\"rfid_keyout\":\"$rfid_keyout\",\"name\":\"$moodleuser[fullname]\"},\"courses\":".json_encode($Jcourse)."}";
} else {
    echo "{\"status\":\"$status\"}";
}
if($debug==1){echo "</pre>";}
if($debug==1){echo "</body>";}
if($debug==1){echo "</html>";}
?>
