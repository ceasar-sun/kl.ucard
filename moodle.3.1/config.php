<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mysqli';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'localhost';
$CFG->dbname    = 'moodle';
$CFG->dbuser    = 'moodleuser';
$CFG->dbpass    = 'moodlepasswd@ucard';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '',
  'dbsocket' => '',
);

#$CFG->upgradekey = 'upgrademe@nchc';

$CFG->wwwroot   = 'http://210.240.1.166/moodle';
$CFG->dataroot  = '/home/www/moodledata';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;

require_once(dirname(__FILE__) . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!

//@error_reporting(E_ALL | E_STRICT); // NOT FOR PRODUCTION SERVERS!
//@ini_set('display_errors', '0');    // NOT FOR PRODUCTION SERVERS!
//
//$CFG->debug = (E_ALL | E_STRICT);   // === DEBUG_DEVELOPER - NOT FOR PRODUCTION SERVERS!
//$CFG->debugdisplay = 0;             // NOT FOR PRODUCTION SERVERS!
//
//$CFG->debugusers = '2';

