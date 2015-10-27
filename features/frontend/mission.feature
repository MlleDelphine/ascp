@mission
Feature: Mission
    In order find a mission
    As a jeune
    I want to be able to browse missions

    Background:
        Given there are following taxonomies defined:
            | name     |
            | thématique |
        And taxonomy "thématique" has following taxons:
            | Culture et loisirs                              |
            | Développement international et aide humanitaire |
            | Éducation pour tous                             |
            | Environnement                                   |
            | Interventions d'urgence en cas de crise         |
            | Mémoire et citoyenneté                          |
            | Santé                                           |
            | Solidarité                                      |
            | Sport                                           |
        And there are organizations:
            | name      |
            | Organization 1 |
            | Organization 2 |
            | Organization 3 |
            | Organization 4 |
            | Organization 5 |
        And there are missions:
            | title     | departement | area  | description      | startDate  | taxons                  | organization    | status    | archived | is_overseas | country |
            | Mission 1 | 75          | 11    | Lorem ipsum      | 2110-02-01 | Culture et loisirs      | Organization 1  | available | 0        | 0           |  FR     |
            | Mission 2 | 75          | 11    | Lorem ipsum      | 2111-02-01 | Santé                   | Organization 2  | available | 0        | 0           |  FR     |
            | Mission 3 | 78          | 11    | Lorem ipsum      | 2112-02-01 | Sport                   | Organization 3  | available | 0        | 0           |  FR     |
            | Mission 4 | 08          | 21    | Lorem ipsum      | 2113-02-01 | Solidarité              | Organization 4  | available | 0        | 0           |  FR     |
            | Mission 5 | 10          | 21    | Ceci est un test | 2114-02-01 | Mémoire et citoyenneté  | Organization 5  | available | 0        | 0           |  FR     |
            | Mission 6 | 10          | 21    | Ceci est un test | 2114-02-01 | Mémoire et citoyenneté  | Organization 5  | available | 1        | 0           |  FR     |
            | Mission 7 |             |       | Ceci est un test | 2114-02-01 | Mémoire et citoyenneté  | Organization 5  | available | 0        | 1           |  DE     |
    Scenario: Viewing the mission list
        When I wait for elastic search "yellow" status
        And I go to the mission list page
        Then I should see there 5 missions
        And I should see "Mission 2"

    Scenario: Viewing a mission page
        When I go to the mission page for "Mission 1"
        Then I should be on the mission page for "Mission 1"
        Then I should see "Lorem ipsum"

    Scenario: Viewing an archived mission page
        When I go to the mission page for "Mission 6"
        Then the response status code should be 404

    Scenario: Submitting search form
        When I wait for elastic search "yellow" status
        And I go to the mission list page
        And I press "Rechercher"
        Then I should be on the mission list page with search-options anchor
        And I should see there 5 missions

    Scenario: Resubmitting search form
        When I wait for elastic search "yellow" status
        And I go to the mission list page
        And I fill in "Pays" with "DE"
        And I press "Rechercher"
        And I fill in "Pays" with ""
        And I press "Rechercher"
        Then I should be on the mission list page with search-options anchor
        And I should see there 5 missions

    Scenario: Searching missions by taxon criteria
        When I wait for elastic search "yellow" status
        And I go to the mission list page
        And I check "Culture et loisirs"
        And I check "Santé"
        And I press "Rechercher"
        Then I should be on the mission list page with search-options anchor
        And I should see there 2 missions

    Scenario: Searching overseas missions by country criteria
        When I wait for elastic search "yellow" status
        And I go to the mission list page
        And I fill in "Pays" with "DE"
        And I select the "À l'étranger" radio button
        And I press "Rechercher"
        Then I should be on the mission list page with search-options anchor
        And I should see there 1 missions

    Scenario: Searching missions by start_date criteria
        When I wait for elastic search "yellow" status
        And I go to the mission list page
        And I fill in "Quand ?" with "2112-02-01"
        And I press "Rechercher"
        Then I should see there 3 missions

    Scenario: Searching missions by department criteria
        When I wait for elastic search "yellow" status
        And I go to the mission list page
        And I fill in "Département" with "75"
        And I press "Rechercher"
        Then the "Région" field should contain "11"
        And I should see there 2 missions

    Scenario: Searching missions by region criteria
        When I wait for elastic search "yellow" status
        And I go to the mission list page
        And I fill in "Région" with "11"
        And I press "Rechercher"
        Then I should see there 3 missions

    Scenario: Searching missions by query criteria
        When I wait for elastic search "yellow" status
        And I go to the mission list page
        And I fill in "Contient le(s) mot(s)" with "test"
        And I press "Rechercher"
        Then I should see there 1 missions

