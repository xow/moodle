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
 * Presents a modal popup for choosing file group and file types.
 *
 * Note: YUI is used for the dialogue only. When a suitable non-
 * YUI substitute becomes available, it should be trivial to
 * adapt this across.
 *
 * @package   assignsubmission_file
 * @copyright 2015 Jonathon Fowler <fowlerj@usq.edu.au>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define(['jquery', 'core/yui', 'core/str', 'core/notification', 'core/ajax', 'core/templates'],
       function($, Y, str, notification, ajax, templates) {
    "use strict";

    // Language strings.
    var strsave;

    // Fetch the strings.
    str.get_string('formctl_types_save', 'assignsubmission_file')
        .done(function (r) {
            strsave = r;
        }).fail(notification.exception);

    /**
     * Handle the UI changes from toggling a group or filetype checkbox.
     * @param {HTMLElement} changeel  the HTML element that toggled.
     * @param {HTMLElement} container  the HTML element containing all the checkboxes.
     */
    function handle_filetype_change(changeel, container) {
        var elvalue = changeel.value;
        var ischecked = changeel.checked;

        if (elvalue.indexOf('/') >= 0) {
            // Single type checkbox toggled, so update any other instances having
            // the same value to be the same state.
            $(container).find('input[type=checkbox]')
                .filter(function () { return this.value === elvalue; })
                .prop('checked', ischecked);
        } else {
            // Group checkbox (i.e. non-mimetype) toggled, so update the group
            // container to hide the types within.
            $(changeel).parentsUntil(container, '.filetypegroup')
                .toggleClass('selected', ischecked);
        }
    }

    /**
     * Refresh the label describing the chosen file types back on the
     * Moodle module form.
     * @param {JQuery} valueelem  the hidden form element containing the control value.
     * @param {JQuery} labelelem  the static form element receiving the user-friendly types chosen.
     * @param {Object[]} typegroups  type groups.
     * @param {Object[]} alltypes  all file types.
     */
    function update_label(valueelem, labelelem, typegroups, alltypes) {
        var values = valueelem.attr('value').split(';'),
            i,
            context = { hasitems: false, items: [] };

        for (i = 0; i < values.length; i++) {
            if (alltypes[values[i]]) {
                context.items.push(alltypes[values[i]]);
            } else if (typegroups[values[i]]) {
                context.items.push(typegroups[values[i]]);
            } else {
                continue;
            }
        }

        context.hasitems = context.items.length > 0;

        templates.render("assignsubmission_file/label", context)
            .done(function (r) {
                labelelem.html(r);
            }).fail(notification.excetion);
    }

    /**
     * Write the chosen group and file type values into the Moodle
     * module form's hidden field.
     * @param {JQuery} valueelem  the hidden form element containing the control value.
     * @param {HTMLElement} container  the HTML element containing all the checkboxes.
     */
    function save_choices(valueelem, container) {
        var chosen = [];

        $(container).find('input[type=checkbox]')
            .each(function () {
                if (this.checked && chosen.indexOf(this.value) < 0) {
                    chosen.push(this.value);
                }
            });
        valueelem.attr('value', chosen.join(';'));
    }

    /**
     * Open the modal popup for choosing group and file types.
     * @param {String} title  the title for the window
     * @param {JQuery} valueelem  the hidden form element containing the control value.
     * @param {JQuery} labelelem  the static form element receiving the user-friendly types chosen.
     * @param {Object[]} typegroups  type groups.
     * @param {Object[]} alltypes  all file types.
     */
    function select_types(title, valueelem, labelelem, typegroups, alltypes) {
        var group,
            values = valueelem.attr('value').split(';'),
            context = { groups: [] },
            groupctx;

        function add_type(type) {
            var typectx = {
                typekey: type,
                selected: (values.indexOf(type) >= 0),
                name: alltypes[type].name,
                extlist: alltypes[type].extlist
            };
            groupctx.types.push(typectx);
        }

        for (group in typegroups) {
            if (typegroups[group] === undefined) {
                // An unrecognised group.
                continue;
            } else if (typegroups[group].types.length < 1) {
                // An empty type group.
                continue;
            }

            groupctx = {
                groupkey: group,
                name: typegroups[group].name,
                extlist: typegroups[group].extlist,
                isoption: (group !== '-' && typegroups[group].isoption),
                selected: (values.indexOf(group) >= 0),
                types: []
            };
            typegroups[group].types.forEach(add_type);
            context.groups.push(groupctx);
        }

        templates.render("assignsubmission_file/selector", context)
            .done(function (content) {
                var bodynode;

                Y.use('moodle-core-notification-dialogue', function () {
                    var dialogue = new M.core.dialogue({
                        bodyContent: content,
                        headerContent: '<h3>' + title + '</h3>',
                        modal: true,
                        extraClasses: ['formctl_types_chooser']
                    });

                    bodynode = dialogue.bodyNode.getDOMNode();
                    dialogue.addButton({
                        name: 'save',
                        label: strsave,
                        action: function () {
                            save_choices(valueelem, bodynode);
                            update_label(valueelem, labelelem, typegroups, alltypes);
                            dialogue.hide();
                        }
                    });
                    dialogue.show();
                    dialogue.after('visibleChange', function () {
                        dialogue.destroy(true);
                    });
                });

                $(bodynode).delegate('input[type=checkbox]', 'change', function (e) {
                    handle_filetype_change(e.target, bodynode);
                });

            }).fail(notification.exception);
    }

    return {
        initialise: function (id, title, filetypes) {
            var valueelem = $(document.getElementById(id)),
                labelelem = $(document.getElementById(id + '_label')),
                buttonelem = $(document.getElementById(id + '_choose'));

            if (!valueelem.length || !labelelem.length || !buttonelem.length) {
                return;
            }

            var ajaxrequests = [{
                methodname: 'assignsubmission_file_get_types_and_groups',
                args: {
                    filetypes: filetypes
                },
                done: function (r) {
                    var typegroups = {},
                        alltypes = {};

                    r.typegroups.forEach(function (el) { typegroups[el.id] = el; });
                    r.alltypes.forEach(function (el) { alltypes[el.id] = el; });

                    update_label(valueelem, labelelem, typegroups, alltypes);
                    buttonelem.on('click', function () {
                        select_types(title, valueelem, labelelem, typegroups, alltypes);
                    });
                },
                fail: notification.exception
            }];

            ajax.call(ajaxrequests, true, false);
        }
    };
});
