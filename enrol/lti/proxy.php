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
 * The endpoint for a tool consumer requesting a tool proxy.
 *
 * @package    enrol_lti
 * @copyright  2016 John Okely <john@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/enrol/lti/ims-blti/blti.php');
require_once($CFG->dirroot . '/enrol/lti/ims-blti/OAuthBody.php');
require_once($CFG->libdir. "/filelib.php");

$filearguments = get_file_argument();
$arguments = explode('/', trim($filearguments, '/'));
if (count($arguments) == 2) { // Can put cartridge.xml at the end, or anything really.
    list($toolid, $token) = $arguments;
}

$toolid = optional_param('id', $toolid, PARAM_INT);
$token = optional_param('token', $token, PARAM_BASE64);

$messagetype = optional_param('lti_message_type', '', PARAM_TEXT);
$userid = optional_param('user_id', null, PARAM_INT);
$roles = optional_param('roles', null, PARAM_TEXT);
$tcprofileurl = optional_param('tc_profile_url', '', PARAM_URL);
$regkey = optional_param('reg_key', '', PARAM_URL);
$regpassword = optional_param('reg_password', '', PARAM_URL);
$launchpresentationreturnurl = optional_param('launch_presentation_return_url', '', PARAM_URL);

$PAGE->set_context(context_system::instance());

// Only show the cartridge if the token parameter is correct.
// If we do not compare with a shared secret, someone could very easily
// guess an id for the enrolment.
\enrol_lti\helper::verify_tool_token($toolid, $token);

// TODO As per the spec, don't we need to add this?
/*$query = http_build_query(array(
         'lti_version' => 'LTI-2p0'
    ));
$tcprofilerequest = $tcprofileurl . '?' . $query;*/

$curl = new curl();
$response = $curl->get($tcprofileurl);
$consumerprofile = json_decode($response);

switch ($messagetype) {
    case 'ToolProxyRegistrationRequest':
        $services = $consumerprofile->service_offered;
        $guid = $consumerprofile->guid;
        foreach ($services as $service) {
            if (in_array('application/vnd.ims.lti.v2.toolproxy+json', $service->format)) {
                $endpoint = $service->endpoint;
                $proxy = \enrol_lti\helper::get_proxy($toolid, $guid, $tcprofileurl);
                $response = sendOAuthBodyPOST('POST', $endpoint, $regkey, $regpassword, 'application/vnd.ims.lti.v2.toolproxy+json', $proxy);
                if (strpos($launchpresentationreturnurl, '?') !== false) { # TODO, do this better
                    $url = $launchpresentationreturnurl . '&';
                } else {
                    $url = $launchpresentationreturnurl . '?';
                }
                $url .= 'status=success';
                $url .= '&guid=' . urlencode($guid);
                echo '<a href="' . $url .  '">Register</a>';
            }
        }
        break;
    default:
        #throw new moodle_exception("Unsupported message type '$messagetype'");
        echo "Unsupported message type '$messagetype'";
        break;
}

