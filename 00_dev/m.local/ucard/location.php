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

$location = "-1";
$debug = $_GET['debug'];
$location = $_GET['location'];
if ($location === ""){
    exit;
}

if ($debug == 1){ echo $location;}
$ucard->init_moodle($token, $server, $dir);
echo $ucard->getLocationID($location);

?>
