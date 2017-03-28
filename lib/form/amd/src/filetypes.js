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
 * This module allows to enhance the form elements MoodleQuickForm_filetypes
 *
 * @module     core_form/filetypes
 * @package    core_form
 * @copyright  2017 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.3
 */
define(['jquery', 'core/log', 'core/str', 'core/modal_factory', 'core/modal_events', 'core/ajax',
        'core/templates', 'core/tree'],
    function($, Log, Str, ModalFactory, ModalEvents, Ajax, Templates, Tree) {

    "use strict";

    /**
     * Constructor of the FileTypes instances.
     *
     * @constructor
     * @param {String} elementId The id of the form element to enhance
     * @param {String} elementLabel The label of the form element used as the modal selector title
     */
    var FileTypes = function(elementId, elementLabel, onlyTypes, allowAll) {

        this.elementId = elementId;
        this.elementLabel = elementLabel;
        this.onlyTypes = onlyTypes;
        this.allowAll = allowAll;

        this.inputField = $('#' + elementId);
        this.wrapperBrowserTrigger = $('[data-filetypesbrowser="' + elementId + '"]');
        this.wrapperDescriptions = $('[data-filetypesdescriptions="' + elementId + '"]');

        if (!this.wrapperBrowserTrigger.length) {
            // This is a valid case. Most probably the element is frozen and
            // the filetypes browser should not be available.
            return;
        }

        if (!this.inputField.length || !this.wrapperDescriptions.length) {
            Log.error('core_form/filetypes: Unexpected DOM structure, unable to enhance filetypes field ' + elementId);
            return;
        }

        this.createBrowserTrigger()
            .then(function() {
                return this.prepareBrowserModal();
            }.bind(this))

            .then(function() {
                return this.setUpBrowserTrigger();
            }.bind(this));
    };

    /**
     * Create the browser trigger widget (this.browserTrigger).
     *
     * @method prepareBrowserTrigger
     * @return {Promise}
     */
    FileTypes.prototype.createBrowserTrigger = function() {
        return Templates.render('core_form/filetypes-trigger', {})
            .then(function(html) {
                this.wrapperBrowserTrigger.html(html);
                this.browserTrigger = this.wrapperBrowserTrigger.find('[data-filetypeswidget="browsertrigger"]');
            }.bind(this));
    };

    /**
     * Prepare the modal for displaying the browser (this.browserModal).
     *
     * @method prepareBrowserModal
     * @return {Promise}
     */
    FileTypes.prototype.prepareBrowserModal = function() {
        return ModalFactory.create({
            type: ModalFactory.types.SAVE_CANCEL,
            title: this.elementLabel
        }).then(function(modal) {
            this.browserModal = modal;

        }.bind(this)).then(function() {
            // Because we have custom conditional modal trigger, we need to
            // handle the focus after closing ourselves, too.
            this.browserModal.getRoot().on(ModalEvents.hidden, function() {
                this.browserTrigger.focus();
            }.bind(this));
        }.bind(this));
    };

    /**
     * Attach the trigger's onclick event to control the modal display.
     *
     * We want to display the browser modal only when the associated input
     * field is not frozen (disabled).
     *
     * @method setUpBrowserTrigger
     * @return {Promise}
     */
    FileTypes.prototype.setUpBrowserTrigger = function() {
        this.browserTrigger.on('click', function(e) {
            if (!this.inputField.is('[disabled]')) {
                e.preventDefault();
                this.browserModal.setBody(this.loadBrowserModalBody());
                this.browserModal.show();

                if (!this.browserTree) {
                    this.browserTree = new Tree(this.browserModal.getBody());
                }
            }
        }.bind(this));

        // Return a resolved promise.
        return $.when();
    };

    /**
     * Load the browser modal body contents.
     */
    FileTypes.prototype.loadBrowserModalBody = function() {

        var args = {
            onlytypes: this.onlyTypes.join(),
            allowall: this.allowAll,
            current: this.inputField.val()
        };

        return Ajax.call([{
            methodname: 'core_form_get_filetypes_browser_data',
            args: args

        }])[0].then(function(browserData) {
            return Templates.render('core_form/filetypes-browser', {
                elementid: this.elementId,
                groups: browserData.groups
            });
        }.bind(this));
    };

    return {
        init: function(elementId, elementLabel, onlyTypes, allowAll) {
            new FileTypes(elementId, elementLabel, onlyTypes, allowAll);
        }
    };
});
