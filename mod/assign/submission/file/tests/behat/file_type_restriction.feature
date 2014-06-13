@mod @mod_assign @assignsubmission_file
Feature: In an assignment, limit submittable file types
  In order to constrain student submissions for marking
  As a teacher
  I need to limit the submittable file types

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following config values are set as admin:
      | filetypes | image/png;spreadsheet | assignsubmission_file |

  @javascript
  Scenario: Configuring permitted file types for an assignment
    Given the following "activities" exist:
      | activity | course | idnumber | name                 | intro                       | duedate    | assignsubmission_onlinetext_enabled | assignsubmission_file_enabled | assignsubmission_file_maxfiles | assignsubmission_file_maxsizebytes | assignsubmission_file_restricttypes |
      | assign   | C1     | assign1  | Test assignment name | Test assignment description | 1388534400 | 0                                   | 1                             | 1                              | 0                                  | 0                                   |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I click on "Edit settings" "link" in the "Administration" "block"
    When I set the field "assignsubmission_file_restricttypes" to "Yes"
    And I click on "assignsubmission_file_filetypes[choose]" "button"
    And I click on "Image (PNG)" "checkbox"
    And I click on "Spreadsheet files" "checkbox"
    And I press "Save choices"
    And I press "Save and display"
    And I click on "Edit settings" "link" in the "Administration" "block"
    Then the field "assignsubmission_file_restricttypes" matches value "Yes"
    And the field "assignsubmission_file_filetypes[value]" matches value "image/png;spreadsheet"
    And I should see "Image (PNG)" in the ".assignsubmission_file_label" "css_element"
    And I should see "Spreadsheet files" in the ".assignsubmission_file_label" "css_element"

  @javascript @_file_upload
  Scenario: Uploading permitted file types for an assignment
    Given the following "activities" exist:
      | activity | course | idnumber | name                 | intro                       | duedate    | assignsubmission_onlinetext_enabled | assignsubmission_file_enabled | assignsubmission_file_maxfiles | assignsubmission_file_maxsizebytes | assignsubmission_file_restricttypes | assignsubmission_file_filetypes |
      | assign   | C1     | assign1  | Test assignment name | Test assignment description | 1388534400 | 0                                   | 1                             | 2                              | 0                                  | 1                                   | image/png;spreadsheet           |
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    When I press "Add submission"
    And I upload "lib/tests/fixtures/gd-logo.png" file to "File submissions" filemanager
    And I upload "lib/tests/fixtures/tabfile.csv" file to "File submissions" filemanager
    And I press "Save changes"
    Then "gd-logo.png" "link" should exist
    And "tabfile.csv" "link" should exist
