<?php
/**
 * @Func:       stat_book version information
 * @License:    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @Author:     Serena Pan
 * @Note:       2016/07/27
*/

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->libdir.'/tablelib.php');
require_once('lib.php');

$id = optional_param('id', 0, PARAM_INT);
$year = optional_param('year', 0, PARAM_INT);
$month = optional_param('month', 0, PARAM_INT);
$schno = optional_param('schno', 0, PARAM_INT);
#$course_stat = optional_param('course_stat', 0, PARAM_RAW);

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
require_capability('block/stat_book_info:view', $context);

global $CFG;

$PAGE->set_context($context);
$PAGE->set_heading($site->fullname);
$PAGE->set_url(new moodle_url('/blocks/stat_book/view.php'));
$PAGE->set_title(get_string('stat_booktitle', 'block_stat_book'));

$navbar = init_stat_book_nav($PAGE);
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

    if ( $year == 0 ){
        $year='2016';
        $month='1';
        $schno='17364';
        //$course_stat='0';
    }

    $nodata = get_string('nodata','block_stat_book');
    $course_arr["0"]='總類&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    $course_arr["1"]='哲學&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    $course_arr["2"]='宗教&nbsp;&nbsp;&nbsp;&nbsp;';
    $course_arr["3"]='自然科學';
    $course_arr["4"]='應用科學';
    $course_arr["5"]='社會科學';
    $course_arr["6"]='中國史地';
    $course_arr["7"]='世界史地';
    $course_arr["8"]='語文&nbsp;&nbsp;&nbsp;&nbsp;';
    $course_arr["9"]='美術';
    date_default_timezone_set("Asia/Taipei");
    #echo $year.",".$month.",".$schno.",".$course_stat."<br>";
    if ( $month == 0 ){
       $eDate=date("Ymt235959",strtotime($year."-12-1"));
       $sDate=date("Ymd000000",strtotime($year."-1-1"));
    }else{
       $eDate=date("Ymt235959",strtotime($year."-".$month."-1"));
       $sDate=date("Ymd000000",strtotime($year."-".$month."-1"));
    }
    
    $mysqli = new mysqli($IP, $dbuser, $dbpasswd, $dbname);
    $sql = "select * from ucard.semester_bookrec WHERE `date_out` > '".$sDate."' AND `date_out` < '".$eDate."'";
    if ( $schno != 0 ){
       $sql.=" AND `schno` = '".$schno."' ";
    }
    if ($month == 0){
       echo "<h1>".$SCHName[$schno]."&nbsp;:&nbsp;".$year."全年"."</h1>";
    }else{
       echo "<h1>".$SCHName[$schno]."&nbsp;:&nbsp;".$year."年".$month."月"."</h1>";
    }
    echo "
<table class='gmisc_table' style='border-collapse: collapse; margin-top: 1em; margin-bottom: 1em;' >
<thead>
<tr>    
";
    foreach($course_arr as $courseItem){
       echo "<th style='border-left: 2px solid grey;border-right: 2px solid grey;border-bottom: 1px solid grey; border-top: 2px solid grey; text-align: center;'>".$courseItem."類</th>\n";
    }
echo "</tr>
</thead>
<tbody>
<tr>
";

    $CountT=array();
    $ARG="";
    for ( $i=0;$i<10;$i++){
       $sql_new = $sql." AND `bk_grp` like '".$i."%'";
       $result = $mysqli->query($sql_new);
       $CountS = $result->num_rows;
       $ARG.= $CountS." ";
       $CountT[]=$CountS;
       echo "<td style='border-left: 2px solid grey;border-right: 2px solid grey;border-bottom: 2px solid grey; text-align: center;'>".$CountS."</td>\n";
    }
    $ARG=$ARG."A".$schno.$sDate.$eDate;
echo "</tr></tbody></table>";

    exec("cd ".dirname(__FILE__)."/runstat;./run.sh ".$ARG);
    //echo "<table style='border-collapse: collapse; margin-top: 1em; margin-bottom: 1em;'>";
    echo "<img src='runstat/A".$schno.$sDate.$eDate.".png' height='600' width='800'/>";
    echo "<table style='border-collapse: collapse; margin-top: 1em; margin-bottom: 1em;'><tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>";
    foreach($course_arr as $courseItem){
       echo "<td style='text-align: center;'>".$courseItem."</td>\n";
    }
    echo "</tr></table>\n";
echo $OUTPUT->footer();
?>
