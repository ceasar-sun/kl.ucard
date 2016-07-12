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
require_once('lib.php');

$site = get_site();
$PAGE->set_pagelayout('standard');
if ($CFG->forcelogin) {
    require_login();
}
$context = context_system::instance();
require_capability('local/courselevel:change', $context);
global $CFG;
global $DB;

$PAGE->set_context($context); 
$PAGE->set_heading($site->fullname);
$PAGE->set_url(new moodle_url('/local/courselevel/index.php'));
$PAGE->set_title(get_string('courseleveltitle', 'local_courselevel')); 

echo $OUTPUT->header(); 
echo $OUTPUT->skip_link_target();
$table = "courselevel";
$courses = get_courses();
$rs = $DB->get_records($table);
$course_level_rs = array();
$num = 0;
foreach ($rs as $level_rs) {
    $course_level_rs[$num]['id'] = $level_rs->id;
    $course_level_rs[$num]['courseid'] = $level_rs->courseid;
    $course_level_rs[$num]['level'] = $level_rs->level;
    $course_level_rs[$num]['location'] = $level_rs->location;
    $num++;
}

$course_level_html="<table width=\"80%\"><tr><th>".get_string('location', 'local_courselevel')."->".get_string('track', 'local_courselevel')."</th><th>".get_string('course').get_string('name', 'local_courselevel')."</th><th>".get_string('level', 'local_courselevel')."</th><th>".get_string('edit', 'local_courselevel')."</th></tr>\n";
foreach ($courses as $course){
    $category = recursivecategorynamebyid($course->category);
    $location = getlocation($course->category);
    $curlocation = 0;
    //$category = categorynamebyid($course->category);
    $categoryid = $course->category;
    if ($categoryid == 0){
	continue;
    }
    $name = $course->fullname." - ".$course->shortname;
    $levelrecord = "";
    $level = -1;
    foreach ($course_level_rs as $record) {
	if ($record['courseid'] === $course->id){
	    $level = $record['level'];
	    $curlocation = $record['location'];
	    $curid = $record['id'];
	    break;
	}
    }
    if (($curlocation != $location) && ($level != -1)){
	$DB->update_record($table, array('id'=>$curid, 'location'=>$location));
    }
    if ($level == -1){
	$DB->insert_record($table,array('courseid'=>$course->id, 'level'=>0, 'location'=>$location));
	$level = 0;
    }
    $resu = get_courseid_by_level_location(10, 1);
    $editlevelurl = new moodle_url('/local/courselevel/edit.php', array('category'=>$categoryid));
    $levelrecord = "<tr><td>$category</td><td>$name</td><td>$level</td><td><a href=$editlevelurl>".get_string('change', 'local_courselevel')."</a></td></tr>\n";
    $course_level_html.=$levelrecord;
}
$course_level_html.="</table>\n";
// delete removed course level
foreach ($course_level_rs as $record_rs) {
    $course = $DB->get_record('course', array('id' => $record_rs['courseid']), '*');
    if (empty($course->id)){
	$DB->delete_records($table, array('courseid'=>$record_rs['courseid']));
    }
}

echo $OUTPUT->box($course_level_html);
echo $OUTPUT->footer();
