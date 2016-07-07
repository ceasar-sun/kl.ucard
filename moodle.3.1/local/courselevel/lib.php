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
