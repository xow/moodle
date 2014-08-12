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

function GradeEditor() {}

GradeEditor.ATTRS= {
    ajaxgraderurl: {
        value: '/grade/report/grader/ajax_callbacks.php'
    }
};

GradeEditor.prototype = {
    setupAjaxEdit: function() {
        this._eventHandles.push(
            this.graderTable.delegate('key', this._finishEdit, 'down:enter', SELECTORS.ACTIVITYACTION, this),
            this.graderTable.delegate('click', this.handle_data_action, SELECTORS.GRADEVALUE, this)
        );
    },

    handle_data_action: function(ev) {
        var node = ev.target;
        if (!node.test(SELECTORS.GRADEVALUE)) {
            node = node.ancestor(SELECTORS.GRADEVALUE);
        }
        this.edit_entry(ev, node);
    },

    edit_entry: function(ev, node) {
        var cell = node.ancestor();

        var gradeeditor = Y.Node.create('<input name="title" type="text" class="'+CSS.GRADEEDITOR+'" />').setAttrs({
                'value': node.getContent(),
                'autocomplete': 'off',
                'aria-describedby': 'id_editinstructions',
                'maxLength': '255'
            });

        gradeeditor.setStyle('maxWidth', cell.get('offsetWidth') + 'px');

        cell.insertBefore(gradeeditor, node);
        gradeeditor.focus();
        this.blurListener = gradeeditor.on('blur', this._finishEdit, this);
        node.setStyle('display', 'none');

        return this;
    },

    _finishEdit: function(ev) {
        this.blurListener.detach();
        var gradedisplay = ev.target.ancestor().one(SELECTORS.GRADEVALUE);
        gradedisplay.setStyle('display', 'inline-block');
        gradedisplay.setContent(ev.target.get('value'));
        ev.target.remove();
    }
};

Y.Base.mix(Y.M.gradereport_grader.ReportTable, [GradeEditor]);
