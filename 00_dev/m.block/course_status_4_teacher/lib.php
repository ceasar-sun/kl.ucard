<?php

/* Course Status for teacher Block
 * The plugin shows the number and list of courses info.
 * @package blocks
 * @author: Jonathan Lin
 */




 /**
  * This function count the total courses of site.
  *
  * @param null
  * @return Array $total_courses return total courses of site.
  */
 function get_all_course()
 {
     global $DB;
     $total_courses = $DB->get_records_sql('SELECT
                                             c.id, c.category, c.shortname
                                            FROM {course} c
                                            WHERE c.id > 1
                                            ORDER BY c.id
                                            ');

     return $total_courses;
 }



 /**
  * This function count the total courses of completed.
  *
  * @param null
  * @return Array $total_courses return total courses of completed.
  */
 function get_count_of_sel_compl_course($cid)
 {

     global $DB;
     $total_courses = $DB->get_record_sql('SELECT
                                           count(course) as total_course
                                           FROM {course_completion_crit_compl} cccc
                                           WHERE course = ?', array($cid));

     error_log(' - comply course: '. print_r($total_courses, true));

     return $total_courses->total_course;
 }



 /**
  * This function count the total courses of selected.
  *
  * @param null
  * @return Array $total_courses return total courses of selected.
  */
 function get_count_of_selected_course($cid)
 {
   global $DB;
   $total_sel_c = $DB->get_records_sql('SELECT
                               cr.id, cr.shortname, cr.fullname, count( ra.id ) AS enrolled
                             FROM `mdl_course` cr
                             JOIN `mdl_context` ct ON ( ct.instanceid = cr.id )
                             LEFT JOIN `mdl_role_assignments` ra ON ( ra.contextid = ct.id )
                             WHERE ct.contextlevel = 50
                             AND cr.id = ?
                             GROUP BY cr.shortname, cr.fullname
                             ORDER BY `enrolled` ASC',
                              array($cid));


   return $total_sel_c[$cid]->enrolled;
 }



 /**
  * This function count the total student of site.
  *
  * @param null
  * @return Array $total_students return total stucent of site.
  */
 function get_all_students()
 {
     global $DB;
     $total_students = $DB->get_records_sql('SELECT
                                             u.id, u.firstname, u.lastname, u.email
                                             FROM {role_assignments} ra
                                               JOIN {user} u ON u.id = ra.userid
                                               JOIN {role} r ON r.id = ra.roleid
                                             WHERE ra.userid = u.id
                                               AND roleid = 5
                                             ORDER BY u.id
                                             ');

     return $total_students;
 }



 function get_compl_course($uid)
 {
     global $DB;
     $total_course = $DB->get_records_sql('SELECT
                                           c.id, c.category, c.shortname
                                           FROM {course_completion_crit_compl} cc
                                             JOIN {user} u ON u.id = cc.userid
                                             JOIN {course} c ON c.id = cc.course
                                           WHERE cc.userid = ?
                                           ORDER BY c.id',
                                             array($uid));

     return $total_course;
 }



function get_inprogress_courses($uid)
{
    global $CFG;
    $tmp_enrol = get_this_students_of_enrolled_course($uid);
    $tmp_enrol_courses = json_decode(json_encode($tmp_enrol), True);

    $tmp_compl = get_compl_course($uid);
    $tmp_compl_courses = json_decode(json_encode($tmp_compl), True);

    error_log(' - uid: ' . print_r($uid, true));
    error_log(' - enrol: ' . print_r($tmp_enrol_courses, true));
    error_log(' - compl: ' . print_r($tmp_compl_courses, true));

    $result = search_inprogress_course($tmp_enrol_courses, $tmp_compl_courses);
    // session_start();
    // $_SESSION['$inprogression_uid'] = $uid;
    // $_SESSION['$inprogress_course_list_row'] = $result;

    error_log(' -- inprogress result: ' . print_r($result, true));

    return $result;
    // return '檢視列表'; //$result[0][fullname];
}


/**
 * This function get the students of enrolled course.
 *
 * @param null
 * @return Array $total_students_enrl_course return total students of enrolled course.
 */
function get_this_students_of_enrolled_course($uid)
{
    global $DB;
    $total_students_enrl_course = $DB->get_records_sql('SELECT
                                                          c.id, c.category, c.shortname
                                                        FROM {user_enrolments} ue
                                                          JOIN {user} u ON u.id = ue.userid
                                                          JOIN {enrol} e ON e.id = ue.enrolid
                                                          JOIN {course} c ON c.id = e.courseid
                                                        WHERE ue.userid = ?
                                                     ORDER BY u.id',
                                                    array($uid));

    return $total_students_enrl_course;
}



  /**
   * This function get the students of enrolled course.
   *
   * @param null
   * @return Array $total_students_enrl_course return total students of enrolled course.
   */
  function get_students_of_enrolled_course()
  {
      global $DB;
      $total_students_enrl_course = $DB->get_records_sql('SELECT
                                                          u.id, u.firstname, u.lastname, u.email
                                                          FROM {user_enrolments} ue
                                                            JOIN {user} u ON u.id = ue.userid
                                                            JOIN {enrol} e ON e.id = ue.enrolid
                                                          WHERE ue.userid = u.id
                                                       ORDER BY u.id
                                                          ');

      return $total_students_enrl_course;
  }



/**
 * This function count the total completed courses of any user
 *
 * @param int   $userid Variable
 * @return String $total_courses return total completed courses of any use.
 */
function count_complete_course($userid)
{
    global $DB;
    $total_courses = $DB->get_record_sql('SELECT count(course) as total_course
                                          FROM {course_completion_crit_compl}
                                          WHERE userid = ?', array($userid));
    $total_courses = $total_courses->total_course;

    return $total_courses;
}


/**
 * This function retrun the total number of enrolled courses
 *
 * @see enrol_get_users_courses()
 * @param int   $userid Moodle user id
 * @return String $count_course return total enrolled courses.
 */
function user_enrolled_courses($userid)
{
    global $CFG;
    $count_course = 0;
    $courses = enrol_get_users_courses($userid, false, 'id, shortname, showgrades');
    if ($courses)
    {
        foreach ($courses as $course)
        {
            $count_course+=1;
        }
    }

    return $count_course;
}


/**
 * This function tells how many enrolled courses criteria has not set yet of the user.
 *
 * @see enrol_get_users_courses()
 * @param int   $userid Moodle user id
 * @return String $count return number that tells total undefined course criteria of course.
 */
function count_course_criteria($userid)
{
    global $DB;
    $count = 0;
    $courses = enrol_get_users_courses($userid, false, 'id, shortname, showgrades');
    if ($courses)
    {
        $course_criteria_ns = array();
        foreach ($courses as $course)
        {
            $exist = $DB->record_exists('course_completion_criteria', array('course' => $course->id));
            if (!$exist)
            {
                $count++;
                $course_criteria_ns[] = $course->id;
            }
        }
    }

    return $count;
}


/**
 * This function return the course category.
 *
 * @param int   $id Moodle course id
 * @return String $module return category name of course.
 */
function module_name($id)
{
    global $DB;
    $module = $DB->get_record_sql('SELECT name FROM {course_categories}  WHERE id = ?', array($id));
    $module = format_string($module->name);
    return $module;
}


/**
 * This function return course name on the base of course id.
 *
 * @param int   $course Moodle course id
 * @return String $course Moodle course name.
 */
function course_name($id)
{
    global $DB;
    $course = $DB->get_record_sql('SELECT fullname  FROM {course} WHERE id = ?', array($id));
    $course = format_string($course->fullname);
    $course = $course . ' ' . get_string('course', 'block_course_status_4_teacher');

    return $course;
}


/**
 * This function return user detail in the form of table.
 *
 * @see report_get_custome_field($id, "Designation") This function return custom field Designation value on the bass userid
 * @param int   $id Moodle userid
 * @return String $table Moodle course name.
 */
function user_details($id)
{
    global $OUTPUT, $DB;
    // $user = new stdClass();
    $user = $DB->get_record('user', array('id' => $id));
    //$user->id = $id; // User Id.

    $user->picture = $OUTPUT->user_picture($user, array('size' => 100));
    // Fetch Data.
    $result = $DB->get_record_sql('SELECT
                                    concat(firstname," ",lastname) as name,department, timecreated as date
                                   FROM {user}
                                   WHERE id = ?', array($id));
    $table = '<table width="80%"><tr><td width="20%" style="vertical-align:middle;" rowspan="5">' . $user->picture . '</td></tr>
           <tr><td width="20%">' . get_string('name', 'block_course_status_4_teacher') . '</td><td>' . $result->name . '</td></tr>';

    $check_designatino_field = report_get_custome_field($id, "Designation"); // Custom Field name for designation is "Designation".
    if ($check_designatino_field != 0)
    {
        $table .='<tr><td>' . get_string('job_title', 'block_course_status_4_teacher') . '</td><td>' . format_string($check_designatino_field) . '</td></tr>';
    }

    $table .='<tr><td>' . get_string('joining_date', 'block_course_status_4_teacher') . '</td><td>' . userdate($result->date, get_string('strftimedate', 'core_langconfig')) . '</td></tr>
             </table>';
    return $table;
}


/**
 * This function return the value of custom field on the base of parameter field name.
 *
 * @param int    $userid Moodle userid
 * @param string $text custom field name
 * @return string Return field value.
 */
function report_get_custome_field($userid, $text)
{
    global $DB;
    $result = $DB->get_record_sql('SELECT table2.data as fieldvalue  FROM {user_info_field} as table1  join  {user_info_data} as table2
                                   on table1.id=table2.fieldid where table2.userid=? AND table1.name=?', array($userid, $text));

    $fieldvalue = $result['fieldvalue'];
    if (empty($fieldvalue))
    {
        return "0";
    }
    else
    {
        return format_string($result->fieldvalue);
    }
}



/**
 * This function return list of courses in which user enrolled.
 *
 * @param null
 * @return  Return table in which teacher can see the all enrolled courses list.
 */
function user_enrolled_courses_report()
{
    global $CFG;
    $count_course = 0;
    $courses = get_all_course();




    if ($courses)
    {
        $table = new html_table();
        $table->head = array(get_string('s_no', 'block_course_status_4_teacher'),
                             get_string('venue', 'block_course_status_4_teacher'),
                             get_string('module', 'block_course_status_4_teacher'),
                             get_string('course_name', 'block_course_status_4_teacher'),
                             get_string('count_of_selected_course', 'block_course_status_4_teacher'),
                             get_string('count_of_sel_cmpl_course', 'block_course_status_4_teacher'),
                             get_string('course_report', 'block_course_status_4_teacher'));
        $table->size = array('5%', '10%', '15%', '50%', '10', '10', '10');
        $table->width = "80%";

        $table->align = array('center', 'left', 'left', 'left', 'center', 'center', 'left');
        $table->data = array();
        $i = 0;


        foreach ($courses as $course)
        {
            $course_categ = $course->category;


            error_log('test 01: ');

            $row = array();
            $row[] = ++$i;
            $row[] = module_name($course_categ-1);
            $row[] = module_name($course_categ);
            $row[] = "<a href=" . $CFG->wwwroot . "/course/view.php?id=" . $course->id . ">" . course_name($course->id) . "</a>";
            $row[] = get_count_of_selected_course($course->id);
            $row[] = get_count_of_sel_compl_course($course->id);
            $row[] = "<a href=" . $CFG->wwwroot . "/report/completion/index.php?course=" . $course->id . ">" . '檢視報表' . "</a>";
            $table->data[] = $row;
        }
    }

    return $table;
}





/**
 * This function return list of courses in which user enrolled.
 *
 * @param null
 * @return  Return table in which teacher can see the all enrolled courses list.
 */
function inprogress_courses_list_report($uid)
{
    global $CFG;
    // session_start();
    // $courses = $_SESSION['$inprogress_course_list_row'];
    $courses = get_inprogress_courses($uid);

    error_log(' -- inprogress courses: ' . print_r($courses, true));


    if ($courses)
    {
        $table = new html_table();
        $table->head = array(get_string('s_no', 'block_course_status_4_teacher'),
                             get_string('venue', 'block_course_status_4_teacher'),
                             get_string('module', 'block_course_status_4_teacher'),
                             get_string('course_name', 'block_course_status_4_teacher'),
                             get_string('course_report', 'block_course_status_4_teacher'));
        $table->size = array('5%', '15%', '15%', '40%', '25%');
        $table->width = "80%";

        $table->align = array('center', 'left', 'left', 'left', 'left');
        $table->data = array();
        $i = 0;

        foreach ($courses as $course)
        {
            $course_categ = $course[category];

            $row = array();
            $row[] = ++$i;
            $row[] = module_name($course_categ-1);
            $row[] = module_name($course_categ);
            $row[] = "<a href=" . $CFG->wwwroot . "/course/view.php?id=" . $course[id] . ">" . course_name($course[id]) . "</a>";
            $row[] = "<a href=" . $CFG->wwwroot . "/report/completion/index.php?course=" . $course[id] . ">" . '檢視報表' . "</a>";
            $table->data[] = $row;
        }
    }
    else
    {
      $table = new html_table();
      $table->head = array(get_string('s_no', 'block_course_status_4_teacher'),
                           get_string('venue', 'block_course_status_4_teacher'),
                           get_string('module', 'block_course_status_4_teacher'),
                           get_string('course_name', 'block_course_status_4_teacher'),
                           get_string('course_report', 'block_course_status_4_teacher'));
      $table->size = array('5%', '15%', '15%', '40%', '25%');
      $table->width = "80%";

      $table->align = array('center', 'left', 'left', 'left', 'left');
      $table->data = array();
      $i = 0;

      $row = array();
      $row[] = $i;
      $row[] = $i;
      $row[] = $i;
      $row[] = $i;
      $row[] = $i;
      $table->data[] = $row;
    }

    return $table;
}



/**
 * @param null
 * @return  Return table in which teacher can see the all students list.
 */
function all_students_report()
{
    global $CFG;
    $students = get_all_students();


    if ($students)
    {
        $table = new html_table();
        $table->head = array(get_string('s_no', 'block_course_status_4_teacher'),
                             get_string('firstname', 'block_course_status_4_teacher'),
                             get_string('lastname', 'block_course_status_4_teacher'),
                             get_string('email', 'block_course_status_4_teacher'),
                             get_string('this_course_status', 'block_course_status_4_teacher'));
        $table->size = array('10%', '20%','5%', '35%', '30%');
        $table->width = "80%";

        $table->align = array('center', 'left', 'left', 'left');
        $table->data = array();
        $i = 0;

        foreach ($students as $student)
        {
            $row = array();
            $row[] = ++$i;
            $row[] = $student->firstname;
            $row[] = $student->lastname;
            $row[] = $student->email;
            $row[] = check_learn_status($student->id);
            $table->data[] = $row;
        }
    }

    return $table;
}



/**
 * @param null
 * @return  Return table in which teacher can see the all students of completed course list.
 */
function students_of_compl_courses_report()
{
    global $CFG;

    session_start();
    $compl_student_row = $_SESSION['$compl_all_course_students_row'];

    if ($compl_student_row)
    {
        $table = new html_table();
        $table->head = array(get_string('s_no', 'block_course_status_4_teacher'),
                             get_string('firstname', 'block_course_status_4_teacher'),
                             get_string('lastname', 'block_course_status_4_teacher'),
                             get_string('email', 'block_course_status_4_teacher'));
        $table->size = array('10%', '25%','10%', '55%');
        $table->width = "80%";

        $table->align = array('center', 'left', 'left', 'left');
        $table->data = array();
        $i = 0;

        foreach ($compl_student_row as $student)
        {
            $row = array();
            $row[] = ++$i;
            $row[] = $student->firstname;
            $row[] = $student->lastname;
            $row[] = $student->email;
            $table->data[] = $row;
        }
    }

    return $table;
}



/**
 * @param null
 * @return  Return table in which teacher can see the all students of in-progress course list.
 */
function students_of_in_progress_courses_report()
{
    global $CFG;

    session_start();
    $in_progress_course_row = $_SESSION['$in_progress_course_students_row'];


    if ($in_progress_course_row)
    {
        $table = new html_table();
        $table->head = array(get_string('s_no', 'block_course_status_4_teacher'),
                             get_string('firstname', 'block_course_status_4_teacher'),
                             get_string('lastname', 'block_course_status_4_teacher'),
                             get_string('email', 'block_course_status_4_teacher'),
                             get_string('in_progress_courses', 'block_course_status_4_teacher'));
        $table->size = array('10%', '20%','5%', '35%', '30%');
        $table->width = "80%";

        $table->align = array('center', 'left', 'left', 'left', 'left');
        $table->data = array();
        $i = 0;

error_log(' . in_progress_course_row: ' . print_r($in_progress_course_row, true));

        foreach ($in_progress_course_row as $student)
        {
            $row = array();
            $row[] = ++$i;
            $row[] = $student->firstname;
            $row[] = $student->lastname;
            $row[] = $student->email;
            $row[] = "<a href='" . $CFG->wwwroot . "/blocks/course_status_4_teacher/view.php?viewpage=6&param=". $student->id . "'>" . '檢視列表' . "</a>";
            $table->data[] = $row;
        }
    }

    return $table;
}



/**
 * @param null
 * @return  Return table in which teacher can see the all students of enrolled course list.
 */
function students_of_enrolled_courses_report()
{
    global $CFG;

    session_start();
    $enrolled_course_row = $_SESSION['$enrolled_course_of_students_row'];

    if ($enrolled_course_row)
    {
        $table = new html_table();
        $table->head = array(get_string('s_no', 'block_course_status_4_teacher'),
                             get_string('firstname', 'block_course_status_4_teacher'),
                             get_string('lastname', 'block_course_status_4_teacher'),
                             get_string('email', 'block_course_status_4_teacher'),
                             get_string('this_course_status', 'block_course_status_4_teacher'));

        $table->size = array('10%', '20%','5%', '35%', '30%');
        $table->width = "80%";

        $table->align = array('center', 'left', 'left', 'left', 'left');
        $table->data = array();
        $i = 0;

        foreach ($enrolled_course_row as $student)
        {
            $row = array();
            $row[] = ++$i;
            $row[] = $student->firstname;
            $row[] = $student->lastname;
            $row[] = $student->email;
            $row[] = check_learn_status($student->id);
            $table->data[] = $row;
        }
    }

    return $table;
}



/**
 * @param user id
 * @return  Return learning status string.
 */
function check_learn_status($userid)
{
  session_start();
  $inprogress_course_row = $_SESSION['$in_progress_course_students_row'];
  $compl_course_row = $_SESSION['$compl_all_course_students_row'];

  $compl_array = json_decode(json_encode($compl_course_row), True);
  $key = searchUserId($userid, $compl_array);

  if ($key === null)
  {
      $inprogress_array = json_decode(json_encode($inprogress_course_row), True);
      $key = searchUserId($userid, $inprogress_array);
      if ($key === null)
      {
          $the_course_status = get_string('course_status_not_enroll', 'block_course_status_4_teacher');
      }
      else
      {
          $the_course_status = get_string('course_status_inprogress', 'block_course_status_4_teacher');
      }
  }
  else
  {
      $the_course_status = get_string('course_status_compl', 'block_course_status_4_teacher');
  }

  return $the_course_status;
}



function search_inprogress_course($inProgCourse, $complCourse)
{
    $inProgCourseArr = array();
    $errors = array_filter($complCourse);

    if (!empty($errors))
    {
      error_log(' . comply course not empty');
      foreach ($inProgCourse as $theCourse => $tmpCourse)
      {
          foreach($complCourse as $theComplCourse => $tmpComplCourse)
          {
             if ($tmpComplCourse['id'] != $tmpCourse['id'])
             {
                  $inProgCourseArr[] = $tmpCourse;
             }
          }
      }
    }
    else
    {
        error_log(' . complCourse is empty');
        foreach ($inProgCourse as $theCourse => $tmpCourse)
        {
            $inProgCourseArr[] = $tmpCourse;
        }
    }

    return $inProgCourseArr;
}


/**
 * @param userid and course status array.
 * @return  Return null or key value.
 */
function searchUserId($id, $array)
{
   foreach ($array as $key => $val)
   {
       if ($val['id'] === $id)
       {
           return $key;
       }
   }

   return null;
}
