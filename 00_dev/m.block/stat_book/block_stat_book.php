<?php

/**
 * @Func:        
 * @License:    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @Author:     Serena Pan
 * @Note:                     
 *
*/

class block_stat_book extends block_base {
    function init() {
        $this->title = get_string('pluginname','block_stat_book');
    }

    function has_config() {
        return false;
    }

    function applicable_formats() {
        return array('all' => true);
    }

    function instance_allow_config() {
        return true;
    }

    function  instance_can_be_hidden() {
        return false;
    }

    function get_content() {
        global $USER, $CFG, $DB, $OUTPUT, $PAGE;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }
        $calculate  = get_string('STAT_BOOK_CAL','block_stat_book');
        $semester  = get_string('STAT_BOOK_SEMTR','block_stat_book');
        $academic_year = get_string('STAT_BOOK_AYEAR','block_stat_book');
        $grade = get_string('STAT_BOOK_GRADE','block_stat_book');
        $course_stat = get_string('STAT_BOOK_COURSE','block_stat_book');
        
        include "db.php";
        $SCHName=array();
        $mysqli = new mysqli($IP, $dbuser, $dbpasswd, $dbname);
        $result = $mysqli->query("SELECT * FROM `semester_schno` WHERE `schclass` = '國小' ORDER BY `schclass` DESC");
        while ($row = $result->fetch_assoc()) {
           $SCHName[$row["schno"]]=$row["schname"];
        }

        $this->content->text  = '<div class="searchform">';
        $this->content->text .= '<form action="'.$CFG->wwwroot.'/blocks/stat_book/view.php" style="display:inline">';
        $this->content->text .= '<select name="schno" id="schno" style="display:inline"/>';
        $this->content->text .= '<option value="ALL">所有國小</option>';
        foreach( $SCHName as $SCHKey => $SCHValue){
           $this->content->text .= '<option value="'.$SCHKey.'">'.$SCHValue.'</option>';
        }
        $this->content->text .= '</select>';
        $this->content->text .= '<input name="id" type="hidden" value="'.$USER->id.'" />';
        $this->content->text .= '<select name="year" id="year" style="display:inline"/>';
        for ( $j=16;$j>1;$j--){
           if ( $j >= 10 ){
              $this->content->text .= '<option value="20'.$j.'">20'.$j.'</option>';
           }else{
              $this->content->text .= '<option value="200'.$j.'">200'.$j.'</option>';
           }
        }
        $this->content->text .= '</select>';
        $this->content->text .= '<label style="display:inline">年</label>';
        $this->content->text .= '<select name="month" id="month" style="display:inline"/>';
        $this->content->text .= '<option value="ALL">ALL</option>';
        for ( $i=1;$i<12;$i++){
           $this->content->text .= '<option value="'.$i.'">'.$i.'</option>';
        }
        $this->content->text .= '</select>';
        $this->content->text .= '<label style="display:inline">月</label>';
/*
        $this->content->text .= '<select name="course_stat" id="course_stat" style="display:inline"/>';
        $this->content->text .= '<option value="0">總類</option>';
        $this->content->text .= '<option value="1">哲學類</option>';
        $this->content->text .= '<option value="2">宗教類</option>';
        $this->content->text .= '<option value="3">自然科學類</option>';
        $this->content->text .= '<option value="4">應用科學類</option>';
        $this->content->text .= '<option value="5">社會科學類</option>';
        $this->content->text .= '<option value="6">中國史地類</option>';
        $this->content->text .= '<option value="7">世界史地類</option>';
        $this->content->text .= '<option value="8">語文類</option>';
        $this->content->text .= '<option value="9">美術類</option>';
        $this->content->text .= '</select>';
        $this->content->text .= '<label style="display:inline">類</label>';*/
        $this->content->text .= '<button id="calculate_button" type="submit" title="'.$calculate.'" >'.$calculate.'</button><br />';
        $this->content->text .= '</form></div>';
        return $this->content;
    }
}

?>
