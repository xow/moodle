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
 * @module moodle-gradereport_grader-gradereporttable
 * @submodule gradeeditor
 */

/**
 * Provides grade editing functionality within the grader report.
 *
 * See {{#crossLink "M.gradereport_grader.ReportTable"}}{{/crossLink}} for details.
 *
 * @namespace M.gradereport_grader
 * @class GradeEditor
 */

var CSS = {
        GRADEEDITOR : 'gradeeditor'
    },
    SELECTOR = {
        GRADEVALUE : 'span.gradevalue'
    },
    BODY = Y.one(document.body);

function GradeEditor() {}

GradeEditor.ATTRS= {
    ajaxgraderurl: {
        value: '/grade/report/grader/ajax_callbacks.php'
    }
};

GradeEditor.prototype = {
    setupAjaxEdit: function() {
        //BODY.delegate('key', this.handle_data_action, 'down:enter', SELECTOR.ACTIVITYACTION, this);
        Y.delegate('click', this.handle_data_action, BODY, SELECTOR.GRADEVALUE, this);
    },

    handle_data_action: function(ev) {
        var node = ev.target;
        if (!node.test(SELECTOR.GRADEVALUE) && 1===2) {
            node = node.ancestor(SELECTOR.GRADEVALUE);
        }
        this.edit_entry(ev, node);
    },

    edit_entry: function(ev, node) {
        var editor = Y.Node.create('<input name="title" type="text" class="'+CSS.TITLEEDITOR+'" />').setAttrs({
                'value': 'test',
                'autocomplete': 'off',
                'aria-describedby': 'id_editinstructions',
                'maxLength': '255'
            });
        node.ancestor().appendChild(editor);
        node.setStyle('display', 'none');
    }
};

Y.Base.mix(Y.M.gradereport_grader.ReportTable, [GradeEditor]);
