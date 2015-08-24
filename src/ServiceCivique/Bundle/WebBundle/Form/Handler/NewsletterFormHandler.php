<?php

namespace ServiceCivique\Bundle\WebBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Form\FormError;

class NewsletterFormHandler
{
    protected $request;
    protected $form;
    protected $newsletterService;

    /**
     * Initialize the handler with the form and the request
     *
     * @param Form    $form
     * @param Request $request
     * @param Service $newsletterService
     *
     */
    public function __construct(Form $form, Request $request, $newsletterService)
    {
        $this->form = $form;
        $this->request = $request;
        $this->newsletterService = $newsletterService;
    }

    /**
     * Process form
     *
     * @return boolean
     */
    public function process()
    {
        if ('POST' == $this->request->getMethod()) {
            $this->form->handleRequest($this->request);
            if ($this->form->isValid()) {
                if ($user = $this->getUserFromForm($this->form)) {
                    return $this->newsletterService->subscribeUserToNewsletter($user);
                }
            }
        }

        return false;
    }

    public function getUserFromForm($form)
    {
        if ($form['isNewsletterSubscribed']->getData() == false && $form['isNewsletterVolunteerSubscribed']->getData() == false) {
            return false;
        }

        $user = [];
        $user['firstName'] = $form['firstName']->getData();
        $user['lastName'] = $form['lastName']->getData();
        $user['email'] = $form['email']->getData();
        $user['role'] = $form['role']->getData();
        $user['organizationName'] = $form['organizationName']->getData();
        $user['isNewsletterSubscribed'] = $form['isNewsletterSubscribed']->getData();
        $user['isNewsletterVolunteerSubscribed'] = $form['isNewsletterVolunteerSubscribed']->getData();
        $user['address'] = $form['location']['address']->getData();
        $user['zipCode'] = $form['location']['zipCode']->getData();
        $user['city'] = $form['location']['city']->getData();
        $user['country'] = $form['location']['country']->getData();

        return $user;
    }

}
