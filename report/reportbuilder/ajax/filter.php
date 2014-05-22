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
 * @author Eugene Venter <eugene@catalyst.net.nz>
 * @package report
 * @subpackage reportbuilder
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once($CFG->dirroot.'/report/reportbuilder/lib.php');

/// Check access
require_sesskey();
require_login();
require_capability('report/reportbuilder:managereports', context_system::instance());

/// Get params
$action = required_param('action', PARAM_ALPHA);
$reportid = required_param('id', PARAM_INT);

switch ($action) {
    case 'add' :
        $filter = required_param('filter', PARAM_TEXT);
        $filtername = optional_param('filtername', '', PARAM_TEXT);
        $customname = optional_param('customname', 0, PARAM_BOOL);
        $advanced = optional_param('advanced', 0, PARAM_BOOL);

        $filter = explode('-', $filter);
        $ftype = $filter[0];
        $fvalue = $filter[1];

        /// Prevent duplicates
        $params = array('reportid' => $reportid, 'type' => $ftype, 'value' => $fvalue,
                        'customname' => $customname, 'filtername' => $filtername);
        if ($DB->record_exists('report_builder_filters', $params)) {
            echo false;
            exit;
        }

        /// Save filter
        $todb = new stdClass();
        $todb->reportid = $reportid;
        $todb->type = $ftype;
        $todb->value = $fvalue;
        $todb->advanced = $advanced;
        $todb->customname = $customname;
        $todb->filtername = $filtername;
        $sortorder = $DB->get_field('report_builder_filters', 'MAX(sortorder) + 1', array('reportid' => $reportid));
        if (!$sortorder) {
            $sortorder = 1;
        }
        $todb->sortorder = $sortorder;
        $id = $DB->insert_record('report_builder_filters', $todb);
        reportbuilder_set_status($reportid);

        echo $id;
        break;
    case 'delete':
        $fid = required_param('fid', PARAM_INT);

        if ($filter = $DB->get_record('report_builder_filters', array('id' => $fid))) {
            $DB->delete_records('report_builder_filters', array('id' => $fid));
            reportbuilder_set_status($reportid);
            echo json_encode((array)$filter);
        } else {
            echo false;
        }
        break;
    case 'movedown':
    case 'moveup':
        $fid = required_param('fid', PARAM_INT);

        $operator = ($action == 'movedown') ? '>' : '<';
        $sortorder = ($action == 'movedown') ? 'ASC' : 'DESC';

        $filter = $DB->get_record('report_builder_filters', array('id' => $fid));
        $sql = "SELECT * FROM {report_builder_filters}
            WHERE reportid = ? AND sortorder $operator ?
            ORDER BY sortorder $sortorder";
        if (!$sibling = $DB->get_record_sql($sql, array($reportid, $filter->sortorder), IGNORE_MULTIPLE)) {
            echo false;
            exit;
        }

        $transaction = $DB->start_delegated_transaction();

        $todb = new stdClass();
        $todb->id = $filter->id;
        $todb->sortorder = $sibling->sortorder;
        $DB->update_record('report_builder_filters', $todb);

        $todb = new stdClass();
        $todb->id = $sibling->id;
        $todb->sortorder = $filter->sortorder;
        $DB->update_record('report_builder_filters', $todb);
        reportbuilder_set_status($reportid);

        $transaction->allow_commit();

        echo "1";
        break;
    default:
        echo '';
        break;
}
