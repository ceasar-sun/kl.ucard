<?php
defined('MOODLE_INTERNAL') || die();
class block_checklist extends block_list {
    function init() {
	$this->title = 'ucard';

    }
    public function get_content() {
	if ($this->content !== null) {
	    return $this->content;
	}

	$this->content         =  new stdClass;
	$this->content->text   = 'The content of our SimpleHTML block!';
	$this->content->footer = 'Footer here...';

	return $this->content;
    }
    public function applicable_formats() {
	  return array('site-index' => true);
    }
}
?>
