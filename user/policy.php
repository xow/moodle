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
 * This file is part of the User section Moodle
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

require_once('../config.php');
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/resourcelib.php');

$agree = optional_param('agree', 0, PARAM_BOOL);

$PAGE->set_url('/user/policy.php');
$PAGE->set_popup_notification_allowed(false);

if (!isloggedin()) {
    require_login();
}

if (isguestuser()) {
    $sitepolicyurl = get_config('core', 'sitepolicyguest');
    $sitepolicytext = get_config('core', 'sitepolicyguest_text');
    $sitesitepolicysource = get_config('core', 'sitepolicysourceguest');
} else {
    $sitepolicyurl = get_config('core', 'sitepolicy');
    $sitepolicytext = get_config('core', 'sitepolicy_text');
    $sitesitepolicysource = get_config('core', 'sitepolicysourceloggedin');
}

if (!empty($SESSION->wantsurl)) {
    $return = $SESSION->wantsurl;
} else {
    $return = $CFG->wwwroot.'/';
}

if ($agree and confirm_sesskey()) {    // User has agreed.
    if (!isguestuser()) {              // Don't remember guests.
        $DB->set_field('user', 'policyagreed', 1, array('id' => $USER->id));
    }
    $USER->policyagreed = 1;
    unset($SESSION->wantsurl);
    redirect($return);
}

$strpolicyagree = get_string('policyagree');
$strpolicyagreement = get_string('policyagreement');
$strpolicyagreementclick = get_string('policyagreementclick');

$PAGE->set_context(context_system::instance());
$PAGE->set_title($strpolicyagreement);
$PAGE->set_heading($SITE->fullname);
$PAGE->navbar->add($strpolicyagreement);

switch ($sitesitepolicysource) {
    case 0:
        // Nothing to agree to, sorry, hopefully we will not get to infinite loop.
        redirect($return);
        break;
    case 1:
        if (empty($sitepolicyurl)) {
            // Nothing to agree to, sorry, hopefully we will not get to infinite loop.
            redirect($return);
        }
        $mimetype = mimeinfo('type', $sitepolicyurl);
        if ($mimetype == 'document/unknown') {
            // Fallback for missing index.php, index.html.
            $mimetype = 'text/html';
        }

        // We can not use our popups here, because the url may be arbitrary, see MDL-9823.
        $clicktoopen = '<a href="' . $sitepolicyurl . '" onclick="this.target=\'_blank\'">' . $strpolicyagreementclick . '</a>';
        $sitepolicy = resourcelib_embed_general($sitepolicyurl, $strpolicyagreement, $clicktoopen, $mimetype);
        break;
    case 2:
        if (empty($sitepolicytext)) {
            // Nothing to agree to, sorry, hopefully we will not get to infinite loop.
            redirect($return);
        }
        $sitepolicy = format_text($sitepolicytext);
        break;
}

echo $OUTPUT->header();
echo $OUTPUT->heading($strpolicyagreement);

echo html_writer::div($sitepolicy, 'noticebox');

$formcontinue = new single_button(new moodle_url('policy.php', array('agree' => 1)), get_string('yes'));
$formcancel = new single_button(new moodle_url($CFG->wwwroot.'/login/logout.php', array('agree' => 0)), get_string('no'));
echo $OUTPUT->confirm($strpolicyagree, $formcontinue, $formcancel);

echo $OUTPUT->footer();

