<?php

namespace ServiceCivique\Bundle\ImporterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ServiceCiviqueImporterBundle:Default:index.html.twig', array('name' => $name));
    }
}
