<?php

/**
 * @Func:       Ucard plugin main useful function
 * @License:    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @Author:     Serena Pan
 * @Note:       First released in 2016/7/27              
 *
*/

defined('MOODLE_INTERNAL') || die;

function init_stat_book_nav($PAGE){
    global $CFG, $DB;
    $stat_book_navbar = $PAGE->navigation->add(get_string("STAT_BOOK_MENU",'block_stat_book'), new moodle_url('/blocks/stat_book/view.php'));
    //$ucard_navbar = $PAGE->navigation->add('UCARD', new moodle_url('/local/stat_book/index.php'));
    #$navbar_analysis = $stat_book_navbar->add(get_string("stat_booktitle",'block_stat_bookd') , new moodle_url('/blocks/stat_book/view.php'));
    //$navbar_courselevel = $ucard_navbar->add('courselevel' , new moodle_url('/local/stat_book/index.php'));
    //$navbar_courselevel_change = $navbar_courselevel->add('change' , null);
    //$navbar_cardlogs = $ucard_navbar->add(get_string("CARD_LOGS",'local_stat_bookd') , new moodle_url('/local/stat_book/card_logs.php'));
    //$navbar_student = $ucard_navbar->add(get_string("STUD_COURSE",'local_stat_bookd')  , new moodle_url('/local/stat_book/student_courses.php'));
    //$navbar_student = $ucard_navbar->add('student course' , new moodle_url('/local/stat_book/student_courses.php'));
    return $stat_book_navbar;
}

