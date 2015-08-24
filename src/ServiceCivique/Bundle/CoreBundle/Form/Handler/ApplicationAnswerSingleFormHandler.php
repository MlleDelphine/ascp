<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use ServiceCivique\Bundle\CoreBundle\Entity\Application;

class ApplicationAnswerSingleFormHandler
{
    protected $form;
    protected $request;
    protected $em;
    protected $scMailer;

    /**
     * Initialize the handler with the form and the request
     *
     * @param Form    $form
     * @param Request $request
     *
     */
    public function __construct(Form $form, Request $request, $em, $scMailer)
    {
        $this->form     = $form;
        $this->request  = $request;
        $this->em       = $em;
        $this->scMailer = $scMailer;
    }

    /**
     * Process form
     *
     * @return boolean
     */
    public function process(Application $application)
    {
         if ('POST' == $this->request->getMethod()) {
            $this->form->bind($this->request);

            if ($this->form->isValid()) {
                $text   = $this->form['messageText']->getData();
                $status = $this->form['status']->getData();

                // @Todo handle errors
                $this->updateApplication($application, $text, $status);
                $settings = [
                    'text'         => $text,
                    'status'       => $this->form['status']->getData(),
                    'missionTitle' => $application->getMission()->getTitle(),
                ];
                $this->notifyJeune($application->getUser()->getEmail(), $settings);

                return true;
            }
        }

        return false;
    }

    protected function updateApplication($application, $text, $status)
    {
        $application->setMessageText($text);
        $application->setStatus($status);
        $this->em->persist($application);
        $this->em->flush($application);
    }

    protected function notifyJeune($jeuneMail, $settings)
    {
        $context = array(
            'text'         => nl2br($settings['text']),
            'status'       => $settings['status'],
            'missionTitle' => $settings['missionTitle'],
        );

        $this->scMailer->sendMessage('ServiceCiviqueWebBundle:Frontend\Application:_mail_application_answer.html.twig', $context, null, $jeuneMail);
    }

}
