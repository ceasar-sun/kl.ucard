<?php
$token = '851fc9fb3410e174ff156b65689f6922';
$server = 'http://moodle.nchc.org.tw';
$dir = '/moodle'; // May be null if moodle runs in the root directory in the server.
$search["key"]="id";
$search["value"]="4";
$params = array(array($search));
$params = array();
var_dump($params);
$request = xmlrpc_encode_request('core_course_get_categories', $params, array('encoding'=>'UTF-8'));

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
echo "ID\tname\tparentid\tdepth\tpath\n";
for ($i = 0; $i < count($response); $i++){
    $id=$response[$i]['id'];
    $name=$response[$i]['name'];
    $parent=$response[$i]['parent'];
    $depth=$response[$i]['depth'];
    $path=$response[$i]['path'];
    echo "$id\t$name\t$parent\t$depth\t$path\n";
}

?>
