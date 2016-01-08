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
 * @module     mod_lti/tool_card_controller
 * @class      tool_card_controller
 * @package    core
 * @copyright  2015 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      3.1
 */
define(['jquery', 'core/ajax', 'core/notification', 'mod_lti/tool_type'], function($, ajax, notification, toolType) {
    var SELECTORS = {
        DELETE_BUTTON: '.delete',
        NAME_ELEMENT: '.name',
        DESCRIPTION_ELEMENT: '.description',
        CAPABILITIES_CONTAINER: '.capabilities-container',
        CAPABILITIES_YES_BUTTON: '.capabilities-container a.yes',
        CAPABILITIES_NO_BUTTON: '.capabilities-container a.no',
        APPROVE_BUTTON: '.tool-card-footer a.approve',
    };

    var KEYS = {
        ENTER: 13,
        SPACE: 13
    };

    var getDeleteButton = function(element) {
        return element.find(SELECTORS.DELETE_BUTTON);
    };

    var getNameElement = function(element) {
        return element.find(SELECTORS.NAME_ELEMENT);
    };

    var getDescriptionElement = function(element) {
        return element.find(SELECTORS.DESCRIPTION_ELEMENT);
    };

    var getApproveButton = function(element) {
        return element.find(SELECTORS.APPROVE_BUTTON);
    };

    var hasApproveButton = function(element) {
        return getApproveButton(element).length ? true : false;
    };

    var hasCapabilitiesContainer = function(element) {
        return element.find(SELECTORS.CAPABILITIES_CONTAINER).length ? true : false;
    };

    var getCapabilitiesApproveButton = function(element) {
        return element.find(SELECTORS.CAPABILITIES_YES_BUTTON);
    };

    var getCapabilitiesDeclineButton = function(element) {
        return element.find(SELECTORS.CAPABILITIES_NO_BUTTON);
    };

    var getTypeId = function(element) {
        return element.attr('data-type-id');
    };

    var clearAllAnnouncements = function(element) {
        element.removeClass('announcement loading success fail capabilities');
    };

    var startLoading = function(element) {
        clearAllAnnouncements();
        element.addClass('announcement loading');
    };

    var stopLoading = function(element) {
        element.removeClass('announcement loading');
    };

    var isLoading = function(element) {
        return element.hasClass('announcement loading');
    };

    var announceSuccess = function(element) {
        var promise = $.Deferred();

        clearAllAnnouncements(element);
        element.addClass('announcement success');
        setTimeout(function() {
            element.removeClass('announcement success');
            promise.resolve();
        }, 2000);

        return promise;
    };

    var announceFailure = function(element) {
        var promise = $.Deferred();

        clearAllAnnouncements(element);
        element.addClass('announcement fail');
        setTimeout(function() {
            element.removeClass('announcement fail');
            promise.resolve();
        }, 2000);

        return promise;
    };

    var deleteType = function(element) {
        var typeId = getTypeId(element);

        if (typeId == "") {
            return;
        }

        startLoading(element);
        var promise = toolType.delete(typeId);

        promise.done(function() {
            stopLoading(element);
            announceSuccess(element).done(function() {
                element.remove();
            });
        });

        promise.fail(function() { announceFailure(element) });
    };

    var setValueSnapshot = function(element, value) {
        element.attr('data-val-snapshot', value);
    };

    var getValueSnapshot = function(element) {
        return element.attr('data-val-snapshot');
    };

    var snapshotDescription = function(element) {
        var descriptionElement = getDescriptionElement(element);

        if (descriptionElement.hasClass('loading')) {
            return;
        }

        var description = descriptionElement.text().trim();
        setValueSnapshot(descriptionElement, description);
    };

    var updateDescription = function(element) {
        var typeId = getTypeId(element);

        // Return early if we don't have an id because it's
        // required to save the changes.
        if (typeId == "") {
            return;
        }

        var descriptionElement = getDescriptionElement(element);

        // Return early if we're already saving a value.
        if (descriptionElement.hasClass('loading')) {
            return;
        }

        var description = descriptionElement.text().trim();
        var snapshotVal = getValueSnapshot(descriptionElement);

        // If the value hasn't change then don't bother sending the
        // update request.
        if (snapshotVal && snapshotVal == description) {
            return;
        }

        descriptionElement.addClass('loading');

        var promise = toolType.update({id: typeId, description: description});

        promise.done(function(type) {
            descriptionElement.removeClass('loading');
            // Make sure the text is updated with the description from the
            // server, just in case the update didn't work.
            descriptionElement.text(type.description);
        });

        // Probably need to handle failures better so that we can revert
        // the value in the input for the user.
        promise.fail(function() { descriptionElement.removeClass('loading'); });

        return promise;
    };

    var snapshotName = function(element) {
        var nameElement = getNameElement(element);

        if (nameElement.hasClass('loading')) {
            return;
        }

        var name = nameElement.text().trim();
        setValueSnapshot(nameElement, name);
    };

    var updateName = function(element, value) {
        var typeId = getTypeId(element);

        // Return if we don't have an id.
        if (typeId == "") {
            return;
        }

        var nameElement = getNameElement(element);

        // Return if we're already saving.
        if (nameElement.hasClass('loading')) {
            return;
        }

        var name = nameElement.text().trim();
        var snapshotVal = getValueSnapshot(nameElement);

        // If the value hasn't change then don't bother sending the
        // update request.
        if (snapshotVal && snapshotVal == name) {
            return;
        }

        nameElement.addClass('loading');
        var promise = toolType.update({id: typeId, name: name});

        promise.done(function(type) {
            nameElement.removeClass('loading');
            // Make sure the text is updated with the name from the
            // server, just in case the update didn't work.
            nameElement.text(type.name);
        });

        // Probably need to handle failures better so that we can revert
        // the value in the input for the user.
        promise.fail(function() { nameElement.removeClass('loading'); });

        return promise;
    };

    var setStatusActive = function(element) {
        announceSuccess(element);
    };

    var displayCapabilitiesApproval = function(element) {
        element.addClass('announcement capabilities');
    };

    var hideCapabilitiesApproval = function(element) {
        element.removeClass('announcement capabilities');
    };

    var approveTool = function(element) {
        if (hasCapabilitiesContainer(element)) {
            displayCapabilitiesApproval(element);
        } else {
            setStatusActive(element);
        }
    };

    var registerEventListeners = function(element) {
        var deleteButton = getDeleteButton(element);
        deleteButton.click(function(e) {
            e.preventDefault();
            deleteType(element);
        });
        deleteButton.keypress(function(e) {
            if (!e.metaKey && !e.shiftKey && !e.altKey && !e.ctrlKey) {
                if (e.keyCode == KEYS.ENTER || e.keyCode == KEYS.SPACE) {
                    e.preventDefault();
                    deleteButton.click();
                }
            }
        });

        var descriptionElement = getDescriptionElement(element);
        descriptionElement.focus(function(e) {
            e.preventDefault();
            snapshotDescription(element);
        });
        descriptionElement.blur(function(e) {
            e.preventDefault();
            updateDescription(element);
        });
        descriptionElement.keypress(function(e) {
            if (!e.metaKey && !e.shiftKey && !e.altKey && !e.ctrlKey) {
                if (e.keyCode == KEYS.ENTER) {
                    e.preventDefault();
                    descriptionElement.blur();
                }
            }
        });

        var nameElement = getNameElement(element);
        nameElement.focus(function(e) {
            e.preventDefault();
            snapshotName(element);
        });
        nameElement.blur(function(e) {
            e.preventDefault();
            updateName(element);
        });
        nameElement.keypress(function(e) {
            if (!e.metaKey && !e.shiftKey && !e.altKey && !e.ctrlKey) {
                if (e.keyCode == KEYS.ENTER) {
                    e.preventDefault();
                    nameElement.blur();
                }
            }
        });

        if (hasApproveButton(element)) {
            var approveButton = getApproveButton(element);
            approveButton.click(function(e) {
                e.preventDefault();
                approveTool(element);
            });
            approveButton.keypress(function(e) {
                if (!e.metaKey && !e.shiftKey && !e.altKey && !e.ctrlKey) {
                    if (e.keyCode == KEYS.ENTER || e.keyCode == KEYS.SPACE) {
                        e.preventDefault();
                        approveButton.click();
                    }
                }
            });
        }

        if (hasCapabilitiesContainer(element)) {
            var capabilitiesApproveButton = getCapabilitiesApproveButton(element);
            capabilitiesApproveButton.click(function(e) {
                e.preventDefault();
                setStatusActive(element);
            });
            capabilitiesApproveButton.keypress(function(e) {
                if (!e.metaKey && !e.shiftKey && !e.altKey && !e.ctrlKey) {
                    if (e.keyCode == KEYS.ENTER || e.keyCode == KEYS.SPACE) {
                        e.preventDefault();
                        capabilitiesApproveButton.click();
                    }
                }
            });

            var capabilitiesDeclineButton = getCapabilitiesDeclineButton(element);
            capabilitiesDeclineButton.click(function(e) {
                e.preventDefault();
                hideCapabilitiesApproval(element);
            });
            capabilitiesDeclineButton.keypress(function(e) {
                if (!e.metaKey && !e.shiftKey && !e.altKey && !e.ctrlKey) {
                    if (e.keyCode == KEYS.ENTER || e.keyCode == KEYS.SPACE) {
                        e.preventDefault();
                        capabilitiesDeclineButton.click();
                    }
                }
            });
        }
    };

    return {
        init: function(element) {
            registerEventListeners(element);
        }
    };
});
