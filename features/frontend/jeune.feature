@jeune
Feature: Jeune
    In order to find a mission
    As a jeune
    I want to be able to apply

    Scenario: Registering a new account
        When I go to the fos user registration register page
        And I select the "Monsieur" radio button
        And I fill in "Nom" with "Loiseau"
        And I fill in "Prénom" with "Kévin"
        And I fill in "fos_user_registration_form_email" with "kevin.l@example.com"
        And I fill in "fos_user_registration_form_plainPassword_first" with "azerty123"
        And I fill in "fos_user_registration_form_plainPassword_second" with "azerty123"
        And I fill in "fos_user_registration_form_profile_birthDate_day" with "1"
        And I fill in "fos_user_registration_form_profile_birthDate_month" with "2"
        And I fill in "fos_user_registration_form_profile_birthDate_year" with "1999"
        And I select "En recherche de mission" from "Type"
        And I press "Se créer un compte"
        Then the response status code should be 200
        And I should see "Votre demande de création de compte a bien été prise en compte."
        And I should see "Vous allez recevoir un email à l'adresse kevin.l@example.com. Pour valider votre compte, il suffit de cliquer sur le lien dans l'email, vous serez ensuite connecté sur le site."


