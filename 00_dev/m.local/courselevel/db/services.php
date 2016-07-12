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
 * User grade report external functions and service definitions.
 *
 * @package    gradereport_user
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(
	'course_level_get_level' => array(
	    'classname' => 'local_courselevel_external',
	    'methodname' => 'getlevel',
	    'classpath' => 'local/courselevel/externallib.php',
	    'description' => 'Get the level for a course',
	    'type' => 'read'
	    ),
	'course_level_id_by_level_location' => array(
	    'classname' => 'local_courselevel_external',
	    'methodname' => 'courseid_by_level_location',
	    'classpath' => 'local/courselevel/externallib.php',
	    'description' => 'Get the course by level and location',
	    'type' => 'read'
	    ),
	);
