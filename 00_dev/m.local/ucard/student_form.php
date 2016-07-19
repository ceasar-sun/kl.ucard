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

	$mform->addElement('header', null, 'rfid_key16');
	$mform->addElement('text', 'rfid_key16');
	$mform->setType('rfid_key16', PARAM_TEXT);
	$mform->setDefault('rfid_key16', '10531247230');
	$mform->addRule('rfid_key16', get_string('error'), 'required', null, 'client');

	$mform->addElement('header', null, 'location');
	$mform->addElement('text', 'location');
	$mform->setType('location', PARAM_INT);
	$mform->setDefault('location', 10);
	$mform->addRule('location', get_string('error'), 'numeric', null, 'client');
	$mform->addRule('location', get_string('error'), 'required', null, 'client');

	$this->add_action_buttons(true, 'search');
    }

    function validation($data, $files) {
	$errors= array();
	return $errors;
    }
}
?>
