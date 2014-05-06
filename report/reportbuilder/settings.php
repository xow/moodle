<?php // $Id$
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
 * @package report
 * @subpackage reportbuilder
 */

/**
 * Add reportbuilder administration menu settings
 */

$ADMIN->add('reports',
    new admin_category('report_reportbuilder',
    get_string('reportbuilder','report_reportbuilder')),
    'comments'
);
// add links to report builder reports
$ADMIN->add('report_reportbuilder',
    new admin_externalpage('rbmanagereports',
        get_string('managereports','report_reportbuilder'),
        "$CFG->wwwroot/report/reportbuilder/index.php",
        array('report/reportbuilder:managereports')
    )
);
$ADMIN->add('report_reportbuilder',
    new admin_externalpage('rbglobalsettings',
        get_string('globalsettings','report_reportbuilder'),
        "$CFG->wwwroot/report/reportbuilder/globalsettings.php",
        array('report/reportbuilder:managereports')
    )
);
$ADMIN->add('report_reportbuilder',
    new admin_externalpage('rbactivitygroups',
        get_string('activitygroups','report_reportbuilder'),
        "$CFG->wwwroot/report/reportbuilder/groups.php",
        array('report/reportbuilder:managereports'),
        true
    )
);
