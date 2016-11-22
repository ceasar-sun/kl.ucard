<?php
/**
 * @Func:       stat_stu version information
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
require_capability('block/stat_stu_info:view', $context);

global $CFG;

$PAGE->set_context($context);
$PAGE->set_heading($site->fullname);
$PAGE->set_url(new moodle_url('/blocks/stat_stu/view.php'));
$PAGE->set_title(get_string('stat_stutitle', 'block_stat_stu'));

$navbar = init_stat_stu_nav($PAGE);
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

    $semester_label  = get_string('STAT_STU_SEMTR','block_stat_stu');
    $academic_year_label = get_string('STAT_STU_AYEAR','block_stat_stu');
    $grade_label = get_string('STAT_STU_GRADE','block_stat_stu');
    $course_label = get_string('STAT_STU_COURSE','block_stat_stu');
    $nodata = get_string('nodata','block_stat_stu');

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

    $table_title_search=array('Quartile','mean Descriptive Statistics','std Descriptive Statistics','skewness Descriptive Statistics','kurtosis Descriptive Statistics');
    $table_title_replace=array('四分位數','平均值','標準差','偏度值','峰度值');
    echo '<h1>'.$SCHName[$schno].'&nbsp;'.$academic_year.'&nbsp;'.$academic_year_label.'&nbsp;'.$semester.'&nbsp;'.$semester_label.'&nbsp;('.$grade.'&nbsp;'.$grade_label.')'.$course_label.":".$course_arr[$course_stat].'</h1>';
    exec("cd ".dirname(__FILE__)."/runstat;./run.sh ".$academic_year." ".$semester." ".$grade." ".$course_stat." ".$schno); 
    if ( file_exists( "runstat/Score_A".$schno.$academic_year.$semester.$grade.$course_stat.".png")){
       
        echo str_replace($table_title_search,$table_title_replace,file_get_contents("runstat/A".$schno.$academic_year.$semester.$grade.$course_stat.".1st"))."</tbody></table>";
        echo str_replace($table_title_search,$table_title_replace,file_get_contents("runstat/A".$schno.$academic_year.$semester.$grade.$course_stat.".2st"))."</tbody></table>";
#        echo '<h2>學習能力指標</h2>';
#        if ( file_exists( "runstat/A".$schno.$academic_year.$semester.$grade.$course_stat.".3st" )){
#           echo str_replace($table_title_search,$table_title_replace,file_get_contents("runstat/A".$schno.$academic_year.$semester.$grade.$course_stat.".3st"))."</tbody></table>";
#           echo str_replace($table_title_search,$table_title_replace,file_get_contents("runstat/A".$schno.$academic_year.$semester.$grade.$course_stat.".4st"))."</tbody></table>";
#        }else{
#            echo '<table><tr><td>沒有資料可以呈現</td></tr></table>';
#        }
         
        echo "<img src='runstat/Score_A".$schno.$academic_year.$semester.$grade.$course_stat.".png' height='600' width='800'/>";
        echo "<img src='runstat/Box_Score_A".$schno.$academic_year.$semester.$grade.$course_stat.".png' height='600' width='800'/>";
        echo "<img src='runstat/Density_Score_A".$schno.$academic_year.$semester.$grade.$course_stat.".png' height='600' width='800'/>";
    }else{
        echo '<h1> 很抱歉, 沒有資料可呈現 </h1>';
    }
echo $OUTPUT->footer();
?>
