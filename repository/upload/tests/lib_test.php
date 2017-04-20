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
 * Unit tests for the Upload repository.
 *
 * @package   repository_upload
 * @copyright 2016 John Okely
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->dirroot . '/repository/upload/lib.php');


/**
 * Upload repository test case.
 *
 * @copyright 2016 John Okely
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_upload_lib_testcase extends advanced_testcase {

    /**
     * Check that the upload escaper performs as expected
     */
    public function upload_successes_provider() {
        return [
            'Test that any type is allowed' => [
                'filename' => 'lib/tests/fixtures/gd-logo.png',
                'type' => '*'
            ],
            'Test that file with allowed type is allowed' => [
                'filename' => 'lib/tests/fixtures/gd-logo.png',
                'type' => ['.png']
            ],
            'Test that file with custom type is allowed' => [
                'filename' => 'lib/tests/fixtures/file.custom',
                'type' => ['.custom'],
            ],
            'Test that file with custom type is allowed, allcaps allowed type' => [
                'filename' => 'lib/tests/fixtures/file.custom',
                'type' => ['.CUSTOM'],
            ],
            'Test that file with custom type is allowed, allcaps filetype' => [
                'filename' => 'lib/tests/fixtures/file2.CUSTOM',
                'type' => ['.custom'],
            ],
        ];
    }

    /**
     * Check that the upload escaper performs as expected
     * @dataProvider upload_successes_provider
     */
    public function test_upload_sucesses($filename, $types) {
        $this->resetAfterTest();
        $this->setAdminUser();

        $_FILES['repo_upload_file'] = [
            'tmp_name' => $filename,
            'name' => $filename
        ];

        $repoid = $this->getDataGenerator()->create_repository('upload')->id;
        $repo = new repository_upload($repoid, SYSCONTEXTID, array());

        $this->assertNotNull($result = $repo->process_upload($filename, 4096, $types));

        unset($_FILES['repo_upload_file']);

    }

    /**
     * Check that the upload escaper performs as expected
     */
    public function upload_failures_provider() {
        return [
            'Test that file with disallowed type is not allowed' => [
                'filename' => 'lib/tests/fixtures/gd-logo.png',
                'type' => ['.txt'],
                'exception' => 'Image (PNG) filetype cannot be accepted.'
            ],
            'Test that file with disallowed type is not allowed' => [
                'filename' => 'lib/tests/fixtures/file.custom',
                'type' => ['.text'],
                'exception' => 'File filetype cannot be accepted.'
            ],
        ];
    }

    /**
     * Check that the upload escaper performs as expected
     * @dataProvider upload_failures_provider
     */
    public function test_upload_failures($filename, $types, $exception) {
        $this->resetAfterTest();
        $this->setAdminUser();

        $_FILES['repo_upload_file'] = [
            'tmp_name' => $filename,
            'name' => $filename
        ];

        $repoid = $this->getDataGenerator()->create_repository('upload')->id;
        $repo = new repository_upload($repoid, SYSCONTEXTID, array());

        try {
            $result = $repo->process_upload($filename, 4096, $types);
            // Fail if no exception is thrown.
            $this->fail();
        } catch (moodle_exception $e) {
            $this->assertEquals($exception, $e->getMessage());
        }
        unset($_FILES['repo_upload_file']);
    }

}
