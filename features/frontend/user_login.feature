@users
Feature: Sign in
    In order to view my mission list
    As a visitor
    I need to be able to log in to the store

    Background:
        Given there are organizations:
            | name           |
            | Organization 1 |
        And there are following users:
            | email       | password | enabled | organization   |
            | bar@foo.com | foo      | yes     | Organization 1 |

    Scenario: Log in with username and password as organization
        Given I am on the homepage
         When I fill in the following:
            | Email        | bar@foo.com |
            | Mot de passe | foo         |
          And I press "_submit"
         Then I should see "Se déconnecter"
          And I should see "Actualiser mon compte"
          And I should see "Voir mes missions"
