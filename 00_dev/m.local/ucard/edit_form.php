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
	$rs = $DB->get_records('courselevel');
	$level_check=function($val){
	    global $DB;
	    $categoryid = $this->_customdata['category'];
	    $limit = $DB->count_records('course', array('category'=>$categoryid));
	    if ((intval($val) > $limit) || (intval($val) <= 0)){
		return false;
	    } else {
		return true;
	    }
	};
	$limit = $DB->count_records('course', array('category'=>$categoryid));
	foreach ($rs as $record) {
	    $course = get_course($record->courseid);
	    if ($course->category == $categoryid){

		$mform->addElement('header', null, get_string("updatelevelof", 'local_ucard', $course->fullname));
		$mform->addElement('text', $record->id, get_string("level", 'local_ucard'));
		$mform->setDefault($record->id,$record->level);
		$mform->setType($record->id, PARAM_INT);
		$mform->addRule($record->id, "level value error(1~$limit)", 'callback', $level_check, 'server', false, true);
	    }
	}

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
	    echo "<div class='felement ftext error'><span class='error'>".$errors['duplicatelevel']."</span></div>";
	}
	return $errors;
    }
}
?>
