<?php

/**
 * @Func:       
 * @License:    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @Author:     Thomas Tsai 
 * @Note:
 *
*/

defined('MOODLE_INTERNAL') || die;
require_once("$CFG->libdir/formslib.php");
require_once("lib.php");
require_once("libucard.php");

class student_form extends moodleform {
    function definition() {
	global $CFG;
	global $DB;
	$mform = $this->_form;

	$mform->addElement('header', null, 'moodleid');
	$mform->addElement('text', 'moodleid');
	$mform->setType('moodleid', PARAM_TEXT);
	$mform->setDefault('moodleid', '9');
	$mform->addRule('moodleid', get_string('error'), 'required', null, 'client');

	$listlocations = listlocation();
	$select_options = array(0=>'all');
	foreach($listlocations as $location){
	    $select_options[$location['id']] = $location['name'];
	}
//	$mform->addElement('header', null, 'location');
//	$mform->addElement('select', 'location', 'location', $select_options);
//	$mform->addElement('text', 'location');
//	$mform->setType('location', PARAM_INT);
//	$mform->setDefault('location', 10);
//	$mform->addRule('location', get_string('error'), 'numeric', null, 'client');
//	$mform->addRule('location', get_string('error'), 'required', null, 'client');

	$this->add_action_buttons(true, 'search');
    }

    function validation($data, $files) {
	$errors= array();
	return $errors;
    }
}
?>
