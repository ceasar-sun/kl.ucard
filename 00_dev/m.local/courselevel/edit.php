<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   local_courselevel
 * @copyright 2016 Thomas Tsai and Free Software Labs
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once('edit_form.php');
require_once('lib.php');

$site = get_site();
if ($CFG->forcelogin) {
    require_login();
}
$context = context_system::instance();
require_capability('local/courselevel:change', $context);
global $CFG;
global $DB;
//$DB->set_debug(true);

$PAGE->set_context($context); 
$PAGE->set_heading($site->fullname);
$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/local/courselevel/edit.php'));
$PAGE->set_title(get_string('welcome', 'local_courselevel')); 

$navbar = init_ucard_nav($PAGE);

echo $OUTPUT->header(); 
echo $OUTPUT->skip_link_target();
$categoryid = required_param('category', PARAM_INT);
$tosave = optional_param('save', 'no', PARAM_TEXT);
$table = 'courselevel';
$level_form = new courselevel_form(null, array('category'=>$categoryid));

if ($level_form->is_cancelled()) {
    $courselevelurl = new moodle_url('/local/courselevel/index.php');
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

	$courselevelurl = new moodle_url('/local/courselevel/index.php');
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
