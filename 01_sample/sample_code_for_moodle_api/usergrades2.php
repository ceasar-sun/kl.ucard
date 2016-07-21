<?php
$token = '851fc9fb3410e174ff156b65689f6922';
$server = 'http://moodle.nchc.org.tw';
$dir = '/moodle'; // May be null if moodle runs in the root directory in the server.
$params = array(7, 6);
//$params = array(5, 5);
var_dump($params);
$request = xmlrpc_encode_request('gradereport_user_get_grades_table', $params, array('encoding'=>'UTF-8'));

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
//var_dump($response);
$tables = $response['tables'];
//var_dump($tables);
$data = $tables[0]['tabledata'];
$total = array_pop($data);
var_dump($total);
echo "user grade: ".$total['grade']['content'];
?>
