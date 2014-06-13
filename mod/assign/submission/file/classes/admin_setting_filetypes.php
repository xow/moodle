<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Custom admin setting to choose system-wide permitted file types.
 *
 * @package   assignsubmission_file
 * @copyright 2016 Jonathon Fowler <fowlerj@usq.edu.au>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace assignsubmission_file;

use admin_setting,
    stdClass,
    html_writer;

defined('MOODLE_INTERNAL') || die;

/**
 * Administration setting to choose system-wide permitted file types.
 *
 * @package   assignsubmission_file
 * @copyright 2016 Jonathon Fowler <fowlerj@usq.edu.au>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class admin_setting_filetypes extends admin_setting {

    /**
     * Return the current setting(s)
     *
     * @return string Current setting
     */
    public function get_setting() {
        return $this->config_read($this->name);
    }

    /**
     * Store the setting
     *
     * @param string $data the setting
     * @return string empty string if ok, string error message otherwise
     */
    public function write_setting($data) {
        $validated = $this->validate($data);
        if ($validated !== true) {
            return $validated;
        }
        if ($this->config_write($this->name, $data)) {
            return '';
        } else {
            return get_string('errorsetting', 'admin');
        }
    }

    /**
     * Validate data before storage
     * @param string $data
     * @return mixed true if ok string if error found
     */
    public function validate($data) {
        if ($data === '') {
            return true;
        }

        $types = new filetypes_helper();
        $alltypes = $types->get_alltypes();
        $typegroups = $types->get_typegroups();

        foreach (explode(';', $data) as $type) {
            if (!isset($alltypes[$type]) && !isset($typegroups[$type])) {
                return get_string('validateerror', 'admin');
            }
            if (isset($typegroups[$type]) && empty($typegroups[$type]->isoption)) {
                return get_string('validateerror', 'admin');
            }
        }
        return true;
    }

    /**
     * Return XHTML field(s) for options
     *
     * @param string $data the setting value
     * @param string $query search query to be highlighted
     * @return string XHTML string for the fields and wrapping div(s)
     */
    public function output_html($data, $query='') {
        global $OUTPUT, $PAGE;

        $out = html_writer::start_div();
        $out .= html_writer::tag('div', '', array(
            'id' => $this->get_id() . '_label',
            'class' => 'formctl_types'
        ));
        $out .= html_writer::empty_tag('input', array(
            'type' => 'button',
            'class' => 'formctl_types_choose',
            'id' => $this->get_id() . '_choose',
            'value' => get_string('formctl_types_choose', 'assignsubmission_file')
        ));
        $out .= html_writer::empty_tag('input', array(
            'type' => 'text',
            'class' => 'formctl_types_value',
            'id' => $this->get_id(),
            'name' => $this->get_full_name(),
            'value' => s($data),
        ));
        $out .= html_writer::end_div();

        $strings = array('filetypewithexts', 'formctl_types_save', 'formctl_types_noselection');
        $PAGE->requires->js_call_amd('assignsubmission_file/formctl_types', 'initialise',
            array(
                $this->get_id(),
                $this->visiblename,
                array()
            )
        );

        $default = $this->get_defaultsetting();
        return format_admin_setting($this, $this->visiblename, $out, $this->description, false, '', $default, $query);
    }
}
