<?php

/* Course Status for teacher Block
 * The plugin shows the number and list of courses info.
 * @package blocks
 * @author: Jonathan Lin
 */


require_once("{$CFG->libdir}/formslib.php");
require_once("lib.php");

/**
 * This class contains function display_report which return user course name, course grade & completion date.
 */
class course_status_form extends moodleform
{

    public function definition()
    {
        return false;
    }

    public function display_report()
    {
        global $DB, $OUTPUT, $CFG, $USER;
        $userid = $USER->id;
        // Page parameters.
        $page = optional_param('page', 0, PARAM_INT);
        $perpage = optional_param('perpage', 30, PARAM_INT);    // How many record per page.
        $sort = optional_param('sort', 'firstname', PARAM_ALPHA);
        $dir = optional_param('dir', 'DESC', PARAM_ALPHA);
        $sql = "SELECT course, gradefinal, timecompleted as dates FROM {course_completion_crit_compl} where userid = " . $userid;
        $changescount = $DB->count_records_sql($sql, array($userid));
        $columns = array('s_no' => get_string('s_no', 'block_course_status_4_teacher'),
            'course_name' => get_string('course_name', 'block_course_status_4_teacher'),
            'course_comp_date' => get_string('course_comp_date', 'block_course_status_4_teacher'),
            'grade' => get_string('grade', 'block_course_status_4_teacher'),
        );

        $hcolumns = array();
        if (!isset($columns[$sort]))
        {
            $sort = 's_no';
        }

        foreach ($columns as $column => $strcolumn)
        {
            if ($sort != $column)
            {
                $columnicon = '';
                if ($column == 's_no')
                {
                    $columndir = 'DESC';
                }
                else
                {
                    $columndir = 'ASC';
                }
            }
            else
            {
                $columndir = $dir == 'ASC' ? 'DESC' : 'ASC';
                if ($column == 's_no')
                {
                    $columnicon = $dir == 'ASC' ? 'up' : 'down';
                }
                else
                {
                    $columnicon = $dir == 'ASC' ? 'down' : 'up';
                }

                $columnicon = " <img src=\"" . $OUTPUT->pix_url('t/' . $columnicon) . "\" alt=\"\" />";
            }

            $hcolumns[$column] = "<a href=\"view.php?viewpage=1&sort=$column&amp;dir=$columndir&amp;page=$page&amp;perpage=$perpage\">" . $strcolumn . "</a>$columnicon";
        }

        $baseurl = new moodle_url('view.php', array('sort' => $sort, 'dir' => $dir, 'perpage' => $perpage));
        echo $OUTPUT->paging_bar($changescount, $page, $perpage, $baseurl);
        $table = new html_table();
        $table->head = array(get_string('s_no', 'block_course_status_4_teacher'), get_string('course_name', 'block_course_status_4_teacher'), get_string('course_comp_date', 'block_course_status_4_teacher'), get_string('grade', 'block_course_status_4_teacher'));
        $table->size = array('15%', '40%', '30%', '15%');
        $table->width = "80%";
        $table->align = array('center', 'left', 'center', 'center');
        $table->data = array();
        $orderby = "$sort $dir";
        $rs = $DB->get_recordset_sql($sql, array(), $page * $perpage, $perpage);
        $i = 0;

        foreach ($rs as $log)
        {
            $row = array();
            $row[] = ++$i;
            $row[] = course_name($log->course);
            $row[] = userdate($log->dates, get_string('strftimedate', 'core_langconfig'));
            $row[] = round($log->gradefinal) . '%';
            $table->data[] = $row;
        }

        return $table;
    }
}
