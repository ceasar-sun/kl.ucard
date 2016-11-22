<?php

/**
 * @Func:       Ucard plugin main useful function
 * @License:    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @Author:     Serena Pan
 * @Note:       First released in 2016/7/27              
 *
*/

defined('MOODLE_INTERNAL') || die;

function init_stat_score_nav($PAGE){
    global $CFG, $DB;
    $stat_score_navbar = $PAGE->navigation->add(get_string("STAT_SCORE_MENU",'block_stat_score'), new moodle_url('/blocks/stat_score/view.php'));
    //$ucard_navbar = $PAGE->navigation->add('UCARD', new moodle_url('/local/stat_score/index.php'));
    #$navbar_analysis = $stat_score_navbar->add(get_string("stat_scoretitle",'block_stat_scored') , new moodle_url('/blocks/stat_score/view.php'));
    //$navbar_courselevel = $ucard_navbar->add('courselevel' , new moodle_url('/local/stat_score/index.php'));
    //$navbar_courselevel_change = $navbar_courselevel->add('change' , null);
    //$navbar_cardlogs = $ucard_navbar->add(get_string("CARD_LOGS",'local_stat_scored') , new moodle_url('/local/stat_score/card_logs.php'));
    //$navbar_student = $ucard_navbar->add(get_string("STUD_COURSE",'local_stat_scored')  , new moodle_url('/local/stat_score/student_courses.php'));
    //$navbar_student = $ucard_navbar->add('student course' , new moodle_url('/local/stat_score/student_courses.php'));
    return $stat_score_navbar;
}

