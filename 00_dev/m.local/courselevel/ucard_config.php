<?php  // Moodle configuration file

unset($UCARD_CFG);
global $UCARD_CFG;
$UCARD_CFG = new stdClass();

$UCARD_CFG->dbhost    = 'localhost';
$UCARD_CFG->dbname    = 'ucard.test';
$UCARD_CFG->dbuser    = 'ucarduser';
$UCARD_CFG->dbpass    = 'ucardpasswd@mysql';

$UCARD_CFG->token = '';
$UCARD_CFG->server = 'http://210.240.1.166/';
$UCARD_CFG->dir = '/moodle.test';

