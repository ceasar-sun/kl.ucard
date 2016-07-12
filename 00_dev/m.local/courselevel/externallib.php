<?php

/**
 * courselevel external file
 *
 * @package    local_courselevel
 * @copyright  20XX YOURSELF
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
	require_once($CFG->dirroot . "/local/courselevel/lib.php");

	
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
	require_once($CFG->dirroot . "/local/courselevel/lib.php");
	
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
