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
 * Unit test for the filter_externalprotocol
 *
 * @package    filter_externalprotocol
 * @category   phpunit
 * @copyright  2014 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/filter/externalprotocol/filter.php'); // Include the code to test


class filter_externalprotocol_testcase extends basic_testcase {

    function get_convert_protocols_test_cases() {
        $texts = array (
            //an http image
            'Image: <img src="http://www.google.com.au/images/srpr/logo11w.png" alt="http" width="538" height="190" style="vertical-align:text-bottom; margin: 0 .5em;" class="img-responsive">' => 'Image: <img src="https://www.google.com.au/images/srpr/logo11w.png" alt="http" width="538" height="190" style="vertical-align:text-bottom; margin: 0 .5em;" class="img-responsive">',
            //an http image with the source attribute last
            'Image: <img alt="http" width="538" height="190" style="vertical-align:text-bottom; margin: 0 .5em;" class="img-responsive" src="http://www.google.com.au/images/srpr/logo11w.png">' => 'Image: <img alt="http" width="538" height="190" style="vertical-align:text-bottom; margin: 0 .5em;" class="img-responsive" src="https://www.google.com.au/images/srpr/logo11w.png">',
            //a https image
        );

        $data = array();
        foreach ($texts as $text => $correctresult) {
            $data[] = array($text, $correctresult);
        }
        return $data;
    }

    /**
     * @dataProvider get_convert_protocols_test_cases
     */
    function test_convert_protocols($text, $correctresult) {
        $testablefilter = new testable_filter_externalprotocol();
        $testablefilter->convert_protocols($text);
        $this->assertEquals($correctresult, $text);
    }

}


/**
 * Test subclass that makes all the protected methods we want to test public.
 */
class testable_filter_externalprotocol extends filter_externalprotocol {
    public function __construct() {
        $this->get_global_config();
        self::$globalconfig->blacklist = 'google.com';
    }
    public function convert_protocols(&$text) {
        parent::convert_protocols($text);
    }
}
