<?php

/* Course Status for teacher Block
 * The plugin shows the number and list of courses info.
 * @package blocks
 * @author: Jonathan Lin
 */

#require_once('../../config.php');
require_once(dirname(__FILE__) . '/../../config.php');
require_once('course_form.php');
require_once('lib.php');
#require_once($CFG->dirroot.'/blocks/course_status_4_teacher/lib.php');

$site = get_site();
require_login();
$context = context_system::instance();
require_capability('block/course_status_info:addinstance', $context);
#require_capability('block/course_status_info:view', $context);

global $DB, $OUTPUT, $PAGE, $CFG, $USER;
$viewpage = required_param('viewpage', PARAM_INT);
$inpgr_uid = required_param('param', PARAM_INT);
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string("pluginname", 'block_course_status_4_teacher'));
$PAGE->set_heading('Course Status');
$pageurl = '/blocks/course_status_4_teacher/view.php?viewpage=' . $viewpage;
$PAGE->set_url($pageurl);
$PAGE->navbar->ignore_active();
$PAGE->navbar->add(get_string("pluginname", 'block_course_status_4_teacher'));
echo $OUTPUT->header();



if ($viewpage == 1)
{
  /*
    $form = new course_status_form();
    $table = $form->display_report();
    if ($table)
    {
        echo "<div id='prints'>";
        // $title = '<center><table width="80%" style="background-color:#F3F3F3;"><tr><td><center><h2>' . get_string('report_coursecompletion', 'block_course_status_tracker') . '</h2></center></td></tr></tr><table></center>';
        $title = '<h2>' . get_string('report_coursecompletion', 'block_course_status_4_teacher') . '</h2>';
        $title.=user_details($USER->id);
        $a = html_writer::table($table);
        echo $title;
        echo $a;
        echo "</div>";
    }
    */

    echo "<div id='prints'>";
    $title = '<h2>' . get_string('report_all_students', 'block_course_status_4_teacher') . '</h2>';
    echo $title;

    $a = html_writer::table(all_students_report());
    echo $a;
    echo "</div>";

}
else if ($viewpage == 2)
{
    echo "<div id='prints'>";
    $title = '<h2>' . get_string('report_all_course', 'block_course_status_4_teacher') . '</h2>';
    echo $title;

    $a = html_writer::table(user_enrolled_courses_report());
    echo $a;
    echo "</div>";
}
else if ($viewpage == 3)
{
    echo "<div id='prints'>";
    $title = '<h2>' . get_string('report_inprogress_courses', 'block_course_status_4_teacher') . '</h2>';
    echo $title;
    echo html_writer::table(students_of_in_progress_courses_report());
    echo "</div>";
}
else if ($viewpage == 4)
{
    echo "<div id='prints'>";
    $title = '<h2>' . get_string('report_coursecompletion', 'block_course_status_4_teacher') . '</h2>';
    echo $title;
    echo html_writer::table(students_of_compl_courses_report());
    echo "</div>";
}
else if ($viewpage == 5)
{
    echo "<div id='prints'>";
    $title = '<h2>' . get_string('report_enrolled_course_students', 'block_course_status_4_teacher') . '</h2>';
    echo $title;
    echo html_writer::table(students_of_enrolled_courses_report());
    echo "</div>";
}
else if ($viewpage == 6)
{
    session_start();
    // $the_inprogress_uid = $_SESSION['$inprogression_uid'];

    echo "<div id='prints'>";
    $title = '<h2>' . get_string('report_inprogress_courses_list', 'block_course_status_4_teacher') . '</h2>';
    $title.=user_details($inpgr_uid);
    echo $title;

    $a = html_writer::table(inprogress_courses_list_report($inpgr_uid));
    echo $a;
    echo "</div>";
}
else
{
    header($CFG->wwwroot);
}

echo $OUTPUT->footer();
