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


class filter_externalprotocol_testcase extends advanced_testcase {
    /**
     * Data provider for test_convert_protocols.
     */
    function test_convert_protocols_provider() {
        return [
            'An HTTP image' => [
                'input' => 'Image: <img src="' . $this->getExternalTestFileUrl('/test.jpg', false) . '" alt="http" width="538" height="190" style="vertical-align:text-bottom; margin: 0 .5em;" class="img-responsive">',
                'expected' => 'Image: <img src="' . $this->getExternalTestFileUrl('/test.jpg', true) . '" alt="http" width="538" height="190" style="vertical-align:text-bottom; margin: 0 .5em;" class="img-responsive">',
            ],
            'An HTTP image with the source attribute last' => [
                'input' => 'Image: <img alt="http" width="538" height="190" style="vertical-align:text-bottom; margin: 0 .5em;" class="img-responsive" src="' . $this->getExternalTestFileUrl('/test.jpg', false) . '">',
                'expected' => 'Image: <img alt="http" width="538" height="190" style="vertical-align:text-bottom; margin: 0 .5em;" class="img-responsive" src="' . $this->getExternalTestFileUrl('/test.jpg', true) . '">',
            ],
            'An HTTPS image' => [
                'input' => 'Image: <img src="' . $this->getExternalTestFileUrl('/test.jpg', false) . '" alt="http" width="538" height="190" style="vertical-align:text-bottom; margin: 0 .5em;" class="img-responsive">',
                'expected' => 'Image: <img src="' . $this->getExternalTestFileUrl('/test.jpg', true) . '" alt="http" width="538" height="190" style="vertical-align:text-bottom; margin: 0 .5em;" class="img-responsive">',
            ],
            'A URL (shouldn\'t be changed)' => [
                'input' => 'Just a URL. ' . $this->getExternalTestFileUrl('/test.jpg', false),
                'expected' => 'Just a URL. ' . $this->getExternalTestFileUrl('/test.jpg', false),
            ],
            'A link (shouldn\'t be changed)' => [
                'input' => 'Just <a href="' . $this->getExternalTestFileUrl('/test.jpg', false) . '">a link</a>',
                'expected' => 'Just <a href="' . $this->getExternalTestFileUrl('/test.jpg', false) . '">a link</a>',
            ],
            'An HTTP iframe' => [
                'input' => '<iframe src="' . $this->getExternalTestFileUrl('/test.html', false) . '"></iframe>',
                'expected' => '<iframe src="' . $this->getExternalTestFileUrl('/test.html', true) . '"></iframe>',
            ],
            'An HTTP js script' => [
                'input' => '<iframe src="' . $this->getExternalTestFileUrl('/test.js', false) . '"></iframe>',
                'expected' => '<iframe src="' . $this->getExternalTestFileUrl('/test.js', true) . '"></iframe>',
            ],
            'An HTTP css sheet' => [
                'input' => '<link href="' . $this->getExternalTestFileUrl('/test.css', false) . '"></link>',
                'expected' => '<link href="' . $this->getExternalTestFileUrl('/test.css', true) . '"></link>',
            ],
            'An HTTP image, but it is on a blacklisted URL' => [
                'input' => 'Image: <img src="http://www.example.com/test.jpg" alt="http">',
                'expected' => 'Image: <img src="http://www.example.com/test.jpg" alt="http">',
                'blacklist' => 'example.com',
            ],
            'An HTTP image, on a whitelisted URL' => [
                'input' => 'Image: <img src="' . $this->getExternalTestFileUrl('/test.jpg', false) . '" alt="http" width="538" height="190" style="vertical-align:text-bottom; margin: 0 .5em;" class="img-responsive">',
                'expected' => 'Image: <img src="' . $this->getExternalTestFileUrl('/test.jpg', true) . '" alt="http" width="538" height="190" style="vertical-align:text-bottom; margin: 0 .5em;" class="img-responsive">',
                'whitelist' => 'moodle.org',
            ],
            'An HTTP image, but it is on a non-whitelisted URL' => [
                'input' => 'Image: <img src="https://moodle.com/wp-content/themes/moodlecom/img/moodle.png" alt="http">',
                'expected' => 'Image: <img src="https://moodle.com/wp-content/themes/moodlecom/img/moodle.png" alt="http">',
                'whitelist' => 'moodle.org',
            ],
        ];
    }

    /**
     * Test that protocols are successfully converted from http to https
     *
     * @param string $text The text to be filtered
     * @param string $expectedresult The text to be filtered
     * @dataProvider test_convert_protocols_provider
     */
    function test_convert_protocols($input, $expected, $whitelist = '', $blacklist = '') {
        $testablefilter = new testable_filter_externalprotocol();
        $result = $testablefilter->convert_protocols($input);
        $this->assertSame($expected, $result);
    }

    // TODO: separate test for whitelisting.

}


/**
 * Test subclass that makes all the protected methods we want to test public.
 */
class testable_filter_externalprotocol extends filter_externalprotocol {

    /**
     * Override constructor, so we can test it more easily
     */
    public function __construct() {
        $this->get_global_config();
    }

    /**
     * Given some text this function converts any external embedded content URLs it finds from one protocol to another
     *
     * @param string $text Passed in by reference. The string to be searched for external content.
     */
    public function convert_protocols($text) {
        return parent::convert_protocols($text);
    }
}
