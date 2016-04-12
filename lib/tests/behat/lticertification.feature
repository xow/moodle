@local @lti_cert
Feature: IMS LTI Certification
  In order to make moodle awesome
  As a developer
  I need to be able to get behat to do all my hard work

  @javascript
  Scenario: Go to the certification site
    Given I go to url "https://www.imsglobal.org/lti/cert/index.php"
    And I press "Process for Tool Consumers"
    And I set the field "Username" to "MartinDougiamas"
    And I set the field "Password" to "moodle123!"
    And I press "Log in"
    And I press "Go to Configuration »"
    And I set the field "name" to "Moodle"
    And I set the field "version" to "3.1"
    And I press "Save settings"
    And I press "Go to Results »"
    When I log in as "admin"
    And I navigate to "Manage external tool registrations" node in "Site administration > Plugins > Activity modules > LTI"
    And I follow "Configure a new external tool registration"
    And I set the following fields to these values:
      | Tool provider name | IMS LTI Certification Registration |
      | Registration URL   | https://www.imsglobal.org/lti/cert/tc_tool.php |
      | Capabilities | basic-lti-launch-request,Context.id,CourseSection.label,CourseSection.longDescription,CourseSection.sourcedId,CourseSection.timeFrame.begin,CourseSection.title,Membership.role,Person.address.country,Person.address.locality,Person.address.street1,Person.address.timezone,Person.email.primary,Person.name.family,Person.name.full,Person.name.given,Person.name.middle,Person.phone.mobile,Person.phone.primary,Person.sourcedId,Person.webaddress,ResourceLink.description,ResourceLink.id,ResourceLink.title,Result.autocreate,Result.sourcedId,User.id,User.username |
      | Services     | Memberships,Tool Consumer Profile,Tool Proxy,Tool Settings |
    And I press "Save changes"
    And I follow "Register"
    And I switch to "contentframe" iframe
    And I should not see "Failed"
    And I go to url "https://www.imsglobal.org/lti/cert/index.php"
    And I press "Run test"
    And I should not see "Failed"
    And I pause
    And I go home
    And I navigate to "Manage external tool types" node in "Site administration > Plugins > Activity modules > LTI"
    And I follow "Add external tool configuration"
    And I set the following fields to these values:
      | Tool name     | IMS LTI Certification Type |
      | Tool base URL | https://www.imsglobal.org/lti/cert/tc_tool.php?x=With%20Space&y=yes |
      | Consumer key  | 0c9cea31-00cc-4e3c-8f04-69f4d331e8f0 |
      | Shared secret | 570c622e150d4 |
    And I set the field "Custom parameters" to multiline
    """
    simple_key=custom_simple_value
    Complex!@#$^*(){}[]KEY=Complex!@#$^*;(){}[]½Value
    cert_userid=$User.id
    cert_username=$User.username
    tc_profile_url=$ToolConsumerProfile.url
    """
    And I press "Save changes"
