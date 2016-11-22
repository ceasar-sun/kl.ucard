<?php

/**
 * @Func:       Ucard plugin main useful function
 * @License:    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @Author:     Serena Pan
 * @Note:       First released in 2016/7/27              
 *
*/

defined('MOODLE_INTERNAL') || die;

function init_stat_stu_nav($PAGE){
    global $CFG, $DB;
    $stat_stu_navbar = $PAGE->navigation->add(get_string("STAT_STU_MENU",'block_stat_stu'), new moodle_url('/blocks/stat_stu/view.php'));
    //$ucard_navbar = $PAGE->navigation->add('UCARD', new moodle_url('/local/stat_stu/index.php'));
    #$navbar_analysis = $stat_stu_navbar->add(get_string("stat_stutitle",'block_stat_stud') , new moodle_url('/blocks/stat_stu/view.php'));
    //$navbar_courselevel = $ucard_navbar->add('courselevel' , new moodle_url('/local/stat_stu/index.php'));
    //$navbar_courselevel_change = $navbar_courselevel->add('change' , null);
    //$navbar_cardlogs = $ucard_navbar->add(get_string("CARD_LOGS",'local_stat_stud') , new moodle_url('/local/stat_stu/card_logs.php'));
    //$navbar_student = $ucard_navbar->add(get_string("STUD_COURSE",'local_stat_stud')  , new moodle_url('/local/stat_stu/student_courses.php'));
    //$navbar_student = $ucard_navbar->add('student course' , new moodle_url('/local/stat_stu/student_courses.php'));
    return $stat_stu_navbar;
}

