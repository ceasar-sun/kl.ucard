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

function getlocationtrackbycourse($courseid){
    global $CFG, $DB;
    $result = "";
    $table = 'course';
    $course_data = $DB->get_record($table, array('id'=>$courseid));
    if (!empty($course_data)){
	return recursivecategorynamebyid($course_data->category);
    }
    return $result;
}
function getlocationbycourse($courseid){
    global $CFG, $DB;
    $result = "";
    $table = 'course';
    $course_data = $DB->get_record($table, array('id'=>$courseid));
    if (!empty($course_data)){
	$category = getlocation($course_data->category);
	return categorynamebyid($category);
    }
    return $result;
}
function listlocation(){
    global $CFG, $DB;
    $result = "";
    $table = 'course_categories';
    $select = "parent = 0 AND id <> 1";
    $category_data = $DB->get_records_select($table, $select);
    if (!empty($category_data)){
	foreach($category_data as $location_data){
	    $location['id'] = $location_data->id;
	    $location['name'] = $location_data->name;
	    $result[] = $location;
	}
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

function get_next_level_courseid($courseid){
    global $CFG, $DB;

    $current_level = get_level_by_courseid($courseid);
    $coursetrack = $DB->get_record('course', array('id'=>$courseid));
    $courseintrack=$DB->get_records('course', array('category'=>$coursetrack->category));
    $trackcourses=array();
    $courselevel=array();
    foreach ($courseintrack as $track){
	//$courses[$track->id] = get_level_by_courseid($track->id);
	$level = get_level_by_courseid($track->id);
	$courses[$level] = $track->id;
    }
    $rcourses = ksort($courses);
    //var_dump($courses);
    $i = 0;
    foreach ($courses as $level => $id){
	$i++;
	if($level >  $current_level){
	    $newcourseid = $id;
	    break;
	}
    }
    return $newcourseid;
}


function get_level_by_courseid($courseid){
    global $CFG, $DB;
    $table = 'courselevel';
    $result = $DB->get_record($table, array('courseid'=>$courseid));
    return $result->level;
}

function get_track_by_courseid($courseid){
    global $CFG, $DB;
    $coursetrack = $DB->get_record('course', array('id'=>$courseid));
    if (!empty($coursetrack)){
	return $coursetrack->category;
    }
    return null;
}
function get_courseid_by_level_location($location, $level){

    global $CFG, $DB;
    $result= array();
    $coursetrack = array();
    $table = 'courselevel';
    $rs = $DB->get_records($table, array('location'=>$location));
    foreach ($rs as $cldata){
	if (!empty($cldata)){
	    $track = get_track_by_courseid($cldata->courseid);
	    $coursetrack[$track][$cldata->level] = $cldata->courseid;
	}
    }
    foreach ($coursetrack as $track){
	ksort($track);
	$nolevel = 0;
	$min = 0;
	foreach ($track as $courselevel => $id){
	    if (($min == 0) && ($courselevel > $level)){
		global $min;
		$min = $id;
	    }
	    if ($courselevel == $level){
		$result[] = array('id'=>$id);
		global $nolevel;
		$nolevel = 1;
	    }
	}
	if($nolevel == 0){
	    $result[] = array('id'=>$min);
	}
    }

    return $result;
}

function get_last_courses($userlevel, $track){
    global $CFG, $DB;
    $result= array();
    $courseintrack=$DB->get_records('course', array('category'=>$track));
    foreach ($courseintrack as $track){
	$level = get_level_by_courseid($track->id);
	if ($level < $userlevel){
	    $result[] = array('id'=>$track->id);
	}
    }
    return $result;
}
