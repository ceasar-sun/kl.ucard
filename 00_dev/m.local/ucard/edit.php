<?php
/**
 * @Func:       取用 ucard 資料庫的全域設定
 * @License:    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @Author:     Thomas Tsai
 * @package   local_courselevel
 * @copyright 2016 Thomas Tsai and Free Software Labs
 * @Note:       First released in 2016/7/15              
 *
*/

require_once(dirname(__FILE__) . '/../../config.php');
require_once('edit_form.php');
require_once('lib.php');

$site = get_site();
if ($CFG->forcelogin) {
    require_login();
}
$context = context_system::instance();
require_capability('local/ucard:change', $context);
global $CFG;
global $DB;
//$DB->set_debug(true);

$PAGE->set_context($context); 
$PAGE->set_heading($site->fullname);
$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/local/ucard/edit.php'));
$PAGE->set_title(get_string('welcome', 'local_ucard')); 

$navbar = init_ucard_nav($PAGE);

echo $OUTPUT->header(); 
echo $OUTPUT->skip_link_target();
$categoryid = required_param('category', PARAM_INT);
$tosave = optional_param('save', 'no', PARAM_TEXT);
$table = 'courselevel';
$level_form = new courselevel_form(null, array('category'=>$categoryid));

if ($level_form->is_cancelled()) {
    $courselevelurl = new moodle_url('/local/ucard/index.php');
    redirect($courselevelurl);
} else if ($data = $level_form->get_data()) {
    $rs = $DB->get_recordset('courselevel');
    if ($tosave === 'yes'){

	foreach ($rs as $record) {
	    $recid = $record->id;
	    foreach ($data as $k=>$recd){
		if ($recid == $k){
		    $DB->update_record($table, array('id'=>$k, 'level'=>$recd));
		}
	    }
	}

	$courselevelurl = new moodle_url('/local/ucard/index.php');
	redirect($courselevelurl);
    }
    $rs->close();
} else {
    echo $level_form->is_validated();
    $pageparams = array('category' => $categoryid);
    $PAGE->set_url('/course/edit.php', $pageparams);
    $level_form->display();
}
echo $OUTPUT->footer();
?>
