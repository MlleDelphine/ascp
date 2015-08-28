@organization2 @mission
Feature: Organization
    In order to publish available missions and follow applications
    As an organization
    I want to be able to publish missions and see application

    Background:
        Given there are following taxonomies defined:
            | name     |
            | thématique |
        And taxonomy "thématique" has following taxons:
            | cat1 |
        And there are organizations:
            | name      |
            | Organization 1 |
            | Organization 2 |
        And there are missions:
            | title     | departement | area  | description      | taxons   | startDate  | organization    | status    | archived | country |
            | Mission 1 | 75          | 11    | Lorem ipsum      | cat1     | 2110-02-01 | Organization 1  | available | 0        | FR      |
            | Mission 2 | 75          | 11    | Lorem ipsum      | cat1     | 2110-02-01 | Organization 2  | available | 0        | FR      |
            | Mission 3 | 75          | 11    | Lorem ipsum      | cat1     | 2110-02-01 | Organization 1  | draft     | 0        | FR      |

    Scenario: View list of organization mission
        Given I am logged in as "Organization 1" organization user
        And I wait for elastic search "yellow" status
        When I go to the organization mission index page
        Then I should see mission with title "Mission 1" in the list
        And I should not see mission with title "Mission 2" in that list

    Scenario: Organization user can create a new mission
        Given I am logged in as "Organization 1" organization user
        And I wait for elastic search "yellow" status
        And I go to the organization mission index page
        And I follow "Créer une mission"
        When I fill in "Titre de la mission" with "Ma nouvelle mission"
        And I select the "Un organisme existant" radio button
#        And I fill in "Nom de l’organisme d’accueil" with "Nom de mon organisme"
        And I fill in "Numéro d’agrément" with "AB-123-12-12345-12"
        And I fill in "Date de début" with "2114-12-12"
        And I select the "En France" radio button
        And I fill in "Région" with "11"
        And I fill in "Département" with "75"
        And I fill in "Présentation de la mission" with "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean non rhoncus nibh. Maecenas id fermentum urna. Suspendisse potenti. Quisque ut tempor lectus. Sed fermentum risus id erat pellentesque, sit amet consectetur libero aliquam. Aliquam ultrices diam id turpis fermentum condimentum. Suspendisse ullamcorper nibh sed mi tincidunt bibendum. Vivamus vitae malesuada odio. Maecenas faucibus malesuada dui quis auctor. Curabitur enim felis, euismod sit amet turpis et, luctus viverra ex.Nulla laoreet vitae mauris eget tristique. Nullam vitae nibh viverra, porttitor tortor at, finibus enim. Nulla facilisi. Aliquam laoreet ultricies odio, id porttitor libero egestas interdum. Duis risus tortor, tempor sed ante ut, facilisis dapibus ipsum. Ut rutrum risus id sem gravida, a lobortis neque aliquam. Mauris eu purus mi. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas."
        And I fill in "Nom du contact" with "Jean-Claude Martin"
        And I fill in "Adresse" with "8 rue de la République"
        And I fill in "Code postal" with "75001"
        And I fill in "Ville" with "PARIS 01"
        And I press "Continuer"
        And I follow "Annuler"
        And I fill in "Titre de la mission" with "Ma nouvelle mission corrigée"
        And I press "Continuer"
        And I follow "Envoyer pour validation"
        Then I should be on the organization mission index page
        And I should see mission with title "Ma nouvelle mission corrigée" in the list

    Scenario: Organization user can create a new mission draft
        Given I am logged in as "Organization 1" organization user
        When I go to the organization mission index page
        And I follow "Créer une mission"
        And I fill in "Titre de la mission" with "Ma mission (brouillon)"
        And I select the "Un organisme existant" radio button
#        And I fill in "Nom de l’organisme d’accueil" with "Nom de mon organisme"
        And I fill in "Numéro d’agrément" with "AB-123-12-12345-12"
        And I fill in "Date de début" with "2114-12-12"
        And I select the "En France" radio button
        And I fill in "Région" with "11"
        And I fill in "Département" with "75"
        And I fill in "Présentation de la mission" with "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean non rhoncus nibh. Maecenas id fermentum urna. Suspendisse potenti. Quisque ut tempor lectus. Sed fermentum risus id erat pellentesque, sit amet consectetur libero aliquam. Aliquam ultrices diam id turpis fermentum condimentum. Suspendisse ullamcorper nibh sed mi tincidunt bibendum. Vivamus vitae malesuada odio. Maecenas faucibus malesuada dui quis auctor. Curabitur enim felis, euismod sit amet turpis et, luctus viverra ex.Nulla laoreet vitae mauris eget tristique. Nullam vitae nibh viverra, porttitor tortor at, finibus enim. Nulla facilisi. Aliquam laoreet ultricies odio, id porttitor libero egestas interdum. Duis risus tortor, tempor sed ante ut, facilisis dapibus ipsum. Ut rutrum risus id sem gravida, a lobortis neque aliquam. Mauris eu purus mi. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas."
        And I fill in "Nom du contact" with "Jean-Claude Martin"
        And I fill in "Adresse" with "8 rue de la République"
        And I fill in "Code postal" with "75001"
        And I fill in "Ville" with "PARIS 01"
        And I press "Enregistrer en brouillon"
        And I wait for elastic search "yellow" status
        Then I should be on the organization mission index page
        And I should see mission with title "Ma mission (brouillon)" in the list

    Scenario: Organization user can publish a mission draft
        Given I am logged in as "Organization 1" organization user
        And I am on the organization mission edit page for "Mission 3"
        And I wait for elastic search "yellow" status
        When I fill in "Titre de la mission" with "Modification du titre de la mission 3"
        And I select the "Un organisme existant" radio button
        And I press "Continuer"
        # And I follow "Annuler"
        Then I should see "Modification du titre de la mission 3"

    # Todo
    #Scenario: Organization user can edit a mission, view a preview and cancel modifications
     #   Given I am logged in as "Organization 1" organization user
      #  And I am on the organization mission edit page for "Mission 3"
       # And I wait for elastic search "yellow" status
        #When I press "Continuer"
       # # And I follow "Valider"
        #Then I should be on the organization mission index page

    Scenario: Organization user can delete his own missions
        Given I am logged in as "Organization 1" organization user
        And I wait for elastic search "yellow" status
        When I go to the mission page for "Mission 1"
        Then I should be on the mission page for "Mission 1"
        And I should see a "#mission-delete-btn" element

    Scenario: Organization user can't delete other organization missions
        Given I am logged in as "Organization 1" organization user
        And I wait for elastic search "yellow" status
        When I go to the mission page for "Mission 2"
        Then I should be on the mission page for "Mission 2"
        And I should not see a "#mission-delete-btn" element

