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
 * @package    filter
 * @subpackage externalprotocol
 * @copyright  2014 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_configmulticheckbox('filter_externalprotocol/formats',
            get_string('settingformats', 'filter_externalprotocol'),
            get_string('settingformats_desc', 'filter_externalprotocol'),
            array(FORMAT_MOODLE => 1, FORMAT_HTML => 1, FORMAT_PLAIN => 1, FORMAT_MARKDOWN => 1), format_text_menu()));

    $settings->add(new admin_setting_configtextarea('filter_externalprotocol/blacklist',
            get_string('settingblacklist', 'filter_externalprotocol'),
            get_string('settingblacklist_desc', 'filter_externalprotocol'),
            ''));
}
