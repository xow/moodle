<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010-2013 Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Simon Coggins <simon.coggins@totaralms.com>
 * @package totara
 * @subpackage reportbuilder
 */

require_once($CFG->dirroot.'/totara/core/db/utils.php');

/**
 * Local database upgrade script
 *
 * @param   integer $oldversion Current (pre-upgrade) local db version timestamp
 * @return  boolean $result
 */
function xmldb_report_reportbuilder_upgrade($oldversion = 2014011602) {
    global $CFG, $DB;
    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes
    
    $result = true;
    
    if ($oldversion < 2014011603) {

        // Define table course_info_field to be created.
        $table = new xmldb_table('course_info_field');

        // Adding fields to table course_info_field.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('fullname', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('shortname', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('datatype', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('categoryid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('hidden', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('locked', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('required', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('forceunique', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, null);
        $table->add_field('defaultdata', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param1', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param2', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param3', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param4', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param5', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table course_info_field.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for course_info_field.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Core savepoint reached.
        upgrade_plugin_savepoint(true, 2014011603, 'report', 'reportbuilder');
    }
    
    if ($oldversion < 2014011602) {

        // Define table pos_type_info_field to be created.
        $table = new xmldb_table('pos_type_info_field');

        // Adding fields to table pos_type_info_field.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('shortname', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('typeid', XMLDB_TYPE_INTEGER, '20', null, null, null, null);
        $table->add_field('datatype', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('hidden', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('locked', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('required', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('forceunique', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('defaultdata', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param1', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param2', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param3', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param4', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param5', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('fullname', XMLDB_TYPE_CHAR, '1024', null, null, null, null);

        // Adding keys to table pos_type_info_field.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('postypeinfofiel_typ_fk', XMLDB_KEY_FOREIGN, array('typeid'), 'pos_type', array('id'));

        // Conditionally launch create table for pos_type_info_field.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Hierarchy savepoint reached.
        upgrade_plugin_savepoint(true, 2014011602, 'report', 'reportbuilder');
    }
    
    if ($oldversion < 2014011601) {

        // Define table org_type_info_field to be created.
        $table = new xmldb_table('org_type_info_field');

        // Adding fields to table org_type_info_field.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('shortname', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('typeid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('datatype', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('hidden', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('locked', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('required', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('forceunique', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('defaultdata', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param1', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param2', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param3', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param4', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param5', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('fullname', XMLDB_TYPE_CHAR, '1024', null, null, null, null);

        // Adding keys to table org_type_info_field.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('orgtypeinfofield_typ_fk', XMLDB_KEY_FOREIGN, array('typeid'), 'org_type', array('id'));

        // Conditionally launch create table for org_type_info_field.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Hierarchy savepoint reached.
        upgrade_plugin_savepoint(true, 2014011601, 'report', 'reportbuilder');
    }
    
    //ADD FIELDS FOR TOTARA_HIERARCHY   
    if ($oldversion < 2014011600) {

         // Define table comp_type_info_field to be created.
        $table = new xmldb_table('comp_type_info_field');

        // Adding fields to table comp_type_info_field.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('shortname', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('typeid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('datatype', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('hidden', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('locked', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('required', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('forceunique', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('defaultdata', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param1', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param2', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param3', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param4', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('param5', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('fullname', XMLDB_TYPE_CHAR, '1024', null, null, null, null);

        // Adding keys to table comp_type_info_field.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('comptypeinfofiel_typ_fk', XMLDB_KEY_FOREIGN, array('typeid'), 'comp_type', array('id'));

        // Conditionally launch create table for comp.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Hierarchy savepoint reached.
        upgrade_plugin_savepoint(true, 2014011600, 'report', 'reportbuilder');
    }

    return $result;
}
