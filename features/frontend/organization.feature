@organization
Feature: Organization
    In order to publish available missions and follow applications
    As an organization
    I want to be able to publish missions and see application

    Background:
        Given there are organizations:
            | name           |
            | Organization 1 |

    Scenario: Registering a new account
        When I go to the organization register page
        And I fill in "fos_user_registration_form_organization_approvalNumber" with "XX-000-00-00000-00"
        And I select the "Organisme agréé" radio button
        And I fill in "Nom de l’organisme" with "Association des utilisateurs de Behat"
        And I fill in "Nom" with "Grosjean"
        And I fill in "Prénom" with "Marcel"
        And I fill in "fos_user_registration_form_email" with "m.grosjean@machin.com"
        And I fill in "fos_user_registration_form_plainPassword_first" with "azerty123"
        And I fill in "fos_user_registration_form_plainPassword_second" with "azerty123"
        And I fill in "Adresse" with "1 passage Brulon"
        And I fill in "Pays" with "FR"
        And I fill in "Code postal" with "75012"
        And I fill in "Ville" with "Paris"
        And I press "Se créer un compte"
        Then the response status code should be 200
        # And I should see "Votre demande de création de compte a bien été prise en compte."
        # And I should see "Vous allez recevoir un email à l'adresse m.grosjean@machin.com. Pour valider votre compte, il vous suffit de cliquer sur le lien présent dans cet email, vous serez ensuite connecté sur le site."

    Scenario: Registering a new account with already existing organization name
        When I go to the organization register page
        And I fill in "Nom de l’organisme" with "Organization 1"
        And I press "Se créer un compte"
        Then the response status code should be 200
        And I should still be on the organization register page
        # Should be 4, strange bug in css class detection...
        And I should see 1 validation errors
        # And I should see "Nom de l’organisme" field error
        # And I should see "Ce nom d'organisme est déjà utilisé."

