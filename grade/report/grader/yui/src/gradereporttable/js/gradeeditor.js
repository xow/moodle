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

    currentCell: null,

    setupAjaxEdit: function() {
        this._eventHandles.push(
            this.graderTable.delegate('key', this._discardEdit, 'down:esc', SELECTORS.ACTIVITYACTION, this),
            this.graderTable.delegate('key', this._saveEdit, 'down:enter', SELECTORS.ACTIVITYACTION, this),
            this.graderTable.delegate('click', this._handleDataAction, SELECTORS.GRADEVALUE, this)
        );
    },

    _handleDataAction: function(ev) {
        var node = ev.target;
        if (!node.test(SELECTORS.GRADEVALUE)) {
            node = node.ancestor(SELECTORS.GRADEVALUE);
        }
        this._editEntry(ev, node);
    },

    _editEntry: function(ev, node) {
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
        this.blurListener = gradeeditor.on('blur', this._saveEdit, this);
        node.setStyle('display', 'none');

        return this;
    },

    _saveEntry: function(properties, values) {
        Y.io.queue.stop();
        if (values.grade !== values.oldgrade) {
            // TODO: this.pendingsubmissions.push({transaction:this.graderTable.Y.io.queue(M.cfg.wwwroot+'/grade/report/grader/ajax_callbacks.php', {
            Y.io.queue(M.cfg.wwwroot+'/grade/report/grader/ajax_callbacks.php', {
                method : 'POST',
                data : 'id='+this.courseid+'&userid='+properties.userid+'&itemid='+properties.itemid+'&action=update&newvalue='+
                       values.grade+'&type='+properties.itemtype+'&sesskey='+M.cfg.sesskey,
                on : {
                    complete : this.submission_outcome
                },
                context : this,
                arguments : {
                    properties : properties,
                    values : values,
                    type : 'grade'
                }
            });
            //}),complete:false,outcome:null});
        }
        Y.io.queue.start();
    },

    _finishEdit: function(ev) {
        this.blurListener.detach();
        var gradeentry = ev.target.ancestor().one(SELECTORS.GRADEVALUE);
        gradeentry.setStyle('display', 'inline-block');
        ev.target.remove();
    },

    _saveEdit: function(ev) {
        var entry = ev.target.ancestor().one(SELECTORS.GRADEVALUE);

        var itemid = entry.ancestor('[data-itemid]').getData('itemid');
        var uid = entry.ancestor('[data-uid]').getData('uid');
        var newvalue = ev.target.get('value');
        var oldvalue = entry.getContent();
        this._saveEntry({itemid: itemid, userid: uid, itemtype: 'value'}, {grade: newvalue, oldgrade: oldvalue}); // TODO: don't hardcode value

        entry.setContent(ev.target.get('value'));
        return this._finishEdit(ev);
    },

    _discardEdit: function(ev) {
        return this._finishEdit(ev);
    }
};

Y.Base.mix(Y.M.gradereport_grader.ReportTable, [GradeEditor]);
