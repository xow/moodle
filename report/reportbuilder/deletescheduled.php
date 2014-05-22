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
 * @author Alastair Munro <alastair.munro@totaralms.com>
 * @package report
 * @subpackage reportbuilder
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot . '/totara/core/lib.php');

require_login();

// Get params
$id = required_param('id', PARAM_INT); //ID
$confirm = optional_param('confirm', '', PARAM_INT); // Delete confirmation hash

$PAGE->set_context(context_user::instance($USER->id));
$PAGE->set_url('/report/reportbuilder/deletescheduled.php', array('id' => $id));
$PAGE->set_totara_menu_selected('myreports');

if (!$report = $DB->get_record('report_builder_schedule', array('id' => $id))) {
    print_error('error:invalidreportscheduleid', 'report_reportbuilder');
}

$reportname = $DB->get_field('report_builder', 'fullname', array('id' => $report->reportid));

$returnurl = new moodle_url('/my/reports.php');
$deleteurl = new moodle_url('/report/reportbuilder/deletescheduled.php', array('id' => $report->id, 'confirm' => '1', 'sesskey' => $USER->sesskey));

if ($confirm == 1) {
    if (!confirm_sesskey()) {
        print_error('confirmsesskeybad', 'error');
    } else {
        $DB->delete_records('report_builder_schedule', array('id' => $report->id));
        add_to_log(SITEID, 'reportbuilder', 'delete', "scheduled.php?id=$report->id", "$reportname (ID $report->id)");

        totara_set_notification(get_string('deletedscheduledreport', 'report_reportbuilder', format_string($reportname)),
                                $returnurl, array('class' => 'notifysuccess'));
    }
}
/// Display page
$PAGE->set_title(' ');
$PAGE->set_heading(' ');
echo $OUTPUT->header();

if (!$confirm) {
    $strdelete = get_string('deletecheckschedulereport', 'report_reportbuilder');
    echo $OUTPUT->confirm($strdelete . str_repeat(html_writer::empty_tag('br'), 2) . format_string($reportname), $deleteurl, $returnurl);

    echo $OUTPUT->footer();
    exit;
}

echo $OUTPUT->footer();
