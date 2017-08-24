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
 * Test for restore_assign_stepslib.
 *
 * @package core_backup
 * @copyright 2017 John Okely <john@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/backup/util/includes/restore_includes.php');
require_once($CFG->dirroot . '/backup/util/includes/backup_includes.php');
require_once($CFG->dirroot . '/backup/util/plan/tests/fixtures/plan_fixtures.php');
require_once($CFG->dirroot . '/mod/assign/backup/moodle2/restore_assign_stepslib.php');
require_once($CFG->dirroot . '/mod/assign/tests/base_test.php');

/**
 * Test for restore_assign_activity_structure_step.
 *
 * @package mod_assign
 * @copyright 2017 John Okely <john@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_assign_restore_assign_activity_structure_step_testcase extends mod_assign_base_testcase {

    /**
     * Provide tests for rewrite_step_backup_file_for_legacy_freeze based upon fixtures.
     *
     * @return array
     */
    public function process_assign_grade_provider() {
        return [
            "Simple correct grade" => [
                "grade" => 27.50000,
                "expectedcount" => 1,
            ],
            "Unset grade" => [
                "grade" => -1,
                "expectedcount" => 0,
            ],
            "Broken grade" => [
                "grade" => -38.5,
                "expectedcount" => 0,
            ]
        ];
    }

    /**
     * Test process_assign_grade in assign restore
     *
     * @param   string  $source     The source file to test
     * @param   string  $expected   The expected result of the transformation
     * @dataProvider process_assign_grade_provider
     */
    public function test_process_assign_grade($grade, $expectedcount) {
        global $DB, $CFG, $USER;

        $this->resetAfterTest();

        $studentid = $this->students[0]->id;

        $assign = $this->create_instance();

        // Try getting a student's grade. This will give a grade of -1.
        // Then we can override it with a bad negative grade.
        $assign->get_user_grade($studentid, true);

        // Set the grade to something errant.
        $DB->set_field(
            'assign_grades',
            'grade',
            $grade,
            [
                'userid' => $studentid,
                'assignment' => $assign->get_instance()->id,
            ]
        );
        $assign->grade = $grade;
        $assigntemp = clone $assign->get_instance();
        $assigntemp->cmidnumber = $assign->get_course_module()->idnumber;
        assign_update_grades($assigntemp);

        // Make a backup.
        $this->setAdminUser();

        print_object($assign->get_course_module()->id);

        // Turn off file logging, otherwise it can't delete the file (Windows).
        $CFG->backup_file_logger_level = backup::LOG_NONE;
        $bc = new backup_controller(backup::TYPE_1ACTIVITY, $assign->get_course_module()->id,
                backup::FORMAT_MOODLE, backup::INTERACTIVE_NO, backup::MODE_GENERAL,
                $USER->id);
        $backupid = $bc->get_backupid();
        $bc->get_plan()->get_setting('users')->set_value(true);
        $bc->execute_plan();
        $bc->destroy();
        $result = $bc->get_results();
        $this->assertTrue(isset($result['backup_destination']));
        $fp = get_file_packer('application/vnd.moodle.backup');
        $tempdir = $CFG->dataroot . '/temp/backup/assigngrades';
        $files = $fp->extract_to_pathname($CFG->dirroot . '/backup/controller/tests/fixtures/deadlock.mbz', $tempdir);

        $newcourseid = restore_dbops::create_new_course(
            $this->course->fullname,
            $this->course->shortname . '_2',
            $this->course->category
        );

        // Restore it.
        $rc = new restore_controller('assigngrades', $newcourseid,
                backup::INTERACTIVE_NO, backup::MODE_GENERAL, $USER->id,
                backup::TARGET_NEW_COURSE);

        $this->assertTrue($rc->execute_precheck());
        $rc->get_plan()->get_setting('users')->set_value(true);
        $rc->execute_plan();
        $rc->destroy();

        print_object($rc->get_courseid());

        // Count number of grades that are not -1.
        $count = $DB->count_records_select(
            'assign_grades',
            'grade <> ?',
            [ASSIGN_GRADE_NOT_SET],
            "COUNT('id')"
        );

        $r = $DB->get_records(
            'assign_grades'
        );

        print_object($r);

        print_object($count);

        $this->assertSame($expectedcount, $count);
    }
}
