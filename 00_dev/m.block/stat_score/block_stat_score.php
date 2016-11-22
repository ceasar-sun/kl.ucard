<?php

/**
 * @Func:        
 * @License:    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @Author:     Serena Pan
 * @Note:                     
 *
*/

class block_stat_score extends block_base {
    function init() {
        $this->title = get_string('pluginname','block_stat_score');
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
        $calculate  = get_string('STAT_SCORE_CAL','block_stat_score');
        $semester  = get_string('STAT_SCORE_SEMTR','block_stat_score');
        $academic_year = get_string('STAT_SCORE_AYEAR','block_stat_score');
        $grade = get_string('STAT_SCORE_GRADE','block_stat_score');
        $course_stat = get_string('STAT_SCORE_COURSE','block_stat_score');
        
        include "db.php";
        $SCHName=array();
        $mysqli = new mysqli($IP, $dbuser, $dbpasswd, $dbname);
        $result = $mysqli->query("SELECT * FROM `semester_schno` WHERE `schclass` = '國小' ORDER BY `schclass` DESC");
        while ($row = $result->fetch_assoc()) {
           $SCHName[$row["schno"]]=$row["schname"];
        }
        $result = $mysqli->query("SELECT `semester` FROM `semester_score` GROUP BY `semester` ORDER BY `semester` DESC");
        $Academic_Year=array();
        while ($row = $result->fetch_assoc()) {
           $AYear=substr($row["semester"],0,3);
           if (! in_array($AYear,$Academic_Year) ){
              $Academic_Year[]=$AYear;
           }
        }        

        $this->content->text  = '<div class="searchform">';
        $this->content->text .= '<form action="'.$CFG->wwwroot.'/blocks/stat_score/view.php" style="display:inline">';
        $this->content->text .= '<select name="schno" id="schno" style="display:inline"/>';
        $this->content->text .= '<option value="ALL">所有國小</option>';
        foreach( $SCHName as $SCHKey => $SCHValue){
           $this->content->text .= '<option value="'.$SCHKey.'">'.$SCHValue.'</option>';
        }
        $this->content->text .= '</select>';
        $this->content->text .= '<input name="id" type="hidden" value="'.$USER->id.'" />';
        $this->content->text .= '<select name="academic_year" id="academic_year" style="display:inline"/>';           foreach( $Academic_Year as $AYearItem){
           $this->content->text .= '<option value="'.$AYearItem.'">'.$AYearItem.'</option>';
        }
        $this->content->text .= '</select>';
        $this->content->text .= '<label style="display:inline">'.$academic_year.'</label>';
        $this->content->text .= '<select name="semester" id="semester" style="display:inline"/>';
        $this->content->text .= '<option value="1">1</option>';
        $this->content->text .= '<option value="2">2</option>';
        $this->content->text .= '</select>';
        $this->content->text .= '<label style="display:inline">'.$semester.'</label>';
        $this->content->text .= '<select name="grade" id="grade" style="display:inline"/>';
        $this->content->text .= '<option value="1">1</option>';
        $this->content->text .= '<option value="2">2</option>';
        $this->content->text .= '<option value="3">3</option>';
        $this->content->text .= '<option value="4">4</option>';
        $this->content->text .= '<option value="5">5</option>';
        $this->content->text .= '<option value="6">6</option>';
        $this->content->text .= '</select>';
        $this->content->text .= '<label style="display:inline">'.$grade.'</label>';
        $this->content->text .= '<select name="course_stat" id="course_stat" style="display:inline"/>';
        $this->content->text .= '<option value="A00">國文與英語</option>';
        $this->content->text .= '<option value="A01">健康與體育</option>';
        $this->content->text .= '<option value="A02">數學</option>';
        $this->content->text .= '<option value="A03">社會</option>';
        $this->content->text .= '<option value="A04">生活課程</option>';
        $this->content->text .= '<option value="A05">自然與生活科技</option>';
        $this->content->text .= '<option value="A06">藝術與人文</option>';
        $this->content->text .= '<option value="A07">綜合活動</option>';
        $this->content->text .= '<option value="A08">彈性學習</option>';
        $this->content->text .= '<option value="ALL">全部課程</option>';
        $this->content->text .= '</select>';
        $this->content->text .= '<label style="display:inline">'.$course_stat.'</label>';
        $this->content->text .= '<button id="calculate_button" type="submit" title="'.$calculate.'" >'.$calculate.'</button><br />';
        $this->content->text .= '</form></div>';
        return $this->content;
    }
}

?>
