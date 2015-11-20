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
 * Class containing data for tool_configure page
 *
 * @package    mod_lti
 * @copyright  2015 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_lti\output;

require_once($CFG->dirroot.'/mod/lti/locallib.php');

use moodle_url;
use renderable;
use templatable;
use renderer_base;
use stdClass;
use core_plugin_manager;

/**
 * Class containing data for tool_configure page
 *
 * @copyright  2015 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_configure_page implements renderable, templatable {

    private function get_tool_icon_url(stdClass $type) {
        global $OUTPUT;

        $iconurl = $type->secureicon;

        if (empty($iconurl)) {
            $iconurl = $type->icon;
        }

        if (empty($iconurl)) {
            $iconurl = $OUTPUT->pix_url('icon', 'lti');
        }

        return $iconurl;
    }

    private function get_tool_edit_url(stdClass $type) {
        $url = new moodle_url('/mod/lti/typessettings.php', array('action' => 'update', 'id' => $type->id, 'sesskey' => sesskey()));
        return $url->out();
    }

    private function get_tool_reject_url(stdClass $type) {
        $url = new moodle_url('/mod/lti/typessettings.php', array('action' => 'reject', 'id' => $type->id, 'sesskey' => sesskey()));
        return $url->out();
    }

    private function get_tool_urls(stdClass $type) {
        return array(
            'icon' => $this->get_tool_icon_url($type),
            'edit' => $this->get_tool_edit_url($type),
            'reject' => $this->get_tool_reject_url($type)
        );
    }

    private function get_tool_state_info(stdClass $type) {
        $state = '';
        $isconfigured = false;
        $ispending = false;
        $isany = false;
        $isrejected = false;
        $isunknown = false;
        switch ($type->state) {
            case LTI_TOOL_STATE_ANY:
                $state = 'any';
                $isany = true;
                break;
            case LTI_TOOL_STATE_CONFIGURED:
                $state = 'configured';
                $isconfigured = true;
                break;
            case LTI_TOOL_STATE_PENDING:
                $state = 'pending';
                $ispending = true;
                break;
            case LTI_TOOL_STATE_REJECTED:
                $state = 'rejected';
                $isrejected = true;
                break;
            default:
                $state = 'unknown state';
                $isunknown = true;
                break;
        }

        return array(
            'text' => $state,
            'pending' => $ispending,
            'configured' => $isconfigured,
            'rejected' => $isrejected,
            'any' => $isany,
            'unknown' => $isunknown
        );
    }

    private function serialise_tool_type(stdClass $type) {
        return array(
            'name' => $type->name,
            'description' => isset($type->description) ? $type->description : "Default tool description placeholder until we can code this in.",
            'urls' => $this->get_tool_urls($type),
            'state' => $this->get_tool_state_info($type)
        );
    }

    private function get_tool_types() {
        $types = lti_get_lti_types();

        return array_map(array($this, "serialise_tool_type"), array_values($types));
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();

        $url = new moodle_url('/mod/lti/typessettings.php', array('sesskey' => sesskey()));
        $data->configuremanualurl = $url->out();

        $data->tools = $this->get_tool_types();

        return $data;
    }
}
