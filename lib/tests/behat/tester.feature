@javascript @current
Feature: tool_monitor_rule
  In order to manage rules
  As an admin
  I need to create a rule, edit a rule, duplicate a rule and delete a rule

  Scenario:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "admin"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Test assignment description |
      | Online text | 1 |
      | File submissions | 0 |
      | Use marking workflow | Yes |
      | Blind marking | Yes |
    And I navigate to "Event monitoring rules" node in "Site administration > Reports"
    And I am on site homepage
    And I follow "Course 1"
    And I navigate to "Logs" node in "Course administration > Reports"
    And I am on site homepage
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I navigate to "Edit settings" in current page administration
