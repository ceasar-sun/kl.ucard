<?php

/* Course Status for teacher Block
 * The plugin shows the number and list of courses info.
 * @package blocks
 * @author: Jonathan Lin
 */

defined('MOODLE_INTERNAL') || die();
$capabilities = array(
    'block/course_status_info:myaddinstance' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        #),
        # 'legacy' => array(
            'student' => CAP_PREVENT,
            'user' => CAP_PREVENT,
            'guest' => CAP_PREVENT
        )
        #'clonepermissionsfrom' => 'moodle/my:manageblocks'
    ),

    'block/course_status_info:addinstance' => array(
        'riskbitmask' => RISK_SPAM | RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'manager'  => CAP_ALLOW,
            ),
        'legacy' => array(
            'student' => CAP_PREVENT,
            'user' => CAP_PREVENT,
            'guest' => CAP_PREVENT
        ),
        'clonepermissionsfrom' => 'moodle/site:manageblocks'
    ),
        'block/course_status_info:view' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_BLOCK,
        'archetypes' => array(
            'editingteacher' => CAP_ALLOW,
            'coursecreator' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'manager'  => CAP_ALLOW,
	    ),
        'legacy' => array(
            'student' => CAP_PREVENT,
            'user' => CAP_PREVENT,
            'guest' => CAP_PREVENT
        ),
        'clonepermissionsfrom' => 'moodle/site:manageblocks'
    ),
);
