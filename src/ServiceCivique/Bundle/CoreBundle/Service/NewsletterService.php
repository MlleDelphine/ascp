<?php

namespace ServiceCivique\Bundle\CoreBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use ServiceCivique\Bundle\UserBundle\Entity\User;
use Mailchimp;
use Mailchimp_Lists;

class NewsletterService
{

    protected $mailchimp;
    protected $newsletterListId;
    protected $mailchimpLists;

    public function __construct(Mailchimp $mailchimp, $newsletterListId)
    {
        $this->mailchimp        = $mailchimp;
        $this->newsletterListId = $newsletterListId;
        $this->mailchimpLists = new Mailchimp_Lists($mailchimp);
    }

    public function subscribeUserToNewsletter($user)
    {
        $user = $this->getFormattedUser($user);
        try {
            $this->mailchimpLists->subscribe($this->newsletterListId, $user['email'], $user['merge_vars'], 'html', false);
            return true;
        } catch (\Mailchimp_Error $e) {
            return false;
        }
    }

    protected function getFormattedUser($user)
    {
        $user = array(
            'email' => array('email' => $user['email']),
            'merge_vars' => $this->getMergeVars($user)
        );

        return $user;
    }

    protected function getMergeVars($user)
    {
        $vars = array(
            'FNAME'      => $user['firstName'],
            'LNAME'      => $user['lastName'],
            'ADDRESS'    => $user['address'],
            'ZIPCODE'    => $user['zipCode'],
            'CITY'       => $user['city'],
            'COUNTRY'    => $user['country'],
        );


        if ($user['isNewsletterSubscribed']) {
            $vars['NEWSLETTER'] = 'Inscrit à la newsletter';
        }
        if ($user['isNewsletterVolunteerSubscribed']) {
            $vars['GROUPINGS']  = [
                [
                    'name' => 'Volontairement vôtre',
                    'groups' => ['Inscription VV depuis le site']
                ]
            ];
        }

        switch ($user['role']) {
            case User::ORGANIZATION_TYPE:
                $vars['STATUT'] = 'Organisme (OSCAR)';
                $vars['MMERGE3'] = $user['organizationName'];
                break;
            case User::VOLUNTEER_TYPE:
                $vars['STATUT'] = 'Volontaire';
                break;
            case User::FORMER_VOLUNTEER_TYPE:
                $vars['STATUT'] = 'Ancien volontaire';
                break;
        }

        return $vars;
    }


}
