<?php
$token = '851fc9fb3410e174ff156b65689f6922';
$server = 'http://moodle.nchc.org.tw';
$dir = '/moodle'; // May be null if moodle runs in the root directory in the server.
$params = array(array('ids'=>array('5')));
$params = array();
$request = xmlrpc_encode_request('core_course_get_courses', $params, array('encoding'=>'UTF-8'));

var_dump($request);  // In case you want to see XML.

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
var_dump($response);
$course_res=$response;
$course['categoryid']=array();
$categories['id']=array();
echo "ID\tshortname\tcategoryid\tidnumber\n";
for ($i = 0; $i < count($response); $i++){
    $id=$response[$i]['id'];
    $shortname=$response[$i]['shortname'];
    $categoryid=$response[$i]['categoryid'];
    $idnumber=$response[$i]['idnumber'];
    $course[$id]=array($id, $shortname, $categoryid, $idnumber);
    if (array_key_exists($categoryid, $categories) == false){ $categories[$categoryid]=array(); }
    array_push($categories[$categoryid], $id);
    //echo "$id\t$shortname\t$categoryid\t$idnumber\n";
}

//var_dump($course);
var_dump($categories);

$search["key"]="id";
$search["value"]="4";
$params = array(array($search));
$params = array();
var_dump($params);
$request = xmlrpc_encode_request('core_course_get_categories', $params, array('encoding'=>'UTF-8'));

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
$cate_res=$response;
echo "ID\tname\tparentid\tdepth\tpath\n";
for ($i = 0; $i < count($response); $i++){
    $id=$response[$i]['id'];
    $name=$response[$i]['name'];
    $parent=$response[$i]['parent'];
    $depth=$response[$i]['depth'];
    $path=$response[$i]['path'];

    echo "$id\t$name\t$parent\t$depth\t$path\n";
    //var_dump($categories[$id]);
    if (array_key_exists($id, $categories) == false){ continue; }
    if (!isset($categories[$id]) && $categories[$id]==null){ continue; }
    if (count($categories[$id]) == 0){ continue; }
    echo "\tid\tshortname\tcategoryid\tidnumber\n";
    foreach ($categories[$id] as $cid){
        echo "\t";
	echo $course[$cid][0]."\t";
	echo $course[$cid][1]."\t";
	echo $course[$cid][2]."\t";
	echo $course[$cid][3]."\t";
        echo "\n"; 
    }
echo "\n";
}



?>
