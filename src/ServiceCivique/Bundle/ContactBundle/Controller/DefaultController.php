<?php

namespace ServiceCivique\Bundle\ContactBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ServiceCivique\Bundle\ContactBundle\Form\Type\ContactType;
use Symfony\Component\HttpFoundation\Request;
use ServiceCivique\Bundle\UserBundle\Entity\User;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();

        //Prefill form with default mail
        $defaultFormData = [];

        if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $defaultFormData['user_email'] = $user->getEmail();
        }

        $form = $this->createForm(new ContactType(), $defaultFormData);
        $form->handleRequest($request);

        // Form validation
        if ('POST' == $request->getMethod() && $form->isValid()) {
            $metadata = [];
            $metadata['global']['user_agent'] = $request->headers->get('User-Agent');
            $metadata['user']['type'] = 'anonymous';

            // Get current user
            if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
                if ($user->isJeune()) {
                    $translator = $this->get('translator');
                    $metadata['user']['type']      = $translator->trans($user->getType());
                } elseif ($user->isOrganization()) {
                    $metadata['user']['type']                 = 'organization';
                    $metadata['user']['organization']['full_name'] = $user->getOrganization()->getName();
                }
                $metadata['user']['full_name'] = $user->getFullName();
                $metadata['user']['email']     = $user->getEmail();
            }

            // Create message
            $mailer = $this->container->get('service_civique.mailer');
            $content = nl2br($form->get('content')->getData());
            $userEmail = $form->get('user_email')->getData();
            $metadata['user']['real_email'] = $userEmail;

            $mail_message = $mailer->createNewMessage(
                'ServiceCiviqueMailerBundle:Notification:contact.html.twig',
                array(
                    'content'   => $content,
                    'metadata'  => $metadata
                ),
                $userEmail, // Sender
                // $this->container->getParameter('mandrill_default_sender') // Here receiver
                'agence@service-civique.gouv.fr' // We will recode all this part
            );

            // Send message
            if ($mailer->send($mail_message)) {
                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Votre message a bien été envoyé, nous vous répondrons au plus vite.'
                );

                // reset form
                $defaultFormData['content'] = '';
                $form = $this->createForm(new ContactType(), $defaultFormData);
            }
        }

        return $this->render('ServiceCiviqueContactBundle:Default:index.html.twig', array(
            'form' => $form->createView(),
            'faq'  => $this->jsonFaqObject(),
        ));
    }

    public function faq($type = 'organisme')
    {
        $parser = new Parser();
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

        return $parser->parse($faqContent);
    }

    public function jsonFaqAction()
    {
        $response = new JsonResponse();

        return $response->setData(array(
            'faq' => array(
                'organisme'  => $this->faq('organisme')['themes'],
                'volontaire' => $this->faq('volontaire')['themes'],
            )
        ));
    }
    public function jsonFaqObject()
    {
        return json_encode(array(
            'faq' => array(
                'organisme'  => $this->faq('organisme')['themes'],
                'volontaire' => $this->faq('volontaire')['themes'],
            )
        ));
    }

    protected function getFaqContent($fileName)
    {
        if (file_exists($fileName)) {
            return file_get_contents($fileName);
        }

        return null;
    }
}
