<?php

namespace ServiceCivique\Bundle\WebBundle\Controller\Backend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use ServiceCivique\Bundle\WebBundle\Form\Type\ImportApprovalFormType;
use ServiceCivique\Bundle\WebBundle\Form\Handler\ImportApprovalFormHandler;
use ServiceCivique\Bundle\CoreBundle\Form\Type\FaqType;
use ServiceCivique\Bundle\WebBundle\Form\Handler\FaqFormHandler;
use ServiceCivique\Bundle\WebBundle\Form\Handler\BannerFormHandler;

class BackendController extends Controller
{
    public function defaultAction()
    {
        return new Response($this->renderView(
            'ServiceCiviqueWebBundle:Backend/Homepage:default.html.twig',
            array()
        ));
    }

    public function importApprovalsAction()
    {
        $form = $this->createForm(new ImportApprovalFormType());
        $request = $this->get('request');
        //$formHandler = $this->get('service_civique.import_approval');
        $em       = $this->get('Doctrine')->getManager();
        $resetter = $this->container->get('fos_elastica.resetter');
        $type     = $this->container->get('fos_elastica.index.service_civique.approval');
        $formHandler = new ImportApprovalFormHandler($form, $request, $em, $resetter, $type);

        $process = $formHandler->process();

        if ($process) {
            $this->get('session')->getFlashBag()->add(
                'success',
                'service_civique.backend.form.upload_success'
            );

            return $this->redirect($this->generateUrl('service_civique_backend_import_approvals'));
        }

        return new Response($this->renderView(
            'ServiceCiviqueWebBundle:Backend/Import:approvals.html.twig',
            array('form' => $form->createView())
        ));
    }

    public function faqAction($type)
    {
        $form = $this->createForm('service_civique_faq', null, array('faqType' => $type));

        $request = $this->get('request');

        $formHandler = new FaqFormHandler($form, $request, $type);

        $process = $formHandler->process();

        if ($process) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'service_civique.backend.form.generic_success'
            );
        }

        return new Response($this->renderView(
            'ServiceCiviqueWebBundle:Backend/Faq:index.html.twig',
            array('form' => $form->createView())
        ));
    }

    public function bannerAction()
    {
        $keyValueStore = $this->container->get('service_civique.key_value_store');
        $defaultData = [];
        if ($bannerDestination = $keyValueStore->get('banner_destination')) {
            $bannerDestinationValue = stream_get_contents($bannerDestination->getDataValue());
            $defaultData = ['destination' => $bannerDestinationValue];
        }

        $form    = $this->createForm('service_civique_banner', $defaultData);
        $request = $this->get('request');
        $em      = $this->get('doctrine')->getManager();

        $formHandler = new BannerFormHandler($form, $request, $em, $keyValueStore);

        $process = $formHandler->process();

        if ($process) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                'service_civique.backend.form.generic_success'
            );
        }

        $uploadDir = $request->server->get('DOCUMENT_ROOT') . '/uploads';
        $isBannerExist = false;
        if (file_exists($uploadDir . '/banniere.gif')) {
            $isBannerExist = true;
        }

        return new Response($this->renderView(
            'ServiceCiviqueWebBundle:Backend/Banner:index.html.twig',
            array(
                'form'   => $form->createView(),
                'isBannerExist' => $isBannerExist
            )
        ));
    }

}
