Feature: check in and check out are monitored for illegal operations

  Scenario: Checking in more than once without checking out will be caught as an anomaly
    Given a building was registered
    And a user checked into the building
    When the user checks into the building
    Then an check-in anomaly was detected
