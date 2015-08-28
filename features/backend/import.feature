@import
Feature: Approvals
    In order to see approvals
    As a webmaster
    I need to be able to upload a csv

    Background:
        Given I am logged in as administrator

    Scenario: Access import approval page
        Given I am on the backend import approvals page
        Then I should see "Valider"

