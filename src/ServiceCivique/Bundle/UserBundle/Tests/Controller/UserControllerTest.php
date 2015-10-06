<?php

namespace ServiceCivique\Bundle\UserBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testSignupAsYouth()
    {
        // On créé le client
        $client = static::createClient();
        // On dirige le crawler sur la page de création de compte pour un organisme
        $crawler = $client->request('GET', '/jeunes/register/');
        // On teste si la réponse est 200
        $this->assertTrue($client->getResponse()->isOk());
        // On vérifie qu'on est bien sur la bonne page
        $this->assertSame(
            'Création compte jeune et connexion',
            trim($crawler->filter('#main-content h1.title span')->text())
        );
        $form = $crawler->selectButton('Se créer un compte')->form();
        $form['fos_user_registration_form[profile][gender]'] = 1;
        $form['fos_user_registration_form[firstname]'] = 'Sophie';
        $form['fos_user_registration_form[lastname]'] =  'Durand';
        $form['fos_user_registration_form[email]'] = 'sophie.durand@mail.com';
        $form['fos_user_registration_form[plainPassword][first]'] = 'monmotdepasse';
        $form['fos_user_registration_form[plainPassword][second]'] = 'monmotdepasse';
        $form['fos_user_registration_form[profile][birthDate][day]'] = '19';
        $form['fos_user_registration_form[profile][birthDate][month]'] = '3';
        $form['fos_user_registration_form[profile][birthDate][year]'] = '1996';
        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertSame(
            'Création de compte',
            trim($crawler->filter('#main-content h1.title span')->text())
        );





    }

    public function testSignupAsOrganization()
    {
        // On créé le client
        $client = static::createClient();
        // On dirige le crawler sur la page de création de compte pour un organisme
        $crawler = $client->request('GET', '/organismes/register');
        // On teste si la réponse est 200
        $this->assertTrue($client->getResponse()->isOk());
        // On vérifie qu'on est bien sur la bonne page
        $this->assertSame(
            'Création compte organisme et connexion',
            trim($crawler->filter('#main-content h1.title span')->text())
        );


    }
}
