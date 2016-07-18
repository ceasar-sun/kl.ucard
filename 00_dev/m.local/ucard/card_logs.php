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
$context = context_system::instance();
require_capability('local/ucard:view', $context);
global $CFG;
global $UCARD_CFG;
global $DB;

$PAGE->set_context($context); 
$PAGE->set_heading($site->fullname);
$PAGE->set_url(new moodle_url('/local/ucard/card_logs.php'));
$PAGE->set_title(get_string('courseleveltitle', 'local_ucard')); 

$navbar = init_ucard_nav($PAGE);
echo $OUTPUT->header(); 
echo $OUTPUT->skip_link_target();

## your content /HTML here

$db = $UCARD_CFG->dbname;
$username = $UCARD_CFG->dbuser;
$password = $UCARD_CFG->dbpass;
$querylimit = 20;

$ucard = new UCard($db, $username, $password);
$cardlogs = $ucard->listCardLogs($querylimit);
$logcount = count($cardlogs);

echo $OUTPUT->box("<p>最新 $querylimit 筆場館打卡資訊</p>\n");

$table = new flexible_table('Card Logs');
$table->define_baseurl(new moodle_url("/local/ucard/card_logs.php"));
$table->define_columns(array("id", "cid", "location", "timestamp"));
$table->define_headers(array("id", "cid", "location", "timestamp"));
$table->sortable(true);
$table->setup();
for($i=0;$i<count($cardlogs);$i++){
    $table->add_data(array($cardlogs[$i]['id'], $cardlogs[$i]['cid'], $cardlogs[$i]['location'], $cardlogs[$i]['dtime']));
}
$table->print_html();

echo $OUTPUT->box("<p>全部$logcount 筆場館打卡資訊</p>\n");


## end of your content /HTML
echo $OUTPUT->footer();
