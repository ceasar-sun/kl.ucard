<?php

/**
 * @Func:       Upgrade code for install
 * @License:    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @Author:     Thomas, Ceasar 
 * @Note:       First released in 2016/7/15              
 *
*/

defined('MOODLE_INTERNAL') || die();

/**
 * upgrade this assignment instance - this function could be skipped but it will be needed later
 * @param int $oldversion The old version of the assign module
 * @return bool
 */
function xmldb_local_courselevel_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2012102912) {

        // Add "latest" column to submissions table to mark the latest attempt.
        $table = new xmldb_table('courselevel');
        $field = new xmldb_field('location', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'location');

        // Conditionally launch add field latest.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }


        upgrade_mod_savepoint(true, 2012102912, 'course');
    }
    return true;
}
