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
 * Unit tests for the filetypes helper.
 *
 * @package assignsubmission_file
 * @category test
 * @copyright 2016 Jonathon Fowler <fowlerj@usq.edu.au>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/assign/tests/base_test.php');


/**
 * Unit tests for the filetypes helper.
 *
 * @package assignsubmission_file
 * @copyright 2016 Jonathon Fowler <fowlerj@usq.edu.au>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assignsubmission_file_filetypes_helper_testcase extends advanced_testcase {
    /**
     * Test fetching all system-level file types and groups.
     */
    public function test_all_types() {
        $this->resetAfterTest();

        $types = new assignsubmission_file\filetypes_helper();
        $this->assertNotEmpty($types->get_typegroups());
        $this->assertNotEmpty($types->get_alltypes());
    }

    /**
     * Test fetching a single file type.
     */
    public function test_specific_type() {
        $this->resetAfterTest();

        $types = new assignsubmission_file\filetypes_helper(array('application/pdf'));
        $this->assertEquals(
            array(
                'document' => (object)array(
                    'name' => 'Document files',
                    'types' => array('application/pdf'),
                    'isoption' => false,
                    // Note: extlist is not returned in this situation.
                )
            ),
            $types->get_typegroups()
        );
        $this->assertEquals(
            array(
                'application/pdf' => (object)array(
                    'name' => 'PDF document',
                    'extlist' => '.pdf',
                )
            ),
            $types->get_alltypes()
        );
    }

    /**
     * Test fetching a single type group.
     */
    public function test_typegroup() {
        $this->resetAfterTest();

        $types = new assignsubmission_file\filetypes_helper(array('document'));
        $typegroups = $types->get_typegroups();
        $alltypes = $types->get_alltypes();

        $this->assertNotEmpty($typegroups);
        $this->assertNotEmpty($alltypes);

        $this->assertArrayHasKey('document', $typegroups);
        $this->assertArrayHasKey('application/pdf', $alltypes);

        // When fetching a full type group, we expect to see the extensions list,
        // and that it be a choosable option.
        $this->assertObjectHasAttribute('extlist', $typegroups['document']);
        $this->assertTrue($typegroups['document']->isoption);
    }
}
