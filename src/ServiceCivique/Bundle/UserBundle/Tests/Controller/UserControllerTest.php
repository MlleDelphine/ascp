<?php

namespace ServiceCivique\Bundle\UserBundle\Tests\Controller;

use ServiceCivique\Bundle\CoreBundle\Entity\Profile;
use ServiceCivique\Bundle\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;
    /**
     * @var \ServiceCivique\Bundle\UserBundle\Entity\User
     */
    protected $user;

    protected $keepUserInDatabase = true;

    protected function setUp()
    {
        // On créé le client
        $client       = static::createClient();
        $this->client = $client;
        $this->user   = new User();
        $this->user->setEmail('sophie.durand@mail.test.com');
        $this->user->setFirstname('Sophie');
        $this->user->setLastname('Durand');
        $this->user->setPlainPassword('monmotdepasse');
        $profile = new Profile();
        $this->user->setProfile($profile);
        $this->user->getProfile()->setBirthDate(new \DateTime('1996-03-19 12:00:00'));
        $this->removeTestUserByEmail($this->user->getEmail());
    }

    protected function tearDown()
    {
        if (!$this->keepUserInDatabase) {
            $this->removeTestUserByEmail($this->user->getEmail());
        }
    }

    public function testSignupAsYouth()
    {
        // On dirige le crawler sur la page de création de compte pour un organisme
        $crawler = $this->client->request('GET', '/jeunes/register/');
        // On teste si la réponse est 200
        $this->assertTrue($this->client->getResponse()->isOk());
        // On vérifie qu'on est bien sur la page d'inscription comme jeune
        $this->assertSame(
            'Création compte jeune et connexion',
            trim($crawler->filter('#main-content h1.title span')->text())
        );
        // On remplit et on soumet le formulaire avec les données de $user
        $form                                                          = $crawler->selectButton('Se créer un compte')->form();
        $form['fos_user_registration_form[profile][gender]']           = 1;
        $form['fos_user_registration_form[firstname]']                 = $this->user->getFirstname();
        $form['fos_user_registration_form[lastname]']                  = $this->user->getLastname();
        $form['fos_user_registration_form[email]']                     = $this->user->getEmail();
        $form['fos_user_registration_form[plainPassword][first]']      = $this->user->getPlainPassword();
        $form['fos_user_registration_form[plainPassword][second]']     = $this->user->getPlainPassword();
        $form['fos_user_registration_form[profile][birthDate][day]']   = $this->user->getProfile()->getBirthDate()->format('j');
        $form['fos_user_registration_form[profile][birthDate][month]'] = $this->user->getProfile()->getBirthDate()->format('n');
        $form['fos_user_registration_form[profile][birthDate][year]']  = $this->user->getProfile()->getBirthDate()->format('Y');
        $this->client->submit($form);
        // Si on est redirigé, on suit la redirection
        if ($this->client->getResponse()->getStatusCode() == '302') {
            $crawler = $this->client->followRedirect();
        }
        // On vérifie qu'on est sur la page après une création normale de compte
        $this->assertSame(
            'Création de compte',
            trim($crawler->filter('#main-content h1.title span')->text())
        );
        // On vérifie qu'à ce niveau là, l'utilisateur créé n'est pas activé
        $user = $this->getRegistredUser($this->user->getEmail());
        $this->assertFalse($user->isEnabled());
        // On dirige le crawler sur la page de validation de compte pour un organisme
        $token    = $this->getUserConfirmationTokenByEmail($this->user->getEmail());
        $crawler  = $this->client->request('GET', '/jeunes/register/confirm/'.$token);
        // On suit la redirection
        $crawler  = $this->client->followRedirect();
        // On vérifie qu'on est bien sur la page de confirmation de l'activation du compte
        $this->assertSame(
            'Bienvenue sur votre compte '.$this->user->getFirstname().', il est maintenant activé.',
            trim($crawler->filter('#main-content div.alert-success p')->text())
        );
        // On récupère l'utilisateur créé
        $user = $this->getRegistredUser($this->user->getEmail());
        // Vérification :  l'utilisateur créé est activé
        $this->assertTrue($user->isEnabled());
        // Vérification :  l'utilisateur créé est du bon type
        $this->assertSame(User::MISSION_SEEKER_TYPE,  $user->getType());
        // Vérification :  l'utilisateur créé a le bon username et email
        $this->assertSame($this->user->getEmail(),  $user->getUsername());
        $this->assertSame($this->user->getEmail(),  $user->getEmail());
    }

    public function testSignupAsOrganization()
    {
        // On dirige le crawler sur la page de création de compte pour un organisme
        $crawler = $this->client->request('GET', '/organismes/register');
        // On teste si la réponse est 200
        $this->assertTrue($this->client->getResponse()->isOk());
        // On vérifie qu'on est bien sur la bonne page
        $this->assertSame(
            'Création compte organisme et connexion',
            trim($crawler->filter('#main-content h1.title span')->text())
        );
    }

    protected function getUserConfirmationTokenByEmail($email)
    {
        $repo = $this->client->getContainer()->get('service_civique.repository.user');
        $user = $repo->findOneByEmail($email);

        if ($user) {
            return $user->getConfirmationToken();
        }

        return null;
    }

    protected function removeTestUserByEmail($email)
    {
        $em   = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $repo = $this->client->getContainer()->get('service_civique.repository.user');
        /* @var $user \ServiceCivique\Bundle\UserBundle\Entity\User */
        $user = $repo->findOneByEmail($email);
        if ($user) {
            if($profile = $user->getProfile()) {
                $em->remove($profile);
            }
            if($organization = $user->getOrganization()) {
                $em->remove($organization);
            }
            $em->remove($user);
            $em->flush();
        }
    }

    protected function getRegistredUser($email)
    {
        $repo = $this->client->getContainer()->get('service_civique.repository.user');
        /* @var $user \ServiceCivique\Bundle\UserBundle\Entity\User */
        $user = $repo->findOneByEmail($email);
        return $user;
    }
}
