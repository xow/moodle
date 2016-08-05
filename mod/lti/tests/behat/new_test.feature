@core @weirdlogintest
Feature: Alternate login parallel behat bug test

  Background:
    Given I log in as "admin"
    And I navigate to "Manage authentication" node in "Site administration > Plugins > Authentication"
    And I set the field "Alternate login URL" to "test/redirect.php"
    And I press "Save changes"
    And I log out

  @javascript
  Scenario: Javascript on
    When I follow "Log in"

  @javascript
  Scenario: Javascript on 2
    When I follow "Log in"
    Then I should see "asdfasdfsdasdf"
    Then I should see "asdfasdfsdasdf"
