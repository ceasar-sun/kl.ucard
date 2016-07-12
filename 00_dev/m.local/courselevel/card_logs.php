<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   local_courselevel
 * @copyright 2016 Thomas Tsai and Free Software Labs
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once('lib.php');
require_once('libucard.php');

$site = get_site();
$PAGE->set_pagelayout('standard');
if ($CFG->forcelogin) {
    require_login();
}
$context = context_system::instance();
require_capability('local/courselevel:change', $context);
global $CFG;
global $DB;

$PAGE->set_context($context); 
$PAGE->set_heading($site->fullname);
$PAGE->set_url(new moodle_url('/local/courselevel/card_logs.php'));
$PAGE->set_title(get_string('courseleveltitle', 'local_courselevel')); 

echo $OUTPUT->header(); 
echo $OUTPUT->skip_link_target();

## your content /HTML here

$db = "ucard.test";
$username = "ucarduser";
$password = "ucardpasswd@mysql";
$querylimit = 20; 

$ucard = new UCard($db, $username, $password);
$cardlogs = $ucard->listCardLogs($querylimit);	// By default "querylimit" is '10' if unset this parameter
$logcount = count($cardlogs);

$course_level_html="<p>最新$querylimit 筆場館打卡資訊</p>"."\n";
$course_level_html.='<table width="700" border="1">'."\n";
$course_level_html.='  <tr>    <td>id</td>    <td>cid</td>    <td>location</td>    <td>timestamp</td>  </tr>'."\n";
for($i=0;$i<count($cardlogs);$i++){
    $course_level_html.="  <tr>\n";
    $course_level_html.="    <td>".$cardlogs[$i]['id']."</td>\n";
    $course_level_html.="    <td>".$cardlogs[$i]['cid']."</td>\n";
    $course_level_html.="    <td>".$cardlogs[$i]['location']."</td>\n";
    $course_level_html.="    <td>".$cardlogs[$i]['dtime']."</td>\n";
    $course_level_html.="  </tr>\n";
}
$course_level_html.="</table>\n";
$course_level_html.="<p>全部".$logcount."筆場館打卡資訊</p>\n";

## end of your content /HTML
echo $OUTPUT->box($course_level_html);
echo $OUTPUT->footer();
