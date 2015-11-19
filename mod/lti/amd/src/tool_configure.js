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
        REGISTRATION_FORM: '#registration-form',
        REGISTRATION_URL: '#registration-url',
        REGISTRATION_SUBMIT_BUTTON: '#registration-submit',
    };

    var KEYS = {
        ENTER: 13,
        SPACE: 13
    };

    var getRegistrationURL = function() {
        return $(SELECTORS.REGISTRATION_URL);
    };

    var getRegistrationSubmitButton = function() {
        return $(SELECTORS.REGISTRATION_SUBMIT_BUTTON);
    };

    var getRegistrationSubmitLoadingButton = function() {
        return $(SELECTORS.REGISTRATION_SUBMIT_LOADING_BUTTON);
    };

    var isCartridgeURL = function() {
        var value = getRegistrationURL().val();
        return /\.xml$/.test(value);
    };

    var startLoading = function() {
        getRegistrationSubmitButton().addClass('loading');
    };

    var stopLoading = function() {
        getRegistrationSubmitButton().removeClass('loading');
    };

    var isLoading = function() {
        return getRegistrationSubmitButton().hasClass('loading');
    }

    var submitCartridgeURL = function() {
        var cartridgeURL = getRegistrationURL().val();
        window.location.search = 'cartridgeurl='+cartridgeURL;

        return $.Deferred();
    };

    var submitRegistrationURL = function() {

    };

    var processURL = function() {
        if (isLoading()) {
            return;
        }

        startLoading();

        var promise = null;
        if (isCartridgeURL()) {
            promise = submitCartridgeURL();
        } else {
            promise = submitRegistrationURL();
        }

        promise.done(function() {
            stopLoading();
        });
    };

    var registerEventListeners = function() {
        var submitButton = getRegistrationSubmitButton();
        submitButton.click(function(e) {
            e.preventDefault();
            processURL();
        });
        submitButton.keypress(function(e) {
            if (!e.metaKey && !e.shiftKey && !e.altKey && !e.ctrlKey) {
                if (e.keyCode == KEYS.ENTER || e.keyCode == KEYS.SPACE) {
                    e.preventDefault();
                    processURL();
                }
            }
        });
    };

    return {
        enhancePage: function() {
            registerEventListeners();
        }
    };
});
