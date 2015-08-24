<?php

namespace ServiceCivique\Bundle\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Yaml\Parser;
use ServiceCivique\Bundle\WebBundle\Form\Type\NewsletterType;
use ServiceCivique\Bundle\WebBundle\Form\Handler\NewsletterFormHandler;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('ServiceCiviqueWebBundle:Default:index.html.twig');
    }

    /**
     * Set acceptCookies cookie to true, and redirect user to the ?destination GET parameters
     */
    public function setCookieAction(Request $request)
    {
        $cookie = new Cookie('acceptCookies', true, "+1 year");

        $response = new Response();
        $response->headers->setCookie($cookie);
        $response->sendHeaders();

        $redirectionPage = $request->headers->get('referer') ? $request->headers->get('referer') : $this->generateUrl('service_civique_mission_list');

        return $this->redirect($redirectionPage);
    }

    public function faqAction(Request $request, $type)
    {
        $parser = new Parser();
        // Set SEO variables
        $seoPage = $this->container->get('sonata.seo.page');
        switch ($type) {
            case 'organisme':
                $faqTitle = 'service_civique.faq.organisme.title';
                $faqContent = $this->getFaqContent('uploads/faq-organisme.yml');
                break;
            case 'volontaire':
            default:
                $faqTitle = 'service_civique.faq.volontaire.title';
                $faqContent = $this->getFaqContent('uploads/faq-volontaire.yml');
        }
        $seoPage->addTitle($this->container->get('translator')->trans($faqTitle));

        return $this->render('ServiceCiviqueWebBundle:Default:faq.html.twig',
            array(
                'faq'  => $parser->parse($faqContent),
                'type' => $type
            )
        );

    }

    protected function getFaqContent($fileName)
    {
        if (file_exists($fileName)) {
            return file_get_contents($fileName);
        }

        return null;
    }

    public function newsletterAction(Request $request)
    {
        // Set SEO variables
        $seoPage = $this->container->get('sonata.seo.page');
        $seoPage->addTitle('Inscrivez-vous à la newletter du Service Civique');

        $form = $this->createForm(new NewsletterType());

        $formHandler = new NewsletterFormHandler($form, $request, $this->container->get('service_civique.service.newsletter_service'));
        if ($request->getMethod() == 'POST') {
            if ($formHandler->process()) {
                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Vous êtes bien inscrit à la newsletter'
                );
                return $this->redirect($this->generateUrl('service_civique_homepage'));
            } else {
                $this->get('session')->getFlashBag()->add(
                    'danger',
                    'Une erreur est survenue'
                );
            }
        }

        return $this->render('ServiceCiviqueWebBundle:Default:newsletter.html.twig',
            [
                'form' => $form->createView(),
            ]
        );

    }

}
