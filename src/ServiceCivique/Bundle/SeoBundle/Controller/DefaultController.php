<?php

namespace ServiceCivique\Bundle\SeoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ServiceCiviqueSeoBundle:Default:index.html.twig', array('name' => $name));
    }
}
