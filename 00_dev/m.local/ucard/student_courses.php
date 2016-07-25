<?php

/**
 * @Func:       顯示學生修課完成狀態
 * @License:    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @Author:     Thomas Tsai, Ceasar Sun 
 * @Note:
 *
*/

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir.'/tablelib.php');
require_once('student_form.php');
require_once('ucard_config.php');
require_once('lib.php');
require_once("libucard.php");

// functions
$db = $UCARD_CFG->dbname;
$username = $UCARD_CFG->dbuser;
$password = $UCARD_CFG->dbpass;

$token = $UCARD_CFG->token;		// '851fc9fb3410e174ff156b65689f6922';
$server = $UCARD_CFG->server; 	//'http://moodle.nchc.org.tw';
$dir = $UCARD_CFG->dir;		//'/moodle';

$ucard = new UCard($db, $username, $password);
$ucard->init_moodle($token, $server, $dir);
function list_student_courses($moodleid){
    global $ucard;
    $usercourses = $ucard->getUserCourses($moodleid);

    $html = "<p></p>\n";

    $table = new flexible_table('Student Courses');
    $table->define_baseurl(new moodle_url("/local/ucard/student_courses.php"));
    $table->define_columns(array("location", "coursename", "level", "status"));
    $table->define_headers(array("Loction", "Course Name", "Level", "Status"));
    $table->sortable(true);
    $table->setup();
    foreach($usercourses as $courseid){
	$courseStatus=$ucard->getCompletionStatus($courseid, $moodleid);
	if ($courseStatus === TRUE){
	    $coursestatus = "completed";
	} else if ($courseStatus === FALSE) {
	    $coursestatus = "in progress";
	} else {
	    $coursestatus = "error/never regist?";
	}

	$coursename_url = new moodle_url('/course/view.php', array('id'=>$courseid));
	$coursename_html = "<a href=\"$coursename_url\">".$ucard->getNameofCourse($courseid)."</a>";
	$data = array(getlocationtrackbycourse($courseid),
		      $coursename_html,
		      $ucard->getLevelbyCourse($courseid),
		      $coursestatus
		      );
	$table->add_data($data);

    }
    $html .= $table->print_html();
    return $html;
}

$site = get_site();
if ($CFG->forcelogin) {
    require_login();
}
$context = context_system::instance();
require_capability('local/ucard:viewlog', $context);
global $CFG;
global $DB;
//$DB->set_debug(true);

$PAGE->set_context($context); 
$PAGE->set_heading($site->fullname);
$PAGE->set_pagelayout('standard');
$PAGE->set_url(new moodle_url('/local/ucard/student_courses.php'));
$PAGE->set_title(get_string("welcome", 'local_ucard')); 

$navbar = init_ucard_nav($PAGE);

echo $OUTPUT->header(); 
echo $OUTPUT->skip_link_target();
if(has_capability('local/ucard:view', $context)){
    $moodleid = optional_param('moodleid', 0, PARAM_INT);
}else{
    $moodleid = $USER->id;
}
$s_form = new student_form(null);
$user = $DB->get_record('user', array('id'=>$moodleid));
echo $OUTPUT->box("User Name:".fullname($user));
if ($s_form->is_cancelled()) {
    $courselevelurl = new moodle_url('/local/ucard/student_courses.php');
    redirect($courselevelurl);
} else if ($moodleid != null) {
    $list_course_html = list_student_courses($moodleid);
    echo $OUTPUT->box($list_course_html);
} else if ($data = $s_form->get_data()) {
    $moodleid = $data->moodleid;
    $list_course_html = list_student_courses($moodleid);
    echo $OUTPUT->box($list_course_html);
} else {
    echo $s_form->is_validated();
    $PAGE->set_url('/course/student_courses.php');
    $s_form->display();
}
echo $OUTPUT->footer();
?>
