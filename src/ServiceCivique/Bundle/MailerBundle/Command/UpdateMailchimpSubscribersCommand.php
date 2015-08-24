<?php

namespace ServiceCivique\Bundle\MailerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Mailchimp;
use Mailchimp_Lists;
use ServiceCivique\Bundle\CoreBundle\Repository\UserRepository;
use ServiceCivique\Bundle\UserBundle\Entity\User;
use ServiceCivique\Bundle\CoreBundle\Entity\Organization;

class UpdateMailchimpSubscribersCommand extends ContainerAwareCommand
{

    protected $repository;
    protected $mailchimpLists;
    protected $newsletterListId;

    public function __construct(UserRepository $repository, Mailchimp $mailchimp)
    {
        $this->repository = $repository;
        $this->mailchimpLists = new Mailchimp_Lists($mailchimp);
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('sc:update_mailchimp_subscribers')
             ->setDescription('Update mailchimp subscribers');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->newsletterListId = $this->getContainer()->getParameter('mailchimp_newsletter_list_id');

        $this->executeBatchSubscribe();
    }

    protected function executeBatchSubscribe()
    {
        $users = $this->repository->getActiveSubscribingUsers();
        $batch = $this->getUsersBatch($users);

        $res = $this->mailchimpLists->batchSubscribe($this->newsletterListId, $batch, false, true);
    }

    protected function getUsersBatch($users, $subscribe = true)
    {
        $batch = array();
        foreach ($users as $user) {
            $batch[] = array(
                'email' => array('email' => $user->getEmail()),
                'email_type' => 'html',
                'merge_vars' => $this->getMergeVars($user)
            );
        }

        return $batch;
    }

    protected function getMergeVars($user)
    {
        $vars = array(
            'FNAME'      => $user->getFirstname(),
            'LNAME'      => $user->getLastname(),
            'NEWSLETTER' => 'Inscrit à la newsletter'
        );
        switch ($user->getType()) {
            case User::ORGANIZATION_TYPE:
                $vars['STATUT'] = 'Organisme (OSCAR)';
                $organization = $user->getOrganization();
                if ($organization instanceof Organization) {
                    $vars['MMERGE3'] = $organization->getName();
                }
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
