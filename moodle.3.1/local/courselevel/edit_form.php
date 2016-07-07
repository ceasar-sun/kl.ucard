<?php
defined('MOODLE_INTERNAL') || die;
require_once("$CFG->libdir/formslib.php");
require_once("lib.php");

class courselevel_form extends moodleform {
    function definition() {
	global $CFG;
	global $DB;
	$mform = $this->_form;
	$categoryid = $this->_customdata['category'];
	$rs = $DB->get_recordset('courselevel');
	foreach ($rs as $record) {
	    $course = get_course($record->courseid);
	    if ($course->category == $categoryid){

		$mform->addElement('header', null, get_string("updatelevelof", 'local_courselevel', $course->fullname));
		$mform->addElement('text', $record->id, get_string("level", 'local_courselevel'));
		$mform->setDefault($record->id,$record->level);
		$mform->setType($record->id, PARAM_INT);
	    }
	}
	$rs->close();

	$mform->addElement('hidden', 'save', 'yes');
	$mform->setType('save', PARAM_TEXT);
	$mform->addElement('hidden', 'category', $categoryid);
	$mform->setType('category', PARAM_INT);
	$this->add_action_buttons();
    }

    function validation($data, $files) {
	$errors= array();
	$values = array();
	foreach ($data as $key=>$value){
	    if (($key != 'category') && ($key != '_qf__courselevel_form')){
		array_push($values, $value);
	    }
	}
	if (levelcheck($values) == 0){
	    $errors['duplicatelevel']='duplicated level';
	    echo $errors['duplicatelevel'];
	}
	return $errors;
    }
}
?>
