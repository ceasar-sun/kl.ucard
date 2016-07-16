<?php
include_once("libucard.php");
$debug = 1;
$db="ucard";
$username="root";
$password="okok7480";

$token = '851fc9fb3410e174ff156b65689f6922';
$server = 'http://moodle.nchc.org.tw';
$dir = '/moodle';


$ucard = new UCard($db, $username, $password);

$cardid = "";
$location = "";
$cardid = $_GET['cid'];
$location = $_GET['location'];
$debug = $_GET['debug'];
//$cardid = filter_var($cardid, FILTER_FLAG_ENCODE_HIGH);
//$location = filter_var($location, FILTER_SANITIZE_SPECIAL_CHARS);

if ($cardid === "" || $location === ""){
    exit;
}

$ucard->init_moodle($token, $server, $dir);
$ucard->logCardID($cardid, $location);
if ($debug == 1){
    $cardlogs = $ucard->listCardLogs();
}
$sid = $ucard->getStudentID($cardid);
$status=0;
if ($sid != NULL){
    $status=1;
}
if($status === 0 ){
    echo "{\"status\":\"$status\"}";
    exit(); 
}
$level = $ucard->getStudentLevel($sid, $location);
$moodleuser = $ucard->getMoodleUserbyStudentID($sid);
$moodleid = $moodleuser['id'];
$levelcourseids = $ucard->getCoursesbyLevelLocation($level, $location);
$usercourses = $ucard->getUserCourses($moodleid);
$courseids_a = array_merge($levelcourseids, $usercourses);
$courseids = array_unique($courseids_a);
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
    <td>cid</td>
    <td>location</td>
    <td>timestamp</td>
  </tr>
<?php
for($i=0;$i<count($cardlogs);$i++){
?>
  <tr>
    <td><?= $cardlogs[$i]['id'] ?></td>
    <td><?= $cardlogs[$i]['cid'] ?></td>
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
    if($debug==1){echo "running course ".$ucard->getNameofCourse($courseid)." [$courseid]";}
    if($debug==1){echo " and level: ".$ucard->getLevelbyCourse($courseid)."\n";}
    if($debug==1){echo " ";}
    if ($courseStatus === TRUE){
	if($debug==1){echo "completion: TRUE\n";}
	$ucard->upgradeCourse($moodleid, $courseid, $location);
	if($debug==1){echo "upgrade done\n";}
    } else if ($courseStatus === FALSE) {
	if($debug==1){echo "completion: FALSE\n";}
	// nice
    } else {
	if($debug==1){echo "completion: no data/ error\n";}
	// regist course
	if($debug==1){echo "regist $moodleid, $courseid";}
	$ucard->registCourse($moodleid, $courseid);
    }
    if($debug==1){echo "<br>\n";}

}


if($debug==1){echo "<p>JSON:</p>";}
if($debug==1){echo "<pre>";}
if ($status === 1){
    echo "{\"status\":\"$status\",\"result\":{\"sid\":\"$sid\",\"cid\":\"XXX\",\"name\":\"$moodleuser[fullname]\"}}";
} else {
    echo "{\"status\":\"$status\"}";
}
if($debug==1){echo "</pre>";}
if($debug==1){echo "</body>";}
if($debug==1){echo "</html>";}
?>
