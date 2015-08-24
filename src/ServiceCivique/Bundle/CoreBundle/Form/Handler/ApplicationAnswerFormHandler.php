<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use ServiceCivique\Bundle\CoreBundle\Entity\Application;

class ApplicationAnswerFormHandler
{
    protected $request;
    protected $form;
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
        $this->form = $form;
        $this->request = $request;
        $this->em = $em;
        $this->scMailer = $scMailer;
    }

    /**
     * Process form
     *
     * @return boolean
     */
    public function process()
    {
        if ('POST' == $this->request->getMethod()) {
            $this->form->bind($this->request);
            if ($this->form->isValid()) {
                $users = $this->getUsers($this->form['mails']->getData());
                $text = $this->form['messageText']->getData();
                $missionSlug = $this->request->get('mission_slug');
                $status = Application::POSITIVE_ANSWER;
                if ($this->request->get('status') == 'negatif') {
                    $status = Application::NEGATIVE_ANSWER;
                }

                $missionRepository = $this->em->getRepository('ServiceCivique\Bundle\CoreBundle\Entity\Mission');
                $mission = $missionRepository->findOneBySlug($missionSlug);

                $applicationRepository = $this->em->getRepository('ServiceCivique\Bundle\CoreBundle\Entity\Application');
                $applications = array();
                foreach ($users as $user) {
                    if($application = $applicationRepository->findOneBy(
                        array(
                            'mission' => $mission->getId(),
                            'user'    => $user->getId()
                        )
                    )) {
                        $applications[] = $application;
                    }
                }

                $this->onSuccess($applications, $text, $status, $mission);

                return true;
            }
        }

        return false;
    }

    protected function getUsers($mails)
    {
        $mails = $this->splitEmails($mails);
        $userRepository = $this->em->getRepository('ServiceCivique\Bundle\UserBundle\Entity\User');
        $users = array();

       foreach ($mails as $mail) {
            if ($user = $userRepository->findOneByEmail($mail)) {
                $users[] = $user;
            }
       }

       return $users;
    }

    protected function onSuccess($applications, $text, $status, $mission)
    {
        foreach ($applications as $application) {
            $application->setMessageText($text);
            $application->setStatus($status);
            $this->em->persist($application);

            $context = array(
                'text'         => nl2br($text),
                'status'       => $status,
                'missionTitle' => $mission->getTitle(),
            );

            $this->scMailer->sendMessage('ServiceCiviqueWebBundle:Frontend\Application:_mail_application_answer.html.twig', $context, null, $application->getUser()->getEmail());
        }
        $this->em->flush();
    }

    protected function splitEmails($emails)
    {
        return explode(',', str_replace(' ', '', $emails));
    }

}
