<?php
$cid= $_GET['cid'];
$location=$_GET['location'];
mysql_connect("localhost","root","okok7480");
mysql_select_db("cardlog");

# get all log
$data=mysql_query("select * from ucard.cardlog");

# insert cid
if ($cid != ""){
echo $cid;
$datai=mysql_query("INSERT INTO `ucard`.`cardlog` (`id`, `cid`, `location`, `dtime`) VALUES (NULL, $cid, $location, CURRENT_TIMESTAMP)");

# check student id
$data_sid = mysql_query("select * from ucard.student where cid=$cid");
if ($data_sid != null){
    $sid_ar = mysql_fetch_row($data_sid);
}else{
    $sid="no data";
}
$sid = $sid_ar[2];
}

# get moodle uid
$token = '851fc9fb3410e174ff156b65689f6922';
$server = 'http://moodle.nchc.org.tw';
$dir = '/moodle';
$search['key']="idnumber";
$search['value']=$sid;
$request = xmlrpc_encode_request('core_user_get_users', array(array($search)), array('encoding'=>'UTF-8'));
$context = stream_context_create(array('http' => array(
				'method' => "POST",
				'header' => "Content-Type: text/xml",
				'content' => $request
				)));

$path = $server.$dir."/webservice/xmlrpc/server.php?wstoken=".$token;
$file = file_get_contents($path, false, $context); // $file is the reply from server.
$response = xmlrpc_decode($file);
$moodleid = $response['users'][0]['id'];
echo $moodleid;


# get student level
$location_a = '1';
$location_b = '2';
$course_a_tbl = 'ucard.student_level_a';
$course_b_tbl = 'ucard.student_level_b';
if ($location == $location_a){
    $level_tbl = $course_a_tbl;
    $courseid='a';
} else if ($location == $location_b){
    $level_tbl = $course_b_tbl;
    $courseid='b';
}

$slevel = mysql_query("select * from $level_tbl where sid = $sid");
$ldata = mysql_fetch_row($slevel);
$level = $ldata[1];

# get course id
$course_id_data = mysql_query("select * from ucard.course_level where level=$level and class=$location");
$course_data = mysql_fetch_row($course_id_data);
$courseid = $course_data[1];


# enroll course
//$courseids = array( $courseid );
//$request = xmlrpc_encode_request('core_course_get_courses', array(array('ids'=>$courseids)), array('encoding'=>'UTF-8'));
//
//$context = stream_context_create(array('http' => array(
//				'method' => "POST",
//				'header' => "Content-Type: text/xml",
//				'content' => $request
//				)));
//
//$path = $server.$dir."/webservice/xmlrpc/server.php?wstoken=".$token;
//// Send XML to server and get a reply from it.
//$file = file_get_contents($path, false, $context); // $file is the reply from server.
//// Decode the reply.
//$response = xmlrpc_decode($file);
//
//// This is our normal exit (returning an array of user properties).
//var_dump($response);

$params = array(array(array('roleid'=>'5', 'userid'=>$moodleid, 'courseid'=>$courseid))); // roleid 5 is "student".

$request = xmlrpc_encode_request('enrol_manual_enrol_users', $params, array('encoding'=>'UTF-8'));

//var_dump($request);  // In case you want to see XML.

$context = stream_context_create(array('http' => array(
				'method' => "POST",
				'header' => "Content-Type: text/xml",
				'content' => $request
				)));

$path = $server.$dir."/webservice/xmlrpc/server.php?wstoken=".$token;
// Send XML to server and get a reply from it.
$file = file_get_contents($path, false, $context); // $file is the reply from server.
// Decode the reply.
$response = xmlrpc_decode($file);

// This is our normal exit (returning an array of user properties).
//var_dump($response);





?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<body>
<p>
add log cid=<?php echo $cid; ?> <br>
and check data<br>
sid = <?php echo $sid; ?> <br>
moodleid = <?php echo $moodleid;?><br>
level = <?php echo $level;?><br>
location = <?php echo $location; ?><br>
courseid = <?php echo $courseid; ?><br>
<a href="http://moodle.nchc.org.tw/moodle/user/index.php?id=<?php echo $courseid;?>">check</a>
</p>
<table width="700" border="1">
  <tr>
    <td>id</td>
    <td>cid</td>
    <td>location</td>
    <td>timestamp</td>
  </tr>
<?php
for($i=1;$i<=mysql_num_rows($data);$i++){
$rs=mysql_fetch_row($data);
?>
  <tr>
    <td><?php echo $rs[0]; ?></td>
    <td><?php echo $rs[1]; ?></td>
    <td><?php echo $rs[2]; ?></td>
    <td><?php echo $rs[3]; ?></td>
  </tr>
<?php
}
?>
</table>
</body>
</html>
