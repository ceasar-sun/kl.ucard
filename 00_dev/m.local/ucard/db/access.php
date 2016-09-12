<?php

/**
 * @Func:	Capability definitions for the ucard module
 * @License:    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @Author:     Thomas, Ceasar 
 * @Note:       First released in 2016/7/15              
 *
*/

defined('MOODLE_INTERNAL') || die();

// Modify capabilities as needed and remove this comment.
$capabilities = array(
    'local/ucard:change' => array(
        'captype'      => 'write',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes'   => array(
            'editingteacher' => CAP_ALLOW,
	    'coursecreator' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'manager'  => CAP_ALLOW
            //'user' => CAP_ALLOW
        )
    ),
    'local/ucard:view' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes'   => array(
            //'editingteacher' => CAP_ALLOW,
	    //'coursecreator' => CAP_ALLOW,
            //'teacher' => CAP_ALLOW,
            //'manager' => CAP_ALLOW
            'user' => CAP_ALLOW
        )
    ),
    'local/ucard:viewlog' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_SYSTEM,
        'archetypes'   => array(
	    'user'  =>CAP_ALLOW,
        )
    ),
);
