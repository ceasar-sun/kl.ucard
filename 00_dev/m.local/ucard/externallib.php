<?php

/**
 * @Func:       courselevel external file
 * @License:    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @Author:     Thomas, Ceasar
 * @Note:       First released in 2016/7/15              
 *
*/

require_once($CFG->libdir . "/externallib.php");

class local_courselevel_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_getlevel_parameters
     */
    public static function getlevel_parameters() {
	return new external_function_parameters(
		array('courseid' => new external_value(PARAM_INT, 'id of course'))
		);
    }

    /**
     * The function itself
     * @return string welcome message
     */
    public static function getlevel($courseid) {

	//Parameters validation
	$params = self::validate_parameters(self::getlevel_parameters(),
		array('courseid' => $courseid));

	global $CFG;
	global $DB;
	require_once($CFG->dirroot . "/local/ucard/lib.php");

	
	$level = get_level_by_courseid($params['courseid']);

	return $level;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function getlevel_returns() {
	return new external_value(PARAM_TEXT, 'The welcome message + user first name');
    }

    /**
     * Returns description of method parameters
     * @return external_getlevel_parameters
     */
    public static function getlastcourse_parameters() {
	return new external_function_parameters(
		array(
		    'level' => new external_value(PARAM_INT, 'level of user'),
		    'track' => new external_value(PARAM_INT, 'track of course')			)
	);
    }


    /**
     * The function itself
     * @return string welcome message
     */
    public static function getlastcourse($level, $track) {

	//Parameters validation
	$params = self::validate_parameters(self::getlastcourse_parameters(),
		array('level' => $level, 'track' => $track));

	global $CFG;
	global $DB;
	require_once($CFG->dirroot . "/local/ucard/lib.php");
	
	$courseids = get_last_courses($params['level'], $params['track']);

	return $courseids;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function getlastcourse_returns() {
    	return new external_multiple_structure(
		new external_single_structure(
		    array(
			'id' => new external_value(PARAM_INT, 'Course ID'),
			)
		    )
		);
    }


    /**
     * Returns description of method parameters
     * @return external_getlevel_parameters
     */
    public static function getnextcourse_parameters() {
	return new external_function_parameters(
		array('courseid' => new external_value(PARAM_INT, 'id of course'))
		);
    }


    /**
     * The function itself
     * @return string welcome message
     */
    public static function getnextcourse($courseid) {

	//Parameters validation
	$params = self::validate_parameters(self::getnextcourse_parameters(),
		array('courseid' => $courseid));

	global $CFG;
	global $DB;
	require_once($CFG->dirroot . "/local/ucard/lib.php");

	
	$courseid = get_next_level_courseid($params['courseid']);

	return $courseid;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function getnextcourse_returns() {
	return new external_value(PARAM_INT, 'the next level of courseid');
    }


    /**
     * Returns description of method parameters
     * @return external_getlevel_parameters
     */
    public static function courseid_by_level_location_parameters() {
	return new external_function_parameters(
		array(
		    'location' => new external_value(PARAM_INT, 'id of location/category'),
		    'level' => new external_value(PARAM_INT, 'value of level')
			)	
		    );
		}

    /**
     * The function itself
     * @return string welcome message
     */
    public static function courseid_by_level_location($location, $level) {

	//Parameters validation
	$params = self::validate_parameters(self::courseid_by_level_location_parameters(),
		array(
		    'location' => $location,
		    'level' => $level
		    )
		);

	global $CFG;
	global $DB;
	require_once($CFG->dirroot . "/local/ucard/lib.php");
	
	$courseids = get_courseid_by_level_location($params['location'], $params['level']);

	return $courseids;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function courseid_by_level_location_returns() {
	return new external_multiple_structure(
		new external_single_structure(
		    array(
			'id' => new external_value(PARAM_INT, 'Course ID'),
			)
		    )
		);
    }



}
