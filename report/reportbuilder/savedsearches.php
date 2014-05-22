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
 * Page containing list of saved searches for this report
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot . '/report/reportbuilder/lib.php');
require_once('report_forms.php');

require_login();

$id = optional_param('id', null, PARAM_INT); // id for report
$sid = optional_param('sid', null, PARAM_INT); // id for saved search
$d = optional_param('d', false, PARAM_BOOL); // delete saved search?
$confirm = optional_param('confirm', false, PARAM_BOOL); // confirm delete
$returnurl = $CFG->wwwroot . '/report/reportbuilder/savedsearches.php?id=' . $id;

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/report/reportbuilder/savedsearches.php', array('id' => $id, 'sid' => $sid));
$PAGE->set_totara_menu_selected('myreports');

$output = $PAGE->get_renderer('report_reportbuilder');

$report = new reportbuilder($id);
if (!$report->is_capable($id)) {
    print_error('nopermission', 'report_reportbuilder');
}

if ($d && $confirm) {
    // delete an existing saved search
    if (!confirm_sesskey()) {
        totara_set_notification(get_string('error:bad_sesskey', 'report_reportbuilder'), $returnurl);
    }

    $transaction = $DB->start_delegated_transaction();

    $DB->delete_records('report_builder_saved', array('id' => $sid));
    $DB->delete_records('report_builder_schedule', array('savedsearchid' => $sid));

    $transaction->allow_commit();

    totara_set_notification(get_string('savedsearchdeleted', 'report_reportbuilder'), $returnurl, array('class' => 'notifysuccess'));

} else if ($d) {
    $fullname = $report->fullname;
    $pagetitle = format_string(get_string('savesearch', 'report_reportbuilder') . ': ' . $fullname);

    $PAGE->set_title($pagetitle);
    $PAGE->set_button($report->edit_button());
    $PAGE->navbar->add(get_string('report', 'report_reportbuilder'));
    $PAGE->navbar->add($fullname);
    $PAGE->navbar->add(get_string('savedsearches', 'report_reportbuilder'));
    echo $output->header();

    echo $output->heading(get_string('savedsearches', 'report_reportbuilder'), 1);
    //is this saved search being used in any scheduled reports?
    if ($scheduled = $DB->get_records('report_builder_schedule', array('savedsearchid' => $sid))) {
        //display a message and list of scheduled reports using this saved search
        ob_start();
        totara_print_scheduled_reports(false, false, array("rbs.savedsearchid = ?", array($sid)));
        $out = ob_get_contents();
        ob_end_clean();

        $messageend = get_string('savedsearchinscheduleddelete', 'report_reportbuilder', $out) . str_repeat(html_writer::empty_tag('br'), 2);
    } else {
        $messageend = '';
    }

    $messageend .= get_string('savedsearchconfirmdelete', 'report_reportbuilder');
    // prompt to delete
    echo $output->confirm($messageend, "savedsearches.php?id={$id}&amp;sid={$sid}&amp;d=1&amp;confirm=1&amp;" .
        "sesskey={$USER->sesskey}", $returnurl);

    echo $output->footer();
    exit;
}

$fullname = $report->fullname;
$pagetitle = format_string(get_string('savesearch', 'report_reportbuilder') . ': ' . $fullname);

$PAGE->set_title($pagetitle);
$PAGE->set_button($report->edit_button());
$PAGE->navbar->add(get_string('report', 'report_reportbuilder'));
$PAGE->navbar->add($fullname);
$PAGE->navbar->add(get_string('savedsearches', 'report_reportbuilder'));
echo $output->header();

echo $output->view_report_link($report->report_url());
echo $output->heading(get_string('savedsearches', 'report_reportbuilder'));

$searches = $DB->get_records('report_builder_saved', array('userid' => $USER->id, 'reportid' => $id), 'name');
if (!empty($searches)) {
    echo $output->saved_searches_table($searches, $report);
} else {
    echo html_writer::tag('p', get_string('error:nosavedsearches', 'report_reportbuilder'));
}

echo $output->footer();
