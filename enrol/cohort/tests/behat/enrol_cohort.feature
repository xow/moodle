@enrol @enrol_cohort @javascript
Feature: Enrolments are synchronised with cohorts
  In order to simplify enrolments of cohorts
  As a teacher
  I need to be able to set up cohort sync

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@asd.com |
      | student2 | Student | 2 | student2@asd.com |
      | student3 | Student | 3 | student3@asd.com |
      | student4 | Student | 4 | student4@asd.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1C1 |
    And the following "cohorts" exist:
      | name          | idnumber |
      | Cohort 1      | CH1      |
      | Cohort 2      | CH1      |
    And I log in as "admin"
    And I am on course index

  Scenario: Add two enrolment instances with the "Add method and create another" button
    When I follow "Course 1"
    And I navigate to "Enrolment methods" node in "Course administration > Users"
    And I select "Cohort sync" from the "Add method" singleselect
    And I set the field "Cohort" to "Cohort 1"
    And I press "Add method and create another"
    And I should see "Method added"
    And I set the field "Cohort" to "Cohort 2"
    And I press "Add method"
    And I should see "Enrolment methods"
    And I should see "Cohort sync (Cohort 1 - Student)"
    And I should see "Cohort sync (Cohort 2 - Student)"
