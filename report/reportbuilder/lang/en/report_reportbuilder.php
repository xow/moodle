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

$string['abstractmethodcalled'] = 'Abstract method {$a} called - must be implemented';
$string['access'] = 'Access';
$string['accessbyrole'] = 'Restrict access by role';
$string['accesscontrols'] = 'Access Controls';
$string['activeonly'] = 'Active users only';
$string['activeuser'] = 'Active user';
$string['activities'] = 'Activities';
$string['activitygroupdesc'] = 'Activity groups let you define sets of activites for the purpose of site-wide reporting.';
$string['activitygroupingx'] = 'Activity grouping \'{$a}\'';
$string['activitygroupnotfound'] = 'The activity group could not be found';
$string['activitygroups'] = 'Activity groups';
$string['add'] = 'Add';
$string['addanothercolumn'] = 'Add another column...';
$string['addanotherfilter'] = 'Add another filter...';
$string['addcohorts'] = 'Add audiences';
$string['addedscheduledreport'] = 'Added new scheduled report';
$string['addscheduledreport'] = 'Add scheduled report';
$string['advanced'] = 'Advanced?';
$string['alldata'] = 'All data';
$string['allofthefollowing'] = 'All of the following';
$string['allreports'] = 'All Reports';
$string['allscheduledreports'] = 'All scheduled reports';
$string['and'] = ' and ';
$string['anycontext'] = 'Users may have role in any context';
$string['anyofthefollowing'] = 'Any of the following';
$string['ascending'] = 'Ascending (A to Z, 1 to 9)';
$string['assignedactivities'] = 'Assigned activities';
$string['at'] = 'at';
$string['backtoallgroups'] = 'Back to all groups';
$string['badcolumns'] = 'Invalid columns';
$string['badcolumnsdesc'] = 'The following columns have been included in this report, but do not exist in the report\'s source. This can occur if the source changes on disk after reports have been generated. To fix, either restore the previous source file, or delete the columns from this report.';
$string['baseactivity'] = 'Base activity';
$string['basedon'] = 'Group based on';
$string['baseitem'] = 'Base item';
$string['baseitemdesc'] = 'The aggregated data available to this group is based on the questions in the activity \'<a href="{$a->url}">{$a->activity}</a>\'.';
$string['both'] = 'Both';
$string['bydateenable'] = 'Show records based on the record date';
$string['bytrainerenable'] = 'Show records by trainer';
$string['byuserenable'] = 'Show records by user';
$string['cache'] = 'Enable Report Caching';
$string['cachegenfail'] = 'The last attempt to generate cache failed. Please try again later.';
$string['cachegenstarted'] = 'Cache generation started at {$a}. This process can take several minutes.';
$string['cachenow'] = 'Generate Now';
$string['cachenow_help'] = '
If **Generate now** is checked, then report cache will be generated immediately after form submit.';
$string['cachenow_title'] = 'Report cache';
$string['cachepending'] = '{$a} There are changes to this report\'s configuration that have not yet been applied. The report will be updated next time the report is generated.';
$string['cannotviewembedded'] = 'Embedded reports can only be accessed through their embedded url';
$string['category'] = 'Category';
$string['choosecomp'] = 'Choose Competency...';
$string['choosecompplural'] = 'Choose Competencies';
$string['chooseorg'] = 'Choose Organisation...';
$string['chooseorgplural'] = 'Choose Organisations';
$string['choosepos'] = 'Choose Position...';
$string['chooseposplural'] = 'Choose Positions';
$string['clearform'] = 'Clear';
$string['column'] = 'Column';
$string['column_deleted'] = 'Column deleted';
$string['column_moved'] = 'Column moved';
$string['column_vis_updated'] = 'Column visibility updated';
$string['columns'] = 'Columns';
$string['columns_updated'] = 'Columns updated';
$string['competency_evidence'] = 'Competency Evidence';
$string['completedorgenable'] = 'Show records completed in the user\'s organisation';
$string['configenablereportcaching'] = 'This will allow administrators to configure report caching';
$string['confirmcoldelete'] = 'Are you sure you want to delete this column?';
$string['confirmcolumndelete'] = 'Are you sure you want to delete this column?';
$string['confirmfilterdelete'] = 'Are you sure you want to delete this filter?';
$string['content'] = 'Content';
$string['contentclassnotexist'] = 'Content class {$a} does not exist';
$string['contentcontrols'] = 'Content Controls';
$string['context'] = 'Context';
$string['couldnotsortjoinlist'] = 'Could not sort join list. Source either contains circular dependencies or references a non-existent join';
$string['course_completion'] = 'Course Completion';
$string['coursecategory'] = 'Course Category';
$string['coursecategoryid'] = 'Course Category ID';
$string['coursecategorylinked'] = 'Course Category (linked to category)';
$string['coursecategorylinkedicon'] = 'Course Category (linked to category with icon)';
$string['courseenrolledincohort'] = 'Course is enrolled in by audience';
$string['courseicon'] = 'Course Icon';
$string['courseid'] = 'Course ID';
$string['courseidnumber'] = 'Course ID Number';
$string['courselanguage'] = 'Course language';
$string['coursename'] = 'Course Name';
$string['coursenameandsummary'] = 'Course Name and Summary';
$string['coursenamelinked'] = 'Course Name (linked to course page)';
$string['coursenamelinkedicon'] = 'Course Name (linked to course page with icon)';
$string['coursenotset'] = 'Course Not Set';
$string['courseshortname'] = 'Course Shortname';
$string['coursestartdate'] = 'Course Start Date';
$string['coursesummary'] = 'Course Summary';
$string['coursetypeicon'] = 'Type';
$string['coursevisible'] = 'Course Visible';
$string['createasavedsearch'] = 'Create a saved search';
$string['createreport'] = 'Create report';
$string['csvformat'] = 'text format';
$string['currentfinancial'] = 'The current financial year';
$string['currentorgenable'] = 'Show records from staff in the user\'s organisation';
$string['currentposenable'] = 'Show records from staff in the user\'s position';
$string['currentsearchparams'] = 'Settings to be saved';
$string['customiseheading'] = 'Customise heading';
$string['customisename'] = 'Customise Field Name';
$string['daily'] = 'Daily';
$string['data'] = 'Data';
$string['dateisbetween'] = 'Is between today and ';
$string['datelabelisdaysafter'] = 'After today\'s date and before {$a->daysafter}';
$string['datelabelisdaysbefore'] = 'Before today\'s date and after {$a->daysbefore}.';
$string['datelabelisdaysbetween'] = '{$a->label} is after {$a->daysbefore} and before {$a->daysafter}';
$string['defaultsortcolumn'] = 'Default column';
$string['defaultsortorder'] = 'Default order';
$string['delete'] = 'Delete';
$string['deletecheckschedulereport'] = 'Are you sure you would like to delete this scheduled report?';
$string['deletedonly'] = 'Deleted users only';
$string['deletedscheduledreport'] = 'Successfully deleted Scheduled Report \'{$a}\'';
$string['deleteduser'] = 'Deleted user';
$string['deletereport'] = 'Report Deleted';
$string['descending'] = 'Descending (Z to A, 9 to 1)';
$string['disabled'] = 'Disabled?';
$string['editreport'] = 'Edit Report \'{$a}\'';
$string['editscheduledreport'] = 'Edit Scheduled Report';
$string['editthisreport'] = 'Edit this report';
$string['embedded'] = 'Embedded';
$string['embeddedaccessnotes'] = '<strong>Warning:</strong> Embedded reports may have their own access restrictions applied to the page they are embedded into. They may ignore the settings below, or they may apply them as well as their own restrictions.';
$string['embeddedcontentnotes'] = '<strong>Warning:</strong> Embedded reports may have further content restrictions applied via <em>embedded parameters</em>. These can further limit the content that is shown in the report';
$string['embeddedreports'] = 'Embedded Reports';
$string['enablereportcaching'] = 'Enable report caching';
$string['enrolledcoursecohortids'] = 'Enrolled course audience IDs';
$string['enrolledprogramcohortids'] = 'Enrolled program audience IDs';
$string['error:addscheduledreport'] = 'Error adding new Scheduled Report';
$string['error:bad_sesskey'] = 'There was an error because the session key did not match';
$string['error:cachenotfound'] = 'Cannot purge cache. Seems it is already clean.';
$string['error:column_not_deleted'] = 'There was a problem deleting that column';
$string['error:column_not_moved'] = 'There was a problem moving that column';
$string['error:column_vis_not_updated'] = 'Column visibility could not be updated';
$string['error:columnextranameid'] = 'Column extra field \'{$a}\' alias must not be \'id\'';
$string['error:columnnameid'] = 'Field \'{$a}\' alias must not be \'id\'';
$string['error:columnoptiontypexandvalueynotfoundinz'] = 'Column option with type "{$a->type}" and value "{$a->value}" not found in source "{$a->source}"';
$string['error:columns_not_updated'] = 'There was a problem updating the columns.';
$string['error:couldnotcreatenewreport'] = 'Could not create new report';
$string['error:couldnotgenerateembeddedreport'] = 'There was a problem generating that report';
$string['error:couldnotsavesearch'] = 'Could not save search';
$string['error:couldnotupdateglobalsettings'] = 'There was an error while updating the global settings';
$string['error:couldnotupdatereport'] = 'Could not update report';
$string['error:creatingembeddedrecord'] = 'Error creating embedded record: {$a}';
$string['error:emptyexportfilesystempath'] = 'If you enabled export to file system, you need to specify file system path.';
$string['error:failedtoremovetempfile'] = 'Failed to remove temporary report export file';
$string['error:filter_not_deleted'] = 'There was a problem deleting that filter';
$string['error:filter_not_moved'] = 'There was a problem moving that filter';
$string['error:filteroptiontypexandvalueynotfoundinz'] = 'Filter option with type "{$a->type}" and value "{$a->value}" not found in source "{$a->source}"';
$string['error:filters_not_updated'] = 'There was a problem updating the filters';
$string['error:fusion_oauthnotsupported'] = 'Fusion export via OAuth is not currently supported.';
$string['error:grouphasreports'] = 'You cannot delete a group that is being used by reports.';
$string['error:groupnotcreated'] = 'Group could not be created';
$string['error:groupnotcreatedinitfail'] = 'Group could not be created - failed to initialize tables!';
$string['error:groupnotcreatedpreproc'] = 'Group could not be created - preprocessor not found!';
$string['error:groupnotdeleted'] = 'Group could not be deleted';
$string['error:invalidreportid'] = 'Invalid report ID';
$string['error:invalidreportscheduleid'] = 'Invalid scheduled report ID';
$string['error:invalidsavedsearchid'] = 'Invalid saved search ID';
$string['error:invaliduserid'] = 'Invalid user ID';
$string['error:joinsforfiltertypexandvalueynotfoundinz'] = 'Joins for filter with type "{$a->type}" and value "{$a->value}" not found in source "{$a->source}"';
$string['error:joinsfortypexandvalueynotfoundinz'] = 'Joins for columns with type "{$a->type}" and value "{$a->value}" not found in source "{$a->source}"';
$string['error:joinxhasdependencyyinz'] = 'Join name "{$a->join}" contains a dependency "{$a->dependency}" that does not exist in the joinlist for source "{$a->source}"';
$string['error:joinxisreservediny'] = 'Join name "{$a->join}" in source "{$a->source}" is an SQL reserved word. Please rename the join';
$string['error:joinxusedmorethanonceiny'] = 'Join name "{$a->join}" used more than once in source "{$a->source}"';
$string['error:missingdependencytable'] = 'In report source {$a->source}, missing dependency table in joinlist: {$a->join}!';
$string['error:mustselectsource'] = 'You must pick a source for the report';
$string['error:nocolumns'] = 'No columns found. Ask your developer to add column options to the \'{$a}\' source.';
$string['error:nocolumnsdefined'] = 'No columns have been defined for this report. Ask you site administrator to add some columns.';
$string['error:nocontentrestrictions'] = 'No content restrictions are available for this source. To use restrictions, ask your developer to add the necessary code to the \'{$a}\' source.';
$string['error:nopermissionsforscheduledreport'] = 'Scheduled Report Error: User {$a->userid} is not capable of viewing report {$a->reportid}.';
$string['error:norolesfound'] = 'No roles found';
$string['error:nosavedsearches'] = 'This report does not yet have any saved searches';
$string['error:nosources'] = 'No sources found. You must have at least one source before you can add reports. Ask your developer to add the necessary files to the codebase.';
$string['error:notapathexportfilesystempath'] = 'Specified file system path is not found.';
$string['error:notdirexportfilesystempath'] = 'Specified file system path does not exist or is not a directory.';
$string['error:notwriteableexportfilesystempath'] = 'Specified file system path is not writeable.';
$string['error:problemobtainingcachedreportdata'] = 'There was a problem obtaining the cached data for this report. It might be due to cache regeneration. Please, try again. If problem persist, disable cache for this report.';
$string['error:problemobtainingreportdata'] = 'There was a problem obtaining the data for this report';
$string['error:processfile'] = 'Unable to create process file. Please, try later.';
$string['error:propertyxmustbesetiny'] = 'Property "{$a->property}" must be set in class "{$a->class}"';
$string['error:reportcacheinitialize'] = 'Cache is disabled for this report';
$string['error:savedsearchnotdeleted'] = 'Saved search could not be deleted';
$string['error:unknownbuttonclicked'] = 'Unknown button clicked';
$string['error:updatescheduledreport'] = 'Error updating Scheduled Report';
$string['excludetags'] = 'Exclude records tagged with';
$string['export'] = 'Export';
$string['exportcsv'] = 'Export in text format';
$string['exportfilesystemoptions'] = 'Export options';
$string['exportfilesystempath'] = 'Export file system path:';
$string['exportfusion'] = 'Export to Google Fusion';
$string['exportods'] = 'Export in ODS format';
$string['exportoptions'] = 'Report Export options';
$string['exportpdf_landscape'] = 'Export in PDF format (Landscape)';
$string['exportpdf_mramlimitexceeded'] = 'Notice: Ram memory limit exceeded! Probably the report being exported is too big, as it took almost {$a} MB of ram memory to create it, please consider reducing the size of the report, applying filters or splitting the report in several files.';
$string['exportpdf_portrait'] = 'Export in PDF format';
$string['exportproblem'] = 'There was a problem downloading the file';
$string['exporttoemail'] = 'Email scheduled report';
$string['exporttoemailandsave'] = 'Email and save scheduled report to file';
$string['exporttofilesystem'] = 'Export to file system';
$string['exporttofilesystemenable'] = 'Enable exporting to file system';
$string['exporttosave'] = 'Save scheduled report to file system only';
$string['exportxls'] = 'Export in Excel format';
$string['extrasqlshouldusenamedparams'] = 'get_sql_filter() extra sql should use named parameters';
$string['filter'] = 'Filter';
$string['filter_deleted'] = 'Filter deleted';
$string['filter_moved'] = 'Filter moved';
$string['filternameformatincorrect'] = 'get_filter_joins(): filter name format incorrect. Query snippets may have included a dash character.';
$string['filters'] = 'Filters';
$string['filters_updated'] = 'Filters updated';
$string['financialyear'] = 'Financial year start';
$string['format'] = 'Format';
$string['globalsettings'] = 'Global settings';
$string['globalsettingsupdated'] = 'Global settings updated';
$string['gradeandgradetocomplete'] = '{$a->grade}% ({$a->pass}% to complete)';
$string['groupconfirmdelete'] = 'Are you sure you want to delete this group?';
$string['groupcontents'] = 'This group currently contains {$a->count} feedback activities tagged with the <strong>\'{$a->tag}\'</strong> official tag:';
$string['groupdeleted'] = 'Group deleted.';
$string['groupingfuncnotinfieldoftypeandvalue'] = 'Grouping function \'{$a->groupfunc}\' doesn\'t exist in field of type \'{$a->type}\' and value \'{$a->$value}\'';
$string['groupname'] = 'Group name';
$string['grouptag'] = 'Group tag';
$string['heading'] = 'Heading';
$string['help:columnsdesc'] = 'The choices below determine which columns appear in the report and how those columns are labelled.';
$string['help:restrictionoptions'] = 'The checkboxes below determine who has access to this report, and which records they are able to view. If no options are checked no results are visible. Click the help icon for more information';
$string['help:searchdesc'] = 'The choices below determine which options appear in the search box at the top of the report.';
$string['hidden'] = 'Hide in My Reports';
$string['hide'] = 'Hide';
$string['hierarchyfiltermusthavetype'] = 'Hierarchy filter of type "{$a->type}" and value "{$a->value}" must have "hierarchytype" set in source "{$a->source}"';
$string['includechildorgs'] = 'Include records from child organisations';
$string['includechildpos'] = 'Include records from child positions';
$string['includeemptydates'] = 'Include record if date is missing';
$string['includerecordsfrom'] = 'Include records from';
$string['includetags'] = 'Include records tagged with';
$string['includetrainerrecords'] = 'Include records from particular trainers';
$string['includeuserrecords'] = 'Include records from particular users';
$string['initialdisplay'] = 'Restrict Initial Display';
$string['initialdisplay_disabled'] = 'This setting is not available when there are no filters enabled';
$string['initialdisplay_error'] = 'The last filter can not be deleted when initial display is restricted';
$string['initialdisplay_heading'] = 'Filters Performance Settings';
$string['initialdisplay_help'] = 'This setting controls how the report is initially displayed and is recommended for larger reports where you will be filtering the results (e.g. sitelogs). It increases the speed of the report by allowing you to apply filters and display only the results instead of initially trying to display *all* the data.

**Disabled**: the report will display all results immediately *(default)*

**Enabled**: the report will not generate results until a filter is applied or an empty search is run.';
$string['initialdisplay_pending'] = 'Please apply a filter to view the results of this report, or hit search without adding any filters to view all entries';
$string['is'] = 'is';
$string['isaftertoday'] = 'days after today (date of report generation)';
$string['isbeforetoday'] = 'days before today (date of report generation)';
$string['isbelow'] = 'is below';
$string['isnotfound'] = ' is NOT FOUND';
$string['isnt'] = 'isn\'t';
$string['isnttaggedwith'] = 'isn\'t tagged with';
$string['istaggedwith'] = 'is tagged with';
$string['joinnotinjoinlist'] = '\'{$a->join}\' not in join list for {$a->usage}';
$string['last30days'] = 'The last 30 days';
$string['lastcached'] = 'Last cached at {$a}';
$string['lastchecked'] = 'Last process date';
$string['lastfinancial'] = 'The previous financial year';
$string['manageactivitygroups'] = 'Manage activity groups';
$string['managereports'] = 'Manage reports';
$string['managername'] = 'Manager\'s Name';
$string['monthly'] = 'Monthly';
$string['movedown'] = 'Move Down';
$string['moveup'] = 'Move Up';
$string['myreports'] = 'My Reports';
$string['name'] = 'Name';
$string['newgroup'] = 'Create a new activity group';
$string['newreport'] = 'New Report';
$string['newreportcreated'] = 'New report created. Click settings to edit filters and columns';
$string['next30days'] = 'The next 30 days';
$string['nice_date_in_timezone_format'] = '%d %B %Y';
$string['nice_time_in_timezone_format'] = '%I:%M %p';
$string['nice_time_unknown_timezone'] = 'Unknown Timezone';
$string['nocolumnsyet'] = 'No columns have been created yet - add them by selecting a column name in the pulldown below.';
$string['nocontentrestriction'] = 'Show all records';
$string['nodeletereport'] = 'Report could not be deleted';
$string['noembeddedreports'] = 'There are no embedded reports. Embedded reports are reports that are hard-coded directly into a page. Typically they will be set up by your site developer.';
$string['noemptycols'] = 'You must include a column heading';
$string['nofilteraskdeveloper'] = 'No filters found. Ask your developer to add filter options to the \'{$a}\' source.';
$string['nofilteroptions'] = 'This filter has no options to select';
$string['nofiltersetfortypewithvalue'] = 'get_field(): no filter set in filteroptions for type\'{$a->type}\' with value \'{$a->value}\'';
$string['nofiltersyet'] = 'No search fields have been created yet - add them by selecting a search term in the pulldown below.';
$string['nogroups'] = 'There are currently no activity groups';
$string['noheadingcolumnsdefined'] = 'No heading columns defined';
$string['noneselected'] = 'None selected';
$string['nopermission'] = 'You do not have permission to view this page';
$string['noreloadreport'] = 'Report settings could not be reset';
$string['norepeatcols'] = 'You cannot include the same column more than once';
$string['norepeatfilters'] = 'You cannot include the same filter more than once';
$string['noreports'] = 'No reports have been created. You can create a report using the form below.';
$string['noreportscount'] = 'No reports using this group';
$string['norestriction'] = 'All users can view this report';
$string['norestrictionsfound'] = 'No restrictions found. Ask your developer to add restrictions to /totara/reportbuilder/sources/{$a}/restrictionoptions.php';
$string['noresultsfound'] = 'No results found';
$string['noscheduledreports'] = 'There are no scheduled reports';
$string['noshortnameorid'] = 'Invalid report id or shortname';
$string['notags'] = 'No official tags exist. You must create one or more official tags to base your groups on.';
$string['notcached'] = 'Not cached yet';
$string['notspecified'] = 'Not specified';
$string['notyetchecked'] = 'Not yet processed';
$string['nouserreports'] = 'You do not have any reports. Report access is configured by your site administrator. If you are expecting to see a report, ask them to check the access permissions on the report.';
$string['numresponses'] = '{$a} response(s).';
$string['occurredafter'] = 'occurred after';
$string['occurredbefore'] = 'occurred before';
$string['occurredprevfinancialyear'] = 'occurred in the previous financial year';
$string['occurredthisfinancialyear'] = 'occurred in this finanicial year';
$string['odsformat'] = 'ODS format';
$string['on'] = 'on';
$string['onlydisplayrecordsfor'] = 'Only display records for';
$string['onthe'] = 'on the';
$string['options'] = 'Options';
$string['or'] = ' or ';
$string['organisationtype'] = 'User\'s Organisation Type';
$string['organisationtypeid'] = 'User\'s Organisation Type ID';
$string['orsuborg'] = '(or a sub organisation)';
$string['orsubpos'] = '(or a sub position)';
$string['participantscurrentorg'] = 'Participant\'s Current Organisation';
$string['participantscurrentorgbasic'] = 'Participant\'s Current Organisation (basic)';
$string['participantscurrentpos'] = 'Participant\'s Current Position';
$string['participantscurrentposbasic'] = 'Participant\'s Current Position (basic)';
$string['pdf_landscapeformat'] = 'pdf format (landscape)';
$string['pdf_portraitformat'] = 'pdf format (portrait)';
$string['performance'] = 'Performance';
$string['pluginadministration'] = 'Report Builder administration';
$string['pluginname'] = 'Report Builder';
$string['posenddate'] = 'User\'s Position End Date';
$string['positiontype'] = 'User\'s Position Type';
$string['positiontypeid'] = 'User\'s Position Type ID';
$string['posstartdate'] = 'User\'s Position Start Date';
$string['programenrolledincohort'] = 'Program is enrolled in by audience';
$string['publicallyavailable'] = 'Let other users view';
$string['records'] = 'Records';
$string['recordsperpage'] = 'Number of records per page';
$string['refreshdataforthisgroup'] = 'Refresh data for this group';
$string['reloadreport'] = 'Report settings have been reset';
$string['report'] = 'Report';
$string['report:cachelast'] = 'Report data last updated: {$a}';
$string['report:cachenext'] = 'Next update due: {$a}';
$string['report:completiondate'] = 'Completion date';
$string['report:coursetitle'] = 'Course title';
$string['report:enddate'] = 'End date';
$string['report:learner'] = 'Learner';
$string['report:learningrecords'] = 'Learning records';
$string['report:nodata'] = 'There is no available data for that combination of criteria, start date and end date';
$string['report:organisation'] = 'Office';
$string['report:startdate'] = 'Start date';
$string['reportbuilder'] = 'Report builder';
$string['reportbuilder:managereports'] = 'Create, edit and delete report builder reports';
$string['reportbuilderaccessmode'] = 'Access Mode';
$string['reportbuilderaccessmode_help'] = '
Access controls are used to restrict which users can view the report.

**Restrict access** sets the overall access setting for the report.

When set to **All users can view this report** there are no restrictions applied to the report and all users will be able to view the report.

When set to **Only certain users can view this report** the report will be restricted to the user groups selected below.

**Note:** access restrictions only control who can view the report, not which records it contains. See the \'Content\' tab for controlling the report contents.';
$string['reportbuilderbaseitem'] = 'Report Builder: Base item';
$string['reportbuilderbaseitem_help'] = '
By grouping a set of activities you are saying that they have something in common, which will allow reports to be generated for all the activities in a group. The base item defines the properties that are considered when aggregation is performed on each member of the group.';
$string['reportbuildercache'] = 'Enable report caching';
$string['reportbuildercache_disabled'] = 'This setting is not available for this report source';
$string['reportbuildercache_heading'] = 'Caching Performance Settings';
$string['reportbuildercache_help'] = '
If **Enable report caching** is checked, then a copy of this report will be generated on a set schedule, and users will see data from the stored report. This will make displaying and filtering of the report faster, but the data displayed will be from the last time the report was generated rather than "live" data. We recommend enabling this setting only if necessary (reports are taking too long to be displayed), and only for specific reports where this is a problem.';
$string['reportbuildercachescheduler'] = 'Cache Schedule (Server Time)';
$string['reportbuildercachescheduler_help'] = 'Determines the schedule used to control how often a new version of the report is generated. The report will be generated on the cron that immediately follows the specified time.

For example, if you have set up your cron to run every 20 minutes at 10, 30 and 50 minutes past the hour and you schedule a report to run at midnight, it will actually run at 10 minutes past midnight.';
$string['reportbuildercacheservertime'] = 'Current Server Time';
$string['reportbuildercacheservertime_help'] = 'All reports are being cached based on server time. Cache status shows you current local time which might be different from server time. Make sure to take into account your server time when scheduling cache.';
$string['reportbuildercolumns'] = 'Columns';
$string['reportbuildercolumns_help'] = '
**Report Columns** allows you to customise the columns that appear on your report. The available columns are determined by the data **Source** of the report. Each report source has a set of default columns set up.

Columns can be added, removed, renamed and sorted.

**Adding Columns:** To add a new column to the report choose the required column from the \'Add another column...\' dropdown list and click **Save changes**. The new column will be added to the end of the list.

**Note:** You can only create one column of each type within a single report. You will receive a validation error if you try to include the same column more than once.

**Hiding columns:** By default all columns appear when a user views the report. Use the \'show/hide\' button (the eye icon) to hide columns you do not want users to see by default.

**Note:** A hidden column is still available to a user viewing the report. Delete columns (the cross icon) that you do not want users to see at all.

**Moving columns:** The columns will appear on the report in the order they are listed. Use the up and down arrows to change the order.

**Deleting columns:** Click the \'Delete\' button (the cross icon) to the right of the report column to remove that column from the report.

**Renaming columns:** You can customise the name of a column by changing the **Heading** name and clicking **Save changes**. The **Heading** is the name that will appear on the report.

**Changing multiple column types:** You can modify multiple column types at the same time by selecting a different column from the dropdown menu and clicking **Save changes**.';
$string['reportbuildercompletedorg'] = 'Show by Completed Organisation';
$string['reportbuildercompletedorg_help'] = '
When **Show records completed in the user\'s organisation** is selected the report displays different completed records depending on the organisation the user has been assigned to. (A user is assigned an organisation in their \'User Profile\' on the \'Positions\' tab).

When **Include records from child organisations** is set to:

*   **Yes** the user viewing the report will be able to view completed records related to their organisation and any child organisations of that organisation
*   **No** the user can only view completed records related to their organisation.';
$string['reportbuildercontentmode'] = 'Content Mode';
$string['reportbuildercontentmode_help'] = '
Content controls allow you to restrict the records and information that are available when a report is viewed.

**Report content** allows you to select the overall content control settings for this report:

When **Show all records** is selected, every available record for this source will be shown and no restrictions will be placed on the content available.

When **Show records matching any of the checked criteria** is selected the report will display records that match any of the criteria set below.

**Note:** If no criteria is set the report will display no records.

When **Show records matching all of the checked criteria** is selected the report will display records that match all the criteria set below.
**Note:** If no criteria is set the report will display no records.';
$string['reportbuildercontext'] = 'Restrict Access by Role';
$string['reportbuildercontext_help'] = '
Context is the location or level within the system that the user has access to. For example a Site Administrator would have System level access (context), while a learner may only have Course level access (context).

**Context** allows you to set the context in which a user has been assigned a role to view the report.

A user can be assigned a role at the system level giving them site wide access or just within a particular context. For instance a trainer may only be assigned the role at the course level.

When **Users must have role in the system context** is selected the user must be assigned the role at a system level (i.e. at a site-wide level) to be able to view the report.

When **User may have role in any context** is selected a user can view the report when they have been assigned the selected role anywhere in the system.';
$string['reportbuildercurrentorg'] = 'Show by Current Organisation';
$string['reportbuildercurrentorg_help'] = '
When **Show records from staff in the user\'s organisation** is selected the report displays different results depending on the organisation the user has been assigned to. (A user is assigned an organisation in their \'User Profile\' on the \'Positions\' tab).

When **Include records from child organisations** is set to:

*   **Yes** the user viewing the report will be able to view records related to their organisation and any child organisations of that organisation
*   **No** the user can only view records related to their organisation.';
$string['reportbuildercurrentpos'] = 'Show by Current Position';
$string['reportbuildercurrentpos_help'] = '
When **Show records from staff in the user\'s position** is selected the report will display different records depending on their assigned position (A user is assigned a position in their \'User Profile\' on the \'Positions\' tab).

When **Include records from child positions** is set to:

*   **Yes** the user viewing the report can view records related to their positions and any child positions related to their positions
*   **No** the user viewing the report can only view records related to their position.';
$string['reportbuilderdate'] = 'Show by date';
$string['reportbuilderdate_help'] = '
When **Show records based on the record date** is selected the report only displays records within the selected timeframe.

The **Include records from** options allow you to set the timeframe for the report:

*   When set to **The past** the report only shows records with a date older than the current date.
*   When set to **The future** the report only shows records with a future date set from the current date.
*   When set to **The last 30 days** the report only shows records between the current time and 30 days before.
*   When set to **The next 30 days** the report only shows records between the current time and 30 days into the future.';
$string['reportbuilderdescription'] = 'Description';
$string['reportbuilderdescription_help'] = 'When a report description is created the information displays in a box above the search filters on the report page.';
$string['reportbuilderdialogfilter'] = 'Report Builder: Dialog filter';
$string['reportbuilderdialogfilter_help'] = '
This filter allows you to filter information based on a hierarchy. The filter has the following options:

*   is any value - this option disables the filter (i.e. all information is accepted by this filter)
*   is equal to - this option allows only information that is equal to the value selected from the list
*   is not equal to - this option allows only information that is different from the value selected from the list

Once a framework item has been selected you can use the \'Include children?\' checkbox to choose whether to match only that item, or match that item and any sub-items belonging to that item.';
$string['reportbuilderexportoptions'] = 'Report Export Settings';
$string['reportbuilderexportoptions_help'] = '
**Report export settings** allows a user with the appropriate permissions to specify the export options that are available for users at the bottom of a report page. This setting affects all **Report builder** reports.

When multiple options are selected the user can choose their preferred options from the export dropdown menu.

When no options are selected the export function is disabled..';
$string['reportbuilderexporttofilesystem'] = 'Enable exporting to file system';
$string['reportbuilderexporttofilesystem_help'] = '**Exporting to file system** allows reports to be saved to a directory on the web server\'s file system, instead of only emailing the report to the user scheduling the report.

This can be useful when the report needs to be accessed by an external system automation, and the report directory might have SFTP access enabled.

Reports saved to the filesystem are saved as **\'Export file system root path\'**/username/report.ext where *username* is an internal username of a user who owns the scheduled report, *report* is the name of the scheduled report with non alpha-numeric characters removed, and *ext* is the appropriate export file name extension.';
$string['reportbuilderfilters'] = 'Search Options (Filters)';
$string['reportbuilderfilters_help'] = '
**Search Options** allows you to customise the filters that appear on your report. The available filters are determined by the **Source** of the report. Each report source has a set of default filters.

Filters can be added, sorted and removed.

**Adding filters:** To add a new filter to the report choose the required filter from the \'Add another filter...\' dropdown menu and click **Save changes**. When **Advanced** is checked the filter will not appear in the \'Search by\' box by default, you can click **Show advanced** when viewing a report to see these filters.

**Moving filters:** The filters will appear in the \'Search by\' box in the order they are listed. Use the up and down arrows to change the order.

**Deleting filters:** Click the **Delete** button (the cross icon) to the right of the report filter to remove that filter from the report.

**Changing multiple filter types:** You can modify multiple filter types at the same time by selecting a different filter from the dropdown menu and clicking **Save changes**.';
$string['reportbuilderfinancialyear'] = 'Report Financial Year Settings';
$string['reportbuilderfinancialyear_help'] = '**Financial year** is used in the reports content controls.

This setting allows to set the start date of the financial year.';
$string['reportbuilderfullname'] = 'Report Name';
$string['reportbuilderfullname_help'] = 'This is the name that will appear at the top of your report page and in the \'Report Manager\' block.';
$string['reportbuilderglobalsettings'] = 'Report Builder Global Settings';
$string['reportbuildergroupname'] = 'Report Builder: Group Name';
$string['reportbuildergroupname_help'] = '
The name of the group. This will allow you to identify the group when you want to create a new report based on it. Look for the name in the report source pulldown menu.';
$string['reportbuildergrouptag'] = 'Report Builder: Group Tag';
$string['reportbuildergrouptag_help'] = '
When you create a group using a tag, any activities that are tagged with the official tag specified automatically form part of the group. If you add or remove tags from an activity, the group will be updated to include/exclude that activity.';
$string['reportbuilderhidden'] = 'Hide in My Reports';
$string['reportbuilderhidden_help'] = '
When **Hide in My Reports** is checked the report will not appear on the \'My Reports\' page for any logged in users.

**Note:** The **Hide in My Reports** option only hides the link to the report. Users with the correct access permissions may still access the report using the URL.';
$string['reportbuilderinitcache'] = 'Cache Status (User Time)';
$string['reportbuilderrecordsperpage'] = 'Number of Records per Page';
$string['reportbuilderrecordsperpage_help'] = '
**Number of records per page** allows you define how many records display on a report page.

The maximum number of records that can be displayed on a page is 9999. The more records set to display on a page the longer the report pages take to display.

Recommendation is to **limit the number of records per page to 40**.';
$string['reportbuilderrolesaccess'] = 'Roles with Access';
$string['reportbuilderrolesaccess_help'] = '
When **Restrict access** is set to **Only certain users can view this report** you can specify which roles can view the report using **Roles with permission to view the report**.

You can select one or multiple roles from the list.

When **Restrict access** is set to **All users can view this report** these options will be disabled.';
$string['reportbuildershortname'] = 'Report Builder: Unique name';
$string['reportbuildershortname_help'] = '
The shortname is used by moodle to keep track of this report. No two reports can be given the same shortname, even if they are based on the same source. Avoid using special characters in this field (text, numbers and underscores are okay).';
$string['reportbuildersorting'] = 'Sorting';
$string['reportbuildersorting_help'] = '
**Sorting** allows you to set a default column and sort order on a report.

A user is still able to manually sort a report while viewing it. The users preferences will be saved during the active session. When they finish the session the report will return to the default sort settings set here.';
$string['reportbuildersource'] = 'Source';
$string['reportbuildersource_help'] = '
The **Source** of a report defines the primary type of data used. Further filtering options are available once you start editing the report.

Once saved, the report source cannot be changed.

**Note:** If no options are available in the **Source** field, or the source you require does not appear you will need your Totara installation to be configured to include the source data you require (This cannot be done via the Totara interface).';
$string['reportbuildertag'] = 'Report Builder: Show by tag';
$string['reportbuildertag_help'] = '
This criteria is enabled by selecting the \'Show records by tag\' checkbox. If selected, the report will show results based on whether the record belongs to an item that is tagged with particular tags.

If any tags in the \'Include records tagged with\' section are selected, only records belonging to an item tagged with all the selected tags will be shown. Records belonging to items with no tags will **not** be shown.

If any tags in the \'Exclude records tagged with\' section are selected, records belonging to a coures tagged with the selected tags will **not** be shown. All records belonging to items without any tags will be shown.

It is possible to include and exclude tags at the same time, but a single tag cannot be both included and excluded.';
$string['reportbuildertrainer'] = 'Report Builder: Show by trainer';
$string['reportbuildertrainer_help'] = '
This criteria is enabled by selecting the \'Show records by trainer\' checkbox. If selected, then the report will show different records depending on who the face-to-face trainer was for the feedback being given.

If \'Show records where the user is the trainer\' is selected, the report will show feedback for sessions where the user viewing the report was the trainer.

If \'Records where one of the user\'s direct reports is the trainer\' is selected, then the report will show records for sessions trained by staff of the person viewing the report.

If \'Both\' is selected, then both of the above records will be shown.';
$string['reportbuilderuser'] = 'Show by User';
$string['reportbuilderuser_help'] = '
When **Show records by user** is selected the report will show different records depending on the user viewing the report and their relationship to other users.

**Include records from a particular user** controls what records a user viewing the report can see:

*   When set to **A user\'s own records** the user can see their own records only.
*   When set to **Records for user\'s direct reports** the user can see the records belonging to any user who reports to them (A user is assigned a manager in their user profile on the \'Positions tab\').
*   When set to **Both** the user can view both their own records and those of their direct reports.';
$string['reportcachingdisabled'] = 'Report caching is disabled. You can enable it <a href="{$a}">here</a>';
$string['reportcolumns'] = 'Report Columns';
$string['reportconfirmdelete'] = 'Are you sure you want to delete this report?';
$string['reportconfirmreload'] = 'This is an embedded report so you cannot delete it (that must be done by your site developer). You can choose to reset the report settings to their original values. Do you want to continue?';
$string['reportcontents'] = 'This report contains records matching the following criteria:';
$string['reportcount'] = '{$a} report(s) based on this group:';
$string['reportmustbedefined'] = 'Report must be defined';
$string['reportname'] = 'Report Name';
$string['reportperformance'] = 'Performance settings';
$string['reports'] = 'Reports';
$string['reportsettings'] = 'Report Settings';
$string['reportshortname'] = 'Short Name';
$string['reportshortnamemustbedefined'] = 'Report shortname must be defined';
$string['reportsto'] = 'reports to';
$string['reporttitle'] = 'Report Title';
$string['reporttype'] = 'Report type';
$string['reportupdated'] = 'Report Updated';
$string['reportwithidnotfound'] = 'Report with id of \'{$a}\' not found in database.';
$string['restoredefaults'] = 'Restore Default Settings';
$string['restrictaccess'] = 'Restrict access';
$string['restrictcontent'] = 'Report content';
$string['restriction'] = 'Restriction';
$string['restrictionswarning'] = '<strong>Warning:</strong> If none of these boxes are checked, all users will be able to view all available records from this source.';
$string['resultsfromfeedback'] = 'Results from <strong>{$a}</strong> completed feedback(s).';
$string['roleswithaccess'] = 'Roles with permission to view this report';
$string['savedsearch'] = 'Saved Search';
$string['savedsearchconfirmdelete'] = 'Are you sure you want to delete this saved search?';
$string['savedsearchdeleted'] = 'Saved search deleted';
$string['savedsearchdesc'] = 'By giving this search a name you will be able to easily access it later or save it to your bookmarks.';
$string['savedsearches'] = 'Saved Searches';
$string['savedsearchinscheduleddelete'] = 'This saved search is currently being used in the following scheduled reports: <br/> {$a} <br/> Deleting this saved search will delete these scheduled reports.';
$string['savedsearchmessage'] = 'Only the data matching the \'{$a}\' search is included.';
$string['savedsearchnotfoundornotpublic'] = 'Saved search not found or search is not public';
$string['savesearch'] = 'Save this search';
$string['saving'] = 'Saving...';
$string['schedule'] = 'Schedule';
$string['scheduledaily'] = 'Daily';
$string['scheduledreportmessage'] = 'Attached is a copy of the \'{$a->reportname}\' report in {$a->exporttype}. {$a->savedtext}

You can also view this report online at:

{$a->reporturl}

You are scheduled to receive this report {$a->schedule}.
To delete or update your scheduled report settings, visit:

{$a->scheduledreportsindex}';
$string['scheduledreports'] = 'Scheduled Reports';
$string['scheduledreportsettings'] = 'Scheduled report settings';
$string['schedulemonthly'] = 'Monthly';
$string['scheduleneedssavedfilters'] = 'This report cannot be scheduled without a saved search.
To view the report, click <a href="{$a}">here</a>';
$string['schedulenotset'] = 'Schedule not set';
$string['scheduleweekly'] = 'Weekly';
$string['search'] = 'Search';
$string['searchby'] = 'Search by';
$string['searchfield'] = 'Search Field';
$string['searchname'] = 'Search Name';
$string['searchoptions'] = 'Report Search Options';
$string['selectitem'] = 'Select item';
$string['selectsource'] = 'Select a source...';
$string['settings'] = 'Settings';
$string['shortnametaken'] = 'That shortname is already in use';
$string['show'] = 'Show';
$string['showbasedonx'] = 'Show records based on {$a}';
$string['showbycompletedorg'] = 'Show by completed organisation';
$string['showbycurrentorg'] = 'Show by current organisation';
$string['showbycurrentpos'] = 'Show by current position';
$string['showbydate'] = 'Show by date';
$string['showbytag'] = 'Show by tag';
$string['showbytrainer'] = 'Show by trainer';
$string['showbyuser'] = 'Show by user';
$string['showbyx'] = 'Show by {$a}';
$string['showhidecolumns'] = 'Show/Hide Columns';
$string['showing'] = 'Showing';
$string['showrecordsbeloworgonly'] = 'Just staff below the user\'s organisation';
$string['showrecordsbelowposonly'] = 'Just staff below the user\'s position';
$string['showrecordsinorg'] = 'Just staff in the user\'s organisation';
$string['showrecordsinorgandbelow'] = 'Staff at or below the user\'s organisation';
$string['showrecordsinpos'] = 'Just staff in the user\'s position';
$string['showrecordsinposandbelow'] = 'Staff at or below the user\'s position';
$string['sorting'] = 'Sorting';
$string['source'] = 'Source';
$string['suspendedonly'] = 'Suspended users only';
$string['suspendeduser'] = 'Suspended user';
$string['systemcontext'] = 'Users must have role in the system context';
$string['tagenable'] = 'Show records by tag';
$string['taggedx'] = 'Tagged \'{$a}\'';
$string['tagids'] = 'Tag IDs';
$string['tags'] = 'Tags';
$string['thefuture'] = 'The future';
$string['thepast'] = 'The past';
$string['trainerownrecords'] = 'Show records where the user is the trainer';
$string['trainerstaffrecords'] = 'Records where one of the user\'s direct reports is the trainer';
$string['type'] = 'Type';
$string['type_cohort'] = 'Audience';
$string['type_comp_type'] = 'Competency custom fields';
$string['type_course'] = 'Course';
$string['type_course_category'] = 'Category';
$string['type_course_custom_fields'] = 'Course Custom Fields';
$string['type_org_type'] = 'Organisation custom fields';
$string['type_pos_type'] = 'Position custom fields';
$string['type_prog'] = 'Program';
$string['type_statistics'] = 'Statistics';
$string['type_tags'] = 'Tags';
$string['type_user'] = 'User';
$string['type_user_profile'] = 'User Profile';
$string['uniquename'] = 'Unique Name';
$string['unknown'] = 'Unknown';
$string['updatescheduledreport'] = 'Successfully updated Scheduled Report';
$string['useraddress'] = 'User\'s Address';
$string['usercity'] = 'User\'s City';
$string['usercohortids'] = 'User audience IDs';
$string['usercountry'] = 'User\'s Country';
$string['userdepartment'] = 'User\'s Department';
$string['useremail'] = 'User\'s Email';
$string['useremailprivate'] = 'Email is private';
$string['useremailunobscured'] = 'User\'s Email (ignoring user display setting)';
$string['userfirstaccess'] = 'User First Access';
$string['userfirstname'] = 'User First Name';
$string['userfullname'] = 'User\'s Fullname';
$string['usergenerated'] = 'User generated';
$string['usergeneratedreports'] = 'User generated Reports';
$string['userid'] = 'User ID';
$string['useridnumber'] = 'User ID Number';
$string['userincohort'] = 'User is a member of audience';
$string['userinstitution'] = 'User\'s Institution';
$string['userlastlogin'] = 'User Last Login';
$string['userlastname'] = 'User Last Name';
$string['username'] = 'Username';
$string['usernamelink'] = 'User\'s Fullname (linked to profile)';
$string['usernamelinkicon'] = 'User\'s Fullname (linked to profile with icon)';
$string['userownrecords'] = 'A user\'s own records';
$string['userphone'] = 'User\'s Phone number';
$string['usersjobtitle'] = 'User\'s Job Title';
$string['usersmanagerfirstname'] = 'User\'s Manager\'s First Name';
$string['usersmanagerid'] = 'User\'s Manager ID';
$string['usersmanageridnumber'] = 'User\'s Manager ID Number';
$string['usersmanagerlastname'] = 'User\'s Manager\'s Last Name';
$string['usersmanagername'] = 'User\'s Manager Name';
$string['usersorgid'] = 'User\'s Organisation ID';
$string['usersorgidnumber'] = 'User\'s Organisation ID Number';
$string['usersorgname'] = 'User\'s Organisation Name';
$string['usersorgpathids'] = 'User\'s Organisation Path IDs';
$string['userspos'] = 'User\'s Position';
$string['usersposid'] = 'User\'s Position ID';
$string['usersposidnumber'] = 'User\'s Position ID Number';
$string['userspospathids'] = 'User\'s Position Path IDs';
$string['userstaffrecords'] = 'Records for user\'s direct reports';
$string['userstatus'] = 'User Status';
$string['usertimecreated'] = 'User Creation Time';
$string['usertimemodified'] = 'User Last Modified';
$string['value'] = 'Value';
$string['viewreport'] = 'View This Report';
$string['viewsavedsearch'] = 'View a saved search...';
$string['weekly'] = 'Weekly';
$string['withcontentrestrictionall'] = 'Show records matching <strong>all</strong> of the checked criteria below';
$string['withcontentrestrictionany'] = 'Show records matching <strong>any</strong> of the checked criteria below';
$string['withrestriction'] = 'Only certain users can view this report (see below)';
$string['xlsformat'] = 'Excel format';
$string['xofyrecord'] = '{$a->filtered} of {$a->unfiltered} record shown';
$string['xofyrecords'] = '{$a->filtered} of {$a->unfiltered} records shown';
$string['xrecord'] = '{$a} record shown';
$string['xrecords'] = '{$a} records shown';


/*
    TOTARA CORE LANG
*/

$string['addanothercolumn'] = 'Add another column...';
$string['allf2fbookings'] = 'All Face to Face Bookings';
$string['alllearningrecords'] = 'All Learning Records';
$string['allmycourses'] = 'All My Courses';
$string['allteammembers'] = 'All Team Members';
$string['alreadyselected'] = '(already selected)';
$string['ampersand'] = 'and';
$string['archivecompletionrecords'] = 'Archive completion records';
$string['assessments'] = 'Assessments';
$string['assessmenttype'] = 'Assessment Type';
$string['assessor'] = 'Assessor';
$string['assessorname'] = 'Assessor Name';
$string['assignedvia'] = 'Assigned Via';
$string['assigngroup'] = 'Assign User Group';
$string['assignincludechildren'] = ' and all below';
$string['blended'] = 'Blended';
$string['bookings'] = 'Bookings';
$string['bookingsfor'] = 'Bookings for ';
$string['browse'] = 'Browse';
$string['browsecategories'] = 'Browse Categories';
$string['calendar'] = 'Calendar';
$string['cannotdownloadtotaralanguageupdatelist'] = 'Cannot download list of language updates from download.totaralms.com';
$string['cannotundeleteuser'] = 'Cannot undelete user';
$string['choosetempmanager'] = 'Choose temporary manager';
$string['choosetempmanager_help'] = 'A temporary manager can be assigned. The assigned Temporary Manager will have the same rights as a normal manager, for the specified amount of time.

Click **Choose temporary manager** to select a temporary manager.

If the name you are looking for does not appear in the list, it might be that the user does not have the necessary rights to act as a temporary manager.';
$string['cliupgradesure'] = 'Your Totara files have been changed, and you are about to automatically upgrade your server from this version:
<br /><br /><strong>{$a->oldversion}</strong>
<br /><br />to this version: <br /><br />
<strong>{$a->newversion}</strong> <br /><br />
Once you do this you can not go back again. <br /><br />
Please note that this process can take a long time. <br /><br />
Are you sure you want to upgrade this server to this version?';
$string['column'] = 'Column';
$string['competency_typeicon'] = 'Competency type icon';
$string['completed'] = 'Completed';
$string['configforcelogintotara'] = 'Normally, the entire site is only available to logged in users. If you would like to make the front page and the course listings (but not the course contents) available without logging in, then you should uncheck this setting.';
$string['core:appearance'] = 'Configure site appearance settings';
$string['core:createcoursecustomfield'] = 'Create a course custom field';
$string['core:delegateownmanager'] = 'Assign a temporary manager to yourself';
$string['core:delegateusersmanager'] = 'Assign a temporary manager to other users';
$string['core:deletecoursecustomfield'] = 'Delete a course custom field';
$string['core:seedeletedusers'] = 'See deleted users';
$string['core:undeleteuser'] = 'Undelete user';
$string['core:updatecoursecustomfield'] = 'Update a course custom field';
$string['core:updateuseridnumber'] = 'Update user ID number';
$string['couldntreaddataforblockid'] = 'Could not read data for blockid={$a}';
$string['couldntreaddataforcourseid'] = 'Could not ready data for courseid={$a}';
$string['coursecategoryicon'] = 'Category icon';
$string['coursecompletion'] = 'Course completion';
$string['coursecompletionsfor'] = 'Course Completions for ';
$string['courseicon'] = 'Course icon';
$string['courseprogress'] = 'Course progress';
$string['courseprogresshelp'] = 'This specifies if the course progress block appears on the homepage';
$string['coursetype'] = 'Course Type';
$string['csvdateformat'] = 'CSV Import date format';
$string['csvdateformatconfig'] = 'Date format to be used in CSV imports like user uploads with date custom profile fields, or Totara Sync.

The date format should be compatible with the formats defined in the <a target="_blank" href="http://www.php.net/manual/en/datetime.createfromformat.php">PHP DateTime class</a>

Examples:
<ul>
<li>d/m/Y if the dates in the CSV are of the form 21/03/2012</li>
<li>d/m/y if the dates in the CSV have 2-digit years 21/03/12</li>
<li>m/d/Y if the dates in the CSV are in US form 03/21/2012</li>
<li>Y-m-d if the dates in the CSV are in ISO form 2012-03-21</li>
</ul>';
$string['csvdateformatdefault'] = 'd/m/Y';
$string['currenticon'] = 'Current icon';
$string['currentlyselected'] = 'Currently selected';
$string['datatable:oPaginate:sFirst'] = 'First';
$string['datatable:oPaginate:sLast'] = 'Last';
$string['datatable:oPaginate:sNext'] = 'Next';
$string['datatable:oPaginate:sPrevious'] = 'Previous';
$string['datatable:sEmptyTable'] = 'No data available in table';
$string['datatable:sInfo'] = 'Showing _START_ to _END_ of _TOTAL_ entries';
$string['datatable:sInfoEmpty'] = 'Showing 0 to 0 of 0 entries';
$string['datatable:sInfoFiltered'] = '(filtered from _MAX_ total entries)';
$string['datatable:sInfoPostFix'] = '';
$string['datatable:sInfoThousands'] = ',';
$string['datatable:sLengthMenu'] = 'Show _MENU_ entries';
$string['datatable:sLoadingRecords'] = 'Loading...';
$string['datatable:sProcessing'] = 'Processing...';
$string['datatable:sSearch'] = 'Search:';
$string['datatable:sZeroRecords'] = 'No matching records found';
$string['datepickerdisplayformat'] = 'dd/mm/y';
$string['datepickerlongyeardisplayformat'] = 'dd/mm/yy';
$string['datepickerlongyearparseformat'] = 'd/m/Y';
$string['datepickerlongyearphpuserdate'] = '%d/%m/%Y';
$string['datepickerlongyearplaceholder'] = 'dd/mm/yyyy';
$string['datepickerlongyearregexjs'] = '[0-3][0-9]/(0|1)[0-9]/[0-9]{4}';
$string['datepickerlongyearregexphp'] = '@^(0?[1-9]|[12][0-9]|3[01])/(0?[1-9]|1[0-2])/([0-9]{4})$@';
$string['datepickerparseformat'] = 'd/m/y';
$string['datepickerphpuserdate'] = '%d/%m/%y';
$string['datepickerplaceholder'] = 'dd/mm/yy';
$string['datepickerregexjs'] = '[0-3][0-9]/(0|1)[0-9]/[0-9]{2}';
$string['datepickerregexphp'] = '@^(0?[1-9]|[12][0-9]|3[01])/(0?[1-9]|1[0-2])/([0-9]{2})$@';
$string['debugstatus'] = 'Debug status';
$string['delete'] = 'Delete';
$string['deleted'] = 'Deleted';
$string['developmentplan'] = 'Development Planner';
$string['downloaderrorlog'] = 'Download error log';
$string['editheading'] = 'Edit the Report Heading Block';
$string['elearning'] = 'E-learning';
$string['elementlibrary'] = 'Element Library';
$string['enabledisabletotarasync'] = 'Select Enable or Disable and then click continue to update Totara Sync for {$a}';
$string['enabletempmanagers'] = 'Enable temporary managers';
$string['enabletempmanagersdesc'] = 'Enable functionality that allows for assigning a temporary manager to a user. Disabling this will cause all current temporary managers to be unassigned on next cron run.';
$string['enrolled'] = 'Enrolled';
$string['error:addpdroom-dialognotselected'] = 'Please select a room';
$string['error:appraisernotselected'] = 'Please select an appraiser';
$string['error:assigncannotdeletegrouptypex'] = 'You cannot delete groups of type {$a}';
$string['error:assignmentbadparameters'] = 'Bad parameter array passed to dialog set_parameters';
$string['error:assignmentgroupnotallowed'] = 'You cannot assign groups of type {$a->grouptype} to {$a->module}';
$string['error:assignmentmoduleinstancelocked'] = 'You cannot make changes to an assignment module instance which is locked';
$string['error:assignmentprefixnotfound'] = 'Assignment class for group type {$a} not found';
$string['error:assigntablenotexist'] = 'Assignment table {$a} does not exist!';
$string['error:autoupdatedisabled'] = 'Automatic checking for Moodle updates is currently disabled in Totara';
$string['error:cannotupgradefrommoodle'] = 'You cannot upgrade to Totara 2.4 from a Moodle version prior to 2.2.7. Please upgrade to Totara 2.2.13+ or Moodle 2.2.7+ first.';
$string['error:cannotupgradefromtotara'] = 'You cannot upgrade to Totara 2.4 from this version of Totara. Please upgrade to Totara 2.2.13 or greater first.';
$string['error:categoryidincorrect'] = 'Category ID was incorrect';
$string['error:columntypenotfound'] = 'The column type \'{$a}\' was defined but is not a valid option. This can happen if you have deleted a custom field or hierarchy depth level. The best course of action is to delete this column by pressing the red cross to the right.';
$string['error:columntypenotfound11'] = 'The column type \'{$a}\' was defined but is not a valid option. This can happen if you have deleted a custom field or hierarchy type. The best course of action is to delete this column by pressing the red cross to the right.';
$string['error:couldnotcreatedefaultfields'] = 'Could not create default fields';
$string['error:couldnotupdatereport'] = 'Could not update report';
$string['error:courseidincorrect'] = 'Course id is incorrect.';
$string['error:dashboardnotfound'] = 'Cannot fully initialize page - could not retrieve dashboard details';
$string['error:datenotinfuture'] = 'The date needs to be in the future';
$string['error:dialognotreeitems'] = 'No items available';
$string['error:duplicaterecordsdeleted'] = 'Duplicate {$a} record deleted: ';
$string['error:duplicaterecordsfound'] = '{$a->count} duplicate record(s) found in the {$a->tablename} table...fixing (see error log for details)';
$string['error:importtimezonesfailed'] = 'Failed to update timezone information.';
$string['error:managernotselected'] = 'Please select a manager';
$string['error:morethanxitemsatthislevel'] = 'There are more than {$a} items at this level.';
$string['error:norolesfound'] = 'No roles found';
$string['error:notificationsparamtypewrong'] = 'Incorrect param type sent to Totara notifications';
$string['error:organisationnotselected'] = 'Please select an organisation';
$string['error:positionnotselected'] = 'Please select a position';
$string['error:positionvalidationfailed'] = 'The problems indicated below must be fixed before your changes can be saved.';
$string['error:staffmanagerroleexists'] = 'A role "staffmanager" already exists. This role must be renamed before the upgrade can proceed.';
$string['error:tempmanagerexpirynotset'] = 'An expiry date for the temporary manager needs to be set';
$string['error:tempmanagernotselected'] = 'Please select a temporary manager';
$string['error:tempmanagernotset'] = 'Temporary manager needs to be set';
$string['error:unknownbuttonclicked'] = 'Unknown button clicked';
$string['error:useridincorrect'] = 'User id is incorrect.';
$string['error:usernotfound'] = 'User not found';
$string['errorfindingcategory'] = 'Error finding the category';
$string['errorfindingprogram'] = 'Error finding the program';
$string['f2fbookings'] = 'Face to Face Bookings';
$string['facetoface'] = 'Face-to-face';
$string['findcourses'] = 'Find Courses';
$string['framework'] = 'Framework';
$string['heading'] = 'Heading';
$string['headingcolumnsdescription'] = 'The fields below define which data appear in the Report Heading Block. This block contains information about a specific user, and can appear in many locations throughout the site.';
$string['headingmissingvalue'] = 'Value to display if no data found';
$string['hierarchies'] = 'Hierarchies';
$string['icon'] = 'Icon';
$string['idnumberduplicates'] = 'Table: "{$a->table}". ID numbers: {$a->idnumbers}.';
$string['idnumberexists'] = 'Record with this ID number already exists';
$string['importtimezonesskipped'] = 'Skipped updating timezone information.';
$string['importtimezonessuccess'] = 'Timezone information updated from source {$a}.';
$string['inprogress'] = 'In Progress';
$string['installdemoquestion'] = 'Do you want to include demo data with this installation?<br /><br />(This will take a long time.)';
$string['installingdemodata'] = 'Installing Demo Data';
$string['invalidsearchtable'] = 'Invalid search table';
$string['itemstoadd'] = 'Items to add';
$string['lasterroroccuredat'] = 'Last error occured at {$a}';
$string['learningplans'] = 'Learning Plans';
$string['learningrecords'] = 'Learning Records';
$string['localpostinstfailed'] = 'There was a problem setting up local modifications to this installation.';
$string['managecertifications'] = 'Manage certifications';
$string['manager(s)'] = 'Manager(s)';
$string['managers'] = 'Manager\'s ';
$string['modulearchive'] = 'Activity archives';
$string['moodlecore'] = 'Moodle core';
$string['movedown'] = 'Move Down';
$string['moveup'] = 'Move Up';
$string['mybookings'] = 'My Bookings';
$string['mycoursecompletions'] = 'My Course Completions';
$string['mydevelopmentplans'] = 'My development plans';
$string['myfuturebookings'] = 'My Future Bookings';
$string['mylearning'] = 'My Learning';
$string['mypastbookings'] = 'My Past Bookings';
$string['myprofile'] = 'My Profile';
$string['myrecordoflearning'] = 'My Record of Learning';
$string['myreports'] = 'My Reports';
$string['myteam'] = 'My Team';
$string['myteaminstructionaltext'] = 'Choose a team member from the table on the right.';
$string['noassessors'] = 'No assessors found';
$string['none'] = 'None';
$string['noresultsfor'] = 'No results found for "{$a->query}".';
$string['nostaffassigned'] = 'You currently do not have a team.';
$string['notapplicable'] = 'Not applicable';
$string['notavailable'] = 'Not available';
$string['notenrolled'] = '<em>You are not currently enrolled in any courses.</em>';
$string['notfound'] = 'Not found';
$string['notimplementedtotara'] = 'Sorry, this feature is only implemented on MySQL, MSSQL and PostgreSQL databases.';
$string['numberofactiveusers'] = '{$a} users have logged in to this site in the last year';
$string['numberofstaff'] = '({$a} staff)';
$string['old_release_security_text_plural'] = ' (including [[SECURITY_COUNT]] new security releases)';
$string['old_release_security_text_singular'] = ' (including 1 new security release)';
$string['old_release_text_plural'] = 'You are not using the most recent release available for this version. There are [[ALLTYPES_COUNT]] new releases available ';
$string['old_release_text_singular'] = 'You are not using the most recent release available for this version. There is 1 new release available ';
$string['options'] = 'Options';
$string['organisation_typeicon'] = 'Organisation type icon';
$string['organisationatcompletion'] = 'Organisation at completion';
$string['organisationsarrow'] = 'Organisations > ';
$string['participant'] = 'Participant';
$string['pastbookingsfor'] = 'Past Bookings for ';
$string['performinglocalpostinst'] = 'Local Post-installation setup';
$string['pluginname'] = 'Totara core';
$string['pluginnamewithkey'] = 'Self enrolment with key';
$string['pos_description'] = 'Description';
$string['pos_description_help'] = 'Description of the position';
$string['position_typeicon'] = 'Position type icon';
$string['positionatcompletion'] = 'Position at completion';
$string['positionsarrow'] = 'Positions > ';
$string['poweredby'] = 'Powered by TotaraLMS';
$string['proficiency'] = 'Proficiency';
$string['progdoesntbelongcat'] = 'The program doesn\'t belong to this category';
$string['programicon'] = 'Program icon';
$string['queryerror'] = 'Query error. No results found.';
$string['recordnotcreated'] = 'Record could not be created';
$string['recordnotupdated'] = 'Record could not be updated';
$string['recordoflearning'] = 'Record of Learning';
$string['recordoflearningfor'] = 'Record of Learning for ';
$string['remotetotaralangnotavailable'] = 'Because Totara can not connect to download.totaralms.com, we are unable to do language pack installation automatically. Please download the appropriate zip file(s) from http://download.totaralms.com/lang/T{$a->totaraversion}/, copy them to your {$a->langdir} directory and unzip them manually.';
$string['report'] = 'Report';
$string['reportedat'] = 'Reported at';
$string['requiresjs'] = 'This {$a} requires Javascript to be enabled.';
$string['returntocourse'] = 'Return to the course';
$string['save'] = 'Save';
$string['search'] = 'Search';
$string['searchcourses'] = 'Search Courses';
$string['searchx'] = 'Search {$a}';
$string['selectanassessor'] = 'Select an assessor...';
$string['selectaproficiency'] = 'Select a proficiency...';
$string['settings'] = 'Settings';
$string['sitemanager'] = 'Site Manager';
$string['siteregistrationemailbody'] = 'Site {$a} was not able to register itself automatically. Access to push data to our registrations site is probably blocked by a firewall.';
$string['staffmanager'] = 'Staff Manager';
$string['startdate'] = 'Start Date';
$string['started'] = 'Started';
$string['strftimedateshortmonth'] = '%d %b %Y';
$string['supported_branch_old_release_text'] = 'You may also want to consider upgrading from {$a} to the most recent version ([[CURRENT_MAJOR_VERSION]]) to benefit from the latest features. ';
$string['supported_branch_text'] = 'You may want to consider upgrading from {$a} to the most recent version ([[CURRENT_MAJOR_VERSION]]) to benefit from the latest features. ';
$string['tab:futurebookings'] = 'Future Bookings';
$string['tab:pastbookings'] = 'Past Bookings';
$string['teammembers'] = 'Team Members';
$string['teammembers_text'] = 'All members of your team are shown below.';
$string['template'] = 'Template';
$string['tempmanager'] = 'Temporary manager';
$string['tempmanagerassignmsgmgr'] = '{$a->tempmanager} has been assigned as temporary manager to {$a->staffmember} (one of your team members).<br>Temporary manager expiry: {$a->expirytime}.<br>View details <a href="{$a->url}">here</a>.';
$string['tempmanagerassignmsgmgrsubject'] = '{$a->tempmanager} is now temporary manager for {$a->staffmember}';
$string['tempmanagerassignmsgstaff'] = '{$a->tempmanager} has been assigned as temporary manager to you.<br>Temporary manager expiry: {$a->expirytime}.<br>View details <a href="{$a->url}">here</a>.';
$string['tempmanagerassignmsgstaffsubject'] = '{$a->tempmanager} is now your temporary manager';
$string['tempmanagerassignmsgtmpmgr'] = 'You have been assigned as temporary manager to {$a->staffmember}.<br>Temporary manager expiry: {$a->expirytime}.<br>View details <a href="{$a->url}">here</a>.';
$string['tempmanagerassignmsgtmpmgrsubject'] = 'You are now {$a->staffmember}\'s temporary manager';
$string['tempmanagerexpiry'] = 'Temporary manager expiry date';
$string['tempmanagerexpiry_help'] = 'Click the calendar icon to select the date the temporary manager will expire.';
$string['tempmanagerexpirydays'] = 'Temporary manager expiry days';
$string['tempmanagerexpirydaysdesc'] = 'Set a default temporary manager expiry period (in days).';
$string['tempmanagerexpiryupdatemsgmgr'] = 'The expiry date for {$a->staffmember}\'s temporary manager ({$a->tempmanager}) has been updated to {$a->expirytime}.<br>View details <a href="{$a->url}">here</a>.';
$string['tempmanagerexpiryupdatemsgmgrsubject'] = 'Expiry date updated for {$a->staffmember}\'s temporary manager';
$string['tempmanagerexpiryupdatemsgstaff'] = 'The expiry date for {$a->tempmanager} (your temporary manager) has been updated to {$a->expirytime}.<br>View details <a href="{$a->url}">here</a>.';
$string['tempmanagerexpiryupdatemsgstaffsubject'] = 'Expiry date updated for your temporary manager';
$string['tempmanagerexpiryupdatemsgtmpmgr'] = 'Your expiry date as temporary manager for {$a->staffmember} has been updated to {$a->expirytime}.<br>View details <a href="{$a->url}">here</a>.';
$string['tempmanagerexpiryupdatemsgtmpmgrsubject'] = 'Temporary manager expiry updated for {$a->staffmember}';
$string['tempmanagerrestrictselection'] = 'Temporary manager selection';
$string['tempmanagerrestrictselectiondesc'] = 'Determine which users will be available in the temporary manager selection dialog. Selecting \'Only staff managers\' will remove any assigned temporary managers who don\'t have the \'staff manager\' role on the next cron run.';
$string['tempmanagers'] = 'Temporary managers';
$string['tempmanagerselectionallusers'] = 'All users';
$string['tempmanagerselectiononlymanagers'] = 'Only staff managers';
$string['tempmanagersupporttext'] = ' Note, only current team managers can be selected.';
$string['timecompleted'] = 'Time completed';
$string['toggletotarasync'] = 'Toggle Totara sync';
$string['toggletotarasyncerror'] = 'Could not enable/disable the totara sync field for user {$a}';
$string['totarabuild'] = 'Totara build number';
$string['totaracopyright'] = '<p class="totara-copyright"><a href="http://www.totaralms.com">TotaraLMS</a> is a distribution of Moodle. A "distro" or distribution is a ready-made extended version of the standard product with its own particular focus and vision. Totara is specifically designed for the requirements of corporate, industry and vocational training in contrast to standard Moodle\'s traditional educational setting.</p><p class="totara-copyright"><a href="http://www.totaralms.com">TotaraLMS</a> extensions are Copyright &copy; 2010 onwards, Totara Learning Solutions Limited.</p>';
$string['totaracore'] = 'Totara core';
$string['totaralogo'] = 'Totara Logo';
$string['totaramenu'] = 'Totara Menu';
$string['totararegistration'] = 'Totara Registration';
$string['totararegistrationinfo'] = '<p>This page configures registration updates which are sent to totaralms.com.
These updates allow Totara to know what versions of Totaralms and support software you are running.
This information will allow Totara to better examine and resolve any support issues you face in the future.</p>
<p>This information will be securely transmitted and held in confidence.</p>';
$string['totararelease'] = 'Totara release identifier';
$string['totarareleaselink'] = 'See the <a href="http://community.totaralms.com/mod/forum/view.php?id=819" target=\"_blank\">release notes</a> for more details.';
$string['totararequiredupgradeversion'] = 'Totara 2.2.13';
$string['totarauniqueidnumbercheckfail'] = 'The following tables contain non-unique values in the column idnumber:<br/><br/>
{$a}
<br/>
Please fix these records before attempting the upgrade.';
$string['totaraunsupportedupgradepath'] = 'You cannot upgrade directly to {$a->attemptedversion} from {$a->currentversion}. Please upgrade to at least {$a->required} before attempting the upgrade to {$a->attemptedversion}.';
$string['totaraupgradecheckduplicateidnumbers'] = 'Check duplicate ID numbers';
$string['totaraupgradesetstandardtheme'] = 'Enable Standard Totara theme';
$string['totaraversion'] = 'Totara version number';
$string['trysearchinginstead'] = 'Try searching instead.';
$string['type'] = 'Type';
$string['typeicon'] = 'Type icon';
$string['unassignall'] = 'Unassign all';
$string['undelete'] = 'Undelete';
$string['undeletecheckfull'] = 'Are you sure you want to undelete {$a}?';
$string['undeletednotx'] = 'Could not undelete {$a} !';
$string['undeletedx'] = 'Undeleted {$a}';
$string['undeleteuser'] = 'Undelete User';
$string['undeleteusernoperm'] = 'You do not have the required permission to undelete a user';
$string['unexpected_installer_result'] = 'Unspecified component install error: {$a}';
$string['unsupported_branch_text'] = 'The version you are using ({$a})  is no longer supported. That means that bugs and security issues are no longer being fixed. You should upgrade to a supported version (such as [[CURRENT_MAJOR_VERSION]]) as soon as possible';
$string['uploadcompletionrecords'] = 'Upload completion records';
$string['userdoesnotexist'] = 'User does not exist';
$string['viewmyteam'] = 'View My Team';
$string['xofy'] = '{$a->count} / {$a->total}';
$string['xpercent'] = '{$a}%';
$string['xpercentcomplete'] = '{$a} % complete';
$string['xpositions'] = '{$a}\'s Positions';
$string['xresultsfory'] = '<strong>{$a->count}</strong> results found for "{$a->query}"';


/*
 * LANG/EN/FILTERS.PHP
 */
 
 $string['actfilterhdr'] = 'Active filters';
$string['addfilter'] = 'Add filter';
$string['anycategory'] = 'any category';
$string['anycourse'] = 'any course';
$string['anyfield'] = 'any field';
$string['anyrole'] = 'any role';
$string['anyvalue'] = 'any value';
$string['matchesanyselected'] = 'matches any selected';
$string['matchesallselected'] = 'matches all selected';
$string['applyto'] = 'Apply to';
$string['categoryrole'] = 'Category role';
$string['filtercheckbox'] = 'Checkbox filter';
$string['filtercheckbox_help'] = '
This filter allows you to filter information based on a set of checkboxes.

The filter has the following options:

* is any value - this option disables the filter (i.e. all information is accepted by this filter)
* matches any selected - this option allows information, if it matches any of the checked options
* matches all selected - this option allows information, if it matches all of the checked options';
$string['filterdate'] = 'Date filter';
$string['filterdate_help'] = 'This filter allows you to filter information from before and/or after selected dates.';
$string['filternumber'] = 'Number filter';
$string['filternumber_help'] = '
This filter allows you to filter numerical information based on its value.

The filter has the following options:

* is equal to - this option allows only information that is equal to the text entered (if no text is entered, then the filter is disabled)
* is not equal to - this option allows only information that is not equal to the text entered (if no text is entered, then the filter is disabled)
* is greater than - this option allows only information that has a numerical value greater than the text entered (if no text is entered, then the filter is disabled)
* is greater than - this option allows only information that has a numerical value greater than the text entered (if no text is entered, then the filter is disabled)
* is less than - this option allows only information that has a numerical value less than the text entered (if no text is entered, then the filter is disabled)
* is greater than or equal to- this option allows only information that has a numerical value greater than or equal to the text entered (if no text is entered, then the filter is disabled)
* is less than or equal to- this option allows only information that has a numerical value less than or equal to the text entered (if no text is entered, then the filter is disabled)';
$string['filtersimpleselect'] = 'Simple select filter';
$string['filtersimpleselect_help'] = 'This filter allows you to filter information based on a drop down list. This filter does not have any extra options.';
$string['filtertext'] = 'Text filter';
$string['filtertext_help'] = '
This filter allows you to filter information based on a free form text.

The filter has the following options:

* contains - this option allows only information that contains the text entered (if no text is entered, then the filter is disabled)
* doesn\'t contain - this option allows only information that does not contain the text entered (if no text is entered, then the filter is disabled)
* is equal to - this option allows only information that is equal to the text entered (if no text is entered, then the filter is disabled)
* starts with - this option allows only information that starts with the text entered (if no text is entered, then the filter is disabled)
* ends with - this option allows only information that ends with the text entered (if no text is entered, then the filter is disabled)
* is empty - this option allows only information that is equal to an empty string (the text entered is ignored)';
$string['filterenrol'] = 'Enrol filter';
$string['filterenrol_help'] = 'This filter allows you to filter information based on whether a user is or isn\'t enrolled in a particular course.

The filter has the following options:

* Is any value - this option disables the filter (i.e. all information is accepted by this filter)
* Yes - this option only returns records where the user is enrolled in the specified course
* No - this option only returns records where the user is not enrolled in the specified course';
$string['filterselect'] = 'Select filter';
$string['filterselect_help'] = '
This filter allows you to filter information based on a drop down list.

The filter has the following options:

* is any value - this option disables the filter (i.e. all information is accepted by this filter)
* is equal to - this option allows only information that is equal to the value selected from the list
* is not equal to - this option allows only information that is different from the value selected from the list';
$string['contains'] = 'contains';
$string['content'] = 'Content';
$string['contentandheadings'] = 'Content and headings';
$string['courserole'] = 'Course role';
$string['courserolelabel'] = '{$a->label} is {$a->rolename} in {$a->coursename} from {$a->categoryname}';
$string['courserolelabelerror'] = '{$a->label} error: course {$a->coursename} does not exist';
$string['datelabelisafter'] = '{$a->label} is after {$a->after}';
$string['datelabelisbefore'] = '{$a->label} is before {$a->before}';
$string['datelabelisbetween'] = '{$a->label} is between {$a->after} and {$a->before}';
$string['defaultx'] = 'Default ({$a})';
$string['disabled'] = 'Disabled';
$string['doesnotcontain'] = 'doesn\'t contain';
$string['endswith'] = 'ends with';
$string['filterallwarning'] = 'Applying filters to headings as well as content can greatly increase the load on your server. Please use that \'Apply to\' settings sparingly. The main use is with the multilang filter.';
$string['filtersettings'] = 'Filter settings';
$string['filtersettings_help'] = 'This page lets you turn filters on or off in a particular part of the site.

Some filters may also let you set local settings, in which case there will be a settings link next to their name.';
$string['filtersettingsforin'] = 'Filter settings for {$a->filter} in {$a->context}';
$string['filtersettingsin'] = 'Filter settings in {$a}';
$string['firstaccess'] = 'First access';
$string['globalrolelabel'] = '{$a->label} is {$a->value}';
$string['includesubcategories'] = 'Include sub-categories?';
$string['isactive'] = 'Active?';
$string['isafter'] = 'is after';
$string['isanyvalue'] = 'is any value';
$string['isbefore'] = 'is before';
$string['isdefined'] = 'is defined';
$string['isempty'] = 'is empty';
$string['isequalto'] = 'is equal to';
$string['isgreaterthan'] = 'is greater than';
$string['islessthan'] = 'is less than';
$string['isgreaterorequalto'] = 'is greater than or equal to';
$string['islessthanorequalto'] = 'is less than or equal to';
$string['isenrolled'] = 'The user is enrolled in the course';
$string['isnotenrolled'] = 'The user is not enrolled in the course';
$string['isnotdefined'] = 'isn\'t defined';
$string['isnotequalto'] = 'isn\'t equal to';
$string['neveraccessed'] = 'Never accessed';
$string['nevermodified'] = 'Never modified';
$string['newfilter'] = 'New filter';
$string['nofiltersenabled'] = 'No filter plugins have been enabled on this site.';
$string['off'] = 'Off';
$string['offbutavailable'] = 'Off, but available';
$string['on'] = 'On';
$string['profilelabel'] = '{$a->label}: {$a->profile} {$a->operator} {$a->value}';
$string['profilelabelnovalue'] = '{$a->label}: {$a->profile} {$a->operator}';
$string['removeall'] = 'Remove all filters';
$string['removeselected'] = 'Remove selected';
$string['selectlabel'] = '{$a->label} {$a->operator} {$a->value}';
$string['selectlabelnoop'] = '{$a->label} {$a->value}';
$string['startswith'] = 'starts with';
$string['tablenosave'] = 'Changes in table above are saved automatically.';
$string['textlabel'] = '{$a->label} {$a->operator} {$a->value}';
$string['textlabelnovalue'] = '{$a->label} {$a->operator}';

// lang file for mod/scorm/rb_sources_scorm.php
$string['sourcetitle_scorm'] = 'SCORM';
// columns
$string['scormtitle'] = 'SCORM Title';
$string['title'] = 'SCO Title';
$string['time'] = 'SCO Start Time';
$string['scostatus'] = 'SCO Status';
$string['statusmodified'] = 'SCO Status Modified';
$string['totaltime'] = 'SCO Total TIme';
$string['score'] = 'SCO Score';
$string['minscore'] = 'SCO Min Score';
$string['maxscore'] = 'SCO Max Score';
$string['attemptnum'] = 'SCO Attempt Number';

// filters
$string['attemptstart'] = 'Attempt Start Time';
$string['rawscore'] = 'Score';
$string['rawmin'] = 'Minimum Score';
$string['rawmax'] = 'Maximum Score';
// content
$string['currentorg'] = 'The user\'s current organisation';
$string['theuser'] = 'The user';
$string['thedate'] = 'The attempt date';
// list
$string['passed'] = 'Passed';
$string['completed'] = 'Completed';
$string['notattempted'] = 'Not Attempted';
$string['incomplete'] = 'Incomplete';
$string['failed'] = 'Failed';

// column types for this source, as strings
$string['type_scorm'] = 'SCORM';
$string['type_sco'] = 'SCO';

// lang file for report/reportbuilder/rb_sources/rb_source_site_logs.php
$string['sourcetitle_site_logs'] = 'Site Logs';
// columns
$string['time'] = 'Time';
$string['ip'] = 'IP address';
$string['module'] = 'Module';
$string['cmid'] = 'CMID';
$string['action'] = 'Action';
$string['actionlink'] = 'Action (linked to url)';
$string['url'] = 'URL';
$string['info'] = 'Info';
// content
$string['currentorg'] = 'The user\'s current organisation';
$string['currentpos'] = 'The user\'s current position';
$string['user'] = 'The user';
$string['date'] = 'The date';

// column types for this source, as strings
$string['type_log'] = 'Log';

// lang file for report/reportbuilder/rb_sources/rb_source_courses.php
$string['sourcetitle_courses'] = 'Courses';
// columns
$string['content'] = 'Content';
// filters
$string['coursecontent'] = 'Course Content';
// content
$string['startdate'] = 'The start date';

// lang strings for report builder 'user' source
$string['sourcetitle_user'] = 'User';
$string['userspicture'] = 'User\'s Picture';
$string['lastlogin'] = 'Last Login';
$string['timecreated'] = 'Time created';
$string['mylearningicons'] = 'User\'s My Learning Icons';
$string['name'] = 'Name';
$string['usersachievedcompcount'] = 'User\'s Achieved Competency Count';
$string['userscoursestartedcount'] = 'User\'s Courses Started Count';
$string['userscoursescompletedcount'] = 'User\'s Courses Completed Count';
$string['usernamewithlearninglinks'] = 'User Fullname (with links to learning components)';
$string['usersname'] = 'User\'s Name';
$string['user'] = 'User';
$string['users'] = 'Users';
$string['picture'] = 'Picture';
$string['options'] = 'Options';
$string['coursesstarted'] = 'Courses Started';
$string['coursescompleted'] = 'Courses Completed';
$string['competenciesachieved'] = 'Competencies Achieved';

$string['records'] = 'Records';
$string['required'] = 'Required';
$string['plans'] = 'Plans';
$string['profile'] = 'Profile';
$string['bookings'] = 'Bookings';

// column types for this source, as strings
$string['type_statistics'] = 'Statistics';
