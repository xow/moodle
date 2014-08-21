YUI.add('moodle-gradereport_grader-gradereporttable', function (Y, NAME) {

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
 * Grader Report Functionality.
 *
 * @module    moodle-gradereport_grader-gradereporttable
 * @package   gradereport_grader
 * @copyright 2014 UC Regents
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Alfonso Roman <aroman@oid.ucla.edu>
 */

/**
 * @module moodle-gradereport_grader-gradereporttable
 */

var SELECTORS = {
        FOOTERROW: '#user-grades .avg',
        GRADECELL: 'td.grade',
        GRADERTABLE: '.gradeparent table',
        GRADEPARENT: '.gradeparent',
        HEADERCELL: '.gradebook-header-cell',
        STUDENTHEADER: '#studentheader',
        SPINNER: '.gradebook-loading-screen',
        USERCELL: '#user-grades .user.cell'
    },
    CSS = {
        OVERRIDDEN: 'overridden',
        STICKYFOOTER: 'gradebook-footer-row-sticky',
        TOOLTIPACTIVE: 'tooltipactive'
    };

/**
 * The Grader Report Table.
 *
 * @namespace M.gradereport_grader
 * @class ReportTable
 * @constructor
 */
function ReportTable() {
    ReportTable.superclass.constructor.apply(this, arguments);
}

Y.extend(ReportTable, Y.Base, {
    /**
     * Array of EventHandles.
     *
     * @type EventHandle[]
     * @property _eventHandles
     * @protected
     */
    _eventHandles: [],

    /**
     * A Node reference to the grader table.
     *
     * @property graderTable
     * @type Node
     */
    graderTable: null,

    /**
     * Setup the grader report table.
     *
     * @method initializer
     */
    initializer: function() {
        // Some useful references within our target area.
        this.graderRegion = Y.one(SELECTORS.GRADEPARENT);
        this.graderTable = Y.one(SELECTORS.GRADERTABLE);

        // Setup the floating headers.
        this.setupFloatingHeaders();

        // Setup the mouse tooltips.
        this.setupTooltips();

        // Hide the loading spinner - we've finished for the moment.
        this._hideSpinner();
    },

    /**
     * Show the loading spinner.
     *
     * @method showSpinner
     * @protected
     */
    showSpinner: function() {
        // Show the grading spinner.
        Y.one(SELECTORS.SPINNER).show();
    },

    /**
     * Hide the loading spinner.
     *
     * @method hideSpinner
     * @protected
     */
    hideSpinner: function() {
        // Hide the grading spinner.
        Y.one(SELECTORS.SPINNER).hide();
    },

    /**
     * Get the text content of the username for the specified grade item.
     *
     * @method getGradeUserName
     * @param {Node} cell The grade item cell to obtain the username for
     * @return {String} The string content of the username cell.
     */
    getGradeUserName: function(cell) {
        var userrow = cell.ancestor('tr'),
            usercell = userrow.one("th.user .username");

        if (usercell) {
            return usercell.get('text');
        } else {
            return '';
        }
    },

    /**
     * Get the text content of the item name for the specified grade item.
     *
     * @method getGradeItemName
     * @param {Node} cell The grade item cell to obtain the item name for
     * @return {String} The string content of the item name cell.
     */
    getGradeItemName: function(cell) {
        var itemcell = Y.one("th.item[data-itemid='" + cell.getData('itemid') + "']");
        if (itemcell) {
            return itemcell.get('text');
        } else {
            return '';
        }
    },

    /**
     * Get the text content of any feedback associated with the grade item.
     *
     * @method getGradeFeedback
     * @param {Node} cell The grade item cell to obtain the item name for
     * @return {String} The string content of the feedback.
     */
    getGradeFeedback: function(cell) {
        return cell.getData('feedback');
    }
});

Y.namespace('M.gradereport_grader').ReportTable = ReportTable;
Y.namespace('M.gradereport_grader').init = function(config) {
    return new Y.M.gradereport_grader.ReportTable(config);
};
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
 * @submodule floatingheaders
 */

/**
 * Provides floating headers to the grader report.
 *
 * See {{#crossLink "M.gradereport_grader.ReportTable"}}{{/crossLink}} for details.
 *
 * @namespace M.gradereport_grader
 * @class FloatingHeaders
 */

var HEIGHT = 'height',
    WIDTH = 'width',
    OFFSETWIDTH = 'offsetWidth',
    OFFSETHEIGHT = 'offsetHeight';

function FloatingHeaders() {}

FloatingHeaders.ATTRS= {
};

FloatingHeaders.prototype = {
    /**
     * The height of the page header if a fixed position, floating header
     * was found.
     *
     * @property pageHeaderHeight
     * @type Number
     * @default 0
     * @protected
     */
    pageHeaderHeight: 0,

    /**
     * A Node representing the header cell.
     *
     * @property headerCell
     * @type Node
     * @protected
     */
    headerCell: null,

    /**
     * A Node representing the first cell which contains user name information.
     *
     * @property firstUserCell
     * @type Node
     * @protected
     */
    firstUserCell: null,

    /**
     * A Node representing the original table footer row.
     *
     * @property tableFooterRow
     * @type Node
     * @protected
     */
    tableFooterRow: null,

    /**
     * A Node representing the floating footer row in the grading table.
     *
     * @property footerRow
     * @type Node
     * @protected
     */
    footerRow: null,

    /**
     * A Node representing the floating assignment header.
     *
     * @property assignmentHeadingContainer
     * @type Node
     * @protected
     */
    assignmentHeadingContainer: null,

    /**
     * A Node representing the floating user header. This is the header with the Surname/First name
     * sorting.
     *
     * @property userColumnHeader
     * @type Node
     * @protected
     */
    userColumnHeader: null,

    /**
     * A Node representing the floating user column. This is the column containing all of the user
     * names.
     *
     * @property userColumn
     * @type Node
     * @protected
     */
    userColumn: null,

    /**
     * The position of the bottom of the first user cell.
     * This is used when processing the scroll event as an optimisation. It must be updated when
     * additional rows are loaded, or the window changes in some fashion.
     *
     * @property firstUserCellBottom
     * @type Node
     * @protected
     */
    firstUserCellBottom: 0,

    /**
     * The position of the left of the first user cell.
     * This is used when processing the scroll event as an optimisation. It must be updated when
     * additional rows are loaded, or the window changes in some fashion.
     *
     * @property firstUserCellLeft
     * @type Node
     * @protected
     */
    firstUserCellLeft: 0,

    /**
     * The position of the top of the final user cell.
     * This is used when processing the scroll event as an optimisation. It must be updated when
     * additional rows are loaded, or the window changes in some fashion.
     *
     * @property lastUserCellTop
     * @type Node
     * @protected
     */
    lastUserCellTop: 0,

    /**
     * Array of EventHandles.
     *
     * @type EventHandle[]
     * @property _eventHandles
     * @protected
     */
    _eventHandles: [],

    /**
     * Setup the grader report table.
     *
     * @method setupFloatingHeaders
     * @chainable
     */
    setupFloatingHeaders: function() {
        // Grab references to commonly used Nodes.
        this.firstUserCell = Y.one(SELECTORS.USERCELL);

        if (!this.firstUserCell) {
            // There was no first user cell - no need to do anything at this stage.
            this._hideSpinner();
            return;
        }

        // Generate floating elements.
        this._setupFloatingUserColumn();
        this._setupFloatingUserHeader();
        this._setupFloatingAssignmentHeaders();
        this._setupFloatingAssignmentFooter();

        // Calculate the positions of edge cells. These are used for positioning of the floating headers.
        // This must be called after the floating headers are setup, but before the scroll event handler is invoked.
        this._calculateCellPositions();

        // Setup the floating element initial positions by simulating scroll.
        this._handleScrollEvent();

        // Setup the event handlers.
        this._setupEventHandlers();

        // Hide the loading spinner - we've finished for the moment.
        this._hideSpinner();

        return this;
    },

    /**
     * Calculate the positions of some cells. These values are used heavily
     * in scroll event handling.
     *
     * @method _calculateCellPositions
     * @protected
     */
    _calculateCellPositions: function() {
        // The header row shows the assigment headers and is floated to the top of the window.
        this.headerCellTop = this.headerCell.getY();

        // The footer row shows the grade averages and will be floated to the page bottom.
        if (this.tableFooterRow) {
            this.footerRowPosition = this.tableFooterRow.getY();
        }

        var userCellList = Y.all(SELECTORS.USERCELL);

        // The left of the user cells matches the left of the headerCell.
        this.firstUserCellLeft = this.headerCell.getX();

        if (userCellList.size() > 1) {
            // Use the top of the second cell for the bottom of the first cell.
            // This is used when scrolling to fix the footer to the top edge of the window.
            this.firstUserCellBottom = userCellList.item(1).getY();

            // Use the top of the penultimate cell when scrolling the header.
            // The header is the same size as the cells.
            this.lastUserCellTop = userCellList.item(userCellList.size() - 2).getY();
        } else {
            var firstItem = userCellList.item(0);
            // We can't use the top of the second row as there is only one row.
            this.lastUserCellTop = firstItem.getY();

            if (this.tableFooterRow) {
                // The footer is present so we can use that.
                this.firstUserCellBottom = this.footerRowPosition;
            } else {
                // No other clues - calculate the top instead.
                this.firstUserCellBottom = firstItem.getY() + firstItem.get('offsetHeight');
            }
        }

        // Check whether a header is present and whether it is floating.
        var header = Y.one('header');
        this.pageHeaderHeight = 0;
        if (header) {
            if (header.getComputedStyle('position') === 'fixed') {
                this.pageHeaderHeight = header.get(OFFSETHEIGHT);
            }
        }
    },

    /**
     * Setup the main event listeners.
     * These deal with things like window events.
     *
     * @method _setupEventHandlers
     * @protected
     */
    _setupEventHandlers: function() {
        this._eventHandles.push(
            // Listen for window scrolls, resizes, and rotation events.
            Y.one(Y.config.win).on('scroll', this._handleScrollEvent, this),
            Y.one(Y.config.win).on('resize', this._handleResizeEvent, this),
            Y.one(Y.config.win).on('orientationchange', this._handleResizeEvent, this)
        );
    },

    /**
     * Show the loading spinner.
     *
     * @method _showSpinner
     * @protected
     */
    _showSpinner: function() {
        // Show the grading spinner.
        Y.one(SELECTORS.SPINNER).show();
    },

    /**
     * Hide the loading spinner.
     *
     * @method _hideSpinner
     * @protected
     */
    _hideSpinner: function() {
        // Hide the grading spinner.
        Y.one(SELECTORS.SPINNER).hide();
    },

    /**
     * Create and setup the floating column of user names.
     *
     * @method _setupFloatingUserColumn
     * @protected
     */
    _setupFloatingUserColumn: function() {
        // Grab all cells in the user names column.
        var userColumn = Y.all(SELECTORS.USERCELL),

        // Create a floating table.
            floatingUserColumn = Y.Node.create('<div aria-hidden="true" role="presentation" id="gradebook-user-container"></div>');

        // Generate the new fields.
        userColumn.each(function(node) {
            // Create and configure the new container.
            var containerNode = Y.Node.create('<div aria-hidden="true" class="gradebook-user-cell"></div>');
            containerNode.set('innerHTML', node.get('innerHTML'))
                    .setAttribute('data-uid', node.ancestor('tr').getData('uid'))
                    .setStyles({
                        height: node.getComputedStyle(HEIGHT),
                        width:  node.getComputedStyle(WIDTH)
                    });

            // Add the new nodes to our floating table.
            floatingUserColumn.appendChild(containerNode);
        }, this);

        // Style the floating user container.
        floatingUserColumn.setStyles({
            left:       this.firstUserCell.getX() + 'px',
            position:   'absolute',
            top:        this.firstUserCell.getY() + 'px'
        });

        // Append to the grader region.
        this.graderRegion.append(floatingUserColumn);

        // Store a reference to this for later - we use it in the event handlers.
        this.userColumn = floatingUserColumn;
    },

    /**
     * Create and setup the floating username header cell.
     *
     * @method _setupFloatingUserHeader
     * @protected
     */
    _setupFloatingUserHeader: function() {
        // We make various references to the this header cell. Store it for later.
        this.headerCell = Y.one(SELECTORS.STUDENTHEADER);

        // Float the 'user name' header cell.
        var floatingUserCell = Y.Node.create('<div aria-hidden="true" role="presentation" id="gradebook-user-header-container"></div>');

        // Append node contents
        floatingUserCell.set('innerHTML', this.headerCell.getHTML());
        floatingUserCell.setStyles({
            height:     this.headerCell.getComputedStyle(HEIGHT),
            left:       this.firstUserCell.getX() + 'px',
            position:   'absolute',
            top:        this.headerCell.getY() + 'px',
            width:      this.firstUserCell.getComputedStyle(WIDTH)
        });

        // Append to the grader region.
        this.graderRegion.append(floatingUserCell);

        // Store a reference to this for later - we use it in the event handlers.
        this.userColumnHeader = floatingUserCell;
    },

    /**
     * Create and setup the floating assignment header row.
     *
     * @method _setupFloatingAssignmentHeaders
     * @protected
     */
    _setupFloatingAssignmentHeaders: function() {
        var gradeHeaders = Y.all('#user-grades tr.heading .cell');

        // Generate a floating headers
        var floatingGradeHeaders = Y.Node.create('<div aria-hidden="true" role="presentation" id="gradebook-header-container"></div>');

        var floatingGradeHeadersWidth = 0;
        var floatingGradeHeadersHeight = 0;
        var gradeHeadersOffset = this.headerCell.getX();

        gradeHeaders.each(function(node) {
            var nodepos = node.getX();

            var newnode = Y.Node.create('<div class="gradebook-header-cell"></div>');
            newnode.append(node.getHTML())
                    .addClass(node.getAttribute('class'))
                    .setData('itemid', node.getData('itemid'))
                    .setStyles({
                        height:     node.getComputedStyle(HEIGHT),
                        left:       (nodepos - gradeHeadersOffset) + 'px',
                        position:   'absolute',
                        width:      node.getComputedStyle(WIDTH)
                    });

            // Sum up total widths - these are used in the container styles.
            // Use the offsetHeight and Width here as this contains the
            // padding, margin, and borders.
            floatingGradeHeadersWidth += parseInt(node.get(OFFSETWIDTH), 10);
            floatingGradeHeadersHeight = node.get(OFFSETHEIGHT);

            // Append to our floating table.
            floatingGradeHeaders.appendChild(newnode);
        }, this);

        // Position header table.
        floatingGradeHeaders.setStyles({
            height:     floatingGradeHeadersHeight + 'px',
            left:       this.headerCell.getX() + 'px',
            position:   'absolute',
            top:        this.headerCell.getY() + 'px',
            width:      floatingGradeHeadersWidth + 'px'
        });

        // Insert in place before the grader headers.
        this.userColumnHeader.insert(floatingGradeHeaders, 'before');

        // Store a reference to this for later - we use it in the event handlers.
        this.assignmentHeadingContainer = floatingGradeHeaders;
    },

    /**
     * Create and setup the floating header row of assignment titles.
     *
     * @method _setupFloatingAssignmentFooter
     * @protected
     */
    _setupFloatingAssignmentFooter: function() {
        this.tableFooterRow = Y.one('#user-grades .avg');
        if (!this.tableFooterRow) {
            Y.log('Averages footer not found - unable to float it.', 'warn', LOGNS);
            return;
        }

        // Generate the sticky footer row.
        var footerCells = this.tableFooterRow.all('.cell');

        // Create a container.
        var floatingGraderFooter = Y.Node.create('<div aria-hidden="true" role="presentation" id="gradebook-footer-container"></div>');
        var footerWidth = 0;
        var footerRowOffset = this.tableFooterRow.getX();

        // Copy cell content.
        footerCells.each(function(node) {
            var newnode = Y.Node.create('<div class="gradebook-footer-cell"></div>');
            newnode.set('innerHTML', node.getHTML());
            newnode.setStyles({
                height:     this._getHeight(node),
                left:       (node.getX() - footerRowOffset) + 'px',
                position:   'absolute',
                width:      this._getWidth(node)
            });

            floatingGraderFooter.append(newnode);
            footerWidth += parseInt(node.get(OFFSETWIDTH), 10);
        }, this);

        // Attach 'Update' button.
        var updateButton = Y.one('#gradersubmit');
        if (updateButton) {
            // TODO decide what to do with classes here to make them compatible with the base themes.
            var button = Y.Node.create('<button class="btn btn-sm btn-default">' + updateButton.getAttribute('value') + '</button>');
            button.on('click', function() {
                    updateButton.simulate('click');
            });
            floatingGraderFooter.one('.gradebook-footer-cell').append(button);
        }

        // Position the row
        floatingGraderFooter.setStyles({
            position:   'absolute',
            left:       this.tableFooterRow.getX() + 'px',
            bottom:     0,
            height:     this._getHeight(this.tableFooterRow),
            width:      footerWidth + 'px'
        });

        // Append to the grader region.
        this.graderRegion.append(floatingGraderFooter);

        this.footerRow = floatingGraderFooter;
    },

    /**
     * Process a Scroll Event on the window.
     *
     * @method _handleScrollEvent
     * @protected
     */
    _handleScrollEvent: function() {
        // Performance is important in this function as it is called frequently and in quick succesion.
        // To prevent layout thrashing when the DOM is repeatedly updated and queried, updated and queried,
        // updates must be batched.

        // Next do all the calculations.
        var assignmentHeadingContainerStyles = {},
            userColumnHeaderStyles = {},
            userColumnStyles = {},
            footerStyles = {};

        // Header position.
        assignmentHeadingContainerStyles.left = this.headerCell.getX();
        if (Y.config.win.pageYOffset + this.pageHeaderHeight > this.headerCellTop) {
            if (Y.config.win.pageYOffset + this.pageHeaderHeight < this.lastUserCellTop) {
                assignmentHeadingContainerStyles.top = Y.config.win.pageYOffset + this.pageHeaderHeight + 'px';
                userColumnHeaderStyles.top = Y.config.win.pageYOffset + this.pageHeaderHeight + 'px';
            } else {
                assignmentHeadingContainerStyles.top = this.lastUserCellTop + 'px';
                userColumnHeaderStyles.top = this.lastUserCellTop + 'px';
            }
        } else {
            assignmentHeadingContainerStyles.top = this.headerCellTop + 'px';
            userColumnHeaderStyles.top = this.headerCellTop + 'px';
        }

        // User column position.
        if (Y.config.win.pageXOffset > this.firstUserCellLeft) {
            userColumnStyles.left = Y.config.win.pageXOffset + 'px';
            userColumnHeaderStyles.left = Y.config.win.pageXOffset + 'px';
        } else {
            userColumnStyles.left = this.firstUserCellLeft + 'px';
            userColumnHeaderStyles.left = this.firstUserCellLeft + 'px';
        }

        // Update footer.
        if (this.footerRow) {
            footerStyles.left = this.headerCell.getX();

            // Determine whether the footer should now be shown as sticky.
            var pageHeight = Y.config.win.pageYOffset + Y.config.win.innerHeight;
            if (pageHeight - this.pageHeaderHeight < this.footerRowPosition) {
                // The footer is off the bottom of the page.
                this.footerRow.addClass(CSS.STICKYFOOTER);
                if (pageHeight - this.pageHeaderHeight > this.firstUserCellBottom) {
                    // The footer is above the bottom of the first user.
                    footerStyles.top = (pageHeight - this.footerRow.get(OFFSETHEIGHT)) + 'px';
                } else {
                    footerStyles.top = this.firstUserCellBottom;
                }
            } else {
                footerStyles.top = this.footerRowPosition + 'px';
                this.footerRow.removeClass(CSS.STICKYFOOTER);
            }
        }

        // Finally, apply the styles.
        this.assignmentHeadingContainer.setStyles(assignmentHeadingContainerStyles);
        this.userColumnHeader.setStyles(userColumnHeaderStyles);
        this.userColumn.setStyles(userColumnStyles);
        this.footerRow.setStyles(footerStyles);
    },

    /**
     * Process a size change Event on the window.
     *
     * @method _handleResizeEvent
     * @protected
     */
    _handleResizeEvent: function() {
        // Recalculate the position of the edge cells for scroll positioning.
        this._calculateCellPositions();

        // Simulate a scroll.
        this._handleScrollEvent();

        // Resize headers & footers.
        // This is an expensive operation, not expected to happen often.
        var headers = this.assignmentHeadingContainer.all(SELECTORS.HEADERCELL);
        var resizedcells = Y.all('#user-grades .heading .cell');

        var headeroffsetleft = this.headerCell.getX();
        var newcontainerwidth = 0;
        resizedcells.each(function(cell, idx) {
            var headercell = headers.item(idx);

            newcontainerwidth += cell.get(OFFSETWIDTH);
            var styles = {
                width: cell.getComputedStyle(WIDTH),
                left: cell.getX() - headeroffsetleft + 'px'
            };
            headercell.setStyles(styles);
        });

        var footers = Y.all('#gradebook-footer-container .gradebook-footer-cell');
        if (footers.size() !== 0) {
            var resizedavgcells = Y.all('#user-grades .avg .cell');

            resizedavgcells.each(function(cell, idx) {
                var footercell = footers.item(idx);
                var styles = {
                    width: cell.getComputedStyle(WIDTH),
                    left: cell.getX() - headeroffsetleft + 'px'
                };
                footercell.setStyles(styles);
            });
        }

        this.assignmentHeadingContainer.setStyle('width', newcontainerwidth);
    },

    /**
     * Determine the height of the specified Node.
     *
     * With IE, the height used when setting a height is the offsetHeight.
     * All other browsers set this as this inner height.
     *
     * @method _getHeight
     * @protected
     * @param {Node} node
     * @return String
     */
    _getHeight: function(node) {
        if (Y.UA.ie) {
            return node.get(OFFSETHEIGHT) + 'px';
        } else {
            return node.getComputedStyle(HEIGHT);
        }
    },

    /**
     * Determine the width of the specified Node.
     *
     * With IE, the width used when setting a width is the offsetWidth.
     * All other browsers set this as this inner width.
     *
     * @method _getWidth
     * @protected
     * @param {Node} node
     * @return String
     */
    _getWidth: function(node) {
        if (Y.UA.ie) {
            return node.get(OFFSETWIDTH) + 'px';
        } else {
            return node.getComputedStyle(WIDTH);
        }
    }
};

Y.Base.mix(Y.M.gradereport_grader.ReportTable, [FloatingHeaders]);


}, '@VERSION@', {"requires": ["base", "node", "event", "node-event-simulate"]});
