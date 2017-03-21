<?php
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
 * Custom form element to select file types and type groups.
 *
 * @package   assignsubmission_file
 * @copyright 2016 Jonathon Fowler <fowlerj@usq.edu.au>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

global $CFG;
require_once($CFG->dirroot.'/lib/form/group.php');

/**
 * Class for a custom form element to select file types and type groups.
 *
 * @package   assignsubmission_file
 * @category  form
 * @copyright 2016 Jonathon Fowler <fowlerj@usq.edu.au>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class MoodleQuickForm_assignsubmission_file_types extends MoodleQuickForm_group {

    /**
     * @var array The system selected file type choices.
     */
    private $filetypes;

    /**
     * Constructor
     *
     * @param string $elementName Element's name
     * @param mixed $elementLabel Label(s) for an element
     * @param mixed $attributes Either a typical HTML attribute string or an associative array
     */
    public function __construct($elementName = null, $elementLabel = null, $attributes = null) {
        HTML_QuickForm_element::__construct($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_appendName = true;
        $this->_type = 'assignsubmission_file_types';
        $this->_separator = '';

        $filetypes = get_config('assignsubmission_file', 'filetypes');
        if ($filetypes) {
            $this->filetypes = explode(';', $filetypes);
        } else {
            $this->filetypes = array();
        }
    }

    /**
     * Assemble the elements of the form control.
     */
    public function _createElements() {
        $attributes = $this->getAttributes();
        if (is_null($attributes)) {
            $attributes = array();
        }

        $this->_generateId();

        $this->_elements = array();

        $labelhtml = html_writer::div('', 'formctl_types',
            array('id' => $this->getAttribute('id') . '_label'));
        $this->_elements['label'] = $this->createFormElement('static', 'label', null, $labelhtml);
        $this->_elements['choose'] = $this->createFormElement('button', 'choose',
            get_string('formctl_types_choose', 'assignsubmission_file'),
            array(
                'id' => $this->getAttribute('id') . '_choose',
                'class' => 'formctl_types_choose',
            )
        );
        $this->_elements['value'] = $this->createFormElement('text', 'value', '',
            array(
                'id' => $this->getAttribute('id'),
                'class' => 'formctl_types_value',
            )
        );
    }

    /**
     * Return the valid selected groups or file types.
     *
     * @param array $submitValues submitted values
     * @param bool $assoc if true the retured value is associated array
     * @return array
     */
    public function exportValue(&$submitValues, $assoc = false) {
        $types = new assignsubmission_file\filetypes_helper($this->filetypes);
        $typegroups = $types->get_typegroups();
        $alltypes = $types->get_alltypes();

        $value = array();
        $formval = $this->_elements['value']->exportValue($submitValues[$this->getName()], false);
        if ($formval) {
            foreach (explode(';', $formval) as $type) {
                // Return only the groups and types we know of.
                if (isset($alltypes[$type])) {
                    $value[] = $type;
                } else if (isset($typegroups[$type])) {
                    if (!empty($typegroups[$type]->isoption)) {
                        $value[] = $type;
                    }
                }
            }
        }

        $value = implode(';', $value);
        if ($assoc) {
            return array($this->getName() => $value);
        }
        return $value;
    }

    /**
     * Accepts a renderer
     *
     * @param HTML_QuickForm_Renderer $renderer An HTML_QuickForm_Renderer object
     * @param bool $required Whether a group is required
     * @param string $error An error message associated with a group
     */
    public function accept(&$renderer, $required = false, $error = null) {
        global $PAGE;

        $strings = array('filetypewithexts', 'formctl_types_save', 'formctl_types_noselection');
        $PAGE->requires->js_call_amd(
            'assignsubmission_file/formctl_types',
            'initialise',
            array(
                $this->getAttribute('id'),
                $this->getLabel(),
                $this->filetypes
            )
        );

        return parent::accept($renderer, $required, $error);
    }

    /**
     * Called by HTML_QuickForm whenever form event is made on this element
     *
     * @param string $event Name of event
     * @param mixed $arg event arguments
     * @param object $caller calling object
     * @return bool
     */
    public function onQuickFormEvent($event, $arg, &$caller) {
        switch ($event) {
            case 'updateValue':
                $value = $this->_findValue($caller->_constantValues);
                if (null === $value) {
                    if ($caller->isSubmitted()) {
                        break;
                    }
                    $value = $this->_findValue($caller->_defaultValues);
                }
                $this->_createElementsIfNotExist();
                $this->_elements['value']->setValue($value);
                return true;
        }
        return parent::onQuickFormEvent($event, $arg, $caller);
    }
}
