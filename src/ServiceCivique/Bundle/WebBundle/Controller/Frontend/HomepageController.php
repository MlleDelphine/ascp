<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ServiceCivique\Bundle\WebBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Frontend homepage controller.
 */
class HomepageController extends Controller
{
    public function defaultAction()
    {
        return new Response($this->renderView(
            'ServiceCiviqueWebBundle:Frontend/Homepage:default.html.twig',
            array('body_classes' => 'front')
        ));
    }

    public function organizationAction()
    {
        $major_programs_repository = $this->get('service_civique.repository.major_program');
        $major_programs = $major_programs_repository->findAll();

        return new Response($this->renderView(
            'ServiceCiviqueWebBundle:Frontend/Homepage:organization.html.twig',
            array(
                'body_classes' => 'front',
                'major_programs' => $major_programs,
            )
        ));
    }

    public function corporateAction()
    {
        return new Response($this->renderView(
            'ServiceCiviqueWebBundle:Frontend/Homepage:corporate.html.twig',
            array('body_classes' => 'front')
        ));
    }

    public function faqsAction()
    {
        return new Response($this->renderView(
            'ServiceCiviqueWebBundle:Frontend/Homepage:faqs.html.twig',
            array('body_classes' => 'front')
        ));
    }
}
