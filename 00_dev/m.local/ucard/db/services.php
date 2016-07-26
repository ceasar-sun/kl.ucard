<?php

/**
 * @Func:	User grade report external functions and service definitions. 
 * @License:    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @Author:     Ceasar, Thomas 
 * @Note:       First released in 2016/7/15              
 *
*/

$functions = array(
	'course_level_get_level' => array(
	    'classname' => 'local_courselevel_external',
	    'methodname' => 'getlevel',
	    'classpath' => 'local/ucard/externallib.php',
	    'description' => 'Get the level for a course',
	    'type' => 'read'
	    ),
	'course_level_get_last_course' => array(
	    'classname' => 'local_courselevel_external',
	    'methodname' => 'getlastcourse',
	    'classpath' => 'local/ucard/externallib.php',
	    'description' => 'Get the last courses',
	    'type' => 'read'
	    ),
	'course_level_get_next_course' => array(
	    'classname' => 'local_courselevel_external',
	    'methodname' => 'getnextcourse',
	    'classpath' => 'local/ucard/externallib.php',
	    'description' => 'Get the next level for a course',
	    'type' => 'read'
	    ),
	'course_level_id_by_level_location' => array(
	    'classname' => 'local_courselevel_external',
	    'methodname' => 'courseid_by_level_location',
	    'classpath' => 'local/ucard/externallib.php',
	    'description' => 'Get the course by level and location',
	    'type' => 'read'
	    ),
	);
