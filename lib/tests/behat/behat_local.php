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
 * Files interactions with behat.
 *
 * Note that steps definitions files can not extend other steps definitions files, so
 * steps definitions which makes use of file attachments or filepicker should
 * extend behat_files instead of behat_base.
 *
 * @package    core
 * @category   test
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../behat/behat_base.php');

use Behat\Mink\Exception\ExpectationException as ExpectationException,
    Behat\Mink\Element\NodeElement as NodeElement;

class behat_local extends behat_base {

    /**
     * Sets the specified value to the field.
     *
     * @Given /^I go home$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $url
     * @return void
     */
    public function i_go_home() {
        $session = $this->getSession();
        $session->visit($this->locate_path('/?redirect=0'));
    }
    /**
     * Sets the specified value to the field.
     *
     * @Given /^I go to url "(?P<url_string>(?:[^"]|\\")*)"$/
     * @throws ElementNotFoundException Thrown by behat_base::find
     * @param string $url
     * @return void
     */
    public function i_go_to_url($url) {
        $session = $this->getSession();
        $session->visit($url);
    }

}
