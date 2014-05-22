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
 * @author Eugene Venter <eugene@catalyst.net.nz>
 * @author Alastair Munro <alastair.munro@totaralms.com>
 * @package report
 * @subpackage reportbuilder
 */

/**
 * Main Class definition and library functions for report builder
 */

require_once($CFG->dirroot . '/calendar/lib.php');
require_once($CFG->dirroot . '/report/reportbuilder/filters/lib.php');
require_once($CFG->dirroot . '/totara/core/lib/scheduler.php');
require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->dirroot . '/totara/core/lib.php');
require_once($CFG->dirroot . '/report/reportbuilder/classes/rb_base_source.php');
require_once($CFG->dirroot . '/report/reportbuilder/classes/rb_base_content.php');
require_once($CFG->dirroot . '/report/reportbuilder/classes/rb_base_access.php');
require_once($CFG->dirroot . '/report/reportbuilder/classes/rb_base_preproc.php');
require_once($CFG->dirroot . '/report/reportbuilder/classes/rb_base_embedded.php');
require_once($CFG->dirroot . '/report/reportbuilder/classes/rb_join.php');
require_once($CFG->dirroot . '/report/reportbuilder/classes/rb_column.php');
require_once($CFG->dirroot . '/report/reportbuilder/classes/rb_column_option.php');
require_once($CFG->dirroot . '/report/reportbuilder/classes/rb_filter_option.php');
require_once($CFG->dirroot . '/report/reportbuilder/classes/rb_param.php');
require_once($CFG->dirroot . '/report/reportbuilder/classes/rb_param_option.php');
require_once($CFG->dirroot . '/report/reportbuilder/classes/rb_content_option.php');

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

/**
 * Content mode options
 */
define('REPORT_BUILDER_CONTENT_MODE_NONE', 0);
define('REPORT_BUILDER_CONTENT_MODE_ANY', 1);
define('REPORT_BUILDER_CONTENT_MODE_ALL', 2);

/**
 * Access mode options
 */
define('REPORT_BUILDER_ACCESS_MODE_NONE', 0);
define('REPORT_BUILDER_ACCESS_MODE_ANY', 1);
define('REPORT_BUILDER_ACCESS_MODE_ALL', 2);

/**
 * Export option codes
 *
 * Bitwise flags, so new ones should be double highest value
 */
define('REPORT_BUILDER_EXPORT_EXCEL', 1);
define('REPORT_BUILDER_EXPORT_CSV', 2);
define('REPORT_BUILDER_EXPORT_ODS', 4);
define('REPORT_BUILDER_EXPORT_FUSION', 8);
define('REPORT_BUILDER_EXPORT_PDF_PORTRAIT', 16);
define('REPORT_BUILDER_EXPORT_PDF_LANDSCAPE', 32);

/**
 * PDF export memory limit (in MBs).
 */
define('REPORT_BUILDER_EXPORT_PDF_MEMORY_LIMIT', 1024);

/*
 * Initial Display Options
 */
define('RB_INITIAL_DISPLAY_SHOW', 0);
define('RB_INITIAL_DISPLAY_HIDE', 1);

/**
 * Report cache status flags
 */
define('RB_CACHE_FLAG_OK', 0);
define('RB_CACHE_FLAG_CHANGED', 1);
define('RB_CACHE_FLAG_FAIL', 2);

global $REPORT_BUILDER_EXPORT_OPTIONS;
$REPORT_BUILDER_EXPORT_OPTIONS = array(
    'xls'           => REPORT_BUILDER_EXPORT_EXCEL,
    'csv'           => REPORT_BUILDER_EXPORT_CSV,
    'ods'           => REPORT_BUILDER_EXPORT_ODS,
    'fusion'        => REPORT_BUILDER_EXPORT_FUSION,
    'pdf_portrait'  => REPORT_BUILDER_EXPORT_PDF_PORTRAIT,
    'pdf_landscape' => REPORT_BUILDER_EXPORT_PDF_LANDSCAPE,
);

/**
 *  Export to file system constants.
 *
 */
define('REPORT_BUILDER_EXPORT_EMAIL', 0);
define('REPORT_BUILDER_EXPORT_EMAIL_AND_SAVE', 1);
define('REPORT_BUILDER_EXPORT_SAVE', 2);

global $REPORT_BUILDER_EXPORT_FILESYSTEM_OPTIONS;
$REPORT_BUILDER_EXPORT_FILESYSTEM_OPTIONS = array(
    'exporttoemail' => REPORT_BUILDER_EXPORT_EMAIL,
    'exporttoemailandsave' => REPORT_BUILDER_EXPORT_EMAIL_AND_SAVE,
    'exporttosave' => REPORT_BUILDER_EXPORT_SAVE
);

// Maximum allowed time for report caching
define('REPORT_CACHING_TIMEOUT', 3600);

/**
 *  Pdf export constants.
 *
 */
define('PDF_FONT_SIZE_DATA', 10);
define('PDF_FONT_SIZE_RECORD', 14);
define('PDF_FONT_SIZE_TITLE', 20);
define('PDF_MARGIN_FOOTER', 10);
define('PDF_MARGIN_BOTTOM', 20);

/**
 * EDITOR_UNLIMITED_FILES - hard-coded value for the 'maxfiles' option
 */
//define('EDITOR_UNLIMITED_FILES', -1);
/**
* Totara textarea options
*/

$totara_maxbytes = get_max_upload_file_size();
$totara_context = context_system::instance();
global $TEXTAREA_OPTIONS;
$TEXTAREA_OPTIONS = array(
        'subdirs' => 0,
        'maxfiles' => EDITOR_UNLIMITED_FILES,
        'maxbytes' => $totara_maxbytes,
        'trusttext' => true,
        'context' => $totara_context,
        'collapsed' => true
);

global $FILEPICKER_OPTIONS;
$FILEPICKER_OPTIONS = array(
        'maxbytes' => $totara_maxbytes,
        'maxfiles' => '1',
        'subdirs' => 0,
        'context' => $totara_context
);

/**
 * Main report builder object class definition
 */
class reportbuilder {
    /**
     * Available filter settings
     */
    const FILTERNONE = 0;
    const FILTER = 1;
    const FILTERALL = 2;

    public $fullname, $shortname, $source, $hidden, $filters, $filteroptions, $columns, $requiredcolumns;
    public $columnoptions, $_filtering, $contentoptions, $contentmode, $embeddedurl, $description;
    public $_id, $recordsperpage, $defaultsortcolumn, $defaultsortorder;
    private $_joinlist, $_base, $_params, $_sid;
    private $_paramoptions, $_embeddedparams, $_fullcount, $_filteredcount;
    public $src, $grouped, $reportfor, $badcolumns, $embedded;
    private $_post_config_restrictions;

    /**
     * @var bool $cache Cache state for current report
     */
    public $cache;

    /**
     *
     * @var bool $cacheignore If true cahce will be ignored during report preparation
     */
    public $cacheignore = false;

    /**
     * @var stdClass $cacheschedule Record of cache scheduling and readyness
     */
    public $cacheschedule = null;

    /**
     * Constructor for reportbuilder object
     *
     * Generates a new reportbuilder report instance.
     *
     * Requires either a valid ID or shortname as parameters.
     *
     * Note: If a report is embedded then it is now guaranteed to have its embedded object loaded.
     * Previously, embedded reports were required to create the embedded object and pass it to this constructor in the
     * $embed_deprecated parameter. Now, this constructor will create the embedded object. The data required by the embedded
     * object should be passed in the $embeddata parameter.
     *
     * @param integer $id ID of the report to generate
     * @param string $shortname Shortname of the report to generate
     * @param object $embed_deprecated Object containing settings for an embedded report - see note above
     * @param integer $sid Saved search ID if displaying a saved search
     * @param integer $reportfor User ID of user who is viewing the report
     *                           (or null to use the current user)
     * @param bool $nocache Force no cache usage. Only works if cache for current report is enabled
     *                       and generated
     * @param array $embeddata data to be passed to the embedded object constructor
     *
     */
    public function __construct($id=null, $shortname=null, $embed_deprecated=false, $sid=null, $reportfor=null,
            $nocache = false, $embeddata = array()) {
        global $USER, $DB;

        if ($id != null) {
            // look for existing report by id
            $report = $DB->get_record('report_builder', array('id' => $id), '*', IGNORE_MISSING);
        } else if ($shortname != null) {
            // look for existing report by shortname
            $report = $DB->get_record('report_builder', array('shortname' => $shortname), '*', IGNORE_MISSING);
        } else {
            // either id or shortname is required
            print_error('noshortnameorid', 'report_reportbuilder');
        }

        // Handle if report not found in db.
        $embed = null;
        if (!$report) {
            // Determine if this is an embedded report with a missing embedded record.
            if ($embed_deprecated) {
                $embed = $embed_deprecated;
            } else if ($shortname !== null) {
                $embed = reportbuilder_get_embedded_report_object($shortname, $embeddata);
            }
            if ($embed) {
                // This is an embedded report - maybe this is the first time we have run it, so try to create it.
                if (! $id = reportbuilder_create_embedded_record($shortname, $embed, $error)) {
                    print_error('error:creatingembeddedrecord', 'report_reportbuilder', '', $error);
                }
                $report = $DB->get_record('report_builder', array('id' => $id));
            }
        }

        if (!$report) {
            print_error('reportwithidnotfound', 'report_reportbuilder', '', $id);
        }

        // If this is an embedded report then load the embedded report object.
        if ($report->embedded && !$embed) {
            $embed = reportbuilder_get_embedded_report_object($report->shortname, $embeddata);
        }

        $this->_id = $report->id;
        $this->source = $report->source;
        $this->src = self::get_source_object($this->source);
        $this->shortname = $report->shortname;
        $this->fullname = $report->fullname;
        $this->hidden = $report->hidden;
        $this->initialdisplay = $report->initialdisplay;
        $this->cache = $report->cache;
        $this->cacheignore = $nocache;
        $this->description = $report->description;
        $this->embedded = $report->embedded;
        $this->contentmode = $report->contentmode;
        // Store the embedded URL for embedded reports only.
        if ($report->embedded && $embed) {
            $this->embeddedurl = $embed->url;
        }
        $this->embedobj = $embed;
        $this->recordsperpage = $report->recordsperpage;
        $this->defaultsortcolumn = $report->defaultsortcolumn;
        $this->defaultsortorder = $report->defaultsortorder;
        $this->_sid = $sid;
        // Assume no grouping initially.
        $this->grouped = false;
        $this->badcolumns = array();

        // Load report cache - requires column options.
        $restored = false;
        if (!$this->cacheignore && $report->cache) {
            if ($this->restore_cached_state()) {
                $restored = true;
            }
        }

        // Determine who is viewing or receiving the report.
        // Used for access and content restriction checks.
        if (isset($reportfor)) {
            $this->reportfor = $reportfor;
        } else {
            $this->reportfor = $USER->id;
        }

        if ($sid) {
            $this->restore_saved_search();
        }

        // Before we pull in the rest of the data, get the parameters and call the post_config method.
        // This allows the source to configure additional tables and columns based on the parameters.
        $this->_paramoptions = $this->src->paramoptions;
        if ($embed) {
            $this->_embeddedparams = $embed->embeddedparams;
        }
        $this->_params = $this->get_current_params();
        $this->src->post_config($this);

        // Pull in the rest of the data for this report from the source.
        $this->_base = $this->src->base . ' base';
        $this->columnoptions = $this->src->columnoptions;
        $this->filteroptions = $this->src->filteroptions;
        $this->contentoptions = $this->src->contentoptions;
        $this->requiredcolumns = $this->src->requiredcolumns;
        if ($restored) {
            $this->columns = $this->get_columns($this->columns);
            $this->filters = $this->get_filters($this->filters);
        } else {
            $this->columns = $this->get_columns();
            $this->filters = $this->get_filters();
        }
        $this->_joinlist = $this->src->joinlist;

        $this->process_filters();
    }


    /**
     * Shortcut to function in report source.
     *
     * This may be called before data is generated for a report (e.g. embedded report page, report.php).
     * It should not be called when data will not be generated (e.g. report setup/config pages).
     */
    public function handle_pre_display_actions() {
        $this->src->pre_display_actions();
    }


    /**
     * Include javascript code needed by report builder
     */
    function include_js() {
        global $CFG, $PAGE, $SESSION;
        require_once($CFG->dirroot . '/totara/core/js/lib/setup.php');

        $dialog = false;
        $treeview = false;

        // Get any required js files that are specified by the source.
        $js = $this->src->get_required_jss();

        // Only include show/hide code for tabular reports.
        $graph = (substr($this->source, 0,
            strlen('graphical_feedback_questions')) ==
            'graphical_feedback_questions');
        if (!$graph) {
            $jsdetails = new stdClass();
            $jsdetails->initcall = 'M.report_reportbuilder_showhide.init';
            $jsdetails->jsmodule = array('name' => 'report_reportbuilder_showhide',
                'fullpath' => '/report/reportbuilder/showhide.js');
            $jsdetails->args = array('hiddencols' => $this->js_get_hidden_columns());
            $jsdetails->strings = array(
                'report_reportbuilder' => array('showhidecolumns'),
                'moodle' => array('ok')
            );
            $js[] = $jsdetails;
            $dialog = true;
        }

        // include JS for dialogs if required for filters
        foreach ($this->filters as $filter) {
            if (in_array($filter->filtertype, array('hierarchy', 'hierarchy_multi', 'cohort'))) {
                $jsdetails = new stdClass();
                $jsdetails->initcall = 'M.report_reportbuilder_filterdialogs.init';
                $jsdetails->jsmodule = array('name' => 'report_reportbuilder_filterdialogs',
                    'fullpath' => '/report/reportbuilder/filter_dialogs.js');
                $jsdetails->strings = array(
                    'totara_hierarchy' => array('chooseposition', 'selected', 'chooseorganisation', 'currentlyselected', 'selectcompetency'),
                    'report_reportbuilder' => array('chooseorgplural', 'chooseposplural', 'choosecompplural'),
                    'totara_cohort' => array('choosecohorts')
                );

                // add currently selected as args
                $cargs = array();
                foreach ($this->filters as $f) {
                    if ($f->filtertype != 'hierarchy') {
                        continue;
                    }
                    $title = $f->type . '-' . $f->value;
                    $currentlyselected = json_encode(dialog_display_currently_selected(
                        get_string('currentlyselected', 'totara_hierarchy'), $title));
                    $cargs[] = "\"{$title}-currentlyselected\":{$currentlyselected}";
                }
                if (!empty($cargs)) {
                    $cargs = implode(', ', $cargs);
                    $jsdetails->args = array('args' => '{' . $cargs . '}');
                }

                $js[] = $jsdetails;

                $dialog = $treeview = true;
                break;
            }
        }


        $code = array();
        if ($dialog) {
            $code[] = TOTARA_JS_DIALOG;
        }
        if ($treeview) {
            $code[] = TOTARA_JS_TREEVIEW;
        }


        local_js($code);
        //error_log(print_r($code));
        foreach ($js as $jsdetails) {
            if (!empty($jsdetails->strings)) {
                foreach ($jsdetails->strings as $scomponent => $sstrings) {
                    $PAGE->requires->strings_for_js($sstrings, $scomponent);
                }
            }

            $PAGE->requires->js_init_call($jsdetails->initcall,
                empty($jsdetails->args) ? null : $jsdetails->args,
                false, $jsdetails->jsmodule);
        }

    }


    /**
     * Method for debugging SQL statement generated by report builder
     */
    function debug($level=1) {
        global $OUTPUT;
        $context = context_system::instance();
        if (!is_siteadmin()) {
            return false;
        }
        list($sql, $params) = $this->build_query(false, true);
        echo $OUTPUT->heading('Query', 3);
        echo html_writer::tag('pre', $sql, array('class' => 'notifymessage'));
        echo $OUTPUT->heading('Query params', 3);
        echo html_writer::tag('pre', s(print_r($params, true)), array('class' => 'notifymessage'));
        if ($level > 1) {
            echo $OUTPUT->heading('Reportbuilder Object', 3);
            echo html_writer::tag('pre', s(print_r($this, true)), array('class' => 'notifymessage'));
        }
    }

    /**
     * Searches for and returns an instance of the specified preprocessor class
     * for a particular activity group
     *
     * @param string $preproc The name of the preproc class to return
     *                       (excluding the rb_preproc prefix)
     * @param integer $groupid The group id to create the preprocessor for
     * @return object An instance of the preproc. Returns false if
     *                the preproc can't be found
     */
    static function get_preproc_object($preproc, $groupid) {
        $sourcepaths = self::find_source_dirs();
        foreach ($sourcepaths as $sourcepath) {
            $classfile = $sourcepath . 'rb_preproc_' . $preproc . '.php';
            if (is_readable($classfile)) {
                include_once($classfile);
                $classname = 'rb_preproc_' . $preproc;
                if (class_exists($classname)) {
                    return new $classname($groupid);
                }
            }
        }
        return false;
    }

    /**
     * Searches for and returns an instance of the specified source class
     *
     * @param string $source The name of the source class to return
     *                       (excluding the rb_source prefix)
     * @return object An instance of the source. Returns false if
     *                the source can't be found
     */
    static function get_source_object($source) {
        $sourcepaths = self::find_source_dirs();
        foreach ($sourcepaths as $sourcepath) {
            $classfile = $sourcepath . 'rb_source_' . $source . '.php';
            if (is_readable($classfile)) {
                include_once($classfile);
                $classname = 'rb_source_' . $source;
                if (class_exists($classname)) {
                    return new $classname();
                }
            }
        }

        // if exact match not found, look for match with group suffix
        // of the form: [sourcename]_grp_[grp_id]
        // if found, call the base source passing the groupid as an argument
        if (preg_match('/^(.+)_grp_([0-9]+)$/', $source, $matches)) {
            $basesource = $matches[1];
            $groupid = $matches[2];
            foreach ($sourcepaths as $sourcepath) {
                $classfile = $sourcepath . 'rb_source_' . $basesource . '.php';
                if (is_readable($classfile)) {
                    include_once($classfile);
                    $classname = 'rb_source_' . $basesource;
                    if (class_exists($classname)) {
                        return new $classname($groupid);
                    }
                }
            }
        }

        // if still not found, look for match with group suffix
        // of the form: [sourcename]_grp_all
        // if found, call the base source passing a groupid of 0 as an argument
        if (preg_match('/^(.+)_grp_all$/', $source, $matches)) {
            $basesource = $matches[1];
            foreach ($sourcepaths as $sourcepath) {
                $classfile = $sourcepath . 'rb_source_' . $basesource . '.php';
                if (is_readable($classfile)) {
                    include_once($classfile);
                    $classname = 'rb_source_' . $basesource;
                    if (class_exists($classname)) {
                        return new $classname(0);
                    }
                }
            }
        }


        // bad source
        throw new ReportBuilderException("Source '$source' not found");
    }

    /**
     * Searches codebase for report builder source files and returns a list
     *
     * @param bool $includenonselectable If true then include sources even if they can't be used in custom reports (for testing)
     * @return array Associative array of all available sources, formatted
     *               to be used in a select element.
     */
    public static function get_source_list($includenonselectable = false) {
        global $DB;

        $output = array();

        foreach (self::find_source_dirs() as $dir) {
            if (is_dir($dir) && $dh = opendir($dir)) {
                while(($file = readdir($dh)) !== false) {
                    if (is_dir($file) ||
                    !preg_match('|^rb_source_(.*)\.php$|', $file, $matches)) {
                        continue;
                    }
                    $source = $matches[1];
                    $src = reportbuilder::get_source_object($source);
                    $sourcename = $src->sourcetitle;
                    $preproc = $src->preproc;

                    if ($src->selectable || $includenonselectable) {
                        if ($src->grouptype == 'all') {
                            $sourcestr = $source . '_grp_all';
                            $output[$sourcestr] = $sourcename;
                        } else if ($src->grouptype != 'none') {
                            // Create a source for every group that's based on this source's preprocessor.
                            $groups = $DB->get_records('report_builder_group', array('preproc' => $preproc));
                            foreach ($groups as $group) {
                                $sourcestr = $source . '_grp_' . $group->id;
                                $output[$sourcestr] = $sourcename . ': ' . $group->name;
                            }
                        } else {
                            // Otherwise, just create a single source.
                            $output[$source] = $sourcename;
                        }
                    }
                }
                closedir($dh);
            }
        }
        asort($output);
        return $output;
    }

    /**
     * Gets list of source directories to look in for source files
     *
     * @return array An array of paths to source directories
     */
    static function find_source_dirs() {
        global $CFG;

        $sourcepaths = array();
        
        //need to fix for core_component::get_plugin_list('mod')
        
        // search for mod/*/rb_sources/ directories
        foreach(core_component::get_plugin_list('mod') as $mod) {
           $dir = "$mod/rb_sources/";
           if (file_exists($dir) && is_dir($dir)) {
               $sourcepaths[] = $dir;
           }
        }

        // search for mod/*/rb_sources/ directories
        /*foreach (get_list_of_plugins('mod') as $mod) {
            $dir = "{$CFG->dirroot}/mod/$mod/rb_sources/";
            if (file_exists($dir) && is_dir($dir)) {
                $sourcepaths[] = $dir;
            }
        }*/
        
        // search for report/*/rb_sources/ directories
        foreach(core_component::get_plugin_list('report', 'db') as $reportmod) {
           $dir = "$reportmod/rb_sources/";
           if (file_exists($dir) && is_dir($dir)) {
               $sourcepaths[] = $dir;
           }
        }
        
        // search for report/*/rb_sources/ directories
        /*foreach (get_list_of_plugins('report', 'db') as $reportmod) {
            $dir = "{$CFG->dirroot}/report/$reportmod/rb_sources/";
            if (file_exists($dir) && is_dir($dir)) {
                $sourcepaths[] = $dir;
            }
        }*/
        
        // search for blocks/*/rb_sources/ directories
        foreach(core_component::get_plugin_list('blocks', 'db') as $block) {
           $dir = "$block/rb_sources/";
           if (file_exists($dir) && is_dir($dir)) {
               $sourcepaths[] = $dir;
           }
        }

        // search for blocks/*/rb_sources/ directories
        /*foreach (get_list_of_plugins('blocks', 'db') as $block) {
            $dir = "{$CFG->dirroot}/blocks/$block/rb_sources/";
            if (file_exists($dir) && is_dir($dir)) {
                $sourcepaths[] = $dir;
            }
        }*/
        
        // search for admin/tool/*/rb_sources/ directories
        foreach(core_component::get_plugin_list('admintool', 'db') as $tool) {
           $dir = "$tool/rb_sources/";
           if (file_exists($dir) && is_dir($dir)) {
               $sourcepaths[] = $dir;
           }
        }

        // search for admin/tool/*/rb_sources/ directories
        /*foreach (get_list_of_plugins('admin/tool', 'db') as $tool) {
            $dir = "{$CFG->dirroot}/admin/tool/$tool/rb_sources/";
            if (file_exists($dir) && is_dir($dir)) {
                $sourcepaths[] = $dir;
            }
        }*/
        
        // search for totara/*/rb_sources/ directories
        foreach(core_component::get_plugin_list('totara', 'db') as $totaramod) {
           $dir = "$totaramod/rb_sources/";
           if (file_exists($dir) && is_dir($dir)) {
               $sourcepaths[] = $dir;
           }
        }

        // search for totara/*/rb_sources/ directories
        /*foreach (get_list_of_plugins('totara', 'db') as $totaramod) {
            $dir = "{$CFG->dirroot}/totara/$totaramod/rb_sources/";
            if (file_exists($dir) && is_dir($dir)) {
                $sourcepaths[] = $dir;
            }
        }*/
        
        // search for local/*/rb_sources/ directories for local customisations
        foreach(core_component::get_plugin_list('local', 'db') as $localmod) {
           $dir = "$localmod/rb_sources/";
           if (file_exists($dir) && is_dir($dir)) {
               $sourcepaths[] = $dir;
           }
        }

        // search for local/*/rb_sources/ directories for local customisations
        /*foreach (get_list_of_plugins('local', 'db') as $localmod) {
            $dir = "{$CFG->dirroot}/local/$localmod/rb_sources/";
            if (file_exists($dir) && is_dir($dir)) {
                $sourcepaths[] = $dir;
            }
        }*/

        return $sourcepaths;
    }


    /**
     * Reduces an array of objects to those that match all specified conditions
     *
     * @param array $items An array of objects to reduce
     * @param array $conditions An associative array of conditions.
     *                          key is the object's property, value is the value
     *                          to match against
     * @param boolean $multiple If true, returns all matches, as an array,
     *                          otherwise returns first match as an object
     *
     * @return mixed An array of objects or a single object that match all
     *               the conditions
     */
    static function reduce_items($items, $conditions, $multiple=true) {
        if (!is_array($items)) {
            throw new ReportBuilderException('Input not an array');
        }
        if (!is_array($conditions)) {
            throw new ReportBuilderException('Conditions not an array');
        }
        $output = array();
        foreach ($items as $item) {
            $status = true;
            foreach ($conditions as $name => $value) {
                // condition fails if property missing
                if (!property_exists($item, $name)) {
                    $status = false;
                    break;
                }
                if ($item->$name != $value) {
                    $status = false;
                    break;
                }
            }
            if ($status && $multiple) {
                $output[] = $item;
            } else if ($status) {
                return $item;
            }
        }
        return $output;
    }

    static function get_single_item($items, $type, $value) {
        $cond = array('type' => $type, 'value' => $value);
        return self::reduce_items($items, $cond, false);
    }


    /**
     * Check the joins provided are in the joinlist
     *
     * @param array $joinlist Join list to check for joins
     * @param mixed $joins Single, or array of joins to check
     * @returns boolean True if all specified joins are in the list
     *
     */
    static function check_joins($joinlist, $joins) {
        // nothing to check
        if ($joins === null) {
            return true;
        }

        // get array of available names from join list provided
        $joinnames = array('base');
        foreach ($joinlist as $item) {
            $joinnames[] = $item->name;
        }

        // return false if any listed joins don't exist
        if (is_array($joins)) {
            foreach ($joins as $join) {
                if (!in_array($join, $joinnames)) {
                    return false;
                }
            }
        } else {
            if (!in_array($joins, $joinnames)) {
                return false;
            }
        }
        return true;
    }


    /**
     * Looks up the saved search ID specified and attempts to restore
     * the SESSION variable if access is permitted
     *
     * @return Boolean True if user can view, error otherwise
     */
    function restore_saved_search() {
        global $SESSION, $DB;
        if ($saved = $DB->get_record('report_builder_saved', array('id' => $this->_sid))) {

            if ($saved->ispublic != 0 || $saved->userid == $this->reportfor) {
                $SESSION->reportbuilder[$this->_id] = unserialize($saved->search);
            } else {
                if (defined('FULLME') and FULLME === 'cron') {
                    mtrace('Saved search not found or search is not public');
                } else {
                    print_error('savedsearchnotfoundornotpublic', 'report_reportbuilder');
                }
                return false;
            }
        } else {
            if (defined('FULLME') and FULLME === 'cron') {
                mtrace('Saved search not found or search is not public');
            } else {
                print_error('savedsearchnotfoundornotpublic', 'report_reportbuilder');
            }
            return false;
        }
        return true;
    }



    /**
     * Gets any filters set for the current report from the database
     *
     * @param array $filters predefined set of filters
     * @return array Array of filters for current report or empty array if none set
     */
    function get_filters(array $filters = array()) {
        global $DB;

        $out = array();
        if (empty($filters)) {
            $filters = $DB->get_records('report_builder_filters', array('reportid' => $this->_id), 'sortorder');
        }
        foreach ($filters as $filter) {
            $type = $filter->type;
            $value = $filter->value;
            $advanced = $filter->advanced;
            $name = "{$filter->type}-{$filter->value}";

            // To properly support multiple languages - only use value in database if it's different from the default.
            // If it's the same as the default for that filter, use the default string directly.
            if (isset($filter->customname)) {
                // Use value from database.
                $filtername = $filter->filtername;
            } else {
                // Use default value.
                $defaultnames = $this->get_default_headings_array();
                $filtername = isset($defaultnames[$filter->type . '-' . $filter->value]) ?
                    $defaultnames[$filter->type . '-' . $filter->value] : null;
            }
            // Only include filter if a valid object is returned.
            if ($filterobj = rb_filter_type::get_filter($type, $value, $advanced, $this)) {
                $filterobj->filterid = $filter->id;
                $filterobj->filtername = $filtername;
                $filterobj->customname = isset($filter->customname) ? $filter->customname : 0;
                $filterobj->advanced = $advanced;
                // Change label if there is a customname for this filter.
                $filterobj->label = ($filter->customname == 1) ? $filtername : $filterobj->label;
                $out[$name] = $filterobj;

                // enabled report grouping if any filters are grouped
                if (isset($filterobj->grouping) && $filterobj->grouping != 'none') {
                    $this->grouped = true;
                }
            }
        }
        return $out;
    }

    /**
     * Returns sql where statement based on active filters
     * @param string $extrasql
     * @param array $extraparams for the extra sql clause (named params)
     * @return array containing one array of SQL clauses and one array of params
     */
    function fetch_sql_filters($extrasql='', $extraparams=array()) {
        global $SESSION;

        $where_sqls = array();
        $having_sqls = array();
        $filterparams = array();

        if ($extrasql != '') {
            if (strpos($extrasql, '?')) {
                print_error('extrasqlshouldusenamedparams', 'report_reportbuilder');
            }
            $where_sqls[] = $extrasql;
        }

        if (!empty($SESSION->reportbuilder[$this->_id])) {
            foreach ($SESSION->reportbuilder[$this->_id] as $fname => $data) {
                if (!array_key_exists($fname, $this->filters)) {
                    continue; // filter not used in this report
                }
                $filter = $this->filters[$fname];
                if ($filter->grouping != 'none') {
                    list($having_sqls[], $params) = $filter->get_sql_filter($data);
                } else {
                    list($where_sqls[], $params) = $filter->get_sql_filter($data);
                }
                $filterparams = array_merge($filterparams, $params);
            }
        }

        $out = array();
        if (!empty($having_sqls)) {
            $out['having'] = implode(' AND ', $having_sqls);
        }
        if (!empty($where_sqls)) {
            $out['where'] = implode(' AND ', $where_sqls);
        }

        return array($out, array_merge($filterparams, $extraparams));
    }

    /**
     * Same as fetch_sql_filters() but returns array of strings
     * describing active filters instead of SQL
     */
    function fetch_text_filters() {
        global $SESSION;
        $out = array();
        if (!empty($SESSION->reportbuilder[$this->_id])) {
            foreach ($SESSION->reportbuilder[$this->_id] as $fname => $data) {
                if (!array_key_exists($fname, $this->filters)) {
                    continue; // filter not used in this report
                }
                $field = $this->filters[$fname];
                $out[] = $field->get_label($data);
            }
        }
        return $out;
    }

    function process_filters() {
        global $CFG;
        require_once($CFG->dirroot . '/report/reportbuilder/report_forms.php');
        $mform = new report_builder_search_form($this->get_current_url(), array('fields' => $this->filters));
        $adddata = $mform->get_data(false);
        $clearfiltersparam = optional_param('clearfilters', 0, PARAM_INT);
        if ($adddata || $clearfiltersparam) {
            foreach ($this->filters as $field) {
                if (isset($adddata->submitgroup['clearfilter']) || $clearfiltersparam) {
                    // Clear out any existing filters.
                    $field->unset_data();
                } else {
                    $data = $field->check_data($adddata);
                    if ($data === false) {
                        // Unset existing result if field has been set back to "not set" position.
                        $field->unset_data();
                    } else {
                        $field->set_data($data);
                    }
                }
            }
        }
    }

    /**
     * Get column names in resulting query
     *
     * @return array
     */
    function get_column_aliases() {
        $fields = array();
        foreach ($this->columns as $column) {
            $fields[] = $column->value;
        }
        return $fields;
    }

    /**
     * Get fields and aliases from appropriate source
     *
     * @param array $source soruce should object with 'field' and 'fieldalias' properties
     * @param bool $aliasonly if enabled will return only aliases of field
     * @return array of SQL snippets
     */
    function get_alias_fields(array $source, $aliasonly = false) {
        $result = array();
        foreach($source as $fields) {
            if (is_object($fields) && (method_exists($fields, 'get_field') || isset($fields->field))) {
                if (method_exists($fields, 'get_field')) {
                    $fieldname = $fields->get_field();
                }
                else {
                    $fieldname = $fields->field;
                }
                // support of several fields in one filter/column/etc
                if (is_array($fieldname)) {
                    $field = array();
                    foreach ($fieldname as $key => $value) {
                        // need to namespace these extra keys to avoid collisions
                        $field["rb_composite_{$key}"] = $value;
                    }
                } else {
                     if (isset($fields->fieldalias)) {
                         $field = array($fields->fieldalias => $fieldname);
                     }
                }

                foreach ($field as $alias=>$name) {
                    if ($aliasonly) {
                        $result[] = $alias;
                    } else {
                        $result[] = "{$name} AS {$alias}";
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Gets any columns set for the current report from the database
     *
     * @param array $columns predefined set of columns
     * @params bool $runcolumngenerators false to disable column generators - used to get the original columns
     * @return array Array of columns for current report or empty array if none set
     */
    public function get_columns(array $columns = array(), $runcolumngenerators = true) {
        global $DB;

        $out = array();
        $id = isset($this->_id) ? $this->_id : null;
        if (empty($id)) {
            return $out;
        }

        if (empty($columns)) {
            $columns = $DB->get_records('report_builder_columns', array('reportid' => $id), 'sortorder');
        }
        foreach ($columns as $column) {
            // Find the column option that matches this column.
            $columnoption = self::get_single_item($this->columnoptions, $column->type, $column->value);

            if ($runcolumngenerators && isset($columnoption->columngenerator)) {
                /* Rather than putting the column into the list, we call the generator and it
                 * will supply an array of columns (0 or more) that should be included. We pass
                 * all available information to the generator (columnoption and hidden). */
                $columngenerator = 'rb_cols_generator_' . $columnoption->columngenerator;
                $results = $this->src->$columngenerator($columnoption, $column->hidden);
                $out = array_merge($out, $results);
            } else {
                /* To properly support multiple languages - only use value
                 * in database if it's different from the default. If it's the
                 * same as the default for that column, use the default string
                 * directly. */
                if ($column->customheading) {
                    // Use value from database.
                    $heading = $column->heading;
                } else {
                    // Use default value.
                    $defaultheadings = $this->get_default_headings_array();
                    $heading = isset($defaultheadings[$column->type . '-' . $column->value]) ?
                        $defaultheadings[$column->type . '-' . $column->value] : null;
                }

                try {
                    $out[$column->id] = $this->src->new_column_from_option(
                        $column->type,
                        $column->value,
                        $heading,
                        $column->customheading,
                        $column->hidden
                    );
                    // Enabled report grouping if any columns are grouped.
                    if ($out[$column->id]->grouping != 'none') {
                        $this->grouped = true;
                    }
                } catch (ReportBuilderException $e) {
                    // Save list of bad columns.
                    $this->badcolumns[] = array(
                        'id' => $column->id,
                        'type' => $column->type,
                        'value' => $column->value,
                        'heading' => $column->heading
                    );
                    trigger_error($e->getMessage(), E_USER_WARNING);
                }
            }
        }

        // now append any required columns
        if (is_array($this->requiredcolumns)) {
            foreach ($this->requiredcolumns as $column) {
                $column->required = true;
                $out[] = $column;
                // enabled report grouping if any columns are grouped
                if ($column->grouping != 'none') {
                    $this->grouped = true;
                }
            }
        }

        return $out;
    }


    /**
     * Returns an associative array of the default headings for this report
     *
     * Looks up all the columnoptions (from this report's source)
     * For each one gets the default heading according the the following criteria:
     *  - if the report is embedded get the heading from the embedded source
     *  - if not embedded or the column's heading isn't specified in the embedded source,
     *    get the defaultheading from the columnoption
     *  - if that isn't specified, use the columnoption name
     *
     * @return array Associtive array of default headings for all the column options in this report
     *               Key is "{$type}-{$value]", value is the default heading string
     */
    function get_default_headings_array() {
        if (!isset($this->src->columnoptions) || !is_array($this->src->columnoptions)) {
            return false;
        }

        $out = array();
        foreach ($this->src->columnoptions as $option) {
            $key = $option->type . '-' . $option->value;

            if ($this->embedobj && $embeddedheading = $this->embedobj->get_embedded_heading($option->type, $option->value)) {
                // use heading from embedded source
                $defaultheading = $embeddedheading;
            } else {
                if (isset($option->defaultheading)) {
                    // use default heading
                    $defaultheading = $option->defaultheading;
                } else {
                    // fall back to columnoption name
                    $defaultheading = $option->name;
                }
            }

            $out[$key] = $defaultheading;
        }
        return $out;
    }

    /**
     * Given a report fullname, try to generate a sensible shortname that will be unique
     *
     * @param string $fullname The report's full name
     * @return string A unique shortname suitable for this report
     */
    public static function create_shortname($fullname) {
        global $DB;

        // leaves only letters and numbers
        // replaces spaces + dashes with underscores
        $validchars = strtolower(preg_replace(array('/[^a-zA-Z\d\s-_]/', '/[\s-]/'), array('', '_'), $fullname));
        $shortname = "report_{$validchars}";
        $try = $shortname;
        $i = 1;
        while($i < 1000) {
            if ($DB->get_field('report_builder', 'id', array('shortname' => $try))) {
                // name exists, try adding a number to make unique
                $try = $shortname . $i;
                $i++;
            } else {
                // return the shortname
                return $try;
            }
        }
        // if all 1000 name tries fail, give up and use a timestamp
        return "report_" . time();
    }


    /**
     * Return the URL to view the current report
     *
     * @return string URL of current report
     */
    function report_url() {
        global $CFG;
        if ($this->embeddedurl === null) {
            return $CFG->wwwroot . '/report/reportbuilder/report.php?id=' . $this->_id;
        } else {
            return $CFG->wwwroot . $this->embeddedurl;
        }
    }


    /**
     * Get the current page url, minus any pagination or sort order elements
     * Good for submitting forms
     *
     * @return string Current URL, minus any spage and ssort parameters
     */
    function get_current_url() {
        // Array of parameters to remove from query string.
        $strip_params = array('spage', 'ssort', 'sid', 'clearfilters');

        $url = new moodle_url(qualified_me());
        foreach ($url->params() as $name =>$value) {
            if (in_array($name, $strip_params)) {
                $url->remove_params($name);
            }
        }
        return html_entity_decode($url->out());
    }


    /**
     * Returns an array of arrays containing information about any currently
     * set URL parameters. Used to determine which joins are required to
     * match against URL parameters
     *
     * @param bool $all Return all params including unused in current request
     *
     * @return array Array of set URL parameters and their values
     */
    function get_current_params($all = false) {
        global $SESSION;

        $clearfiltersparam = optional_param('clearfilters', 0, PARAM_INT);

        $out = array();
        if (empty($this->_paramoptions)) {
            return $out;
        }
        foreach ($this->_paramoptions as $param) {
            $name = $param->name;
            if ($param->type == 'string') {
                $var = optional_param($name, null, PARAM_TEXT);
            } else {
                $var = optional_param($name, null, PARAM_INT);
            }
            if (isset($this->_embeddedparams[$name])) {
                // Embedded params take priority over url params.
                $res = new rb_param($name, $this->_paramoptions);
                $res->value = $this->_embeddedparams[$name];
                $out[] = $res;
            } else if ($all) {
                // When all parameters required, they are not restricted to particular value.
                $out[] = new rb_param($name, $this->_paramoptions);
            } else if (isset($var) || $clearfiltersparam) {
                if (isset($var)) {
                    // This url param exists, add to params to use.
                    $res = new rb_param($name, $this->_paramoptions);
                    $res->value = $var; // Save the value.
                    $out[] = $res;
                    $SESSION->reportbuilder[$this->_id][$name] = $var; // And save to session variable.
                } else {
                    unset($SESSION->reportbuilder[$this->_id][$name]);
                }
            } else if (isset($SESSION->reportbuilder[$this->_id][$name])) {
                // This param is stored in the session variable.
                $res = new rb_param($name, $this->_paramoptions);
                $res->value = $SESSION->reportbuilder[$this->_id][$name];
                $out[] = $res;
            }

        }
        return $out;
    }


    /**
     * Wrapper for displaying search form from filtering class
     *
     * @return Nothing returned but prints the search box
     */
    function display_search() {
        global $CFG;
        require_once($CFG->dirroot . '/report/reportbuilder/report_forms.php');
        $mform = new report_builder_search_form($this->get_current_url(), array('fields' => $this->filters));
        $mform->display();
    }

    /** Returns true if the current user has permission to view this report
     *
     * @param integer $id ID of the report to be viewed
     * @param integer $userid ID of user to check permissions for
     * @return boolean True if they have any of the required capabilities
     */
    public static function is_capable($id, $userid=null) {
        global $USER;

        $foruser = isset($userid) ? $userid : $USER->id;
        $allowed = array_keys(reportbuilder::get_permitted_reports($foruser, true));
        $permitted = in_array($id, $allowed);
        return $permitted;
    }


    /**
    * Returns an array of defined reportbuilder access plugins
    *
    * @return array Array of access plugin names
    */
    public static function get_all_access_plugins() {
        $plugins = array();
        // loop round classes, only considering classes that extend rb_base_access
        foreach (get_declared_classes() as $class) {
            if (is_subclass_of($class, 'rb_base_access')) {
                // remove rb_ prefix
                $plugins[] = substr($class, 3);
            }
        }
        return $plugins;
    }

    /**
    * Returns an array of associative arrays keyed by reportid,
    * each associative array containing ONLY the plugins actually enabled on each report,
    * with a 0/1 value of whether the report passes each plugin checks for the specified user
    * For example a return array in the following form
    *
    * array[1] = array('role_access' => 1, 'individual_access' => 0)
    * array[4] = array('role_access' => 0, 'individual_access' => 0, 'hierarchy_access' => 0)
    *
    * would mean:
    * report id 1 has 'role_access' and 'individual_access' plugins enabled,
    * this user passed role_access checks but failed the individual_access checks;
    * report id 4 has 'role_access', 'individual_access and 'hierarchy_access' plugins enabled,
    * and the user failed access checks in all three.
    *
    * @param int $userid The user to check which reports they have access to
    * @param array $plugins array of particular plugins to check
    * @return array Array of reports, with enabled plugin names and access status
    */
    public static function get_reports_plugins_access($userid, $plugins=NULL) {
        global $DB;
        //create return variable
        $report_plugin_access = array();
        //if no list of plugins specified, check them all
        if (empty($plugins)) {
            $plugins = self::get_all_access_plugins();
        }
        //keep track of which plugins are actually active according to report_builder_settings
        $active_plugins = array();
        //now get the info for plugins that are actually enabled for any reports
        list($insql, $params) = $DB->get_in_or_equal($plugins);
        $sql = "SELECT id,reportid,type
                  FROM {report_builder_settings}
                 WHERE type $insql
                   AND name = ?
                   AND value = ?";
        $params[] = 'enable';
        $params[] = '1';
        $reportinfo = $DB->get_records_sql($sql, $params);

        foreach ($reportinfo as $id => $plugin) {
            //foreach scope variables for efficiency
            $rid = $plugin->reportid;
            $ptype = '' . $plugin->type;
            //add to array of plugins that are actually active
            if (!in_array($ptype, $active_plugins)) {
                $active_plugins[] = $ptype;
            }
            //set up enabled plugin info for this report
            if (isset($report_plugin_access[$rid])) {
                $report_plugin_access[$rid][$ptype] = 0;
            } else {
                $report_plugin_access[$rid] = array($ptype => 0);
            }
        }
        //now call the plugin class to get the accessible reports for each actually used plugin
        foreach ($active_plugins as $plugin) {
            $class = "rb_" . $plugin;
            $obj = new $class($userid);
            $accessible = $obj->get_accessible_reports();
            foreach ($accessible as $key => $rid) {
                if (isset($report_plugin_access[$rid]) && is_array($report_plugin_access[$rid])) {
                    //report $rid has passed checks in $plugin
                    //the plugin should already have an entry with value 0 from above
                    if (isset($report_plugin_access[$rid][$plugin])) {
                        $report_plugin_access[$rid][$plugin] = 1;
                    }
                }
            }
        }

        return $report_plugin_access;
    }

    /**
     * Returns an array of reportbuilder objects that the user can view
     *
     * @param int $userid The user to check which reports they have access to
     * @param boolean $showhidden If true, reports which are hidden
     *                            will also be included
     * @return array Array of results from the report_builder table
     */
    public static function get_permitted_reports($userid=NULL, $showhidden=false) {
        global $DB, $USER;

        // check access for specified user, or the current user if none set
        $foruser = isset($userid) ? $userid : $USER->id;
        //array to hold the final list
        $permitted_reports = array();
        //get array of plugins
        $all_plugins = self::get_all_access_plugins();
        //get array of all reports with enabled plugins and whether they passed or failed each enabled plugin
        $enabled_plugins = self::get_reports_plugins_access($foruser, $all_plugins);
        //get basic reports list
        $hidden = (!$showhidden) ? ' WHERE hidden = 0 ' : '';
        $sql = "SELECT *
                  FROM {report_builder}
                 $hidden
                 ORDER BY fullname ASC";
        $reports = $DB->get_records_sql($sql);
        //we now have all the information we need
        if ($reports) {
            foreach ($reports as $report) {
                if ($report->accessmode == REPORT_BUILDER_ACCESS_MODE_NONE) {
                    $permitted_reports[$report->id] = $report;
                    continue;
                }
                if ($report->accessmode == REPORT_BUILDER_ACCESS_MODE_ANY) {
                    if (!empty($enabled_plugins) && isset($enabled_plugins[$report->id])) {
                        foreach ($enabled_plugins[$report->id] as $plugin => $value) {
                            if ($value == 1) {
                                //passed in some plugin so allow it
                                $permitted_reports[$report->id] = $report;
                                break;
                            }
                        }
                        continue;
                    } else {
                        // bad data - set to "any plugin passing", but no plugins actually have settings to check for this report
                        continue;
                    }
                }
                if ($report->accessmode == REPORT_BUILDER_ACCESS_MODE_ALL) {
                    if (!empty($enabled_plugins) && isset($enabled_plugins[$report->id])) {
                        $status=true;
                        foreach ($enabled_plugins[$report->id] as $plugin => $value) {
                            if ($value == 0) {
                                //failed in some expected plugin, reject
                                $status = false;
                                break;
                            }
                        }
                        if ($status) {
                            $permitted_reports[$report->id] = $report;
                            continue;
                        }
                    } else {
                        // bad data - set to "all plugins passing", but no plugins actually have settings to check for this report
                        continue;
                    }
                }
            }
        }
        return $permitted_reports;
    }


    /**
     * Get the value of the specified parameter, or null if not found
     *
     * @param string $name name of the parameter
     * @return mixed the value
     */
    public function get_param_value($name) {
        foreach ($this->_params as $param) {
            if ($param->name == $name) {
                return $param->value;
            }
        }
        return null;
    }


    /**
     * Returns an SQL snippet that, when applied to the WHERE clause of the query,
     * reduces the results to only include those matched by any specified URL parameters
     * @param bool $cache if enabled only field alias will be used
     *
     * @return array containing SQL snippet (created from URL parameters) and SQL params
     */
    function get_param_restrictions($cache = false) {
        $out = array();
        $sqlparams = array();
        $params = $this->_params;
        if (is_array($params)) {
            $count = 1;
            foreach ($params as $param) {
                $field = ($cache) ? $param->fieldalias : $param->field;
                $value = $param->value;
                $type = $param->type;
                // don't include if param not set to anything
                if (!isset($value) || strlen(trim($value)) == 0) {
                    continue;
                }

                $wherestr = $field;

                // if value starts with '!', do a not equals match
                // to the rest of the string
                $uniqueparam = rb_unique_param("pr{$count}_");
                if (substr($value, 0, 1) == '!') {
                    $wherestr .= " != :{$uniqueparam}";
                    // Strip off the leading '!'
                    $sqlparams[$uniqueparam] = substr($value, 1);
                } else {
                    // normal match
                    $wherestr .= " = :{$uniqueparam}";
                    $sqlparams[$uniqueparam] = $value;
                }

                $out[] = $wherestr;
                $count++;
            }
        }
        if (count($out) == 0) {
            return array('', array());
        }
        return array('(' . implode(' AND ', $out) . ')', $sqlparams);
    }


    /**
     * Returns an SQL snippet that, when applied to the WHERE clause of the query,
     * reduces the results to only include those matched by any specified content
     * restrictions
     * @param bool $cache if enabled, only alias fields will be used
     *
     * @return array containing SQL snippet created from content restrictions, as well as SQL params array
     */
    function get_content_restrictions($cache = false) {
        // if no content restrictions enabled return a TRUE snippet
        // use 1=1 instead of TRUE for MSSQL support
        if ($this->contentmode == REPORT_BUILDER_CONTENT_MODE_NONE) {
            return array("( 1=1 )", array());
        } else if ($this->contentmode == REPORT_BUILDER_CONTENT_MODE_ALL) {
            // require all to match
            $op = "\n    AND ";
        } else {
            // require any to match
            $op = "\n    OR ";
        }

        $reportid = $this->_id;
        $out = array();
        $params = array();

        // go through the content options
        if (isset($this->contentoptions) && is_array($this->contentoptions)) {
            foreach ($this->contentoptions as $option) {
                $name = $option->classname;
                $classname = 'rb_' . $name . '_content';
                $settingname = $name . '_content';
                $field = ($cache) ? $option->fieldalias : $option->field;
                if (class_exists($classname)) {
                    $class = new $classname($this->reportfor);

                    if (reportbuilder::get_setting($reportid, $settingname,
                        'enable', $cache)) {
                        // this content option is enabled
                        // call function to get SQL snippet
                        list($out[], $contentparams) = $class->sql_restriction($field, $reportid);
                        $params = array_merge($params, $contentparams);
                    }
                } else {
                    print_error('contentclassnotexist', 'report_reportbuilder', '', $classname);
                }
            }
        }
        // show nothing if no content restrictions enabled
        if (count($out) == 0) {
            // use 1=0 instead of FALSE for MSSQL support
            return array('(1=0)', array());
        }

        return array('(' . implode($op, $out) . ')', $params);
    }

    /**
     * Returns human readable descriptions of any content or
     * filter restrictions that are limiting the number of results
     * shown. Used to let the user known what a report contains
     *
     * @param string $which Which restrictions to return, defaults to all
     *                      but can be 'filter' or 'content' to just return
     *                      restrictions of that type
     * @return array An array of strings containing descriptions
     *               of any restrictions applied to this report
     */
    function get_restriction_descriptions($which='all') {
        // include content restrictions
        $content_restrictions = array();
        $reportid = $this->_id;
        $res = array();
        if ($this->contentmode != REPORT_BUILDER_CONTENT_MODE_NONE) {
            foreach ($this->contentoptions as $option) {
                $name = $option->classname;
                $classname = 'rb_' . $name . '_content';
                $settingname = $name . '_content';
                $title = $option->title;
                if (class_exists($classname)) {
                    $class = new $classname($this->reportfor);
                    if (reportbuilder::get_setting($reportid, $settingname,
                        'enable')) {
                        // this content option is enabled
                        // call function to get text string
                        $res[] = $class->text_restriction($title, $reportid);
                    }
                } else {
                    print_error('contentclassnotexist', 'report_reportbuilder', '', $classname);
                }
            }
            if ($this->contentmode == REPORT_BUILDER_CONTENT_MODE_ALL) {
                // 'and' show one per line
                $content_restrictions = $res;
            } else {
                // 'or' show as a single line
                $content_restrictions[] = implode(get_string('or', 'report_reportbuilder'), $res);
            }
        }

        $filter_restrictions = $this->fetch_text_filters();

        switch($which) {
        case 'content':
            $restrictions = $content_restrictions;
            break;
        case 'filter':
            $restrictions = $filter_restrictions;
            break;
        default:
            $restrictions = array_merge($content_restrictions, $filter_restrictions);
        }
        return $restrictions;
    }




    /**
     * Returns an array of fields that must form part of the SQL query
     * in order to provide the data need to display the columns required
     *
     * Each element in the array is an SQL snippet with an alias built
     * from the $type and $value of that column
     *
     * @param int $mode How aliases for grouping columns should be prepared
     * @return array Array of SQL snippets for use by SELECT query
     *
     */
    function get_column_fields($mode = rb_column::REGULAR) {
        $fields = array();
        $src = $this->src;
        foreach ($this->columns as $column) {
            $fields = array_merge($fields, $column->get_fields($src, $mode));
        }
        return $fields;
    }


    /**
     * Returns the names of all the joins in the joinlist
     *
     * @return array Array of join names from the joinlist
     */
    function get_joinlist_names() {
        $joinlist = $this->_joinlist;
        $joinnames = array();
        foreach ($joinlist as $item) {
            $joinnames[] = $item->name;
        }
        return $joinnames;
    }


    /**
     * Return a join from the joinlist by name
     *
     * @param string $name Join name to get from the join list
     *
     * @return object {@link rb_join} object for the matching join, or false
     */
    function get_joinlist_item($name) {
        $joinlist = $this->_joinlist;
        foreach ($joinlist as $item) {
            if ($item->name == $name) {
                return $item;
            }
        }
        return false;
    }


    /**
     * Given an item, returns an array of {@link rb_join} objects needed by this item
     *
     * @param object $item An object containing a 'joins' property
     * @param string $usage The function is called to obtain joins for various
     *                     different elements of the query. The usage is displayed
     *                     in the error message to help with debugging
     * @return array An array of {@link rb_join} objects used to build the join part of the query
     */
    function get_joins($item, $usage) {
        $output = array();
        
        /*error_log("FUNCTION GET_JOINS");
        error_log(print_r($item->joins));
        error_log($usage);*/
        
        // extract the list of joins into an array format
        if (isset($item->joins) && is_array($item->joins)) {
            //error_log("FUNCTION GET_JOINS: IF");
            $joins = $item->joins;
        } else if (isset($item->joins)) {
            //error_log("FUNCTION GET_JOINS: ELSE IF");
            $joins = array($item->joins);
        } else {
            //error_log("FUNCTION GET_JOINS: ELSE");
            $joins = array();
        }

        foreach ($joins as $join) {
            //error_log($join);
            if ($join == 'base') {
                continue;
            }
            //error_log("$join.$usage");
            $joinobj = $this->get_single_join($join, $usage);
            $output[] = $joinobj;
            //error_log(print_r($joinobj));
            $this->get_dependency_joins($output, $joinobj);

        }

        return $output;
    }

    /**
     * Given a join name, look for it in the joinlist and return the join object
     *
     * @param string $join A single join name (should match joinlist item name)
     * @param string $usage The function is called to obtain joins for various
     *                      different elements of the query. The usage is
     *                      displayed in the error message to help with debugging
     * @return string An rb_join object for the specified join, or error
     */
    function get_single_join($join, $usage) {

        if ($match = $this->get_joinlist_item($join)) {
            // return the join object for the item
            return $match;
        } else {
            print_error('joinnotinjoinlist', 'report_reportbuilder', '', (object)array('join' => $join, 'usage' => $usage));
            return false;
        }
    }

    /**
     * Recursively build an array of {@link rb_join} objects that includes all
     * dependencies
     */
    function get_dependency_joins(&$joins, $joinobj) {

        // get array of dependencies, excluding references to the
        // base table
        if (isset($joinobj->dependencies)
            && is_array($joinobj->dependencies)) {

            $dependencies = array();
            foreach ($joinobj->dependencies as $item) {
                // ignore references to base as a dependency
                if ($item == 'base') {
                    continue;
                }
                $dependencies[] = $item;
            }
        } else if (isset($joinobj->dependencies)
                && $joinobj->dependencies != 'base') {

            $dependencies = array($joinobj->dependencies);
        } else {
            $dependencies = array();
        }

        // loop through dependencies, adding any that aren't already
        // included
        foreach ($dependencies as $dependency) {
            $joinobj = $this->get_single_join($dependency, 'dependencies');
            if (in_array($joinobj, $joins)) {
                // prevents infinite loop if dependencies include
                // circular references
                continue;
            }
            // add to list of current joins
            $joins[] = $joinobj;

            // recursively get dependencies of this dependency
            $this->get_dependency_joins($joins, $joinobj);
        }

    }


    /**
     * Return an array of {@link rb_join} objects containing the joins required by
     * the current enabled content restrictions
     *
     * @return array An array of {@link rb_join} objects containing join information
     */
    function get_content_joins() {
        $reportid = $this->_id;

        if ($this->contentmode == REPORT_BUILDER_CONTENT_MODE_NONE) {
            // no limit on content so no joins necessary
            return array();
        }
        $contentjoins = array();
        foreach ($this->contentoptions as $option) {
            $name = $option->classname;
            $classname = 'rb_' . $name . '_content';
            if (class_exists($classname)) {
                // @TODO take settings form instance, not database, otherwise caching will fail after content settings change
                if (reportbuilder::get_setting($reportid, $name . '_content', 'enable')) {
                    // this content option is enabled
                    // get required joins
                    $contentjoins = array_merge($contentjoins,
                        $this->get_joins($option, 'content'));
                }
            }
        }
        return $contentjoins;
    }

    /**
     * Return an array of strings containing the fields required by
     * the current enabled content restrictions
     *
     * @return array An array for strings conaining SQL snippets for field list
     */
    function get_content_fields() {
        $reportid = $this->_id;

        if ($this->contentmode == REPORT_BUILDER_CONTENT_MODE_NONE) {
            // no limit on content so no joins necessary
            return array();
        }

        $fields = array();
        if (isset($this->contentoptions) && is_array($this->contentoptions)) {
            foreach ($this->contentoptions as $option) {
                $name = $option->classname;
                $classname = 'rb_' . $name . '_content';
                $settingname = $name . '_content';
                if (class_exists($classname)) {
                    if (reportbuilder::get_setting($reportid, $settingname, 'enable')) {
                            $fields[] = $option->field . ' AS ' . $option->fieldalias;
                    }
                }
            }
        }
        return $fields;
    }


    /**
     * Return an array of {@link rb_join} objects containing the joins required by
     * the current column list
     *
     * @return array An array of {@link rb_join} objects containing join information
     */
    function get_column_joins() {
        $coljoins = array();
        foreach ($this->columns as $column) {
            $coljoins = array_merge($coljoins,
                $this->get_joins($column, 'column'));
        }
        return $coljoins;
    }

    /**
     * Return an array of {@link rb_join} objects containing the joins required by
     * the current param list
     *
     * @param bool $all Return all joins even for unused params
     *
     * @return array An array of {@link rb_join} objects containing join information
     */
    function get_param_joins($all = false) {
        $paramjoins = array();
        foreach ($this->_params as $param) {
            $value = $param->value;
            // don't include joins if param not set
            if (!$all && (!isset($value) || $value == '')) {
                continue;
            }
            $paramjoins = array_merge($paramjoins,
                $this->get_joins($param, 'param'));
        }
        return $paramjoins;
    }


    /**
     * Return an array of {@link rb_join} objects containing the joins required by
     * the source joins
     *
     * @return array An array of {@link rb_join} objects containing join information
     */
    function get_source_joins() {
        // no where clause - don't add any joins
        // as they won't be used
        if (empty($this->src->sourcewhere)) {
            return array();
        }

        // no joins specified
        if (empty($this->src->sourcejoins)) {
            return array();
        }

        $item = new stdClass();
        $item->joins = $this->src->sourcejoins;

        return $this->get_joins($item, 'source');

    }

    /**
     * Return an array of {@link rb_join} objects containing the joins of all enabled
     * filters regardless their usage in current request (useful for caching)
     *
     * @return array An array of {@link rb_join} objects containing join information
     */
    function get_all_filter_joins() {
        $filterjoins = array();
        foreach ($this->filters as $filter) {
            $value = $filter->value;
            // don't include joins if param not set
            if (!isset($value) || $value == '') {
                continue;
            }
            $filterjoins = array_merge($filterjoins,
                $this->get_joins($filter, 'filter'));
        }
        return $filterjoins;
    }

    /**
     * Check the current session for active filters, and if found
     * collect together join data into a format suitable for {@link get_joins()}
     *
     * @return array An array of arrays containing filter join information
     */
    function get_filter_joins() {
        $shortname = $this->shortname;
        global $SESSION;
        $filterjoins = array();
        // check session variable for any active filters
        // if they exist we need to make sure we have included joins for them too
        if (isset($SESSION->reportbuilder[$this->_id]) &&
            is_array($SESSION->reportbuilder[$this->_id])) {
            foreach ($SESSION->reportbuilder[$this->_id] as $fname => $unused) {
                if (!array_key_exists($fname, $this->filters)) {
                    continue; // filter not used in this report
                }
                $filter = $this->filters[$fname];

                $filterjoins = array_merge($filterjoins,
                    $this->get_joins($filter, 'filter'));
            }
        }
        return $filterjoins;
    }


    /**
     * Given an array of {@link rb_join} objects, convert them into an SQL snippet
     *
     * @param array $joins Array of {@link rb_join} objects
     *
     * @return string SQL snippet that includes all the joins in the order provided
     */
    function get_join_sql($joins) {
        $out = array();

        foreach ($joins as $join) {
            $name = $join->name;
            $type = $join->type;
            $table = $join->table;
            $conditions = $join->conditions;

            if (array_key_exists($name, $out)) {
                // we've already added this join
                continue;
            }
            // store in associative array so we can tell which
            // joins we've already added
            $sql = "$type JOIN $table $name";
            if (!empty($conditions)) {
                $sql .= "\n        ON $conditions";
            }
            $out[$name] = $sql;
        }
        return implode("\n    ", $out) . " \n";
    }


    /**
     * Sort an array of {@link rb_join} objects
     *
     * Given an array of {@link rb_join} objects, sorts them such that:
     * - any duplicate joins are removed
     * - any joins with dependencies appear after those dependencies
     *
     * This is achieved by repeatedly looping through the list of
     * joins, moving joins to the sorted list only when all their
     * dependencies are already in the sorted list.
     *
     * On the first pass any joins that have no dependencies are
     * saved to the sorted list and removed from the current list.
     *
     * References to the moved items are then removed from the
     * dependencies lists of all the remaining items and the loop
     * is repeated.
     *
     * The loop continues until there is an iteration where no
     * more items are removed. At this point either:
     * - The current list is empty
     * - There are references to joins that don't exist
     * - There are circular references
     *
     * In the later two cases we throw an error, otherwise return
     * the sorted list.
     *
     * @param array Array of {@link rb_join} objects to be sorted
     *
     * @return array Sorted array of {@link rb_join} objects
     */
    function sort_joins($unsortedjoins) {

        // get structured list of dependencies for each join
        $items = $this->get_dependencies_array($unsortedjoins);

        // make an index of the join objects with name as key
        $joinsbyname = array();
        foreach ($unsortedjoins as $join) {
            $joinsbyname[$join->name] = $join;
        }

        // loop through items, storing any that don't have
        // dependencies in the output list

        // safety net to avoid infinite loop if something
        // unexpected happens
        $maxdepth = 50;
        $i = 0;
        $output = array();
        while($i < $maxdepth) {

            // items with empty dependencies array
            $nodeps = $this->get_independent_items($items);

            foreach ($nodeps as $nodep) {
                $output[] = $joinsbyname[$nodep];
                unset($items[$nodep]);
                // remove references to this item from all
                // the other dependency lists
                $this->remove_from_dep_list($items, $nodep);
            }

            // stop when no more items can be removed
            // if all goes well, this will be after all items
            // have been removed
            if (count($nodeps) == 0) {
                break;
            }

            $i++;
        }

        // we shouldn't have any items left once we've left the loop
        if (count($items) != 0) {
            print_error('couldnotsortjoinlist', 'report_reportbuilder');
        }

        return $output;
    }


    /**
     * Remove joins that have no impact on the results count
     *
     * Given an array of {@link rb_join} objects we want to return a similar list,
     * but with any joins that have no effect on the count removed. This is
     * done for performance reasons when calculating the count.
     *
     * The only joins that can be safely removed match the following criteria:
     * 1- Only LEFT joins are safe to remove
     * 2- Even LEFT joins are unsafe, unless the relationship is either
     *   One-to-one or many-to-one
     * 3- The join can't have any dependencies that don't also match the
     *   criteria above: e.g.:
     *
     *   base LEFT JOIN table_a JOIN table_b
     *
     *   Table_b can't be removed because it fails criteria 1. Table_a
     *   can't be removed, even though it passes criteria 1 and 2, because
     *   table_b is dependent on it.
     *
     * To achieve this result, we use a similar strategy to sort_joins().
     * As a side effect, duplicate joins are removed but note that this
     * method doesn't change the sort order of the joins provided.
     *
     * @param array $unprunedjoins Array of rb_join objects to be pruned
     *
     * @return array Array of {@link rb_join} objects, minus any joins
     *               that don't affect the total record count
     */
    function prune_joins($unprunedjoins) {
        // get structured list of dependencies for each join
        $items = $this->get_dependencies_array($unprunedjoins);

        // make an index of the join objects with name as key
        $joinsbyname = array();
        foreach ($unprunedjoins as $join) {
            $joinsbyname[$join->name] = $join;
        }

        // safety net to avoid infinite loop if something
        // unexpected happens
        $maxdepth = 100;
        $i = 0;
        $output = array();
        while($i < $maxdepth) {
            $prunecount = 0;
            // items with empty dependencies array
            $nodeps = $this->get_nondependent_items($items);
            foreach ($nodeps as $nodep) {
                if ($joinsbyname[$nodep]->pruneable()) {
                    unset($items[$nodep]);
                    $this->remove_from_dep_list($items, $nodep);
                    unset($joinsbyname[$nodep]);
                    $prunecount++;
                }
            }

            // stop when no more items can be removed
            if ($prunecount == 0) {
                break;
            }

            $i++;
        }

        return array_values($joinsbyname);
    }


    /**
     * Reformats an array of {@link rb_join} objects to a structure helpful for managing dependencies
     *
     * Saves the dependency info in the following format:
     *
     * array(
     *    'name1' => array('dep1', 'dep2'),
     *    'name2' => array('dep3'),
     *    'name3' => array(),
     *    'name4' => array(),
     * );
     *
     * This has the effect of:
     * - Removing any duplicate joins (joins with the same name)
     * - Removing any references to 'base' in the dependencies list
     * - Converting null dependencies to array()
     * - Converting string dependencies to array('string')
     *
     * @param array $joins Array of {@link rb_join} objects
     *
     * @return array Array of join dependencies
     */
    private function get_dependencies_array($joins) {
        $items = array();
        foreach ($joins as $join) {

            // group joins in a more consistent way and remove all
            // references to 'base'
            if (is_array($join->dependencies)) {
                $deps = array();
                foreach ($join->dependencies as $dep) {
                    if ($dep == 'base') {
                        continue;
                    }
                    $deps[] = $dep;
                }
                $items[$join->name] = $deps;
            } else if (isset($join->dependencies)
                && $join->dependencies != 'base') {
                $items[$join->name] = array($join->dependencies);
            } else {
                $items[$join->name] = array();
            }
        }
        return $items;
    }


    /**
     * Remove references to a particular join from the
     * join dependencies list
     *
     * Given a list of join dependencies (as generated by
     * get_dependencies_array() ) remove all references to
     * the join named $joinname
     *
     * @param array &$items Array of dependencies. Passed by ref
     * @param string $joinname Name of join to remove from list
     *
     * @return true;
     */
    private function remove_from_dep_list(&$items, $joinname) {
        foreach ($items as $join => $deps) {
            foreach ($deps as $key => $dep) {
                if ($dep == $joinname) {
                    unset($items[$join][$key]);
                }
            }
        }
        return true;
    }


    /**
     * Return a list of items with no dependencies (e.g. the 'tips' of the tree)
     *
     * Given a list of join dependencies (as generated by
     * get_dependencies_array() ) return the names (keys)
     * of elements with no dependencies.
     *
     * @param array $items Array of dependencies
     *
     * @return array Array of names of independent items
     */
    private function get_independent_items($items) {
        $nodeps = array();
        foreach ($items as $join => $deps) {
            if (count($deps) == 0) {
                $nodeps[] = $join;
            }
        }
        return $nodeps;
    }


    /**
     * Return a list of items which no other items depend on (e.g the 'base' of
     * the tree)
     *
     * Given a list of join dependencies (as generated by
     * get_dependencies_array() ) return the names (keys)
     * of elements which are not dependent on any other items
     *
     * @param array $items Array of dependencies
     *
     * @return array Array of names of non-dependent items
     */
    private function get_nondependent_items($items) {
        $alldeps = array();
        // get all the dependencies in one array
        foreach ($items as $join => $deps) {
            foreach ($deps as $dep) {
                $alldeps[] = $dep;
            }
        }
        $nondeps = array();
        foreach (array_keys($items) as $join) {
            if (!in_array($join, $alldeps)) {
                $nondeps[] = $join;
            }
        }
        return $nondeps;
    }


    /**
     * Returns the ORDER BY SQL snippet for the current report
     *
     * @param object $table Flexible table object to use to find the sort parameters (optional)
     *                      If not provided a new object will be created based on the report's
     *                      shortname
     *
     * @return string SQL string to order the report to be appended to the main query
     */
    public function get_report_sort($table = null) {
        global $SESSION;

        // check the sort session var doesn't contain old columns that no
        // longer exist
        $this->check_sort_keys();

        // unless the table object is provided we need to call get_sql_sort() statically
        // and pass in the report's unique id (shortname)
        if (!isset($table)) {
            $shortname = $this->shortname;
            $sort = trim(flexible_table::get_sort_for_table($shortname));
        } else {
            $sort = trim($table->get_sql_sort());
        }

        // always include the base id as a last resort to ensure order is
        // predetermined for pagination
        $baseid = $this->grouped ? 'min(base.id)' : 'base.id';
        $order = ($sort != '') ? " ORDER BY $sort, $baseid" : " ORDER BY $baseid";

        return $order;
    }

    /**
     * Is report caching enabled and cache is ready and not cache is not ignored
     *
     * @return bool
     */
    function is_cached() {
        $enabled = !$this->cacheignore && $this->cache;
        return $enabled && isset($this->cacheschedule) && $this->cacheschedule->cachetable != '';
    }

    /**
     * Load back previously stored configuration
     *
     * @param string $sdata Serialized configuration
     */
    public function import_config($sdata) {
        $data = unserialize(base64_decode($sdata));
        if (!$data) {
            throw new ReportBuilderException('Cache unserialization failed');
        }
        foreach ($data as $property => $value) {
            $this->{$property} = $value;
        }
    }
    /**
     * Support of configuration caching
     *
     * Number of fields depends on report configuration (filters, contents, columns, etc).
     * To ensure that cache will work after changes in configuration, current configuration also
     * should be cached. This function serialize all necessary report configuration to save it in
     * cache database
     *
     * @return array
     */
    public function export_config() {
        // save filters
        $filters = array();
        foreach ($this->filters as $filterinfo) {
            $filter = new stdClass();
            $filter->type = $filterinfo->type;
            $filter->value = $filterinfo->value;
            $filter->advanced = $filterinfo->advanced;
            $filter->id = $filterinfo->id;
            $filter->filtername = $filterinfo->filtername;
            $filter->customname = $filterinfo->customname;
            $filters[] = $filter;
        }
        // save columns
        $columns = array();
        foreach ($this->columns as $columnid => $columninfo) {
            if (!in_array($columninfo, $this->requiredcolumns)) {
                $column = new stdClass();
                $column->id = $columnid;
                $column->heading = $columninfo->heading;
                $column->customheading = $columninfo->customheading;
                $column->type = $columninfo->type;
                $column->value = $columninfo->value;
                $column->hidden = $columninfo->hidden;
                $column->grouping = $columninfo->grouping;
                $columns[] = $column;
            }
        }
        return base64_encode(serialize(array('filters' => $filters, '_params' => $this->_params,
                     'contentoptions' => $this->contentoptions, 'contentmode' => $this->contentmode,
                     'columns' => $columns, 'src' => $this->src, 'source' => $this->source,
                     'requiredcolumns' => $this->requiredcolumns,
                     '_paramoptions' => $this->_paramoptions, '_id' => $this->_id,
                     'filteroptions' => $this->filteroptions, 'columnoptions' => $this->columnoptions,
                     '_joinlist' => $this->_joinlist, '_base' => $this->_base,
                     'embeddedurl' => $this->embeddedurl, 'defaultsortorder' => $this->defaultsortorder,
                     'defaultsortcolumn' => $this->defaultsortcolumn, 'embedded' => $this->embedded,
                     'description' => $this->description, 'hidden' => $this->hidden,
                     'fullname' => $this->fullname)));
    }

    /**
     * Load cached configuration
     *
     * @return bool if object was restored from cache
     */
    public function restore_cached_state() {
        global $DB;
        $this->cacheschedule = $DB->get_record('report_builder_cache',
                                                array('reportid' => $this->_id), '*',
                                                IGNORE_MISSING);
        if ($this->cacheschedule) {
            $sdata = $this->cacheschedule->config;
            if ($sdata == '') {
                return false;
            }
            $this->import_config($sdata);
        } else {
            return false;
        }
        return true;
    }

    /**
     * This function builds the main SQL query used to generate cache for report
     *
     * @return array containing the full SQL query and SQL params
     */
    function build_create_cache_query() {
        // Save report instance state
        $paramssave = $this->_params;
        $groupedsave = $this->grouped;
        // Prepare instance to generate cache:
        // - Disable grouping
        // - Enable all params (not only used in request)
        $this->cacheignore = true;
        $this->_params = $this->get_current_params(true);
        $this->grouped = false;
        // get the fields required by display, any filter, param, or content option used in report
        $fields = array_merge($this->get_column_fields(rb_column::NOGROUP),
                              $this->get_content_fields(),
                              $this->get_alias_fields($this->filters),
                              $this->get_alias_fields($this->_params));
        $fields = array_unique($fields);
        $joins = $this->collect_joins(reportbuilder::FILTERALL);

        $where = array();
        if (!empty($this->src->sourcewhere)) {
            $where[] = $this->src->sourcewhere;
        }
        $sql = $this->collect_sql($fields, $this->src->base, $joins, $where);

        // Revert report instance state
        $this->_params = $paramssave;
        $this->cacheignore = false;
        $this->grouped = $groupedsave;
        return array($sql, array());
    }

    /**
     * This function builds main cached SQL query to get the data for page
     *
     * @return array array($sql, $params). If no cache found array('', array()) will be returned
     */
    function build_cache_query($countonly = false, $filtered = false) {
        global $DB;

        if (!$this->is_cached()) {
            return array('', array());
        }
        $cache = $this->cacheschedule;
        $fields = $this->get_column_fields(rb_column::CACHE);

        list($where, $group, $having, $sqlparams, $allgrouped) = $this->collect_restrictions($filtered, true);

        $sql = $this->collect_sql($fields, $cache->cachetable, array(), $where, $group, $having,
                                  $countonly, $allgrouped);

        return array($sql, $sqlparams, (array)$cache);
    }

    /**
     * This function builds the main SQL query used to get the data for the page
     *
     * @param boolean $countonly If true returns SQL to count results, otherwise the
     *                           query requests the fields needed for columns too.
     * @param boolean $filtered If true, includes any active filters in the query,
     *                           otherwise returns results without filtering
     * @param boolean $allowcache If true tries to use cache for query
     * @return array containing the full SQL query, SQL params, and cache meta information
     */
    function build_query($countonly = false, $filtered = false, $allowcache = true) {
        global $CFG;

        /*if ($allowcache && $CFG->enablereportcaching) {
            $cached = $this->build_cache_query($countonly, $filtered);
            if ($cached[0] != '') {
                return $cached;
            }
        }*/
        $fields = $this->get_column_fields();

        $filter = ($filtered) ? reportbuilder::FILTER : reportbuilder::FILTERNONE;
        $joins = $this->collect_joins($filter, $countonly);

        list($where, $group, $having, $sqlparams, $allgrouped) = $this->collect_restrictions($filtered);

        // apply any SQL specified by the source
        if (!empty($this->src->sourcewhere)) {
            $where[] = $this->src->sourcewhere;
        }
        $sql = $this->collect_sql($fields, $this->src->base, $joins, $where, $group, $having, $countonly, $allgrouped);
         return array($sql, $sqlparams, array());
    }

    /**
     * Return SQL snippet for field name depending on report cache settings.
     *
     * This is intended to be used during post_config.
     */
    public function get_field($type, $value, $field) {
        if ($this->is_cached()) {
            return $type . '_' . $value;
        }
        return $field;
    }

    /**
     * Get joins used for query building
     *
     * @param int $filtered reportbuilder::FILTERNONE - for no filter joins,
     *             reportbuilder::FILTER - for enabled filters, reportbuilder::FILTERALL - for all filters
     * @param bool $countonly If true prune joins that don't influent on resulting count
     * @return array of {@link rb_join} objects
     */
    protected function collect_joins($filtered, $countonly = false) {
        // get the joins needed to display requested columns and do filtering and restrictions
        $columnjoins = $this->get_column_joins();

        // if we are only counting, don't need all the column joins. Remove
        // any that don't affect the count
        if ($countonly && !$this->grouped) {
            $columnjoins = $this->prune_joins($columnjoins);
        }
        if ($filtered == reportbuilder::FILTERALL) {
            $filterjoins = $this->get_all_filter_joins();
        } else if ($filtered == reportbuilder::FILTER) {
            $filterjoins = $this->get_filter_joins();
        } else {
            $filterjoins = array();
        }
        $paramjoins = $this->get_param_joins(true);
        $contentjoins = $this->get_content_joins();
        $sourcejoins = $this->get_source_joins();
        $joins = array_merge($columnjoins, $filterjoins, $paramjoins, $contentjoins, $sourcejoins);

        // sort the joins to remove duplicates and resolve any dependencies
        $joins = $this->sort_joins($joins);
        return $joins;
    }

    /**
     * Get all restrictions to filter query
     *
     * @param bool $cache
     * @return array of arrays of strings array(where, group, having, bool allgrouped)
     */
    protected function collect_restrictions($filtered, $cache = false) {
        global $DB;
        $where = array();
        $group = array();
        $having = array();
        $sqlparams = array();
        list($restrictions, $contentparams) = $this->get_content_restrictions($cache);
        if ($restrictions != '') {
            $where[] = $restrictions;
            $sqlparams = array_merge($sqlparams, $contentparams);
        }
        unset($contentparams);

        if ($filtered === true) {
            list($sqls, $filterparams) = $this->fetch_sql_filters();
            if (isset($sqls['where']) && $sqls['where'] != '') {
                $where[] = $sqls['where'];
            }
            if (isset($sqls['having']) && $sqls['having'] != '') {
                $having[] = $sqls['having'];
            }
            $sqlparams = array_merge($sqlparams, $filterparams);
            unset($filterparams);
        }

        list($paramrestrictions, $paramparams) = $this->get_param_restrictions($cache);
        if ($paramrestrictions != '') {
            $where[] = $paramrestrictions;
            $sqlparams = array_merge($sqlparams, $paramparams);
        }
        unset($paramparams);

        list($postconfigrestrictions, $postconfigparams) = $this->get_post_config_restrictions();
        if ($postconfigrestrictions != '') {
            $where[] = $postconfigrestrictions;
            $sqlparams = array_merge($sqlparams, $postconfigparams);
        }
        unset($postconfigparams);

        $allgrouped = true;

        if ($this->grouped) {
            $group = array();
            // We use FIELDONLY for the GROUP BY clause because MSSQL does not allow aliases in grouping.
            // In the same time pgsql does not allow grouping by text constants which can be fieldnames
            $mode = rb_column::CACHE;
            if (!$cache) {
                $mode = rb_column::ALIASONLY;
                if ($DB->get_dbfamily() == 'mssql') {
                    $mode = rb_column::FIELDONLY;
                }
            }

            foreach ($this->columns as $column) {
                if ($column->grouping == 'none') {
                    $allgrouped = false;
                    $group = array_merge($group, $column->get_fields($this->src, $mode, true));
                } else {
                    // We still need to add extrafields to the GROUP BY if there is a displayfunc
                    if ($column->extrafields !== null && $column->displayfunc !== null) {
                        foreach ($column->extrafields as $alias => $field) {
                            $gp = ($mode == rb_column::CACHE || $mode == rb_column::ALIASONLY) ? $alias : $field;
                            if (!in_array($gp, $group)) {
                                $group[] = $gp;
                                $allgrouped = false;
                            }
                        }
                    }
                }
            }
        }

        return array($where, $group, $having, $sqlparams, $allgrouped);
    }

    /**
     * Compile SQL query from prepared parts
     *
     * @param array $fields
     * @param string $base
     * @param array $joins
     * @param array $where
     * @param array $group
     * @param array $having
     * @param bool $countonly
     * @param bool $allgrouped
     * @return string
     */
    protected function collect_sql(array $fields, $base, array $joins, array $where = null,
                                    array $group = null, array $having = null, $countonly = false,
                                    $allgrouped = false) {

        if ($countonly && !$this->grouped) {
            $selectsql = "SELECT COUNT(*) ";
        } else {
            $baseid = ($this->grouped) ? "min(base.id) AS id" : "base.id";
            array_unshift($fields, $baseid);
            $selectsql = "SELECT " . implode($fields, ",\n     ") . " \n";

        }
        $joinssql = (count($joins) > 0) ? $this->get_join_sql($joins) : '';

        $fromsql = "FROM $base base\n    " . $joinssql;

        $wheresql = (count($where) > 0) ? "WHERE " . implode("\n    AND ", $where) . "\n" : '';

        $groupsql = '';
        if (count($group) > 0 && !$allgrouped) {
            $groupsql = ' GROUP BY ' . implode(', ', $group) . ' ';
        }

        $havingsql = '';
        if (count($having) > 0) {
            $havingsql = ' HAVING ' . implode(' AND ', $having) . "\n";
        }

        if ($countonly && $this->grouped) {
            $sql = "SELECT COUNT(*) FROM ($selectsql $fromsql $wheresql $groupsql $havingsql) AS query";
        } else {
            $sql = "$selectsql $fromsql $wheresql $groupsql $havingsql";
        }
        return $sql;
    }

    /**
     * Return the total number of records in this report (after any
     * restrictions have been applied but before any filters)
     *
     * @return integer Record count
     */
    function get_full_count() {
        global $DB;

        // use cached value if present
        if (empty($this->_fullcount)) {
            list($sql, $params) = $this->build_query(true);
            $this->_fullcount = $DB->count_records_sql($sql, $params);
        }
        return $this->_fullcount;
    }

    /**
     * Return the number of filtered records in this report
     *
     * @return integer Filtered record count
     */
    function get_filtered_count() {
        global $DB;

        // use cached value if present
        if (empty($this->_filteredcount)) {
            list($sql, $params) = $this->build_query(true, true);
            $this->_filteredcount = $DB->count_records_sql($sql, $params);
        }
        return $this->_filteredcount;
    }

    /**
     * Exports the data from the current results, maintaining
     * sort order and active filters but removing pagination
     *
     * @param string $format Format for the export ods/csv/xls
     * @return No return but initiates save dialog
     */
    function export_data($format) {
        $columns = $this->columns;
        $count = $this->get_filtered_count();
        list($sql, $params, $cache) = $this->build_query(false, true);
        $order = $this->get_report_sort();

        // array of filters that have been applied
        // for including in report where possible
        $restrictions = $this->get_restriction_descriptions();

        $headings = array();
        foreach ($columns as $column) {
            // check that column should be included
            if ($column->display_column(true)) {
                $headings[] = $column;
            }
        }
        switch($format) {
            case 'ods':
                $this->download_ods($headings, $sql . $order, $params, $count, $restrictions, null, $cache);
            case 'xls':
                $this->download_xls($headings, $sql . $order, $params, $count, $restrictions, null, $cache);
            case 'csv':
                $this->download_csv($headings, $sql . $order, $params, $count);
            case 'fusion':
                $this->download_fusion();
            case 'pdf_portrait':
                $this->download_pdf($headings, $sql . $order, $params, $count, $restrictions, true, null, $cache);
            case 'pdf_landscape':
                $this->download_pdf($headings, $sql . $order, $params, $count, $restrictions, false, null, $cache);
        }
        die;
    }

    /**
     * Display the results table
     *
     * @return No return value but prints the current data table
     */
    function display_table() {
        global $SESSION, $DB, $OUTPUT;

        define('DEFAULT_PAGE_SIZE', $this->recordsperpage);
        define('SHOW_ALL_PAGE_SIZE', 9999);
        $spage     = optional_param('spage', 0, PARAM_INT);                    // which page to show
        $perpage   = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT);
        $ssort     = optional_param('ssort', '', PARAM_TEXT);

        $columns = $this->columns;
        $shortname = $this->shortname;
        $countfiltered = $this->get_filtered_count();

        if (count($columns) == 0) {
            echo html_writer::tag('p', get_string('error:nocolumnsdefined', 'report_reportbuilder'));
            return false;
        }

        list($sql, $params, $cache) = $this->build_query(false, true);

        $tablecolumns = array();
        $tableheaders = array();
        foreach ($columns as $column) {
            $type = $column->type;
            $value = $column->value;
            if ($column->display_column()) {
                $tablecolumns[] = "{$type}_{$value}"; // used for sorting
                $tableheaders[] = format_string($column->heading);
            }
        }

        // prevent notifications boxes inside the table
        echo $OUTPUT->container_start('nobox');

        // Output cache information if needed
        if ($cache) {
            $usertz = totara_get_clean_timezone();
            $lastreport = userdate($cache['lastreport'], '', $usertz);
            $nextreport = userdate($cache['nextreport'], '', $usertz);

            $html = html_writer::start_tag('div', array('class' => 'noticebox'));
            $html .= get_string('report:cachelast', 'report_reportbuilder', $lastreport);
            $html .= html_writer::empty_tag('br');
            $html .= get_string('report:cachenext', 'report_reportbuilder', $nextreport);
            $html .= html_writer::end_tag('div');
            echo $html;
        }
        $table = new flexible_table($shortname);
        $table->define_columns($tablecolumns);
        $table->define_headers($tableheaders);
        $table->define_baseurl($this->get_current_url());
        foreach ($columns as $column) {
            if ($column->display_column()) {
                $ident = "{$column->type}_{$column->value}";
                // Assign $type_$value class to each column.
                $classes = $ident;
                // Apply any column-specific class.
                if (is_array($column->class)) {
                    foreach ($column->class as $class) {
                        $classes .= ' ' . $class;
                    }
                }
                $table->column_class($ident, $classes);
                // Apply any column-specific styling.
                if (is_array($column->style)) {
                    foreach ($column->style as $property => $value) {
                        $table->column_style($ident, $property, $value);
                    }
                }
                // Hide any columns where hidden flag is set.
                if ($column->hidden != 0) {
                    $table->column_style($ident, 'display', 'none');
                }

                // Disable sorting on column where indicated.
                if ($column->nosort) {
                    $table->no_sorting($ident);
                }
            }
        }
        $table->set_attribute('cellspacing', '0');
        $table->set_attribute('id', $shortname);
        $table->set_attribute('class', 'logtable generalbox reportbuilder-table');
        $table->set_control_variables(array(
            TABLE_VAR_SORT    => 'ssort',
            TABLE_VAR_HIDE    => 'shide',
            TABLE_VAR_SHOW    => 'sshow',
            TABLE_VAR_IFIRST  => 'sifirst',
            TABLE_VAR_ILAST   => 'silast',
            TABLE_VAR_PAGE    => 'spage'
        ));
        $table->sortable(true, $this->defaultsortcolumn, $this->defaultsortorder); // sort by name by default
        $table->setup();
        $table->initialbars(true);
        $table->pagesize($perpage, $countfiltered);

        // get the ORDER BY SQL fragment from table
        $order = $this->get_report_sort($table);
        
        /*error_log($sql);
        error_log($order);
        error_log(print_r($params, true));
        error_log($table->get_page_start());
        error_log($table->get_page_size());*/
        
        try {
            if ($records = $DB->get_recordset_sql($sql.$order, $params, $table->get_page_start(), $table->get_page_size())) {
                foreach ($records as $record) {
                    $record_data = $this->process_data_row($record);
                    $table->add_data($record_data);
                }
            } else if ($data === false) {
                print_error('error:problemobtainingreportdata', 'report_reportbuilder');
            }
        } catch (dml_read_exception $e) {
            if ($this->is_cached()) {
                print_error('error:problemobtainingcachedreportdata', 'report_reportbuilder');
            } else {
                print_error('error:problemobtainingreportdata', 'report_reportbuilder');
            }
        }

        // display the table
        $table->print_html();

        // end of .nobox div
        echo $OUTPUT->container_end();
    }

    /**
     * If a redirect url has been displayed in the source then output a redirect link.
     */
    public function display_redirect_link() {
        if (isset($this->src->redirecturl)) {
            if (isset($this->src->redirectmessage)) {
                $message = '&laquo; ' . $this->src->redirectmessage;
            } else {
                $message = '&laquo; ' . get_string('selectitem', 'report_reportbuilder');
            }
            echo html_writer::link($this->src->redirecturl, $message);
        }
    }

    /**
     * Get column identifiers of columns that should be hidden on page load
     * The hidden columns are stored in the session
     *
     * @return array of column identifiers, usable by js selectors
     */
    function js_get_hidden_columns() {
        global $SESSION;
        $cols = array();

        $shortname = $this->shortname;
        // javascript to hide columns based on session variable
        if (isset($SESSION->rb_showhide_columns[$shortname])) {
            foreach ($this->columns as $column) {
                $ident = "{$column->type}_{$column->value}";
                if (isset($SESSION->rb_showhide_columns[$shortname][$ident])) {
                    if ($SESSION->rb_showhide_columns[$shortname][$ident] == 0) {
                        $cols[] = "#{$shortname} .{$ident}";
                    }
                }
            }
        }

        return $cols;
    }

    /**
     * Look up the sort keys and make sure they still exist in table
     * (could have been deleted in report builder)
     *
     * @return true May unset flexible table sort keys if they are not
     *              found in the column list
     */
    function check_sort_keys() {
        global $SESSION;
        $shortname = $this->shortname;
        $sortarray = isset($SESSION->flextable[$shortname]->sortby) ? $SESSION->flextable[$shortname]->sortby : null;
        if (is_array($sortarray)) {
            foreach ($sortarray as $sortelement => $unused) {
                // see if sort element is in columns array
                $set = false;
                foreach ($this->columns as $col) {
                    if ($col->type . '_' . $col->value == $sortelement) {
                        $set = true;
                    }
                }
                // if it's not remove it from sort SESSION var
                if ($set === false) {
                    unset($SESSION->flextable[$shortname]->sortby[$sortelement]);
                }
            }
        }
        return true;
    }


    /**
     * Given a record, returns an array of data for the record. If display
     * functions exist for any columns the data is passed to the display
     * function and the result included instead.
     *
     * @param array $record A record returnd by a recordset
     * @param boolean $striptags If true, returns the data with any html tags removed
     * @param boolean $isexport If true, data is being exported
     * @param boolean $excel true if processing data for an export_xls
     * @return array Outer array are table rows, inner array are columns
     *               False is returned if the SQL query failed entirely
     */
    function process_data_row($record, $striptags=false, $isexport=false, $excel=false) {
        $columns = $this->columns;
        $columnoptions = $this->columnoptions;

        $tabledata = array();
        foreach ($columns as $column) {
            // check column should be shown
            if ($column->display_column($isexport)) {
                $type = $column->type;
                $value = $column->value;
                $field = "{$type}_{$value}";
                // treat fields different if display function exists
                if (isset($column->displayfunc)) {
                    $func = 'rb_display_'.$column->displayfunc;
                    if (method_exists($this->src, $func)) {
                        if ($column->displayfunc == 'customfield_textarea' || $column->displayfunc == 'customfield_file' || $column->displayfunc == 'tinymce_textarea') {
                            $tabledata[] = $this->src->$func($field, $record->$field, $record, $isexport);
                        } else if (($column->displayfunc == 'nice_date' || $column->displayfunc == 'nice_datetime') && $excel) {
                            $tabledata[] = $record->$field;
                        } else {
                            $tabledata[] = $this->src->$func(format_text($record->$field, FORMAT_HTML), $record, $isexport);
                        }
                    } else {
                        $tabledata[] = format_text($record->$field, FORMAT_HTML);
                    }
                } else {
                    $tabledata[] = format_text($record->$field, FORMAT_HTML);
                }
            }
        }
        if ($striptags === true) {
            return $this->strip_tags_r($tabledata);
        } else {
            return $tabledata;
        }
    }


    /**
     * Recursive version of strip_tags
     *
     * @param array $value A nested array of strings
     * @return array The same array with HTML stripped from all strings
     */
    function strip_tags_r($value) {
        return is_array($value) ? array_map(array($this, 'strip_tags_r'), $value) :
            strip_tags($value);
    }


    /**
     * Returns a menu that when selected, takes the user to the specified saved search
     *
     * @return string HTML to display a pulldown menu with saved search options
     */
    function view_saved_menu() {
        global $USER, $DB, $OUTPUT;
        $id = $this->_id;
        $sid = $this->_sid;
        $savedoptions = array();
        if ($this->embedded) {
            $common = new moodle_url($this->get_current_url());
        } else {
            $common = new moodle_url('/report/reportbuilder/report.php', array('id' => $id));
        }
        // Are there saved searches for this report and user?
        $saved = $DB->get_records('report_builder_saved', array('reportid' => $id, 'userid' => $USER->id));
        foreach ($saved as $item) {
            $savedoptions[$item->id] = format_string($item->name);
        }
        // are there public saved searches for this report?
        $saved = $DB->get_records('report_builder_saved', array('reportid' => $id, 'ispublic' => 1));
        foreach ($saved as $item) {
            $savedoptions[$item->id] = format_string($item->name);
        }

        if (count($savedoptions) > 0) {
            $select = new single_select($common, 'sid', $savedoptions, $sid);
            $select->label = get_string('viewsavedsearch', 'report_reportbuilder');
            $select->formid = 'viewsavedsearch';

            return $OUTPUT->render($select);

        } else {
            return '';
        }
    }


    /**
     * Diplays a table containing the save search button and pulldown
     * of existing saved searches (if any)
     *
     * @return string HTML to display the table
     */
    public function display_saved_search_options() {
        global $PAGE;
        $output = $PAGE->get_renderer('report_reportbuilder');

        $savedbutton = $output->save_button($this);
        $savedmenu = $this->view_saved_menu();

        // no need to print anything
        if (strlen($savedmenu) == 0 && strlen($savedbutton) == 0) {
            return '';
        }

        $table = new html_table();
        $row = new html_table_row();
        $table->attributes['class'] = 'invisiblepadded rb-search';

        if (strlen($savedbutton) != 0) {
            $row->cells[] = new html_table_cell($savedbutton);
        }
        if (strlen($savedmenu) != 0) {
             $cell = new html_table_cell($savedmenu);
             $cell->attributes['class'] .= 'rb-search-menu';
             $row->cells[] = $cell;
        }

        $table->data = array($row);
        return html_writer::table($table);

    }


    /**
     * Returns HTML for a button that when clicked, takes the user to a page which
     * allows them to edit this report
     *
     * @return string HTML to display the button
     */
    function edit_button() {
        global $OUTPUT;
        $context = context_system::instance();
        // TODO what capability should be required here?
        if (has_capability('report/reportbuilder:managereports', $context)) {
            return $OUTPUT->single_button(new moodle_url('/report/reportbuilder/general.php', array('id' => $this->_id)), get_string('editthisreport', 'report_reportbuilder'), 'get');
        } else {
            return '';
        }
    }


    /** Download current table in ODS format
     * @param array $fields Array of column headings
     * @param string $query SQL query to run to get results
     * @param array $params SQL query params
     * @param integer $count Number of filtered records in query
     * @param array $restrictions Array of strings containing info
     *                            about the content of the report
     * @param string $file path to the directory where the file will be saved
     * @param array $cache report cache information
     * @return Returns the ODS file
     */
    function download_ods($fields, $query, $params, $count, $restrictions = array(), $file = null, $cache = array()) {
        global $CFG, $DB;

        require_once("$CFG->libdir/odslib.class.php");
        $fullname = strtolower(preg_replace(array('/[^a-zA-Z\d\s-_]/', '/[\s-]/'), array('', '_'), $this->fullname));
        $filename = clean_filename($fullname . '_report.ods');

        if (!$file) {
            header("Content-Type: application/download\n");
            header("Content-Disposition: attachment; filename=$filename");
            header("Expires: 0");
            header("Cache-Control: must-revalidate,post-check=0,pre-check=0");
            header("Pragma: public");

            $workbook = new MoodleODSWorkbook($filename);
        } else {
            $workbook = new MoodleODSWorkbook($file, true);
        }

        $worksheet = array();

        $worksheet[0] = $workbook->add_worksheet('');
        $row = 0;
        $col = 0;

        if (is_array($restrictions) && count($restrictions) > 0) {
            $worksheet[0]->write($row, 0, get_string('reportcontents', 'report_reportbuilder'));
            $row++;
            foreach ($restrictions as $restriction) {
                $worksheet[0]->write($row, 0, $restriction);
                $row++;
            }
        }

        // Add report caching data.
        if ($cache) {
            $usertz = totara_get_clean_timezone();
            $a = userdate($cache['lastreport'], '', $usertz);
            $worksheet[0]->write($row, 0, get_string('report:cachelast', 'report_reportbuilder', $a));
            $row++;
        }

        // Leave an empty row between any initial info and the header row.
        if ($row != 0) {
            $row++;
        }

        foreach ($fields as $field) {
            $worksheet[0]->write($row, $col, strip_tags($field->heading));
            $col++;
        }
        $row++;

        $numfields = count($fields);

        // Use recordset so we can manage very large datasets.
        if ($records = $DB->get_recordset_sql($query, $params)) {
            foreach ($records as $record) {
                $record_data = $this->process_data_row($record, true, true);

                for($col=0; $col<$numfields; $col++) {
                    if (isset($record_data[$col])) {
                        $worksheet[0]->write($row, $col, html_entity_decode($record_data[$col], ENT_COMPAT, 'UTF-8'));
                    }
                }
                $row++;
            }
            $records->close();
        } else {
            // This indicates a failed query, not just 0 results.
            return false;
        }

        $workbook->close();
        if (!$file) {
            die;
        }
    }

    /** Download current table in XLS format
     * @param array $fields Array of column headings
     * @param string $query SQL query to run to get results
     * @param array $params SQL query params
     * @param integer $count Number of filtered records in query
     * @param array $restrictions Array of strings containing info
     *                            about the content of the report
     * @param string $file path to the directory where the file will be saved
     * @param array $cache Report cache information
     * @return Returns the Excel file
     */
    function download_xls($fields, $query, $params, $count, $restrictions = array(), $file = null, $cache = array()) {
        global $CFG, $DB;

        require_once("$CFG->libdir/excellib.class.php");

        $fullname = strtolower(preg_replace(array('/[^a-zA-Z\d\s-_]/', '/[\s-]/'), array('', '_'), $this->fullname));
        $filename = clean_filename($fullname . '_report.xls');

        if (!$file) {
            $workbook = new MoodleExcelWorkbook($filename);
        } else {
            $workbook = new MoodleExcelWorkbook($file, 'Excel2007', true);
        }

        $worksheet = array();

        $worksheet[0] = $workbook->add_worksheet('');
        $row = 0;
        $col = 0;
        $dateformat = $workbook->add_format();
        $dateformat->set_num_format('dd mmm yyyy');
        $datetimeformat = $workbook->add_format();
        $datetimeformat->set_num_format('dd mmm yyyy h:mm');

        if (is_array($restrictions) && count($restrictions) > 0) {
            $worksheet[0]->write($row, 0, get_string('reportcontents', 'report_reportbuilder'));
            $row++;
            foreach ($restrictions as $restriction) {
                $worksheet[0]->write($row, 0, $restriction);
                $row++;
            }
        }

        // Add report caching data.
        if ($cache) {
            $usertz = totara_get_clean_timezone();
            $a = userdate($cache['lastreport'], '', $usertz);
            $worksheet[0]->write($row, 0, get_string('report:cachelast', 'report_reportbuilder', $a));
            $row++;
        }

        // Leave an empty row between any initial info and the header row.
        if ($row != 0) {
            $row++;
        }

        foreach ($fields as $field) {
            $worksheet[0]->write($row, $col, strip_tags($field->heading));
            $col++;
        }
        $row++;

        $numfields = count($fields);

        // User recordset so we can handle large datasets.
        if ($records = $DB->get_recordset_sql($query, $params)) {
            foreach ($records as $record) {
                $record_data = $this->process_data_row($record, true, true, true);
                for ($col=0; $col<$numfields; $col++) {
                    if (isset($record_data[$col]) && !empty($record_data[$col])) {
                        if ($fields[$col]->displayfunc == 'nice_date') {
                            $worksheet[0]->write_date($row, $col, $record_data[$col], $dateformat);
                        } else if ($fields[$col]->displayfunc == 'nice_datetime') {
                            $worksheet[0]->write_date($row, $col, $record_data[$col], $datetimeformat);
                        } else {
                            $worksheet[0]->write($row, $col, html_entity_decode($record_data[$col], ENT_COMPAT, 'UTF-8'));
                        }
                    }
                }
                $row++;
            }
            $records->close();
        } else {
            // This indicates a failed query, not just 0 results.
            return false;
        }

        $workbook->close();
        if (!$file) {
            die;
        }
    }

     /** Download current table in CSV format
     * @param array $fields Array of column headings
     * @param string $query SQL query to run to get results
     * @param array $params SQL query params
     * @param integer $count Number of filtered records in query
     * @return Returns the CSV file
     */
    function download_csv($fields, $query, $params, $count, $file=null) {
        global $DB, $CFG;

        require_once("{$CFG->libdir}/csvlib.class.php");

        $fullname = strtolower(preg_replace(array('/[^a-zA-Z\d\s-_]/', '/[\s-]/'), array('', '_'), $this->fullname));
        $filename = clean_filename($fullname . '_report.csv');

        $export = new csv_export_writer();
        $export->filename = $filename;

        $row = array();
        foreach ($fields as $field) {
            $row[] = strip_tags($field->heading);
        }

        $export->add_data($row);
        $numfields = count($fields);

        if ($records = $DB->get_recordset_sql($query, $params)) {
            foreach ($records as $record) {
                $record_data = $this->process_data_row($record, true, true);
                $row = array();
                for ($j=0; $j<$numfields; $j++) {
                    if (isset($record_data[$j])) {
                        $row[] = html_entity_decode($record_data[$j], ENT_COMPAT, 'UTF-8');
                    } else {
                        $row[] = '';
                    }
                }
                $export->add_data($row);
            }
            $records->close();
        } else {
            // this indicates a failed query, not just 0 results
            return false;
        }

        if ($file) {
            $fp = fopen($file, "w");
            fwrite($fp, $export->print_csv_data(true));
            fclose($fp);
        } else {
            $export->download_file();
        }
    }

    /**
     * Download current table in a Pdf format
     * @param array $fields Array of column headings
     * @param string $query SQL query to run to get results
     * @param array $params SQL query params
     * @param integer $count Number of filtered records in query
     * @param array $restrictions Array of strings containing info
     *                            about the content of the report
     * @param boolean $portrait A boolean representing the print layout
     * @param string a path where to save file
     * @param array $cache Report cache information
     * @return Returns the PDF file
     */
    function download_pdf($fields, $query, $params, $count, $restrictions = array(), $portrait = true, $file = null, $cache = array()) {
        global $DB, $CFG;

        require_once $CFG->libdir . '/pdflib.php';

        // Increasing the execution time to no limit.
        set_time_limit(0);

        $fullname = strtolower(preg_replace(array('/[^a-zA-Z\d\s-_]/', '/[\s-]/'), array('', '_'), $this->fullname));
        $filename = clean_filename($fullname . '_report.pdf');

        // Table.
        $html = '';
        $numfields = count($fields);

        if (!$records = $DB->get_recordset_sql($query, $params)) {
            return false;
        }

        // Layout options.
        if ($portrait) {
            $pdf = new PDF('P', 'mm', 'A4', true, 'UTF-8');
        } else {
            $pdf = new PDF('L', 'mm', 'A4', true, 'UTF-8');
        }

        $pdf->setTitle($filename);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->AddPage();

        // Get current language to set the font properly.
        $language = current_language();
        $font = $this->get_font($language);
        // Check if language is RTL.
        if (right_to_left()) {
            $pdf->setRTL(true);
        }

        $pdf->SetFont($font, 'B', PDF_FONT_SIZE_TITLE);
        $pdf->Write(0, format_string($this->fullname), '', 0, 'L', true, 0, false, false, 0);

        $resultstr = $count == 1 ? 'record' : 'records';
        $recordscount = get_string('x' . $resultstr, 'report_reportbuilder', $count);
        $pdf->SetFont($font, 'B', PDF_FONT_SIZE_RECORD);
        $pdf->Write(0, $recordscount, '', 0, 'L', true, 0, false, false, 0);

        $pdf->SetFont($font, '', PDF_FONT_SIZE_DATA);

        if (is_array($restrictions) && count($restrictions) > 0) {
            $pdf->Write(0, get_string('reportcontents', 'report_reportbuilder'), '', 0, 'L', true, 0, false, false, 0);
            foreach ($restrictions as $restriction) {
                $pdf->Write(0, $restriction, '', 0, 'L', true, 0, false, false, 0);
            }
        }

        // Add report caching data.
        if ($cache) {
            $usertz = totara_get_clean_timezone();
            $a = userdate($cache['lastreport'], '', $usertz);
            $lastcache = get_string('report:cachelast', 'report_reportbuilder', $a);
            $pdf->Write(0, $lastcache, '', 0, 'L', true, 0, false, false, 0);
        }

        $html .= '<table border="1" cellpadding="2" cellspacing="0">
                        <thead>
                            <tr style="background-color: #CCC;">';
        foreach ($fields as $field) {
            $html .= '<th>' . strip_tags($field->heading) . '</th>';
        }
        $html .= '</tr></thead><tbody>';

        foreach ($records as $record) {
            $record_data = $this->process_data_row($record, true, true);
            $html .= '<tr>';
            for ($j = 0; $j < $numfields; $j++) {
                if (isset($record_data[$j])) {
                    $cellcontent = html_entity_decode($record_data[$j], ENT_COMPAT, 'UTF-8');
                } else {
                    $cellcontent = '';
                }
                $html .= '<td>' . $cellcontent . '</td>';
            }
            $html .= '</tr>';

            // Check memory limit.
            $mramuse = ceil(((memory_get_usage(true)/1024)/1024));
            if (REPORT_BUILDER_EXPORT_PDF_MEMORY_LIMIT <= $mramuse) {
                // Releasing resources.
                $records->close();
                // Notice message.
                print_error('exportpdf_mramlimitexceeded', 'report_reportbuilder', '', REPORT_BUILDER_EXPORT_PDF_MEMORY_LIMIT);
            }
        }
        $html .= '</tbody></table>';

        // Closing the pdf.
        $pdf->WriteHTML($html, true, false, false, false, '');

        // Releasing recordset resources.
        $records->close();

        // Returning the complete pdf.
        if (!$file) {
            $pdf->Output($filename, 'D');
        } else {
            $pdf->Output($file, 'F');
        }
    }

    /**
     * Returns the font that must be used based on the language
     *
     * @param string $language Language that is being used
     * @return string The appropriate font based on the language
     */
    function get_font($language) {
        if (in_array($language, array('zh_cn', 'ja'))) {
            return 'droidsansfallback';
        } else if ($language == 'th') {
            return 'cordiaupc';
        } else {
            return 'dejavusans';
        }
    }

    /* Download current table to Google Fusion
     * @param array $fields Array of column headings
     * @param string $query SQL query to run to get results
     * @param integer $count Number of filtered records in query
     * @param array $restrictions Array of strings containing info
     *                            about the content of the report
     * @return Returns never
     */
    function download_fusion() {
        $jump = new moodle_url('/report/reportbuilder/fusionexporter.php', array('id' => $this->_id, 'sid' => $this->_sid));
        redirect($jump->out());
        die;
    }

    /**
     * Returns array of content options allowed for this report's source
     *
     * @return array An array of content option names
     */
    function get_content_options() {

        $contentoptions = array();
        if (isset($this->contentoptions) && is_array($this->contentoptions)) {
            foreach ($this->contentoptions as $option) {
                $contentoptions[] = $option->classname;
            }
        }
        return $contentoptions;
    }


    ///
    /// Functions for Editing Reports
    ///


    /**
     * Parses the filter options data for this source into a data structure
     * suitable for an HTML select pulldown.
     *
     * @return array An Array with $type-$value as key and $label as value
     */
    function get_filters_select() {
        $filters = $this->filteroptions;
        $ret = array();
        if (!isset($this->filteroptions)) {
            return $ret;
        }

        // are we handling a 'group' source?
        if (preg_match('/^(.+)_grp_([0-9]+|all)$/', $this->source, $matches)) {
            // use original source name (minus any suffix)
            $sourcename = $matches[1];
        } else {
            // standard source
            $sourcename = $this->source;
        }

        foreach ($filters as $filter) {
            $langstr = 'type_' . $filter->type;
            // is there a type string in the source file?
            if (get_string_manager()->string_exists($langstr, 'rb_source_' . $sourcename)) {
                $section = get_string($langstr, 'rb_source_' . $sourcename);
            // how about in report builder?
            } else if (get_string_manager()->string_exists($langstr, 'report_reportbuilder')) {
                $section = get_string($langstr, 'report_reportbuilder');
            } else {
            // fall back on original approach to cope with dynamic types in feedback sources
                $section = ucwords(str_replace(array('_', '-'), array(' ', ' '), $filter->type));
            }

            $key = $filter->type . '-' . $filter->value;
            $ret[$section][$key] = $filter->label;
        }
        return $ret;
    }

    /**
     * Parses the column options data for this source into a data structure
     * suitable for an HTML select pulldown
     *
     * @return array An array with $type-$value as key and $name as value
     */
    function get_columns_select() {
        $columns = $this->columnoptions;
        $ret = array();
        if (!isset($this->columnoptions)) {
            return $ret;
        }

        // are we handling a 'group' source?
        if (preg_match('/^(.+)_grp_([0-9]+|all)$/', $this->source, $matches)) {
            // use original source name (minus any suffix)
            $sourcename = $matches[1];
        } else {
            // standard source
            $sourcename = $this->source;
        }

        foreach ($columns as $column) {
            // don't include unselectable columns
            if (!$column->selectable) {
                continue;
            }
            $langstr = 'type_' . $column->type;
            // is there a type string in the source file?
            if (get_string_manager()->string_exists($langstr, 'rb_source_' . $sourcename)) {
                $section = get_string($langstr, 'rb_source_' . $sourcename);
            // how about in report builder?
            } else if (get_string_manager()->string_exists($langstr, 'report_reportbuilder')) {
                $section = get_string($langstr, 'report_reportbuilder');
            } else {
            // fall back on original approach to cope with dynamic types in feedback sources
                $section = ucwords(str_replace(array('_', '-'), array(' ', ' '), $column->type));
            }

            $key = $column->type . '-' . $column->value;
            $ret[$section][$key] = $column->name;
        }
        return $ret;
    }

    /**
     * Given a column id, sets the default visibility to show or hide
     * for that column on current report
     *
     * @param integer $cid ID of the column to be changed
     * @param integer $hide 0 to show column, 1 to hide it
     * @return boolean True on success, false otherwise
     */
    function showhide_column($cid, $hide) {
        global $DB;

        $col = $DB->get_record('report_builder_columns', array('id' => $cid));
        if (!$col) {
            return false;
        }

        $todb = new stdClass();
        $todb->id = $cid;
        $todb->hidden = $hide;
        $DB->update_record('report_builder_columns', $todb);
        return true;
    }

    /**
     * Given a column id, removes that column from the current report
     *
     * @param integer $cid ID of the column to be removed
     * @return boolean True on success, false otherwise
     */
    function delete_column($cid) {
        global $DB;

        $id = $this->_id;
        $sortorder = $DB->get_field('report_builder_columns', 'sortorder', array('id' => $cid));
        if (!$sortorder) {
            return false;
        }
        $transaction = $DB->start_delegated_transaction();

        $DB->delete_records('report_builder_columns', array('id' => $cid));
        $allcolumns = $DB->get_records('report_builder_columns', array('reportid' => $id));
        foreach ($allcolumns as $column) {
            if ($column->sortorder > $sortorder) {
                $todb = new stdClass();
                $todb->id = $column->id;
                $todb->sortorder = $column->sortorder - 1;
                $DB->update_record('report_builder_columns', $todb);
            }
        }
        $transaction->allow_commit();

        $this->columns = $this->get_columns();
        return true;
    }

    /**
     * Given a filter id, removes that filter from the current report and
     * updates the sortorder for other filters
     *
     * @param integer $fid ID of the filter to be removed
     * @return boolean True on success, false otherwise
     */
    function delete_filter($fid) {
        global $DB;

        $id = $this->_id;

        $sortorder = $DB->get_field('report_builder_filters', 'sortorder', array('id' => $fid));
        if (!$sortorder) {
            return false;
        }

        $transaction = $DB->start_delegated_transaction();

        $DB->delete_records('report_builder_filters', array('id' => $fid));
        $allfilters = $DB->get_records('report_builder_filters', array('reportid' => $id));
        foreach ($allfilters as $filter) {
            if ($filter->sortorder > $sortorder) {
                $todb = new stdClass();
                $todb->id = $filter->id;
                $todb->sortorder = $filter->sortorder - 1;
                $DB->update_record('report_builder_filters', $todb);
            }
        }

        $transaction->allow_commit();

        $this->filters = $this->get_filters();
        return true;
    }

    /**
     * Given a column id and a direction, moves a column up or down
     *
     * @param integer $cid ID of the column to be moved
     * @param string $updown String 'up' or 'down'
     * @return boolean True on success, false otherwise
     */
    function move_column($cid, $updown) {
        global $DB;

        $id = $this->_id;

        // assumes sort order is well behaved (no gaps)
        if (!$itemsort = $DB->get_field('report_builder_columns', 'sortorder', array('id' => $cid))) {
            return false;
        }
        if ($updown == 'up') {
            $newsort = $itemsort - 1;
        } else if ($updown == 'down') {
            $newsort = $itemsort + 1;
        } else {
            // invalid updown string
            return false;
        }
        if ($neighbour = $DB->get_record('report_builder_columns', array('reportid' => $id, 'sortorder' => $newsort))) {
            $transaction = $DB->start_delegated_transaction();
            // swap sort orders
            $todb = new stdClass();
            $todb->id = $cid;
            $todb->sortorder = $neighbour->sortorder;
            $todb2 = new stdClass();
            $todb2->id = $neighbour->id;
            $todb2->sortorder = $itemsort;
            $DB->update_record('report_builder_columns', $todb);
            $DB->update_record('report_builder_columns', $todb2);
            $transaction->allow_commit();
        } else {
            // no neighbour
            return false;
        }
        $this->columns = $this->get_columns();
        return true;
    }


    /**
     * Given a filter id and a direction, moves a filter up or down
     *
     * @param integer $fid ID of the filter to be moved
     * @param string $updown String 'up' or 'down'
     * @return boolean True on success, false otherwise
     */
    function move_filter($fid, $updown) {
        global $DB;

        $id = $this->_id;

        // assumes sort order is well behaved (no gaps)
        if (!$itemsort = $DB->get_field('report_builder_filters', 'sortorder', array('id' => $fid))) {
            return false;
        }
        if ($updown == 'up') {
            $newsort = $itemsort - 1;
        } else if ($updown == 'down') {
            $newsort = $itemsort + 1;
        } else {
            // invalid updown string
            return false;
        }
        if ($neighbour = $DB->get_record('report_builder_filters', array('reportid' => $id, 'sortorder' => $newsort))) {
            $transaction = $DB->start_delegated_transaction();
            // swap sort orders
            $todb = new stdClass();
            $todb->id = $fid;
            $todb->sortorder = $neighbour->sortorder;
            $todb2 = new stdClass();
            $todb2->id = $neighbour->id;
            $todb2->sortorder = $itemsort;
            $DB->update_record('report_builder_filters', $todb);
            $DB->update_record('report_builder_filters', $todb2);
            $transaction->allow_commit();
        } else {
            // no neighbour
            return false;
        }
        $this->filters = $this->get_filters();
        return true;
    }

    /**
     * Method for obtaining a report builder setting
     *
     * @param integer $reportid ID for the report to obtain a setting for
     * @param string $type Identifies the class using the setting
     * @param string $name Identifies the particular setting
     * @param bool $cache Use cached settings
     * @return mixed The value of the setting $name or null if it doesn't exist
     */
    static function get_setting($reportid, $type, $name, $cache = false) {
        global $DB;
        $field = ($cache) ? 'cachedvalue' : 'value';
        return $DB->get_field('report_builder_settings', $field, array('reportid' => $reportid, 'type' => $type, 'name' => $name));
    }

    /**
     * Return an associative array of all settings of a particular type
     *
     * @param integer $reportid ID of the report to get settings for
     * @param string $type Identifies the class to get settings from
     * @param bool $cache Use cached settings
     * @return array Associative array of name|value settings
     */
    static function get_all_settings($reportid, $type, $cache = false) {
        global $DB;

        $settings = array();
        $field = ($cache) ? 'cachedvalue' : 'value';
        $records = $DB->get_records('report_builder_settings', array('reportid' => $reportid, 'type' => $type));
        foreach ($records as $record) {
            $settings[$record->name] = $record->{$field};
        }
        return $settings;
    }

    /**
     * Method for updating a setting for a particular report
     *
     * Will create a DB record if no setting is found
     *
     * @param integer $reportid ID of the report to update the settings of
     * @param string $type Identifies the class to be updated
     * @param string $name Identifies the particular setting to update
     * @param string $value The new value of the setting
     * @return boolean True if the setting could be updated or created
     */
    static function update_setting($reportid, $type, $name, $value) {
        global $DB;

        if ($record = $DB->get_record('report_builder_settings', array('reportid' => $reportid, 'type' => $type, 'name' => $name))) {
            // update record
            $todb = new stdClass();
            $todb->id = $record->id;
            $todb->value = $value;
            $DB->update_record('report_builder_settings', $todb);
        } else {
            // insert record
            $todb = new stdClass();
            $todb->reportid = $reportid;
            $todb->type = $type;
            $todb->name = $name;
            $todb->value = $value;
            $DB->insert_record('report_builder_settings', $todb);
        }
        return true;
    }


    /**
     * Return HTML to display the results of a feedback activity
     */
    function print_feedback_results() {
        global $DB, $OUTPUT;
        // get paging parameters
        define('DEFAULT_PAGE_SIZE', $this->recordsperpage);
        define('SHOW_ALL_PAGE_SIZE', 9999);
        $spage     = optional_param('spage', 0, PARAM_INT);                    // which page to show
        $perpage   = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT);
        $countfiltered = $this->get_filtered_count();

        $out = '';
        $groupid = $this->src->groupid;
        $out .= $OUTPUT->box_start();

        if (!$groupid) {
            $out .= get_string('activitygroupnotfound', 'report_reportbuilder');
        }
        $questionstable = "report_builder_fbq_{$groupid}_q";
        $optionstable = "report_builder_fbq_{$groupid}_opt";
        $answerstable = "report_builder_fbq_{$groupid}_a";

        $questions = $DB->get_records($questionstable, null, 'sortorder');
        $options = $DB->get_records($optionstable, null, 'qid, sortorder');
        $grouped_options = array();
        foreach ($options as $option) {
            $grouped_options[$option->qid][] = $option;
        }

        // get first column and use as heading
        $columns = $this->columns;
        if (count($columns) > 0) {
            $primary_field = current($columns);
            if ($primary_field->required == true) {
                $primary_field = null;
            }

            // get any extra (none required) columns
            $additional_fields = array();
            while($col = next($columns)) {
                if ($col->required == false) {
                    $additional_fields[] = $col;
                }
            }
        }

        // get data
        list($sql, $params) = $this->build_query(false, true);

        $baseid = $this->grouped ? 'min(base.id)' : 'base.id';

        // use default sort data if set
        if (isset($this->defaultsortcolumn)) {
            if (isset($this->defaultsortorder) &&
                $this->defaultsortorder == SORT_DESC) {
                $order = 'DESC';
            } else {
                $order = 'ASC';
            }

            // see if sort element is in columns array
            $set = false;
            foreach ($this->columns as $col) {
                if ($col->type . '_' . $col->value == $this->defaultsortcolumn) {
                    $set = true;
                }
            }
            if ($set) {
                $sort = " ORDER BY {$this->defaultsortcolumn} {$order}, {$baseid}";
            } else {
                $sort = " ORDER BY {$baseid}";
            }
        } else {
            $sort = " ORDER BY {$baseid}";
        }
        $data = $DB->get_records_sql($sql . $sort, $params, $spage * $perpage, $perpage);
        $first = true;

        foreach ($data as $item) {
            // dividers between feedback results
            if ($first) {
                $pagingbar = new paging_bar($countfiltered, $spage, $perpage, $this->report_url());
                $pagingbar->pagevar = 'spage';
                $out .= $OUTPUT->render($pagingbar);

                $first = false;
            } else {
                $out .= html_writer::empty_tag('hr', array('class' => 'feedback-separator'));
            }

            if (isset($primary_field)) {
                // print primary heading
                $primaryname = $primary_field->type . '_' . $primary_field->value;
                $primaryheading = $primary_field->heading;

                // treat fields different if display function exists
                if (isset($primary_field->displayfunc)) {
                    $func = 'rb_display_' . $primary_field->displayfunc;
                    if (method_exists($this->src, $func)) {
                        $primaryvalue = $this->src->$func(format_text($item->$primaryname, FORMAT_HTML), $item, false);
                    } else {
                        $primaryvalue = (isset($item->$primaryname)) ? format_text($item->$primaryname, FORMAT_HTML) : get_string('unknown', 'report_reportbuilder');
                    }
                } else {
                    $primaryvalue = (isset($item->$primaryname)) ? format_text($item->$primaryname, FORMAT_HTML) : get_string('unknown', 'report_reportbuilder');
                }

                $out .= $OUTPUT->heading($primaryheading . ': ' . $primaryvalue, 2);
            }

            if (isset($additional_fields)) {
                // print secondary details
                foreach ($additional_fields as $additional_field) {
                    $addname = $additional_field->type . '_' . $additional_field->value;
                    $addheading = $additional_field->heading;
                    $addvalue = (isset($item->$addname)) ? $item->$addname : get_string('unknown', 'report_reportbuilder');
                    // treat fields different if display function exists
                    if (isset($additional_field->displayfunc)) {
                        $func = 'rb_display_' . $additional_field->displayfunc;
                        if (method_exists($this->src, $func)) {
                            $addvalue = $this->src->$func(format_text($item->$addname, FORMAT_HTML), $item);
                        } else {
                            $addvalue = (isset($item->$addname)) ? format_text($item->$addname, FORMAT_HTML) : get_string('unknown', 'report_reportbuilder');
                        }
                    } else {
                        $addvalue = (isset($item->$addname)) ? format_text($item->$addname, FORMAT_HTML) : get_string('unknown', 'report_reportbuilder');
                    }

                    $out .= html_writer::tag('strong', $addheading . ': '. $addvalue) . html_writer::empty_tag('br');
                }
            }

            // print count of number of results
            $out .= html_writer::tag('p', get_string('resultsfromfeedback', 'report_reportbuilder', $item->responses_number));

            // display answers
            foreach ($questions as $question) {
                $qnum = $question->sortorder;;
                $qname = $question->name;
                $qid = $question->id;
                $out .= $OUTPUT->heading('Q' . $qnum . ': ' . $qname, 3);

                switch($question->typ) {
                case 'dropdown':
                case 'dropdownrated':
                case 'check':
                case 'radio':
                case 'radiorated':
                    // if it's an option based question, display bar chart if there are options
                    if (!array_key_exists($qid, $grouped_options)) {
                        continue;
                    }
                    $out .= $this->get_feedback_option_answer($qid, $grouped_options[$qid], $item);
                    break;
                case 'textarea':
                case 'textfield':
                    // if it's a text based question, print all answers in a text field
                    $out .= $this->get_feedback_standard_answer($qid, $item);
                    break;
                case 'numeric':
                default:
                }

            }
        }

        $pagingbar = new paging_bar($countfiltered, $spage, $perpage, $this->report_url());
        $pagingbar->pagevar = 'spage';
        $out .= $OUTPUT->render($pagingbar);

        $out .= $OUTPUT->box_end();

        return $out;
    }

    function get_feedback_standard_answer($qid, $item) {
        $out = '';
        $count = 'q' . $qid . '_count';
        $answer = 'q' . $qid . '_list';
        if (isset($item->$count)) {
            $out .= html_writer::tag('p', get_string('numresponses', 'report_reportbuilder', $item->$count));
        }
        if (isset($item->$answer) && $item->$answer != '') {
            $responses = str_replace(array('<br />'), array("\n"), $item->$answer);
            $out .= html_writer::tag('textarea', $responses, array('rows' => '6', 'cols' => '100'));
        }
        return $out;
    }

    function get_feedback_option_answer($qid, $options, $item) {
        $out = '';
        $count = array();
        $perc = array();
        // group answer counts and percentages
        foreach ($options as $option) {
            $oid = $option->sortorder;
            $countname = 'q' . $qid . '_' . $oid . '_sum';
            $percname = 'q' . $qid . '_' . $oid . '_perc';
            if (isset($item->$countname)) {
                $count[$oid] = $item->$countname;
            } else {
                $count[$oid] = null;
            }
            if (isset($item->$percname)) {
                $perc[$oid] = $item->$percname;
            } else {
                $perc[$oid] = null;
            }
        }
        $maxcount = max($count);
        $maxbarwidth = 100; // percent

        $numresp = 'q' . $qid . '_total';
        if (isset($item->$numresp)) {
            $out .= html_writer::tag('p', get_string('numresponses', 'report_reportbuilder', $item->$numresp));
        }

        $table =- new html_table();
        $table->attributes['class'] = 'feedback-table';
        foreach ($options as $option) {
            $cells = array();
            $oid = $option->sortorder;
            $cell = new html_table_cell($oid);
            $cell->attributes['class'] = 'feedback-option-number';
            $cells[] = $cell;
            $cell = new html_table_cell($option->name);
            $cell->attributes['class'] = 'feedback-option-name';
            $cells[] = $cell;
            $barwidth = $perc[$oid];
            $spacewidth = 100 - $barwidth;
            $innertable = new html_table();
            $innertable->attributes['class'] = 'feedback-bar-chart';
            $innercells = array();
            $cell = new html_table_cell('');
            $cell->attributes['class'] = 'feedback-bar-color';
            $cell->attributes['width'] = $barwidth.'%';
            $innercells[] = $cell;
            $cell = new html_table_cell('');
            $cell->attributes['class'] = 'feedback-bar-blank';
            $cell->attributes['width'] = $spacewidth.'%';
            $innercells[] = $cell;
            $innertable->data[] = new html_table_row($innercells);
            $cell = new html_table_cell(html_writer::table($innertable));
            $cell->attributes['class'] = 'feedback-option-chart';
            $cells[] = $cell;
            $content = $count[$oid];
            if (isset($perc[$oid])) {
                $content .= ' (' . $perc[$oid] . '%)';
            }
            $cell = new html_table_cell($content);
            $cell->attributes['class'] = 'feedback-option-count';
            $cells[] = $cell;
            $table->data[] = new html_table_row($cells);
        }
        $out .= html_writer::table($table);
        return $out;
    }

    /**
     * Determines if this report currently has any active filters or not
     *
     * This is done by fetching the filtering SQL to see if it is set yet
     *
     * @return boolean True if one or more filters are currently active
     */
    function is_report_filtered() {
        $filters = $this->fetch_sql_filters();
        if (isset($filters[0]['where']) && $filters[0]['where'] != '') {
            return true;
        }
        if (isset($filters[0]['having']) && $filters[0]['having'] != '') {
            return true;
        }
        return false;
    }

    /**
     * Setter for post_config_restrictions property
     *
     * This is an array of the form:
     *
     * $restrictions = array(
     *     "sql_where_snippet",
     *     array('paramkey' => 'paramvalue')
     * );
     *
     * i.e. it provides both a string of SQL and any parameters used by that string.
     *
     * @param array Restrictions to be added to the query WHERE clause.
     */
    public function set_post_config_restrictions($restrictions) {
        $this->_post_config_restrictions = $restrictions;
    }

    /**
     * Getter for post_config_restrictions.
     */
    public function get_post_config_restrictions() {
        if (empty($this->_post_config_restrictions)) {
            return array('', array());
        }
        return $this->_post_config_restrictions;
    }

} // End of reportbuilder class

class ReportBuilderException extends Exception { }



/**
 * Run the reportbuilder cron
 */
function totara_reportbuilder_cron() {
    global $CFG;
    require_once($CFG->dirroot . '/report/reportbuilder/cron.php');
    reportbuilder_cron();
}

/**
 * Returns the proper SQL to create table based on a query
 * @param string $table
 *
 * @return string SQL to execute
 */
function sql_table_from_select($table, $select, array $params = array()) {
    global $DB;
    $hashtablename = substr(md5($table), 0, 15);
    switch ($DB->get_dbfamily()) {
        case 'mysql':
            $columnssql = "SHOW COLUMNS FROM `{$table}`";
            $indexsql = "CREATE INDEX rb_cache_{$hashtablename}_%1\$s ON {$table} (%2\$s)";
            $indexlongsql = "CREATE INDEX rb_cache_{$hashtablename}_%1\$s ON {$table} (%2\$s(%3\$d))";
            $fieldname = 'field';

            // Find out if want some special db engine.
            $enginesql = $DB->get_dbengine() ? " ENGINE = " . $DB->get_dbengine() : '';

            // Do we know collation?
            $collation = $DB->get_dbcollation();
            $collationsql = '';
            if ($collation) {
                if (strpos($collation, 'utf8_') === 0) {
                    $collationsql .= " DEFAULT CHARACTER SET utf8";
                }
                $collationsql .= " DEFAULT COLLATE = {$collation}";
            }

            $sql = "CREATE TABLE `{$table}` $enginesql $collationsql $select";
            $result = $DB->execute($sql, $params);
            break;
        case 'mssql':
            $viewname = 'tmp_'.$hashtablename;
            $viewsql = "CREATE VIEW $viewname AS $select";
            $DB->execute($viewsql, $params);

            $sql = "SELECT * INTO {$table} FROM $viewname";
            $result = $DB->execute($sql);

            $removeviewsql = "DROP VIEW $viewname";
            $DB->execute($removeviewsql);

            $columnssql = "SELECT sc.name, sc.system_type_id, sc.max_length, st.name as field_type FROM sys.columns sc
                    LEFT JOIN sys.types st ON (st.system_type_id = sc.system_type_id
                        AND st.name <> 'sysname' AND st.name <> 'geometry' AND st.name <> 'hierarchyid')
                    WHERE sc.object_id = OBJECT_ID('{$table}')";
            $indexsql = "CREATE INDEX rb_cache_{$hashtablename}_%1\$s ON {$table} (%2\$s)";
            $fieldname = 'name';
            break;
        case 'postgres':
        default:
            $sql = "CREATE TABLE \"{$table}\" AS $select";
            $columnssql = "SELECT column_name, data_type FROM information_schema.columns WHERE table_name ='{$table}'";
            $indexsql = "CREATE INDEX rb_cache_{$hashtablename}_%1\$s ON {$table} (%2\$s)";
            $fieldname = 'column_name';
            $result = $DB->execute($sql, $params);
            break;
    }
    if (!$result) return false;

    // Create indexes
    $fields = $DB->get_records_sql($columnssql);
    foreach ($fields as $field) {
        $hashfieldname = substr(md5($field->$fieldname), 0, 15);
        $sql = sprintf($indexsql, $hashfieldname, $field->$fieldname);

        // db engines specifics
        switch ($DB->get_dbfamily()) {
            case 'mysql':
                // Do not index fields with size 0
                if (strpos($field->type, '(0)') !== false) {
                    continue 2;
                }
                if (strpos($field->type, 'blob') !== false || strpos($field->type, 'text') !== false) {
                    // Index only first 255 symbols (mysql maximum = 767)
                    $sql = sprintf($indexlongsql, $hashfieldname, $field->$fieldname, 255);
                }
            break;
            case 'mssql':
                if ($field->field_type == 'image' || $field->field_type == 'binary') { // image
                    continue;
                }
                if ($field->field_type == 'text' || $field->field_type == 'ntext') {
                    $altersql = "ALTER TABLE {$table} ALTER COLUMN {$field->name} NVARCHAR(450)"; //Maximum index size = 900 bytes or 450 unicode chars
                    try {
                        // Attempt to convert field to indexable
                        $DB->execute($altersql);
                    } catch (dml_write_exception $e) {
                        // Recoverable exception
                        // Field has data longer than maximum index, proceed unindexed
                        continue;
                    }
                }
            break;
            case 'postgres':
                if ($field->data_type == 'unknown') {
                    $altersql = "ALTER TABLE {$table} ALTER COLUMN {$field->column_name} type varchar(255)";
                    $DB->execute($altersql);
                }
            break;
        }
        $DB->execute($sql);
    }
    return true;
}

/**
 * Returns the proper SQL to aggregate a field by joining with a specified delimiter
 *
 *
 */
function sql_group_concat($field, $delimiter=', ', $unique=false) {
    global $DB;

    // if not supported, just return single value - use min()
    $sql = " MIN($field) ";

    switch ($DB->get_dbfamily()) {
        case 'mysql':
            // use native function
            $distinct = $unique ? 'DISTINCT' : '';
            $sql = " GROUP_CONCAT($distinct $field SEPARATOR '$delimiter') ";
            break;
        case 'postgres':
            // use custom aggregate function - must have been defined
            // in db/upgrade.php
            $distinct = $unique ? 'TRUE' : 'FALSE';
            $sql = " GROUP_CONCAT($field, '$delimiter', $distinct) ";
            break;
    }

    return $sql;
}

/**
 * Schedule reporting cache
 *
 * @global object $DB
 * @param int $reportid report id
 * @param array|stdClass $form data from form element
 * @return type
 */
function reportbuilder_schedule_cache($reportid, $form = array()) {
    global $DB;
    if (is_object($form)) {
        $form = (array)$form;
    }
    $cache = $DB->get_record('report_builder_cache', array('reportid' => $reportid), '*', IGNORE_MISSING);
    if (!$cache) {
        $cache = new stdClass();
    }
    $cache->reportid = $reportid;
    $schedule = new scheduler($cache, array('nextevent' => 'nextreport'));
    $schedule->from_array($form);

    if (!isset($cache->id)) {
        $result = $DB->insert_record('report_builder_cache', $cache);
    } else {
        $result = $DB->update_record('report_builder_cache', $cache);
    }
    return $result;
}

/**
 * Shift next scheduled execution if report was generated after scheduled time
 *
 * @param int $reportid Report id
 * @return boolean is operation success
 */
function reportbuilder_fix_schedule($reportid) {
    global $DB;

    $cache = $DB->get_record('report_builder_cache', array('reportid' => $reportid), '*', IGNORE_MISSING);
    if (!$cache) {
        var_dump("cache not found");
        return false;
    }

    $schedule = new scheduler($cache, array('nextevent' => 'nextreport'));
    if ($schedule->get_scheduled_time() < $cache->lastreport) {
        $schedule->next();
    }

    if ($schedule->is_changed()) {
        $result = $DB->update_record('report_builder_cache', $cache);
    }
    return true;
}

/**
 * Returns reports that the current user can view
 *
 * @param boolean showhidden If true include hidden reports
 *
 * @return array Array of report records
 */
function reportbuilder_get_reports($showhidden=false) {
    global $reportbuilder_permittedreports;
    if (!isset($reportbuilder_permittedreports) || !is_array($reportbuilder_permittedreports)) {
        $reportbuilder_permittedreports = reportbuilder::get_permitted_reports(null,$showhidden);
    }
    return $reportbuilder_permittedreports;
}

/**
 * Purge cache to force report update during next load
 *
 * @param int|object $cache either data from rb cache table or report id
 * @param bool $unschedule If true drops scheduling as well
 */
function reportbuilder_purge_cache($cache, $unschedule = false) {
    global $DB;
    if (is_number($cache)) {
        $cache = $DB->get_record('report_builder_cache', array('reportid' => $cache));
    }
    if (!is_object($cache) || !isset($cache->reportid)) {
        error_log(get_string('error:cachenotfound', 'report_reportbuilder'));
        return false;
    }
    if ($cache->cachetable != '') {
        sql_drop_table_if_exists($cache->cachetable);
    }
    if ($unschedule) {
        $DB->delete_records_select('report_builder_cache', 'reportid = :reportid', array('reportid' => $cache->reportid));
        $report = new stdClass();
        $report->cache = 0;
        $report->id = $cache->reportid;
        $DB->update_record('report_builder', $report);
    } else {
        $cache->cachetable = '';
        $cache->config = '';
        $DB->update_record('report_builder_cache', $cache);
    }
}

/**
 * Purge all caches for report builder
 *
 * @param bool $unschedule Turn off caching after purge for all reports
 */
function reportbuilder_purge_all_cache($unschedule = false) {
    global $DB;
    try {
        $caches = $DB->get_records('report_builder_cache');
        foreach ($caches as $cache) {
            reportbuilder_purge_cache($cache, $unschedule);
        }
    } catch (dml_exception $e) {
        // This error is possible during installation process
        return;
    }
}


/**
 * Set flag to report that it is changed and cache settings are out of date or fail
 *
 * @param mixed stdClass|int $report Report id or report_builder_cache record object
 * @param int $flag Change flag - just changed or fail
 * @return bool result
 */
function reportbuilder_set_status($reportcache, $flag = RB_CACHE_FLAG_CHANGED) {
    global $DB;
    $reportid = 0;
    if (is_object($reportcache)) {
        $reportid = $reportcache->reportid;
        $reportcache->changed = 1;
    } else if (is_numeric($reportcache)) {
        $reportid = $reportcache;
    }
    if (!$reportid) return false;

    $sql = 'UPDATE {report_builder_cache} SET changed = ? WHERE reportid = ?';
    $result = $DB->execute($sql, array($flag, $reportid));
    return $result;
}

/**
 * Get reportbuilder cache status flags
 *
 * @param mixed stdClass|int $report Report id or report_builder_cache record object
 * @return int result
 */
function reportbuilder_get_status($reportcache) {
    global $DB;
    if (is_numeric($reportcache)) {
        $reportcache = reportbuilder_get_cached($reportcache);
    }
    if (is_object($reportcache)) {
        return $reportcache->changed;
    }
    return false;
}

/**
 * Report cache (re-)generation
 *
 * @int $reportid Report id
 * @return bool Is cache generated
 */
function reportbuilder_generate_cache($reportid) {
    global $DB;

    $success = false;

    // Prepare record for cache
    $rbcache = $DB->get_record('report_builder_cache', array('reportid' => $reportid));
    if (!$rbcache) {
        $cache = new stdClass();
        $cache->reportid = $reportid;
        $cache->frequency = 0;
        $cache->schedule = 0;
        $cache->changed = 0;
        $cache->genstart = 0;
        $DB->insert_record('report_builder_cache', $cache);
    }

    // set report generation timestamp
    $sql = 'UPDATE {report_builder_cache} SET genstart = ? WHERE reportid = ?';
    $params = array(time(), $reportid);
    $DB->execute($sql, $params);

    $transaction = $DB->start_delegated_transaction();
    // Moodle rollback rethrow exception, so have to use nested try..catch blocks
    try {
        try {
            $sql = 'SELECT rbc.id, rbc.cachetable, rb.embedded, rb.shortname  FROM {report_builder} rb
                    LEFT JOIN {report_builder_cache} rbc ON rb.id = rbc.reportid
                    WHERE rbc.reportid = ?';
            $reportcacherecord = $DB->get_record_sql($sql, array($reportid), MUST_EXIST);

            // Instantiate
            if ($reportcacherecord->embedded) {
                $shortname = $reportcacherecord->shortname;
                $report = reportbuilder_get_embedded_report($shortname, array(), true, 0);
            } else {
                $report = new reportbuilder($reportid, null, false, null, null, true);
            }

            // Get caching query
            list($query, $params) = $report->build_create_cache_query();

            $date = date("YmdHis");
            $oldtable = isset($reportcacherecord->cachetable) ? $reportcacherecord->cachetable : '';
            $newtable = "{report_builder_cache_{$reportid}_{$date}}";

            $result = sql_table_from_select($newtable, $query, $params);

            // Save new table only if success
            if ($result) {
                $cache = new stdClass();
                $cache->id = $reportcacherecord->id;
                $cache->lastreport = time();
                $cache->cachetable = $newtable;
                $cache->config = $report->export_config();
                $cache->changed = 0;
                $cache->genstart = 0;

                // Cache settings
                $settingsql = "UPDATE {report_builder_settings} SET cachedvalue = value WHERE reportid = ?";
                $DB->execute($settingsql, array($reportid));

                $DB->update_record('report_builder_cache', $cache);

                if ($oldtable != '') {
                    sql_drop_table_if_exists($oldtable);
                }
                $success = true;
            }
        } catch (dml_exception $e) {
            $transaction->rollback($e);
        }
    } catch (dml_exception $e) {
        // drop report generation timestamp
        $sql = 'UPDATE {report_builder_cache} SET genstart = ?, changed = ? WHERE reportid = ?';
        $params = array(0, RB_CACHE_FLAG_FAIL, $reportid);
        $DB->execute($sql, $params);

        throw $e;
    }

    if ($success) {
        $transaction->allow_commit();
        return true;
    }
    return false;
}

/**
 *  Send Scheduled report to a user
 *
 *  @param object $sched Object containing data from schedule table
 *
 *  @return boolean True if email was successfully sent
 */
function reportbuilder_send_scheduled_report($sched) {
    global $CFG, $DB, $REPORT_BUILDER_EXPORT_OPTIONS;
    $export_codes = array_flip($REPORT_BUILDER_EXPORT_OPTIONS);

    if (!$user = $DB->get_record('user', array('id' => $sched->userid))) {
        error_log(get_string('error:invaliduserid', 'report_reportbuilder'));
        return false;
    }

    if (!$report = $DB->get_record('report_builder', array('id' => $sched->reportid))) {
        error_log(get_string('error:invalidreportid', 'report_reportbuilder'));
        return false;
    }

    // don't send the report if the user doesn't have permission
    // to view it
    if (!reportbuilder::is_capable($sched->reportid, $sched->userid)) {
        error_log(get_string('error:nopermissionsforscheduledreport', 'report_reportbuilder', $sched));
        return false;
    }

    $attachment = reportbuilder_create_attachment($sched, $user->id);

    switch($sched->format) {
        case REPORT_BUILDER_EXPORT_EXCEL:
            $attachmentfilename = 'report.xlsx';
            break;
        case REPORT_BUILDER_EXPORT_CSV:
            $attachmentfilename = 'report.csv';
            break;
        case REPORT_BUILDER_EXPORT_ODS:
            $attachmentfilename = 'report.ods';
            break;
        case REPORT_BUILDER_EXPORT_PDF_LANDSCAPE:
        case REPORT_BUILDER_EXPORT_PDF_PORTRAIT:
            $attachmentfilename = 'report.pdf';
            break;
    }

    $reporturl = reportbuilder_get_report_url($report);
    if ($sched->savedsearchid != 0) {
        $reporturl .= '&sid=' . $sched->savedsearchid;
    }
    $strmgr = get_string_manager();
    $messagedetails = new stdClass();
    $messagedetails->reportname = $report->fullname;
    $messagedetails->exporttype = $strmgr->get_string($export_codes[$sched->format] . 'format', 'report_reportbuilder', null, $user->lang);
    $messagedetails->reporturl = $reporturl;
    $messagedetails->scheduledreportsindex = $CFG->wwwroot . '/my/reports.php#scheduled';

    $schedule = new scheduler($sched, array('nextevent' => 'nextreport'));
    $messagedetails->schedule = $schedule->get_formatted($user);

    $subject = $report->fullname . ' ' . $strmgr->get_string('report', 'report_reportbuilder', null, $user->lang);

    if ($sched->savedsearchid != 0) {
        if (!$savename = $DB->get_field('report_builder_saved', 'name', array('id' => $sched->savedsearchid))) {
            mtrace(get_string('error:invalidsavedsearchid', 'report_reportbuilder'));
        } else {
            $messagedetails->savedtext = $strmgr->get_string('savedsearchmessage', 'report_reportbuilder', $savename, $user->lang);
        }
    } else {
        $messagedetails->savedtext = '';
    }

    $message = $strmgr->get_string('scheduledreportmessage', 'report_reportbuilder', $messagedetails, $user->lang);

    $fromaddress = $CFG->noreplyaddress;
    $emailed = false;

    if ($sched->exporttofilesystem != REPORT_BUILDER_EXPORT_SAVE) {
        $emailed = email_to_user($user, $fromaddress, $subject, $message, '', $attachment, $attachmentfilename);
    }

    if (!unlink($CFG->dataroot . DIRECTORY_SEPARATOR . $attachment)) {
        mtrace(get_string('error:failedtoremovetempfile', 'report_reportbuilder'));
    }

    return $emailed;
}

/**
 * Creates an export of a report in specified format (xls, csv or ods)
 * for adding to email as attachment
 *
 * @param record $sched schedule record
 * @param integer userid ID of the user the report is for
 *
 * @return string Filename of the created attachment
 */
function reportbuilder_create_attachment($sched, $userid) {
    global $CFG;

    $reportid = $sched->reportid;
    $format = $sched->format;
    $exporttofilesystem = $sched->exporttofilesystem;
    $sid = $sched->savedsearchid;
    $scheduleid = $sched->id;

    $report = new reportbuilder($reportid, null, false, $sid, $userid);
    $columns = $report->columns;
    $count = $report->get_filtered_count();
    list($sql, $params) = $report->build_query(false, true);

    // array of filters that have been applied
    // for including in report where possible
    $restrictions = $report->get_restriction_descriptions();

    $headings = array();
    foreach ($columns as $column) {
        // check that column should be included
        if ($column->display_column(true)) {
            $headings[] = $column;
        }
    }
    $tempfilename = md5(time());
    $tempfilepathname = $CFG->dataroot . DIRECTORY_SEPARATOR . $tempfilename;

    switch ($format) {
        case REPORT_BUILDER_EXPORT_ODS:
            $filename = $report->download_ods($headings, $sql, $params, $count, $restrictions, $tempfilepathname);
            if ($exporttofilesystem != REPORT_BUILDER_EXPORT_EMAIL) {
                $reportfilepathname = reportbuilder_get_export_filename($report, $userid, $scheduleid) . '.ods';
                $filename = $report->download_ods($headings, $sql, $params, $count, $restrictions, $reportfilepathname);
            }
            break;
        case REPORT_BUILDER_EXPORT_EXCEL:
            $filename = $report->download_xls($headings, $sql, $params, $count, $restrictions, $tempfilepathname);
            if ($exporttofilesystem != REPORT_BUILDER_EXPORT_EMAIL) {
                $reportfilepathname = reportbuilder_get_export_filename($report, $userid, $scheduleid) . '.xlsx';
                $filename = $report->download_xls($headings, $sql, $params, $count, $restrictions, $reportfilepathname);
            }
            break;
        case REPORT_BUILDER_EXPORT_CSV:
            $filename = $report->download_csv($headings, $sql, $params, $count, $tempfilepathname);
            if ($exporttofilesystem != REPORT_BUILDER_EXPORT_EMAIL) {
                $reportfilepathname = reportbuilder_get_export_filename($report, $userid, $scheduleid) . '.csv';
                $filename = $report->download_csv($headings, $sql, $params, $count, $reportfilepathname);
            }
            break;
        case REPORT_BUILDER_EXPORT_PDF_PORTRAIT:
            $filename = $report->download_pdf($headings, $sql, $params, $count, $restrictions, true, $tempfilepathname);
            if ($exporttofilesystem != REPORT_BUILDER_EXPORT_EMAIL) {
                $reportfilepathname = reportbuilder_get_export_filename($report, $userid, $scheduleid) . '.pdf';
                $filename = $report->download_pdf($headings, $sql, $params, $count, $restrictions, true, $reportfilepathname);
            }
            break;
        case REPORT_BUILDER_EXPORT_PDF_LANDSCAPE:
            $filename = $report->download_pdf($headings, $sql, $params, $count, $restrictions, false, $tempfilepathname);
            if ($exporttofilesystem != REPORT_BUILDER_EXPORT_EMAIL) {
                $reportfilepathname = reportbuilder_get_export_filename($report, $userid, $scheduleid) . '.pdf';
                $filename = $report->download_pdf($headings, $sql, $params, $count, $restrictions, false, $reportfilepathname);
            }
            break;
    }

    return $tempfilename;
}

/**
 * Checks if username directory under given path exists
 * If it does not it creates it and returns fullpath with filename
 * userdir + report fullname + time created + schedule id
 *
 * @param record $report
 * @param int $userid
 * @param int $scheduleid
 *
 * @return string reportfullpathname
 */
function reportbuilder_get_export_filename($report, $userid, $scheduleid) {
    global $DB;
    $reportfilename = format_string($report->fullname) . '_' .
            userdate(time(), get_string('datepickerlongyearphpuserdate', 'report_reportbuilder')) . '_' . $scheduleid;
    $reportfilename = clean_param($reportfilename, PARAM_FILE);
    $username = $DB->get_field('user', 'username', array('id' => $userid));

    $dir = get_config('reportbuilder', 'exporttofilesystempath') . DIRECTORY_SEPARATOR . $username;
    if (!is_directory_a_preset($dir) && !file_exists($dir)) {
        mkdir($dir);
    }
    $reportfilepathname = $dir . DIRECTORY_SEPARATOR . $reportfilename;

    return $reportfilepathname;
}

/**
 * Given a report database record, return the URL to the report
 *
 * For use when a reportbuilder object is not available. If a reportbuilder
 * object is being used, call {@link reportbuilder->report_url()} instead
 *
 * @param object $report Report builder database object. Must contain id, shortname and embedded parameters
 *
 * @return string URL of the report provided or false
 */
function reportbuilder_get_report_url($report) {
    global $CFG;
    if ($report->embedded == 0) {
        return $CFG->wwwroot . '/report/reportbuilder/report.php?id=' . $report->id;
    } else {
        // use report shortname to find appropriate embedded report object
        if ($embed = reportbuilder_get_embedded_report_object($report->shortname)) {
            return $CFG->wwwroot . $embed->url;
        } else {
            return $CFG->wwwroot;
        }
    }

}

/**
 * Generate object used to describe an embedded report
 *
 * This method returns a new instance of an embedded report object
 * Given an embedded report name, it finds the class, includes it then
 * calls the class passing in any data provided. The object created
 * by that call is returned, or false if something went wrong.
 *
 * @param string $embedname Shortname of embedded report
 *                          e.g. X from rb_X_embedded.php
 * @param array $data Associative array of data needed by source (optional)
 *
 * @return object Embedded report object
 */
function reportbuilder_get_embedded_report_object($embedname, $data=array()) {
    global $CFG;
    $sourcepath = $CFG->dirroot . '/report/reportbuilder/embedded/';

    $classfile = $sourcepath . 'rb_' . $embedname . '_embedded.php';
    if (is_readable($classfile)) {
        include_once($classfile);
        $classname = 'rb_' . $embedname . '_embedded';
        if (class_exists($classname)) {
            return new $classname($data);
        }
    }
    // file or class not found
    return false;
}


/**
 * Generate actual embedded report
 *
 * This function is an alias to "new reportbuilder()", for use within embedded report pages. The embedded object
 * will be created within the reportbuilder constructor.
 *
 * @param string $embedname Shortname of embedded report
 *                          e.g. X from rb_X_embedded.php
 * @param array $data Associative array of data needed by source (optional)
 * @param bool $nocache Disable cache
 * @param int $sid saved search id
 *
 * @return reportbuilder Embedded report
 */
function reportbuilder_get_embedded_report($embedname, $data = array(), $nocache = false, $sid = 'nosidsupplied') {
    if ($sid === 'nosidsupplied') {
        debugging('Call to reportbuilder_get_embedded_report without supplying $sid is probably an error - if you
            want to save searches on your embedded report then you must pass in $sid here, otherwise pass 0 to remove
            this warning');
        $sid = 0;
    }
    return new reportbuilder(null, $embedname, false, $sid, null, $nocache, $data);
}


/**
 * Returns an array of all embedded reports found in the filesystem, sorted by name
 *
 * Looks in the totara/reportbuilder/embedded/ directory and creates a new
 * object for each embedded report definition found. These are returned
 * as an array, sorted by the report fullname
 *
 * @return array Array of embedded report objects
 */
function reportbuilder_get_all_embedded_reports() {
    global $CFG;
    $sourcepath = $CFG->dirroot . '/report/reportbuilder/embedded/';

    $embedded = array();
    if ($dh = opendir($sourcepath)) {
        while(($file = readdir($dh)) !== false) {
            if (is_dir($file) ||
                !preg_match('|^rb_(.*)_embedded\.php$|', $file, $matches)) {
                continue;
            }
            $name = $matches[1];
            $embed = false;
            if ($embed = reportbuilder_get_embedded_report_object($name)) {
                $embedded[] = $embed;
            }
        }
    }
    // sort by fullname before returning
    usort($embedded, 'reportbuilder_sortbyfullname');
    return $embedded;
}

/**
 * Return object with cached record for report or false if not found
 *
 * @param int $reportid
 */
function reportbuilder_get_cached($reportid) {
    global $DB;
    $sql = "SELECT rbc.*, rb.cache, rb.fullname, rb.shortname, rb.embedded
            FROM {report_builder} rb
            LEFT JOIN {report_builder_cache} rbc ON rbc.reportid = rb.id
            WHERE rb.cache = 1
              AND rb.id = ?";
    return $DB->get_record_sql($sql, array($reportid));
}

/**
 * Get all reports with enabled caching
 *
 * @return array of stdClass
 
function reportbuilder_get_all_cached() {
    global $DB, $CFG;
    if (!$CFG->enablereportcaching) {
        return array();
    }
    $sql = "SELECT rbc.*, rb.cache, rb.fullname, rb.shortname, rb.embedded
            FROM {report_builder} rb
            LEFT JOIN {report_builder_cache} rbc
                ON rb.id = rbc.reportid
            WHERE rb.cache = 1";
    $caches = $DB->get_records_sql($sql);
    $result = array();
    foreach ($caches as $c) {
        $result[$c->reportid] = $c;
    }
    return $result;
}*/ 

function reportbuilder_get_all_cached() {
    return array();
}

/**
 * Function for sorting by report fullname, used in usort as callback
 *
 * @param object $a The first array element
 * @param object $a The second array element
 *
 * @return integer 1, 0, or -1 depending on sort order
 */
function reportbuilder_sortbyfullname($a, $b) {
    return strcmp($a->fullname, $b->fullname);
}


/**
 * Returns the ID of an embedded report from its shortname, creating if necessary
 *
 * To save on db calls, you need to pass an array of the existing embedded
 * reports to this method, in the format key=id, value=shortname.
 *
 * If the shortname doesn't exist in the array provided this method will
 * create a new embedded report and return the new ID generated or false
 * on failure
 *
 * @param string $shortname The shortname you need the ID of
 * @param array $embedded_ids Array of embedded report IDs and shortnames
 *
 * @return integer ID of the requested embedded report
 */
function reportbuilder_get_embedded_id_from_shortname($shortname, $embedded_ids) {
    // return existing ID if a database record exists already
    if (is_array($embedded_ids)) {
        foreach ($embedded_ids as $id => $embed_shortname) {
            if ($shortname == $embed_shortname) {
                return $id;
            }
        }
    }
    // otherwise, create a new embedded report and return the new ID
    // returns false if creation fails
    $embed = reportbuilder_get_embedded_report_object($shortname);
    $error = null;
    return reportbuilder_create_embedded_record($shortname, $embed, $error);
}


/**
 * Creates a database entry for an embedded report when it is first viewed
 * so the settings can be edited
 *
 * @param string $shortname The unique name for this embedded report
 * @param object $embed An object containing the embedded reports settings
 * @param string &$error Error string to return on failure
 *
 * @return boolean ID of new database record, or false on failure
 */
function reportbuilder_create_embedded_record($shortname, $embed, &$error) {
    global $DB;
    $error = null;

    // check input
    if (!isset($shortname)) {
        $error = 'Bad shortname';
        return false;
    }
    if (!isset($embed->source)) {
        $error = 'Bad source';
        return false;
    }
    if (!isset($embed->filters) || !is_array($embed->filters)) {
        $embed->filters = array();
    }
    if (!isset($embed->columns) || !is_array($embed->columns)) {
        $error = 'Bad columns';
        return false;
    }
    // hide embedded reports from report manager by default
    $embed->hidden = isset($embed->hidden) ? $embed->hidden : 1;
    $embed->accessmode = isset($embed->accessmode) ? $embed->accessmode : 0;
    $embed->contentmode = isset($embed->contentmode) ? $embed->contentmode : 0;

    $embed->accesssettings = isset($embed->accesssettings) ? $embed->accesssettings : array();
    $embed->contentsettings = isset($embed->contentsettings) ? $embed->contentsettings : array();

    $embed->defaultsortcolumn = isset($embed->defaultsortcolumn) ? $embed->defaultsortcolumn : '';
    $embed->defaultsortorder = isset($embed->defaultsortorder) ? $embed->defaultsortorder : SORT_ASC;

    $todb = new stdClass();
    $todb->shortname = $shortname;
    $todb->fullname = $embed->fullname;
    $todb->source = $embed->source;
    $todb->hidden = 1; // hide embedded reports by default
    $todb->accessmode = $embed->accessmode;
    $todb->contentmode = $embed->contentmode;
    $todb->embedded = 1;
    $todb->defaultsortcolumn = $embed->defaultsortcolumn;
    $todb->defaultsortorder = $embed->defaultsortorder;

    try {
        $transaction = $DB->start_delegated_transaction();

        $newid = $DB->insert_record('report_builder', $todb);
        // Add columns.
        $so = 1;
        foreach ($embed->columns as $column) {
            $todb = new stdClass();
            $todb->reportid = $newid;
            $todb->type = $column['type'];
            $todb->value = $column['value'];
            $todb->heading = $column['heading'];
            $todb->sortorder = $so;
            $todb->customheading = 0; // Initially no columns are customised.
            $todb->hidden = isset($column['hidden']) ? $column['hidden'] : 0;
            $DB->insert_record('report_builder_columns', $todb);
            $so++;
        }
        // Add filters.
        $so = 1;
        foreach ($embed->filters as $filter) {
            $todb = new stdClass();
            $todb->reportid = $newid;
            $todb->type = $filter['type'];
            $todb->value = $filter['value'];
            $todb->advanced = $filter['advanced'];
            $todb->filtername = '';
            $todb->customname = 0; // Initially no filters are customised.
            $todb->sortorder = $so;
            $DB->insert_record('report_builder_filters', $todb);
            $so++;
        }
        // add content restrictions
        foreach ($embed->contentsettings as $option => $settings) {
            $classname = $option . '_content';
            if (class_exists('rb_' . $classname)) {
                foreach ($settings as $name => $value) {
                    if (!reportbuilder::update_setting($newid, $classname, $name,
                        $value)) {
                            throw new moodle_exception('Error inserting content restrictions');
                        }
                }
            }
        }
        // add access restrictions
        foreach ($embed->accesssettings as $option => $settings) {
            $classname = $option . '_access';
            if (class_exists($classname)) {
                foreach ($settings as $name => $value) {
                    if (!reportbuilder::update_setting($newid, $classname, $name,
                        $value)) {
                            throw new moodle_exception('Error inserting access restrictions');
                        }
                }
            }
        }
        $transaction->allow_commit();
    } catch (Exception $e) {
        $transaction->rollback($e);
        $error = $e->getMessage();
        return false;
    }

    return $newid;
}


/**
 * Attempt to ensure an SQL named param is unique by appending a random number value
 * and keeping records of other param names
 *
 * @param string $name the param name to make unique
 * @return string the unique string
 */
function rb_unique_param($name) {
    static $UNIQUE_PARAMS = array();

    $param = $name . rand(1, 30777);

    while (in_array($param, $UNIQUE_PARAMS)) {
        $param = $name . rand(1, 30777);
    }

    $UNIQUE_PARAMS[] = $param;

    return $param;
}

/**
 * Helper function for renaming the data in the columns/filters table
 *
 * Useful when a field is renamed and the report data needs to be updated
 *
 * @param string $table Table to update, either 'filters' or 'columns'
 * @param string $source Name of the source or '*' to update all sources
 * @param string $oldtype The type of the item to change
 * @param string $oldvalue The value of the item to change
 * @param string $newtype The new type of the item
 * @param string $newvalue The new value of the item
 *
 * @return boolean Result from the update query or true if no data to update
 */
function reportbuilder_rename_data($table, $source, $oldtype, $oldvalue, $newtype, $newvalue) {
    global $DB;

    if ($source == '*') {
        $sourcesql = '';
        $params = array();
    } else {
        $sourcesql = ' AND rb.source = :source';
        $params = array('source' => $source);
    }

    $sql = "SELECT rbt.id FROM {report_builder_{$table}} rbt
        JOIN {report_builder} rb
        ON rbt.reportid = rb.id
        WHERE rbt.type = :oldtype AND rbt.value = :oldvalue
        $sourcesql";
    $params['oldtype'] = $oldtype;
    $params['oldvalue'] = $oldvalue;

    $items = $DB->get_fieldset_sql($sql, $params);

    if (!empty($items)) {
        list($insql, $params) = $DB->get_in_or_equal($items, SQL_PARAMS_NAMED);
        $sql = "UPDATE {report_builder_{$table}}
            SET type = :newtype, value = :newvalue
            WHERE id $insql";
        $params['newtype'] = $newtype;
        $params['newvalue'] = $newvalue;
        $DB->execute($sql, $params);
    }
    return true;
}


/**
* Serves reportbuilder file type files. Required for M2 File API
*
* @param object $course
* @param object $cm
* @param object $context
* @param string $filearea
* @param array $args
* @param bool $forcedownload
* @param array $options
* @return bool false if file not found, does not return if found - just send the file
*/
function totara_reportbuilder_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, $options=array()) {
    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/{$context->id}/report_reportbuilder/$filearea/$args[0]/$args[1]";
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        return false;
    }
    // finally send the file
    send_stored_file($file, 86400, 0, true, $options); // download MUST be forced - security!
}
