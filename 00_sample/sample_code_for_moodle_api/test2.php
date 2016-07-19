<?php
$token = '851fc9fb3410e174ff156b65689f6922';
$server = 'http://moodle.nchc.org.tw';
$dir = '/moodle'; // May be null if moodle runs in the root directory in the server.

$user_sid='11223344';
// Initialize the class.
$search['key']="idnumber";
$search['value']=$user_sid;
$request = xmlrpc_encode_request('core_user_get_users', array(array($search)), array('encoding'=>'UTF-8'));

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
//$user = $response['users'];
//var_dump($user);
//$moodleid=$user[0]['id'];
//var_dump($moodleid);
$moodleid = $response['users'][0]['id'];
echo $moodleid;

?>
