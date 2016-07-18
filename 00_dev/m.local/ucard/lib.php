<?php

/**
 * @Func:       Ucard plugin main useful function
 * @License:    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @Author:     Ceasar Sun, Thomas Tsai 
 * @Note:       First released in 2016/7/15              
 *
*/

defined('MOODLE_INTERNAL') || die;

function init_ucard_nav($PAGE){
    global $CFG, $DB;
    $ucard_navbar = $PAGE->navigation->add(get_string("UCARD_MENU",'local_ucard'), new moodle_url('/local/ucard/index.php'));
    //$ucard_navbar = $PAGE->navigation->add('UCARD', new moodle_url('/local/ucard/index.php'));
    $navbar_courselevel = $ucard_navbar->add(get_string("COURSE_LEVEL",'local_ucard') , new moodle_url('/local/ucard/index.php'));
    //$navbar_courselevel = $ucard_navbar->add('courselevel' , new moodle_url('/local/ucard/index.php'));
    //$navbar_courselevel_change = $navbar_courselevel->add('change' , null);
    $navbar_cardlogs = $ucard_navbar->add(get_string("CARD_LOGS",'local_ucard') , new moodle_url('/local/ucard/card_logs.php'));
    $navbar_student = $ucard_navbar->add(get_string("STUD_COURSE",'local_ucard')  , new moodle_url('/local/ucard/student_courses.php'));
    //$navbar_student = $ucard_navbar->add('student course' , new moodle_url('/local/ucard/student_courses.php'));
    return $ucard_navbar;
}

function categorynamebyid($categoryid){

    global $CFG, $DB;
    $result = "";
    $table = 'course_categories';
    $category_data = $DB->get_record($table, array('id'=>$categoryid));
    if (!empty($category_data)){
	$result = $category_data->name;
    }
    return $result;
}

function getlocation($categoryid){
    global $CFG, $DB;
    $result = "";
    $table = 'course_categories';
    $category_data = $DB->get_record($table, array('id'=>$categoryid));
    if (!empty($category_data)){
	$path = $category_data->path;
	$path_id = explode("/", $path);
	$result = $path_id[1];
    }
    return $result;
}


function recursivecategorynamebyid($categoryid){
    global $CFG, $DB;
    $result = "";
    $table = 'course_categories';
    $category_data = $DB->get_record($table, array('id'=>$categoryid));
    if (!empty($category_data)){
	$path = $category_data->path;
	$path_id = explode("/", $path);
	$first = 0;
	foreach ($path_id as $pid){
	    if ($pid != ''){
		if ($first != 0){
		    $result .= "->";
		}
		$result .= categorynamebyid($pid);
		$first++;
	    }
	}
    }
    return $result;
}

function levelcheck($data){
    if(count(array_unique($data))<count($data))
    {
	// Array has duplicates
	return "0";
    }
    else
    {
	// Array does not have duplicates
	return "1";
    }
}

function get_level_by_courseid($courseid){
    global $CFG, $DB;
    $table = 'courselevel';
    $result = $DB->get_record($table, array('courseid'=>$courseid));
    return $result->level;
}

function get_courseid_by_level_location($location, $level){

    global $CFG, $DB;
    //$result[] = array('id'=>1);
    //$result[] = array('id'=>2);
    $result= array();
    $table = 'courselevel';
    $rs = $DB->get_records($table, array('location'=>$location, 'level'=>$level));
    foreach ($rs as $cldata){
	if (!empty($cldata)){
	    $result[] = array('id'=>$cldata->courseid);
	}
    }
    return $result;
}