<?php

/* Course Status for teacher Block
 * The plugin shows the number and list of courses info.
 * @package blocks
 * @author: Jonathan Lin
 */

// require_once('lib.php');
require_once($CFG->dirroot.'/blocks/course_status_4_teacher/lib.php');


/**
 * This class shows the content in block through calling lib.php function.
 */
class block_course_status_4_teacher extends block_base
{

    public function init()
    {
        $this->title = get_string('course_status_info', 'block_course_status_4_teacher');
    }

    /**
     * Where to add the block
     *
     * @return boolean
     * */
    public function applicable_formats()
    {
        return array('all' => true);
    }

    /**
     * Gets the contents of the block (course view)
     *
     * @return object An object with the contents
     * */
    public function isguestuser($user = null)
    {
        return false;
    }


    public function get_content()
    {
        global $CFG, $OUTPUT, $USER, $DB;
        if ($this->content !== null)
        {
            return $this->content;
        }


        $this->content = new stdClass;
        if ($CFG->enablecompletion)
        {

            // -- variables -- begin
            $all_courses = count(get_all_course());

            $all_students = get_all_students();
            $count_all_students = count($all_students);
            $count_students_compl_course = 0;
            $all_students_of_enrolled_course = get_students_of_enrolled_course();

            $compl_all_course_students_row = array();
            // -- variables -- end




            error_log(' -- block test v.154 -- ');




            // -- Count students of all Course compeleted -- begin
            foreach($all_students_of_enrolled_course as $perStudent)
            {
                $compl_courses = $DB->get_record_sql('SELECT
                                                      count(course) as total_course
                                                      FROM {course_completion_crit_compl} c
                                                      WHERE c.userid = ?', array($perStudent->id));

                $total_compl_courses = $compl_courses->total_course;

                if ($total_compl_courses == $all_courses)
                {
                    $count_students_compl_course ++;
                    $compl_all_course_students_row[] = $perStudent;
                }
                else
                {
                    $in_progress_courses_students_row[] = $perStudent;
                }
            }
            // -- Count students of all Course compeleted -- end





            // $ar2 = json_decode(json_encode($in_progress_courses_students_row), True);
            // $key = array_search(22, array_column($ar2, 'id'));

            // error_log(' -- compl: ' . print_r($ar2, true));
            // if (empty($key))
            // {
            //     error_log(' -- key2: ' . print_r($key, true));
            // }



            session_start();
            $_SESSION['$enrolled_course_of_students_row'] = $all_students_of_enrolled_course;
            $_SESSION['$in_progress_course_students_row'] = $in_progress_courses_students_row;
            $_SESSION['$compl_all_course_students_row'] = $compl_all_course_students_row;



            // -- Count of Course inprogress -- begin
            $count_inprogress_courses = abs(count($all_students_of_enrolled_course) - $count_students_compl_course);


            // link of courses Info
            if ($all_courses > 0)
            {
                $link_enrolled_courses = "<u><a href='" . $CFG->wwwroot . "/blocks/course_status_4_teacher/view.php?viewpage=2&param=0'>" .
                        $all_courses . "</a></u>";
            }
            else
            {
                $link_enrolled_courses = $all_courses;
            }

            // link of students info
            if ($count_all_students > 0)
            {
                $link_count_all_students = "<u><a href='" . $CFG->wwwroot . "/blocks/course_status_4_teacher/view.php?viewpage=1&param=0'>" .
                        $count_all_students . "</a></u>";
            }
            else
            {
                $link_count_all_students = $count_all_students;
            }

            // link of enrolled course
            if (count($all_students_of_enrolled_course) > 0)
            {
                $link_all_students_of_enrol_course = "<u><a href='" . $CFG->wwwroot . "/blocks/course_status_4_teacher/view.php?viewpage=5&param=0'>" .
                        count($all_students_of_enrolled_course) . "</a></u>";
            }
            else
            {
                $link_all_students_of_enrol_course = $count_all_students;
            }

            // link of in-progress course
            if ($count_inprogress_courses > 0)
            {
                $link_in_progress_courses_students = "<u><a href='" . $CFG->wwwroot . "/blocks/course_status_4_teacher/view.php?viewpage=3&param=0'>" .
                        $count_inprogress_courses . "</a></u>";
            }
            else
            {
                $link_in_progress_courses_students = $count_inprogress_courses;
            }

            // link of compl_all_course_students
            if (count($compl_all_course_students_row) > 0)
            {
                $link_compl_all_courses_of_students = "<u><a href='" . $CFG->wwwroot . "/blocks/course_status_4_teacher/view.php?viewpage=4&param=0'>" .
                        count($compl_all_course_students_row) . "</a></u>";
            }
            else
            {
                $link_compl_all_courses_of_students = count($compl_all_course_students_row);
            }


            // caculate percentage of inprogress and completed courses.
            /*
            $percent_in_progress = number_format(((count($count_inprogress_courses)/$count_all_students) * 100), 0);
            $percent_courses_compl = number_format(((count($compl_all_course_students_row)/$count_all_students) * 100), 0);
            */

            $this->content->text .= get_string('total_courses', 'block_course_status_4_teacher') . " :	<b>" . $link_enrolled_courses . "</b><br>";
            $this->content->text .= get_string('total_students', 'block_course_status_4_teacher') . " : <b>" . $link_count_all_students . "</b><br>";
            $this->content->text .= get_string('enrolled_course_students', 'block_course_status_4_teacher') . " : <b>" . $link_all_students_of_enrol_course . "</b><br>";
            $this->content->text .= get_string('inprogress_courses', 'block_course_status_4_teacher') . " : <b>" . $link_in_progress_courses_students . "</b><br>";
            $this->content->text .= get_string('completed_coursecriteria', 'block_course_status_4_teacher') . " : <b>" . $link_compl_all_courses_of_students . "</b><br>";
        }
        else
        {
            $this->content->text .= get_string('coursecompletion_setting', 'block_course_status_4_teacher');
        }

        return $this->content;
    }
}
