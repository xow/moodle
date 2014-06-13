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
 * This is the external API for the file submissions plugin.
 *
 * @package    assignsubmission_file
 * @copyright  2016 Jonathon Fowler <fowlerj@usq.edu.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace assignsubmission_file;

require_once("$CFG->libdir/externallib.php");

use external_api;
use external_function_parameters;
use external_value;
use external_format_value;
use external_single_structure;
use external_multiple_structure;
use invalid_parameter_exception;

/**
 * This is the external API for the file submissions plugin.
 *
 * @copyright  2016 Jonathon Fowler <fowlerj@usq.edu.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {

    /**
     * Returns description of get_types_and_groups() parameters.
     *
     * @return external_function_parameters
     */
    public static function get_types_and_groups_parameters() {
        $filetypes = new external_multiple_structure(
            new external_value(PARAM_RAW),
            'The file types and groups to limit to',
            VALUE_DEFAULT,
            array()
        );
        $params = array('filetypes' => $filetypes);
        return new external_function_parameters($params);
    }

    /**
     * Loads type groups and file types.
     * @param string $filetypes Filters the result set to the types/groups given.
     * @return array typegroups and alltypes, each an array
     */
    public static function get_types_and_groups($filetypes) {
        $params = self::validate_parameters(
            self::get_types_and_groups_parameters(),
            array(
                'filetypes' => $filetypes,
            )
        );

        $types = new filetypes_helper($filetypes);
        $return = array(
            'typegroups' => array(),
            'alltypes' => array(),
        );

        // Put the array keys into the objects since they are stripped by
        // external service response processing.
        foreach ($types->get_typegroups() as $key => $obj) {
            $obj->id = $key;
            $return['typegroups'][] = $obj;
        }
        foreach ($types->get_alltypes() as $key => $obj) {
            $obj->id = $key;
            $return['alltypes'][] = $obj;
        }

        return $return;
    }

    /**
     * Returns description of get_types_and_groups() result value.
     *
     * @return external_description
     */
    public static function get_types_and_groups_returns() {
        $type = new external_single_structure(
            array(
                'id' => new external_value(PARAM_RAW, 'The mimetype'),
                'name' => new external_value(PARAM_RAW, 'The mimetype name'),
                'extlist' => new external_value(PARAM_RAW, 'List of all file extensions for the mimetype'),
            )
        );

        $typegroup = new external_single_structure(
            array(
                'id' => new external_value(PARAM_RAW, 'The type group identifier'),
                'name' => new external_value(PARAM_RAW, 'The type group name'),
                'types' => new external_multiple_structure(new external_value(PARAM_RAW), 'The mimetypes in the group'),
                'extlist' => new external_value(PARAM_RAW, 'List of all file extensions in the group', VALUE_OPTIONAL),
                'isoption' => new external_value(PARAM_BOOL, 'Whether the group itself is a selectable option',
                    VALUE_DEFAULT, false),
            )
        );

        return new external_single_structure(
            array(
                'typegroups' => new external_multiple_structure($typegroup, 'Type groups'),
                'alltypes' => new external_multiple_structure($type, 'All mime types'),
            )
        );
    }
}
