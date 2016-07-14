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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Library of useful functions
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_course
 */

defined('MOODLE_INTERNAL') || die;

function init_ucard_nav($PAGE){
    global $CFG, $DB;
    
    $ucard_navbar = $PAGE->navigation->add('UCARD' , new moodle_url('/local/courselevel/index.php'));
    $navbar_courselevel = $ucard_navbar->add('courselevel' , new moodle_url('/local/courselevel/index.php'));
    $navbar_courselevel_change = $navbar_courselevel->add('change' , null);
    $navbar_cardlogs = $ucard_navbar->add('card logs' , new moodle_url('/local/courselevel/card_logs.php'));
    $navbar_student = $ucard_navbar->add('student course' , new moodle_url('/local/courselevel/student_courses.php'));
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
