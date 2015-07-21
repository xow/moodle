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
 * Hidden password element used to deter password autofillers
 *
 * Contains HTML class for hiddenpassword type element
 *
 * @package   core_form
 * @copyright 2015 John Okely <john@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once("$CFG->libdir/form/hidden.php");

/**
 * hiddenpassword type form element
 *
 * @package   core_form
 * @category  form
 * @copyright 2015 John Okely <john@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_hiddenpassword extends MoodleQuickForm_hidden {

    /**
     * Constructs a new hidden password element
     *
     * @param string $elementname (optional) name of the html editor
     * @param string $elementlabel (optional) editor label
     * @param array $attributes (optional) Either a typical HTML attribute string
     *              or an associative array
     */
    public function MoodleQuickForm_hiddenpassword($elementname=null, $elementlabel=null, $attributes=null) {
        parent::MoodleQuickForm_hidden($elementname, $elementlabel, $attributes);
        $this->setType('password');
    }
}
