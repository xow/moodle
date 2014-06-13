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
 * Unit tests for WS in assignment file submissions.
 *
 * @package assignsubmission_file
 * @category test
 * @copyright 2016 Jonathon Fowler <fowlerj@usq.edu.au>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * Unit tests for WS in assignment file submissions.
 *
 * @package assignsubmission_file
 * @copyright 2016 Jonathon Fowler <fowlerj@usq.edu.au>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assignsubmission_file_external_testcase extends externallib_advanced_testcase {
    /**
     * Test get_types_and_groups
     */
    public function test_get_types_and_groups() {
        $this->resetAfterTest();

        $result = assignsubmission_file\external::get_types_and_groups(array('application/pdf', 'web_file'));
        $result = external_api::clean_returnvalue(assignsubmission_file\external::get_types_and_groups_returns(), $result);

        $this->assertArrayHasKey('typegroups', $result);
        $this->assertArrayHasKey('alltypes', $result);

        $this->assertNotEmpty($result['typegroups']);
        $this->assertNotEmpty($result['alltypes']);

        $this->assertArrayHasKey('id', $result['typegroups'][0]);
        $this->assertArrayHasKey('id', $result['alltypes'][0]);
    }
}
