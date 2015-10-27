@users
Feature: Sign in
    In order to access to the backoffice
    As a webmaster
    I need to be able to log in to the backoffice

    Background:
        Given I am logged in as administrator

    Scenario: Log in with username and password
        Given I am on the backend homepage page
        Then I should see "Déconnexion"
