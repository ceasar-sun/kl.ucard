<?php

/**
 * @Func:       顯示學生修課完成狀態
 * @License:    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @Author:     Thomas Tsai, Ceasar Sun 
 * @Note:
 *
*/

require_once(dirname(__FILE__) . '/../../config.php');
require_once('student_form.php');
require_once('ucard_config.php');
require_once('lib.php');
require_once("libucard.php");

// functions

function list_cid_courses($cid, $location){
    $db = $UCARD_CFG->dbname;
    $username = $UCARD_CFG->dbuser;
    $password = $UCARD_CFG->dbpass;

    $token = $UCARD_CFG->token;		// '851fc9fb3410e174ff156b65689f6922';
    $server = $UCARD_CFG->server; 	//'http://moodle.nchc.org.tw';
    $dir = $UCARD_CFG->dir;		//'/moodle';

    $ucard = new UCard($db, $username, $password);
    $ucard->init_moodle($token, $server, $dir);
    $sid = $ucard->getStudentID($cid);
    $level = $ucard->getStudentLevel($sid, $location);
    $levelcourseids = $ucard->getCoursesbyLevelLocation($level, $location);
    $moodleid = $ucard->getMoodleIDbyStudentID($sid);
    $usercourses = $ucard->getUserCourses($moodleid);
    $courseids = array_merge($levelcourseids, $usercourses);
    $courseids = array_unique($courseids);
    $html = "<p>";
    $html .= "卡號：$cid<br>\n";
    $html .= "學號：$sid<br>\n";
    $html .= "moodle帳號id：$moodleid<br>\n";
    $html .= "學年：$level<br>\n";
    $html .= "活動場館：$location<br>\n";
    $html .= "</p>\n";

    $html .= "<p>課程相關資訊</p>\n";

    foreach($courseids as $courseid){
	$courseStatus=$ucard->getCompletionStatus($courseid, $moodleid);
	$html .= "running course ".$ucard->getNameofCourse($courseid)." [$courseid]";
	$html .= " and level: ".$ucard->getLevelbyCourse($courseid)."\n";
	$html .= " ";
	if ($courseStatus === TRUE){
	    $html .= "completion: TRUE\n";
	    $ucard->upgradeCourse($moodleid, $courseid, $location);
	    $html .= "upgrade done\n";
	} else if ($courseStatus === FALSE) {
	    $html .= "completion: FALSE\n";
	    // nice
	} else {
	    $html .= "completion: no data/ error\n";
	    // regist course
	    $html .= "regist $moodleid, $courseid";
	    $ucard->registCourse($moodleid, $courseid);
	}
	$html .= "<br>\n";

    }
    return $html;
}

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
$PAGE->set_url(new moodle_url('/local/ucard/student_courses.php'));
$PAGE->set_title(get_string("welcome", 'local_ucard')); 

$navbar = init_ucard_nav($PAGE);

echo $OUTPUT->header(); 
echo $OUTPUT->skip_link_target();
$cid = optional_param('cid', 0, PARAM_INT);
$location = optional_param('location', 0, PARAM_INT);
$s_form = new student_form(null);

if ($s_form->is_cancelled()) {
    $courselevelurl = new moodle_url('/local/ucard/student_courses.php');
    redirect($courselevelurl);
} else if ($data = $s_form->get_data()) {
    $cid = $data->cid;
    $location = $data->location;
    $list_course_html = list_cid_courses($cid, $location);
    echo $OUTPUT->box($list_course_html);
} else {
    echo $s_form->is_validated();
    $PAGE->set_url('/course/student_courses.php');
    $s_form->display();
}
echo $OUTPUT->footer();
?>
