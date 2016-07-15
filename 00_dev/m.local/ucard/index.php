<?php

/**
 * @Func:       課程階層管理介面
 * @License:    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @Author:     Thoomas Tsai , Ceasar Sun
 * @Note:                     
 *
*/

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir.'/tablelib.php');
require_once('lib.php');

$site = get_site();
$PAGE->set_pagelayout('standard');
if ($CFG->forcelogin) {
    require_login();
}
$context = context_system::instance();
require_capability('local/ucard:change', $context);
global $CFG;
global $DB;

$PAGE->set_context($context); 
$PAGE->set_heading($site->fullname);
$PAGE->set_url(new moodle_url('/local/ucard/index.php'));
$PAGE->set_title(get_string('courseleveltitle', 'local_ucard')); 

$navbar = init_ucard_nav($PAGE);
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

$table = new flexible_table('Course Level Setup');

$table->define_baseurl(new moodle_url("/local/ucard/index.php"));
$table->define_columns(array('location', 'track', 'coursename', 'level'));
$table->define_headers(array(
	    get_string('location', 'local_ucard')." -> ".get_string('track', 'local_ucard'),
	    get_string('course', 'local_ucard'),
	    get_string('level', 'local_ucard'),
	    get_string('change', 'local_ucard')
));
$table->sortable(true);
$table->setup();
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
    $editlevelurl = new moodle_url('/local/ucard/edit.php', array('category'=>$categoryid));
    $editlink = "<a href=\"$editlevelurl\" \"title=change\">".get_string('change', 'local_ucard')."</a>";
    $table->add_data(array($category, $name, $level, $editlink));
}
$table->print_html();
// delete removed course level
foreach ($course_level_rs as $record_rs) {
    $course = $DB->get_record('course', array('id' => $record_rs['courseid']), '*');
    if (empty($course->id)){
	$DB->delete_records($table, array('courseid'=>$record_rs['courseid']));
    }
}

echo $OUTPUT->footer();
