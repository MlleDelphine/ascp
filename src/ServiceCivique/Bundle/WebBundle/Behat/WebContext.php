<?php

namespace ServiceCivique\Bundle\WebBundle\Behat;

use Behat\Mink\Exception\ElementNotFoundException;
use ServiceCivique\Bundle\CoreBundle\Behat\DefaultContext;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Web context.
 */
class WebContext extends DefaultContext
{
    /**
     * @Then /^the page title should be "([^""]*)"$/
     */
    public function thePageTitleShouldBe($title)
    {
        $this->assertSession()->elementTextContains('css', 'title', $title);
    }

    /**
     * @When /^I go to the website root$/
     */
    public function iGoToTheWebsiteRoot()
    {
        $this->getSession()->visit('/');
    }

    /**
     * @Given /^I am on the (.+) (page|step)?$/
     * @When /^I go to the (.+) (page|step)?$/
     */
    public function iAmOnThePage($page)
    {
        $this->getSession()->visit($this->generatePageUrl($page));
    }

    /**
     * @Then /^I should be on the ([^"]*) (page|step)$/
     * @Then /^I should be redirected to the ([^"]*) (page|step)$/
     * @Then /^I should still be on the ([^"]*) (page|step)$/
     * @Then /^I should be on the ([^"]*) (page|step) with ([^"]*) anchor$/
     */
    public function iShouldBeOnThePage($page, $param, $anchor = null)
    {
        $pageUrl = $this->generatePageUrl($page);
        if ($anchor) {
            $pageUrl .= "#" . $anchor;
        }

        $this->assertSession()->addressEquals($pageUrl);
        $this->assertStatusCodeEquals(200);
    }

    /**
     * @Then /^I should be on login page$/
     */
    public function iShouldBeOnLoginPage()
    {
        $this->assertSession()->addressEquals($this->generatePageUrl('fos_user_security_login'));
        $this->assertStatusCodeEquals(200);
    }

    /**
     * @Then /^I should be on registration page$/
     */
    public function iShouldBeOnRegistrationPage()
    {
        $this->assertSession()->addressEquals($this->generatePageUrl('fos_user_registration_register'));
        $this->assertStatusCodeEquals(200);
    }

    /**
     * @Given /^I am on the page of ([^""]*) with ([^""]*) "([^""]*)"$/
     * @Given /^I go to the page of ([^""]*) with ([^""]*) "([^""]*)"$/
     * @param string $property
     */
    public function iAmOnTheResourcePage($type, $property, $value)
    {
        $type = str_replace(' ', '_', $type);

        $resource = $this->findOneBy($type, array($property => $value));

        $this->getSession()->visit($this->generatePageUrl(sprintf('backend_%s_show', $type), array('id' => $resource->getId())));
    }

    /**
     * @Given /^I am on the page of ([^""(w)]*) "([^""]*)"$/
     * @Given /^I go to the page of ([^""(w)]*) "([^""]*)"$/
     */
    public function iAmOnTheResourcePageByName($type, $name)
    {
        $this->iAmOnTheResourcePage($type, 'name', $name);
    }

    /**
     * @Then /^I should be on the page of ([^""]*) with ([^""]*) "([^""]*)"$/
     * @Then /^I should still be on the page of ([^""]*) with ([^""]*) "([^""]*)"$/
     * @param string $property
     */
    public function iShouldBeOnTheResourcePage($type, $property, $value)
    {
        $type = str_replace(' ', '_', $type);
        $resource = $this->findOneBy($type, array($property => $value));

        $this->assertSession()->addressEquals($this->generatePageUrl(sprintf('backend_%s_show', $type), array('id' => $resource->getId())));
        $this->assertStatusCodeEquals(200);
    }

    /**
     * @Then /^I should be on the page of ([^""(w)]*) "([^""]*)"$/
     * @Then /^I should still be on the page of ([^""(w)]*) "([^""]*)"$/
     */
    public function iShouldBeOnTheResourcePageByName($type, $name)
    {
        $this->iShouldBeOnTheResourcePage($type, 'name', $name);
    }

    /**
     * @Given /^I am (building|viewing|editing) ([^""]*) with ([^""]*) "([^""]*)"$/
     * @param string $property
     */
    public function iAmDoingSomethingWithResource($action, $type, $property, $value)
    {
        $type = str_replace(' ', '_', $type);

        $action = str_replace(array_keys($this->actions), array_values($this->actions), $action);
        $resource = $this->findOneBy($type, array($property => $value));

        $this->getSession()->visit($this->generatePageUrl(sprintf('backend_%s_%s', $type, $action), array('id' => $resource->getId())));
    }

    /**
     * @Given /^I am (building|viewing|editing) ([^""(w)]*) "([^""]*)"$/
     */
    public function iAmDoingSomethingWithResourceByName($action, $type, $name)
    {
        $this->iAmDoingSomethingWithResource($action, $type, 'name', $name);
    }

    /**
     * @Then /^I should be (building|viewing|editing) ([^"]*) with ([^"]*) "([^""]*)"$/
     * @param string $property
     */
    public function iShouldBeDoingSomethingWithResource($action, $type, $property, $value)
    {
        $type = str_replace(' ', '_', $type);

        $action = str_replace(array_keys($this->actions), array_values($this->actions), $action);
        $resource = $this->findOneBy($type, array($property => $value));

        $this->assertSession()->addressEquals($this->generatePageUrl(sprintf('sylius_backend_%s_%s', $type, $action), array('id' => $resource->getId())));
        $this->assertStatusCodeEquals(200);
    }

    /**
     * @Then /^I should be (building|viewing|editing) ([^""(w)]*) "([^""]*)"$/
     */
    public function iShouldBeDoingSomethingWithResourceByName($action, $type, $name)
    {
        $this->iShouldBeDoingSomethingWithResource($action, $type, 'name', $name);
    }

    /**
     * @Then /^(?:.* )?"([^"]*)" should appear on the page$/
     */
    public function textShouldAppearOnThePage($text)
    {
        $this->assertSession()->pageTextContains($text);
    }

    /**
     * @Then /^(?:.* )?"([^"]*)" should not appear on the page$/
     */
    public function textShouldNotAppearOnThePage($text)
    {
        $this->assertSession()->pageTextNotContains($text);
    }

    /**
     * @When /^I click "([^"]+)"$/
     */
    public function iClick($link)
    {
        $this->clickLink($link);
    }

    /**
     * @Given /^I fill in province name with "([^"]*)"$/
     */
    public function iFillInProvinceNameWith($value)
    {
        $this->fillField('sylius_country[provinces][0][name]', $value);
    }

    /**
     * @Given /^I fill in the (billing|shipping) address to (.+)$/
     */
    public function iFillInCheckoutAddress($type, $country)
    {
        $base = sprintf('sylius_checkout_addressing[%sAddress]', $type);

        $this->iFillInAddressFields($base, $country);
    }

    /**
     * @Given /^I fill in the users (billing|shipping) address to (.+)$/
     */
    public function iFillInUserAddress($type, $country)
    {
        $base = sprintf('%s[%sAddress]', 'sylius_user', $type);
        $this->iFillInAddressFields($base, $country);
    }

    /**
     * @Given /^I fill in the users account address to (.+)$/
     */
    public function iFillInUserAccountAddress($country)
    {
        $this->iFillInAddressFields('sylius_address', $country);
    }

    /**
     * @param string $base
     */
    protected function iFillInAddressFields($base, $country)
    {
        $this->fillField($base.'[firstName]', 'John');
        $this->fillField($base.'[lastName]', 'Doe');
        $this->fillField($base.'[street]', 'Pvt. Street 15');
        $this->fillField($base.'[city]', 'Lodz');
        $this->fillField($base.'[postcode]', '95-253');
        $this->selectOption($base.'[country]', $country);
    }

    /**
     * @Given /^I select the "(?P<field>([^""]|\\")*)" radio button$/
     */
    public function iSelectTheRadioButton($field)
    {
        $field = str_replace('\\"', '"', $field);
        $radio = $this->getSession()->getPage()->findField($field);

        if (null === $radio) {
            throw new ElementNotFoundException(
                $this->getSession(), 'form field', 'id|name|label|value', $field
            );
        }

        $this->fillField($radio->getAttribute('name'), $radio->getAttribute('value'));
    }

    /**
     * @Given /^I should see an? "(?P<element>[^"]*)" element near "([^"]*)"$/
     */
    public function iShouldSeeAElementNear($element, $value)
    {
        $tr = $this->assertSession()->elementExists('css', sprintf('table tbody tr:contains("%s")', $value));
        $this->assertSession()->elementExists('css', $element, $tr);
    }

    /**
     * @When /^I click "([^"]*)" near "([^"]*)"$/
     * @When /^I press "([^"]*)" near "([^"]*)"$/
     */
    public function iClickNear($button, $value)
    {
        $tr = $this->assertSession()->elementExists('css', sprintf('table tbody tr:contains("%s")', $value));

        $locator = sprintf('button:contains("%s")', $button);

        if ($tr->has('css', $locator)) {
            $tr->find('css', $locator)->press();
        } else {
            $tr->clickLink($button);
        }
    }

    /**
     * @Then /^I should see "([^"]*)" field error$/
     */
    public function iShouldSeeFieldError($field)
    {
        $this->assertSession()->elementExists('xpath', sprintf(
            "//div[contains(@class, 'error')]//label[text()[contains(., '%s')]]", ucfirst($field)
        ));
    }

    /**
     * @Given /^I should see (\d+) validation errors$/
     */
    public function iShouldSeeFieldsOnError($amount)
    {
        $this->assertSession()->elementsCount('css', '.form-error', $amount);
    }

    /**
     * @Given /^I leave "([^"]*)" empty$/
     * @Given /^I leave "([^"]*)" field blank/
     */
    public function iLeaveFieldEmpty($field)
    {
        $this->fillField($field, '');
    }

    /**
     * For example: I should see product with name "Wine X" in that list.
     *
     * @Then /^I should see [\w\s]+ with [\w\s]+ "([^""]*)" in (that|the) list$/
     */
    public function iShouldSeeResourceWithValueInThatList($value)
    {
        $this->assertSession()->elementTextContains('css', 'table', $value);
    }

    /**
     * For example: I should not see product with name "Wine X" in that list.
     *
     * @Then /^I should not see [\w\s]+ with [\w\s]+ "([^""]*)" in (that|the) list$/
     */
    public function iShouldNotSeeResourceWithValueInThatList($value)
    {
        $this->assertSession()->elementTextNotContains('css', 'table', $value);
    }

    /**
     * For example: I should see 10 products in that list.
     *
     * @Then /^I should see (\d+) ([^""]*) in (that|the) list$/
     */
    public function iShouldSeeThatMuchResourcesInTheList($amount, $type)
    {
        if (1 === count($this->getSession()->getPage()->findAll('css', 'table'))) {
            $this->assertSession()->elementsCount('css', 'table tbody > tr', $amount);
        } else {
            $this->assertSession()->elementsCount('css', sprintf('table#%s tbody > tr', str_replace(' ', '-', $type)), $amount);
        }
    }

    /**
     * For example: I should see 10 missions.
     *
     * @Then /^I should see there (\d+) missions/
     */
    public function iShouldSeeThatMuchMissions($amount)
    {
        $this->assertSession()->elementsCount('css', '.mission', $amount);
    }

    /**
     * @Given /^I am on the mission page for "([^"]*)"$/
     * @Given /^I go to the mission page for "([^"]*)"$/
     */
    public function iAmOnTheMissionPage($title)
    {
        $mission = $this->findOneBy('mission', array('title' => $title));

        $this->getSession()->visit($this->generatePageUrl(
            'mission_show',
            array('slug' => $mission->getSlug())
        ));
    }

    /**
     * @Given /^I am on the organization mission edit page for "([^"]*)"$/
     * @Given /^I go to the organization mission edit page for "([^"]*)"$/
     */
    public function iAmOnTheOrganizationMissionEditPage($title)
    {
        $mission = $this->findOneBy('mission', array('title' => $title));

        $this->getSession()->visit($this->generatePageUrl(
            'organization_missions_update',
            array('id' => $mission->getId())
        ));
    }

    /**
     * @Then /^I should be on the organization mission edit page for "([^"]*)"$/
     */
    public function iShouldBeOnTheOrganizationMissionEditPage($title)
    {
        $this->iAmOnTheOrganizationMissionEditPage($title);

        $this->assertStatusCodeEquals(200);
    }

    /**
     * @Then /^I should be on the mission page for "([^"]*)"$/
     * @Then /^I should still be on the mission page for "([^"]*)"$/
     */
    public function iShouldBeOnTheMissionPage($title)
    {
        $this->iAmOnTheMissionPage($title);

        $this->assertStatusCodeEquals(200);
    }

    /**
     * @Given /^I am not authenticated$/
     * @Given /^I am not logged in anymore$/
     */
    public function iAmNotAuthenticated()
    {
        $this->getSecurityContext()->setToken(null);
        $this->getContainer()->get('session')->invalidate();
    }

    /**
     * @Given /^I log in with "([^""]*)" and "([^""]*)"$/
     */
    public function iLogInWith($email, $password)
    {
        $this->getSession()->visit($this->generatePageUrl('fos_user_security_login'));

        $this->fillField('Email', $email);
        $this->fillField('Password', $password);
        $this->pressButton('login');
    }

    /**
     * @Then /^I should be logged in$/
     */
    public function iShouldBeLoggedIn()
    {
        if (!$this->getSecurityContext()->isGranted('ROLE_USER')) {
            throw new AuthenticationException('User is not authenticated.');
        }
    }

    /**
     * @Then /^I should not be logged in$/
     */
    public function iShouldNotBeLoggedIn()
    {
        if ($this->getSecurityContext()->isGranted('ROLE_USER')) {
            throw new AuthenticationException('User was not expected to be logged in, but he is.');
        }
    }

    /**
     * @Given /^I click "([^"]*)" from the confirmation modal$/
     */
    public function iClickOnConfirmationModal($button)
    {
        $this->assertSession()->elementExists('css', '#confirmation-modal');

        $modalContainer = $this->getSession()->getPage()->find('css', '#confirmation-modal');
        $primaryButton = $modalContainer->find('css', sprintf('a:contains("%s")' ,$button));

        $this->getSession()->wait(100);

        if (!preg_match('/in/', $modalContainer->getAttribute('class'))) {
            throw new \Exception('The confirmation modal was not opened...');
        }

        $this->getSession()->wait(100);

        $primaryButton->press();
    }

    /**
     * Assert that given code equals the current one.
     *
     * @param integer $code
     */
    protected function assertStatusCodeEquals($code)
    {
        $this->assertSession()->statusCodeEquals($code);
    }
}
