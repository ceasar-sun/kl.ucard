<?php
/**
 * @Func:       stat_score version information
 * @License:    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @Author:     Serena Pan
 * @Note:       2016/07/27
*/

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir.'/tablelib.php');
require_once('lib.php');

$id = optional_param('id', 0, PARAM_INT);
$academic_year = optional_param('academic_year', 0, PARAM_INT);
$semester = optional_param('semester', 0, PARAM_INT);
$grade = optional_param('grade', 0, PARAM_INT);
$schno = optional_param('schno', 0, PARAM_INT);
$course_stat = optional_param('course_stat', 0, PARAM_RAW);

$site = get_site();
$PAGE->set_pagelayout('standard');
if ($CFG->forcelogin) {
    require_login();
}
// See your own profile by default.
if (empty($id)) {
    require_login();
    $id = $USER->id;
}

$context = context_system::instance();
require_capability('block/stat_score_info:view', $context);

global $CFG;

$PAGE->set_context($context);
$PAGE->set_heading($site->fullname);
$PAGE->set_url(new moodle_url('/blocks/stat_score/view.php'));
$PAGE->set_title(get_string('stat_scoretitle', 'block_stat_score'));

$navbar = init_stat_score_nav($PAGE);
echo $OUTPUT->header();
echo $OUTPUT->skip_link_target();
     include "db.php";
     $SCHName=array();
     $mysqli = new mysqli($IP, $dbuser, $dbpasswd, $dbname);
     $result = $mysqli->query("SELECT * FROM `semester_schno` WHERE `schclass` = '國小' ORDER BY `schclass` DESC");
     while ($row = $result->fetch_assoc()) {
        $SCHName[$row["schno"]]=$row["schname"];
     }
     $SCHName[0]='所有國小';

    if ( $academic_year == 0 || $semester==0 || $grade==0 || !is_numeric($academic_year) || !is_numeric($semester) || !is_numeric($grade)){
        $academic_year='104';
        $semester='1';
        $grade='1';
        $course_stat='A00';
    }

    $semester_label  = get_string('STAT_SCORE_SEMTR','block_stat_score');
    $academic_year_label = get_string('STAT_SCORE_AYEAR','block_stat_score');
    $grade_label = get_string('STAT_SCORE_GRADE','block_stat_score');
    $course_label = get_string('STAT_SCORE_COURSE','block_stat_score');
    $nodata = get_string('nodata','block_stat_score');

    $course_arr["A00"]='國文與英語';
    $course_arr["A01"]='健康與體育';
    $course_arr["A02"]='數學';
    $course_arr["A03"]='社會';
    $course_arr["A04"]='生活課程';
    $course_arr["A05"]='自然與生活科技';
    $course_arr["A06"]='藝術與人文';
    $course_arr["A07"]='綜合活動';
    $course_arr["A08"]='彈性學習';
    $course_arr["ALL"]='所有科目';

    $ScoreRange=array();
    $ScoreRangeArr=array();
    $ScoreRangeArr[0]="";
    $ScoreRangeArr[1]="AND score >= 90 AND score <= 100 ";
    $ScoreRangeArr[2]="AND score >= 80 AND score < 90 ";
    $ScoreRangeArr[3]="AND score >= 70 AND score < 80 ";
    $ScoreRangeArr[4]="AND score >= 60 AND score < 70 ";
    $ScoreRangeArr[5]="AND score < 60 ";

    $sql="SELECT COUNT(idno) FROM `semester_score` WHERE stdyear = '".$grade."' AND semester = '".$academic_year.$semester."' ";
 
    if ( $course_stat != 'ALL' ){
        $sql.= "AND stdlib = '".$course_stat."' ";
    }

    if ( $schno != 'ALL' ){
        $sql.= "AND schno = '".$schno."' ";
    }
    #SELECT COUNT(idno) FROM `semester_score` WHERE score >= 90 AND score <= 100 AND stdyear = 1 AND stdlib = 'A00' AND semester=1031
    echo '<h1><學習能力指標><br>'.$SCHName[$schno].'&nbsp;'.$academic_year.'&nbsp;'.$academic_year_label.'&nbsp;'.$semester.'&nbsp;'.$semester_label.'&nbsp;('.$grade.'&nbsp;'.$grade_label.')'.$course_label.":".$course_arr[$course_stat].'</h1>';
    foreach( $ScoreRangeArr as $ScoreRangeSql ){
       $result = $mysqli->query($sql.$ScoreRangeSql);
       #echo $sql.$ScoreRangeSql."<br>";
       while ($row = $result->fetch_assoc()) {
          $ScoreRange[] = $row["COUNT(idno)"];
          #echo $row["COUNT(idno)"]."<br>";
       }
    }
    #var_dump($ScoreRangePer);
    echo '<img src="showpic.php?a0='.$ScoreRange[1].'&a1='.$ScoreRange[2].'&a2='.$ScoreRange[3].'&a3='.$ScoreRange[4].'&a4='.$ScoreRange[5].'" />';
    echo '<h1><深美國小－學習能力指標></h1><br>';
    $mysqli = new mysqli($IP, $dbuser, $dbpasswd, $dbname);
    if ( $course_stat == 'ALL' ){
       $course_stat='A';
    }
    $result = $mysqli->query("SELECT `value` FROM `semester_setgrp` WHERE `schno` = '173641' AND `stdyear` = '".$grade."' AND `semester` = '".$academic_year.$semester."' AND `subno` LIKE '%".$course_stat."%' GROUP BY `value`");

    $ValueList=array();
    
    while ($row = $result->fetch_assoc()) {
        $ValueList[]=$row["value"];
    }
    
    $ScoreList=array();
    
    echo '<table style="border:1px solid #cccccc; border-collapse:collapse;">';
    echo '<tr><th style="border:1px solid #cccccc; color:#ffffff;background-color:#aaaacc; " >評語</th><th style="border:1px solid #cccccc; color:#ffffff;background-color:#aaaacc;">&nbsp;&nbsp;98&nbsp;&nbsp;</th><th style="border:1px solid #cccccc; color:#ffffff;background-color:#aaaacc;">&nbsp;&nbsp;88&nbsp;&nbsp;</th><th style="border:1px solid #cccccc;color:#ffffff;background-color:#aaaacc;">&nbsp;&nbsp;78&nbsp;&nbsp;</th><th style="border:1px solid #cccccc;color:#ffffff;background-color:#aaaacc;">&nbsp;&nbsp;68&nbsp;&nbsp;</th><th style="border:1px solid #cccccc;color:#ffffff;background-color:#aaaacc;">&nbsp;&nbsp;58&nbsp;&nbsp;</th></tr>';
    foreach ( $ValueList as $key => $ValueItems){
        $result = $mysqli->query("SELECT `score` FROM `semester_setgrp` WHERE `schno` = '173641' AND `stdyear` = '".$grade."' AND `semester` = '".$academic_year.$semester."' AND `subno` LIKE '%".$course_stat."%' AND `value` = '".$ValueItems."'");
        $ScoreSum=array();
        while ($row = $result->fetch_assoc()) {
            if ( isset($ScoreSum[$row["score"]]) ){
               $ScoreSum[$row["score"]]++;
            }else{
               $ScoreSum[$row["score"]]=1;
            }
        }
        echo '<tr>';
        echo '<td style="border:1px solid #cccccc; background-color:#3377cc; color:#ffffff;text-align:left;line-height:28px;">'.$ValueItems.'</td>';
        krsort($ScoreSum);
        $ScoreList[$key]=$ScoreSum;
        $count=0;
        foreach($ScoreSum as $ScoreValue => $ScoreCount){
           echo '<td style="border:1px solid #cccccc;text-align:center;">'.$ScoreCount.'</td>';
           $count++;
        }
        if ( $count == 4){
           echo '<td style="border:1px solid #cccccc;">&nbsp;&nbsp;&nbsp;&nbsp;</td>';
        }
        echo '</tr>';
    }
    echo "</table>"; 
   

echo $OUTPUT->footer();
?>
