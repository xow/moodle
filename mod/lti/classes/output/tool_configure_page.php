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

    private function deserialise_tool_type(stdClass $type) {
        return array(
            'name' => $type->name,
            'description' => isset($type->description) ? $type->description : "Default tool description placeholder until we can code this in.",
            'iconurl' => isset($type->icon) ? $type->icon : ''
        );
    }

    private function get_tool_types() {
        $types = lti_get_lti_types();

        return array_map(array($this, "deserialise_tool_type"), array_values($types));
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

        #print_r($data);

        return $data;
    }
}
