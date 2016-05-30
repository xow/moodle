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
 * The main entry point for the external system.
 *
 * @package    enrol_lti
 * @copyright  2016 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot . '/enrol/lti/ims-blti/blti.php');

$filearguments = get_file_argument();
$arguments = explode('/', trim($filearguments, '/'));
if (count($arguments) == 2) { // Can put cartridge.xml at the end, or anything really.
    list($toolid, $token) = $arguments;
}

$toolid = optional_param('id', $toolid, PARAM_INT);
$token = optional_param('token', $token, PARAM_BASE64);

// Only show the cartridge if the token parameter is correct.
// If we do not compare with a shared secret, someone could very easily
// guess an id for the enrolment.
\enrol_lti\helper::verify_tool_token($toolid, $token);

$tool = \enrol_lti\helper::get_lti_tool($toolid);
$name = \enrol_lti\helper::get_name($tool);
$description = \enrol_lti\helper::get_description($tool);
$secret = $tool->secret;
$vendorurl = new \moodle_url('/');
$vendorname = $SITE->fullname;
$vendorshortname = $SITE->shortname;
$vendordescription = trim(html_to_text($SITE->summary));
$guid = "TODO";

$toolproxy = <<<EOF
{
  "@context": "http://purl.imsglobal.org/ctx/lti/v2/ToolProxy",
  "@type": "ToolProxy",
  "lti_version": "LTI-2p0",
  "tool_profile": {
    "product_instance": {
      "guid": "$guid",
      "product_info": {
        "product_name": {
          "default_value": "$name"
        },
        "product_version": "1.0"
        "description": {
          "default_value": "$description"
        },
        "product_family": {
          "@id": "$vendorurl",
          "code": "$vendorshortname",
          "vendor": {
            "code": "$vendorshortname",
            "timestamp": "2016-05-30T15:28:16+08:00",
            "vendor_name": {
              "default_value": "$vendorname"
            },
            "description": {
              "default_value": "$vendordescription"
            }
          }
        }
      },
      "support": {
        "email": "support@moodle.org"
      },
    },
    "lti_version": "LTI-2p0",
    "message": [
      {
        "message_type": [
          "ToolProxyRegistrationRequest",
          "ToolProxyReregistrationRequest"
        ],
        "path": "__LAUNCH_REGISTRATION__",
        "parameter": [
          {
            "variable": "ToolConsumerProfile.url",
            "name": "tc_profile_url"
          }
        ]
      }
    ],
    "resource_handler": [
      {
        "resource_type": {
            "code" : "__REPLACE__urn:lti:ResourceType:acme.example.com/nitrolab/homework"
        },
        "message": [
          {
            "path": "__LAUNCH_PATH__",
            "parameter": [
              {
                "name": "theanswer",
                "fixed": "42"
              },
              {
                "name": "ltilink_custom_url",
                "variable": "LtiLink.custom.url"
              },
              {
                "name": "toolproxy_custom_url",
                "variable": "ToolProxy.custom.url"
              },
              {
                "name": "toolproxybinding_custom_url",
                "variable": "ToolProxyBinding.custom.url"
              },
              {
                "name": "result_url",
                "variable": "Result.url"
              },
              {
                "name": "person_email_primary",
                "variable": "Person.email.primary"
              },
              {
                "name": "person_name_full",
                "variable": "Person.name.full"
              },
              {
                "name": "person_name_given",
                "variable": "Person.name.given"
              },
              {
                "name": "person_name_family",
                "variable": "Person.name.family"
              },
              {
                "name": "user_id",
                "variable": "User.id"
              },
              {
                "name": "user_image",
                "variable": "User.image"
              },
              {
                "name": "membership_role",
                "variable": "Membership.role"
              }
            ],
            "message_type": "basic-lti-launch-request",
            "enabled_capability" : [ "User.id" ]
          }
        ],
        "name": {
          "default_value": "$name",
          "key": "resource.name"
        },
        "short_name": {
          "default_value": "$name",
          "key": "resource.name"
        },
        "description": {
          "default_value": "$description",
          "key": "resource.description"
        }
      }
    ],
    "base_url_choice": [
      {
        "selector": {
          "applies_to": [
            "IconEndpoint",
            "MessageHandler"
          ]
        },
        "secure_base_url": "$vendorurl",
        "default_base_url": "$vendorurl"
      }
    ]
  },
  "security_contract": {
    "shared_secret": "$secret",
    "tool_service": [
      {
        "@type": "RestService",
        "@id": "ltitcp:ToolProxy.collection",
        "service": "__TODOFROM_ID_INTC_PROFILE__http://localhost:4000/tools",
        "action": "POST",
        "format": "application/vnd.ims.lti.v2.ToolProxy+json"
      }
    ]
  }
}
EOF;
echo $toolproxy;
