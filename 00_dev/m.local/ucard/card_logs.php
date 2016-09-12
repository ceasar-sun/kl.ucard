<?php

/**
 * @Func:       顯示打卡記錄
 * @License:    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @Author:     Thomas Tsai, Ceasar Sun 
 * @Note:       First released in 2016/7/15              
 *
*/

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir.'/tablelib.php');
require_once('ucard_config.php');
require_once('lib.php');
require_once('libucard.php');

$site = get_site();
$PAGE->set_pagelayout('standard');
if ($CFG->forcelogin) {
    require_login();
}
$isUcardTeacher = 0;
$context = context_system::instance();
require_capability('local/ucard:viewlog', $context);
if(is_siteadmin() == true){
//if(has_capability('local/ucard:view', $context)){
    $isUcardTeacher = 1;
}

global $CFG;
global $UCARD_CFG;
global $DB;

$PAGE->set_context($context); 
$PAGE->set_heading($site->fullname);
$PAGE->set_url(new moodle_url('/local/ucard/card_logs.php'));
$PAGE->set_title(get_string('courseleveltitle', 'local_ucard')); 

$token = $UCARD_CFG->token;		// '851fc9fb3410e174ff156b65689f6922';
$server = $UCARD_CFG->server; 	//'http://moodle.nchc.org.tw';
$dir = $UCARD_CFG->dir;		//'/moodle';


$navbar = init_ucard_nav($PAGE);
echo $OUTPUT->header(); 
echo $OUTPUT->skip_link_target();

## your content /HTML here

//echo $USER->id;
//$cid=3;
//$cContext =context_course::instance($cid);
//$isStudent = current(get_user_roles($cContext, $USER->id))->shortname=='student'? true : false; // instead of shortname you can also use roleid
//var_dump($isStudent);


$db = $UCARD_CFG->dbname;
$username = $UCARD_CFG->dbuser;
$password = $UCARD_CFG->dbpass;
$querylimit = 20;

$ucard = new UCard($db, $username, $password);
$ucard->init_moodle($token, $server, $dir);
if($isUcardTeacher == 1){
    $cardlogs = $ucard->listCardLogs($querylimit);
}else{
    $cardlogs = $ucard->listUserCardLogs($USER->id, $querylimit);
}
$logcount = count($cardlogs);

echo $OUTPUT->box("<p>最新 $querylimit 筆場館打卡資訊</p>\n");

$table = new flexible_table('Card Logs');
$table->define_baseurl(new moodle_url("/local/ucard/card_logs.php"));
$table->define_columns(array("id", "rfid_keyout", "location", "timestamp"));
$table->define_headers(array
		    (get_string("id", 'local_ucard'),
		    get_string("name-rfid", 'local_ucard'),
		    get_string("location", 'local_ucard'),
		    get_string("timestamp", 'local_ucard')));
$table->sortable(true);
$table->setup();
for($i=0;$i<count($cardlogs);$i++){
    $sid = $ucard->getStudentID($cardlogs[$i]['rfid_key16']); // for moodle idnumber
    $rfid_keyout = $ucard->getRFIDKeyOut($cardlogs[$i]['rfid_key16']);
    $moodleuser = $ucard->getMoodleUserbyStudentID($sid);
    $userlink = new moodle_url('/local/ucard/student_courses.php', array('moodleid'=>$cardlogs[$i]['moodleid']));
    $user_course_link = "<a href=\"$userlink\">".$moodleuser['fullname']." ($rfid_keyout)</a>";


    $table->add_data(array($cardlogs[$i]['id'], $user_course_link, categorynamebyid($cardlogs[$i]['location']), $cardlogs[$i]['dtime']));
}
$table->print_html();

echo $OUTPUT->box("<p>全部$logcount 筆場館打卡資訊</p>\n");
## end of your content /HTML
echo $OUTPUT->footer();
