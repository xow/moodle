<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 - 2013 Totara Learning Solutions LTD
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
 * @package report
 * @subpackage reportbuilder
 */

/**
 * Formslib template for creating activity groups
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir . '/formslib.php');

class report_builder_new_group_form extends moodleform {

    /**
     * Definition for the new group form
     */
    function definition() {
        global $DB;
        $mform =& $this->_form;

        // get all feedback activities
        $sql = "SELECT f.id, f.name, c.fullname
            FROM {feedback} f
            LEFT JOIN {course} c ON c.id = f.course
            ORDER BY c.fullname, f.name";
        $activities = $DB->get_records_sql($sql);
        if (!$activities) {
            $activities = array();
        }

        // group activities by course
        $grouped_activities = array();
        foreach ($activities as $activity) {
            $course = ($activity->fullname === null) ?
                get_string('coursenotset', 'report_reportbuilder') : $activity->fullname;
            $grouped_activities[$course][$activity->id] = $activity->name;
        }

        $mform->addElement('header', 'general', get_string('newgroup', 'report_reportbuilder'));

        // get all official tags
        $tags = $DB->get_records_menu('tag', array('tagtype' => 'official'), 'id', 'id, name');
        if (!$tags) {
            $mform->addElement('html', html_writer::tag('p', get_string('notags', 'report_reportbuilder')));
            return;
        }

        $mform->addElement('text', 'name', get_string('groupname', 'report_reportbuilder'),
            'maxlength="255"');
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required');
        $mform->addHelpButton('name', 'reportbuildergroupname', 'report_reportbuilder');

        $mform->addElement('select', 'assignvalue', get_string('grouptag',
            'report_reportbuilder'), $tags);
        // invalid if not set
        $mform->addHelpButton('assignvalue', 'reportbuildergrouptag', 'report_reportbuilder');

        // code to limit width of pulldown but expand when viewed
        // required for IE compatibility
        $attributes = array(
            'class' => 'totara-limited-width',
            'onMouseDown' =>
                "if (document.all) this.className = 'totara-expanded-width';",
            'onBlur' => "if (document.all) this.className = 'totara-limited-width';",
            'onChange' => "if (document.all) this.className = 'totara-limited-width';"
        );
        // create a pulldown with activities grouped by course
        $baseselect =& $mform->addElement('selectgroups', 'baseitem',
            get_string('basedon', 'report_reportbuilder'), $grouped_activities, $attributes);
        $mform->addHelpButton('baseitem', 'reportbuilderbaseitem', 'totara_reportbuilder');

        // other assignment types (like manual) may be added later
        $mform->addElement('hidden', 'assigntype', 'tag');
        // other group types may be added later
        $mform->addElement('hidden', 'preproc', 'feedback_questions');

        $this->add_action_buttons();
    }
}

