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
 * Standard Ajax wrapper for Moodle. It calls the central Ajax script,
 * which can call any existing webservice using the current session.
 * In addition, it can batch multiple requests and return multiple responses.
 *
 * @module     mod_lti/tool_configure
 * @class      tool_configure
 * @package    core
 * @copyright  2015 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
define(['jquery'], function($) {
    var SELECTORS = {
        REGISTRATION_URL: '#registration-url',
        REGISTRATION_NAME: '#registration-name',
        REGISTRATION_SUBMIT_BUTTON: '#registration-submit'
    };

    var getRegistrationURL = function() {
        return $(SELECTORS.REGISTRATION_URL);
    };

    var getRegistrationName = function() {
        return $(SELECTORS.REGISTRATION_NAME);
    };

    var getRegistrationSubmitButton = function() {
        return $(SELECTORS.REGISTRATION_SUBMIT_BUTTON);
    };

    var enableRegistrationName = function() {
        getRegistrationName().removeAttr('disabled');
    };

    var disableRegistrationName = function() {
        getRegistrationName().attr('disabled', true);
    }

    var isURLCartridge = function() {
        var value = getRegistrationURL().val();
        return /\.xml$/.test(value);
    };

    var handleRegistrationURLChange = function(e) {
        if (isURLCartridge()) {
            disableRegistrationName();
        } else {
            enableRegistrationName();
        }
    };

    var registerEventListeners = function() {
        getRegistrationURL().on('change input', handleRegistrationURLChange);
    };

    return {
        enhancePage: function() {
            registerEventListeners();
        }
    };
});
