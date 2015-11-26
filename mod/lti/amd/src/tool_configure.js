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
define(['jquery', 'core/ajax', 'core/notification', 'core/templates'], function($, ajax, notification, templates) {
    var SELECTORS = {
        REGISTRATION_FORM: '#registration-form',
        REGISTRATION_URL: '#registration-url',
        REGISTRATION_SUBMIT_BUTTON: '#registration-submit',
        MAIN_CONTENT_CONTAINER: '#main-content-container',
        EXTERNAL_REGISTRATION_CONTAINER: '#external-registration-container',
        EXTERNAL_REGISTRATION_TEMPLATE_CONTAINER: '#external-registration-template-container',
        EXTERNAL_REGISTRATION_CANCEL_BUTTON: '#cancel-external-registration',
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

    var getExternalRegistrationCancelButton = function() {
        return $(SELECTORS.EXTERNAL_REGISTRATION_CANCEL_BUTTON);
    };

    var getExternalRegistrationTemplateContainer = function() {
        return $(SELECTORS.EXTERNAL_REGISTRATION_TEMPLATE_CONTAINER);
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

    var startLoadingCancel = function() {
        getExternalRegistrationCancelButton().addClass('loading');
    };

    var stopLoadingCancel = function() {
        getExternalRegistrationCancelButton().removeClass('loading');
    };

    var isLoading = function() {
        return getRegistrationSubmitButton().hasClass('loading');
    };

    var hideMainContent = function() {
        var container = $(SELECTORS.MAIN_CONTENT_CONTAINER);
        container.addClass('hidden');
    };

    var showMainContent = function() {
        var container = $(SELECTORS.MAIN_CONTENT_CONTAINER);
        container.removeClass('hidden');
    };

    var hideExternalRegistrationContent = function() {
        var container = $(SELECTORS.EXTERNAL_REGISTRATION_CONTAINER);
        container.addClass('hidden');
    };

    var showExternalRegistrationContent = function() {
        var container = $(SELECTORS.EXTERNAL_REGISTRATION_CONTAINER);
        container.removeClass('hidden');
    };

    var setToolProxyId = function(id) {
        var button = getExternalRegistrationCancelButton();
        button.attr('data-tool-proxy-id', id);
    };

    var getToolProxyId = function() {
        var button = getExternalRegistrationCancelButton();
        return button.attr('data-tool-proxy-id');
    };

    var clearToolProxyId = function() {
        var button = getExternalRegistrationCancelButton();
        button.removeAttr('data-tool-proxy-id');
    };

    var hasCreatedToolProxy = function() {
        return getToolProxyId() ? true : false;
    };

    var submitCartridgeURL = function() {
        var cartridgeURL = getRegistrationURL().val();
        window.location.search = 'cartridgeurl='+cartridgeURL;

        return $.Deferred();
    };

    var createToolProxy = function() {
        var url = getRegistrationURL().val();
        var request = {
            methodname: 'mod_lti_create_tool_proxy',
            args: {
                regurl: url
            }
        };

        return ajax.call([request])[0];
    };

    var deleteToolProxy = function(id) {
        var request = {
            methodname: 'mod_lti_delete_tool_proxy',
            args: {
                id: id
            }
        };

        return ajax.call([request])[0];
    };

    var getRegistrationRequest = function(id) {
        var request = {
            methodname: 'mod_lti_get_tool_proxy_registration_request',
            args: {
                id: id
            }
        };

        return ajax.call([request])[0];
    };

    var renderExternalRegistrationWindow = function(newWindow, registrationRequest) {
        var promise = templates.render('mod_lti/tool_proxy_registration_form', registrationRequest);

        promise.done(function(html, js) {

            var container = getExternalRegistrationTemplateContainer();
            container.append(html);
            templates.runTemplateJS(js);

            container.find('form').submit();
            showExternalRegistrationContent();
            hideMainContent();
            /*
            newWindow.document.write(html);

            $(newWindow.document).ready(function() {
                var form = $(newWindow.document.body).find('form');
                form.submit();
            });
            */
        });

        return promise;
    };

    var submitExternalRegistration = function(newWindow) {
        var promise = $.Deferred();

        createToolProxy().done(function(result) {
            var id = result.id;
            var regURL = result.regurl;

            // Save the id on the DOM to cleanup later.
            setToolProxyId(id);

            getRegistrationRequest(id).done(function(registrationRequest) {

                registrationRequest.reg_url = regURL;
                renderExternalRegistrationWindow(newWindow, registrationRequest).done(function() {

                    promise.resolve();

                }).fail(promise.fail);

            }).fail(promise.fail);

        }).fail(promise.fail);

        return promise;
    };

    var finishExternalRegistration = function() {
        if (hasCreatedToolProxy()) {
            clearToolProxyId();
        };

        hideExternalRegistrationContent();
        showMainContent();
        var container = getExternalRegistrationTemplateContainer();
        container.empty();
    };

    var cancelExternalRegistration = function() {
        startLoadingCancel();
        var promise = $.Deferred();

        if (hasCreatedToolProxy()) {
            var id = getToolProxyId();
            deleteToolProxy(id).done(function() {
                promise.resolve();
            });
        } else {
            promise.resolve();
        }

        promise.done(function() {
            finishExternalRegistration();
        });
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
            promise = submitExternalRegistration();
        }

        promise.done(function() {
            stopLoading();
        }).fail(function() { stopLoading(); }, notification.exception);
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

        var cancelButton = getExternalRegistrationCancelButton();
        cancelButton.click(function(e) {
            e.preventDefault();
            cancelExternalRegistration();
        });
        cancelButton.keypress(function(e) {
            if (!e.metaKey && !e.shiftKey && !e.altKey && !e.ctrlKey) {
                if (e.keyCode == KEYS.ENTER || e.keyCode == KEYS.SPACE) {
                    e.preventDefault();
                    cancelExternalRegistration();
                }
            }
        });

        // This is gross but necessary due to isolated jQuery scopes between
        // child iframe and parent windows. There is no other way to communicate.
        window.triggerExternalRegistrationComplete = function(data) {
            finishExternalRegistration();
        };
    };

    return {
        enhancePage: function() {
            registerEventListeners();
        }
    };
});
