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
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/report/reportbuilder/lib.php');
require_once($CFG->dirroot . '/report/reportbuilder/report_forms.php');

admin_externalpage_setup('rbglobalsettings');

$returnurl = $CFG->wwwroot . "/report/reportbuilder/globalsettings.php";
$output = $PAGE->get_renderer('report_reportbuilder');

// form definition
$mform = new report_builder_global_settings_form();

// form results check
if ($mform->is_cancelled()) {
    redirect($returnurl);
}
if ($fromform = $mform->get_data()) {

    if (empty($fromform->submitbutton)) {
        totara_set_notification(get_string('error:unknownbuttonclicked', 'report_reportbuilder'), $returnurl);
    }

    update_global_settings($fromform);

    totara_set_notification(get_string('globalsettingsupdated', 'report_reportbuilder'), $returnurl, array('class' => 'notifysuccess'));
}

echo $output->header();

echo $output->container_start('reportbuilder-navlinks');
echo $output->view_all_reports_link();
echo $output->container_end();

echo $output->heading(get_string('reportbuilderglobalsettings', 'report_reportbuilder'));

// display the form
$mform->display();

echo $output->footer();

/**
 * Update global report builder settings
 *
 * @param object $fromform Moodle form object containing global setting changes to apply
 *
 * @return True if settings could be successfully updated
 */
function update_global_settings($fromform) {
    global $REPORT_BUILDER_EXPORT_OPTIONS;

    $exportoptions = 0;
    foreach ($REPORT_BUILDER_EXPORT_OPTIONS as $option => $code) {
        $checkboxname = 'export' . $option;
        if (isset($fromform->$checkboxname) && $fromform->$checkboxname == 1) {
            $exportoptions += $code;
        }
    }
    set_config('exportoptions', $exportoptions, 'reportbuilder');

    // Export to file system checkbox option.
    $exporttofilesystemoption = 0;
    if (isset($fromform->exporttofilesystem)) {
        $exporttofilesystemoption = $fromform->exporttofilesystem;
    }
    set_config('exporttofilesystem', $exporttofilesystemoption, 'reportbuilder');

    // Export to file system path textbox.
    $exportfilesystempath = '';
    if (isset($fromform->exportfilesystempath)) {
        $exportfilesystempath = $fromform->exportfilesystempath;
    }
    set_config('exporttofilesystempath', $exportfilesystempath, 'reportbuilder');

    $financialyear = 'financialyear';
    $newconfig = $fromform->$financialyear;
    set_config('financialyear', date("dm", mktime(0, 0, 0, $newconfig["F"], $newconfig["d"], 0)), 'reportbuilder');

    return true;
}
