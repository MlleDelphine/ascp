<?php

namespace ServiceCivique\Bundle\CoreBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ServiceCivique\Bundle\MailerBundle\TwigMandrillMailer;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\GenericEvent;
use ServiceCivique\Bundle\UserBundle\Entity\User;
use ServiceCivique\Bundle\CoreBundle\Entity\Mission;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Listen events and send notification
 *
 * @see EventSubscriberInterface
 */
class Notifier implements EventSubscriberInterface
{
    /**
     * mailer
     *
     * @var TwigMandrillMailer
     */
    protected $mailer;

    /**
     * mailer
     *
     * @var TwigMandrillMailer
     */
    protected $router;
    protected $departmentProvider;

    /**
     * __construct
     *
     * @param TwigMandrillMailer $mailer
     * @param Router             $router
     * @param ObjectRepository   $userRepository
     * @param $departmentProvider
     */
    public function __construct(TwigMandrillMailer $mailer, Router $router, RepositoryInterface $userRepository, $departmentProvider)
    {
        $this->mailer         = $mailer;
        $this->router         = $router;
        $this->userRepository = $userRepository;
        $this->departmentProvider = $departmentProvider;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'service_civique.mission.post_create'     => 'onMissionPostUpdateCreate',
            'service_civique.mission.post_update'     => 'onMissionPostUpdateCreate',
            // 'service_civique.application.post_create' => 'onApplicationPostCreate',
            'service_civique.application.validation' => 'onApplicationPostCreate',
        ];
    }

    public function onApplicationPostCreate(GenericEvent $event)
    {
        $application = $event->getSubject();
        $mission     = $application->getMission();
        $user        = $application->getUser();

        // send message to candidate
        $candidate_message = $this->mailer->createNewMessage(
            'ServiceCiviqueMailerBundle:Notification:candidate_application_post_create.html.twig',
            array(
                'firstname'       => $user->getFirstname(),
                'mission_title'   => $mission->getTitle(),
                'mission_url'     => $this->router->generate('service_civique_mission_show', array(
                    'slug' => $mission->getSlug()
                ), true),
                'application_url' => $this->router->generate('service_civique_application_show', array(
                    'mission_slug' => $mission->getSlug()
                ), true)
            ),
            null,
            $user->getEmail()
        );

        $this->mailer->send($candidate_message);

        $organization_users = $this->getMissionUsers($mission);

        if (!count($organization_users)) {
            return;
        }

        $organizationUser = array_shift($organization_users);

        // send message to organization
        $cvPath = '';
        if ($application->getPath() != '') {
            $cvPath = $this->router->generate('service_civique_resume_show', array(
                'slug' => $application->getPath(),
            ), true);
        }

        $organization_message = $this->mailer->createNewMessage(
            'ServiceCiviqueMailerBundle:Notification:organization_application_post_create.html.twig',
            array(
                'fullname'             => $organizationUser->getFullname(),
                'mission_title'        => $mission->getTitle(),
                'mission_url'          => $this->router->generate('service_civique_mission_show', array(
                    'slug' => $mission->getSlug()
                ), true),
                'mission_area'   => $this->departmentProvider->findRegionByCode($mission->getArea()),
                'mission_department'         => $this->departmentProvider->findDepartementByCode($mission->getDepartment()),
                'candidate_firstname'  => $application->getUser()->getFirstname(),
                'candidate_lastname'   => $application->getUser()->getLastname(),
                'candidate_motivation' => $application->getMotivation(),
                'application_url'      => $this->router->generate('service_civique_application_missions_application_show', array(
                    'id'               => $mission->getId(),
                    'application_id'   => $application->getId()
                ), true),
                'answer_negative'      => $this->router->generate('service_civique_application_answer_create', array(
                    'mission_slug' => $mission->getSlug(),
                    'status'       => 'negatif',
                    'mails'        => $application->getUser()->getEmail()
                ), true),
                'answer_positive'     => $this->router->generate('service_civique_application_answer_create', array(
                    'mission_slug' => $mission->getSlug(),
                    'status'       => 'positif',
                    'mails'        => $application->getUser()->getEmail()
                ), true),
                'candidate_cv'        => $cvPath,
                'candidate_phone'     => $application->getPhoneNumber(),
                'candidate_city'      => $application->getCity(),
                'candidate_zipcode'   => $application->getZipCode(),
                'candidate_address'   => $application->getAddress(),
                'candidate_country'   => $application->getCountry(),
                'candidate_birthdate' => $application->getUser()->getProfile()->getBirthDate()->format('d/m/Y'),
                'candidate_age'       => $this->getAge($application->getUser()->getProfile()->getBirthDate()),
            ),
            null,
            $organizationUser->getEmail()
        );

        foreach ($organization_users as $organization_user) {
            $organization_message->addTo(
                $organization_user->getEmail(),
                $organization_user->getFullname(),
                'cc'
            );
        }

        $this->mailer->send($organization_message);
    }

    private function getAge($birthdate)
    {
        $now = new \DateTime();
        $age = $now->diff($birthdate);

        return  $age->y;
    }

    public function getMissionUsers($mission)
    {
        $users = $this->getOrganizationUsers($mission->getOrganization());

        // optional additional contact
        if ($additionalEmailContact = $mission->getAdditionalEmailContact()) {
            $additionalContactUser = $this->userRepository->createNew();
            $additionalContactUser->setEmail($additionalEmailContact);
            $users[] = $additionalContactUser;
        }

        return $users;
    }

    public function getOrganizationUsers($organization)
    {
        $users = array();

        // retreive $mission organization user
        $organizationUser = $organization->getUser();

        if ($organizationUser) {
            $users[] = $organizationUser;
        } elseif (($invitation = $organization->getInvitation())) {
            $organizationUser = $this->userRepository->createNew();
            $organizationUser->setEmail($invitation->getEmail());
            $users[] = $organizationUser;
        }

        if (!$organization->isApprovedOrganization() && $organization->getApprovedOrganization() && $approvedOrganizationUser = $organization->getApprovedOrganization()->getUser()) {
            $users[] = $approvedOrganizationUser;
        }

        return $users;
    }

    /**
     * When a new mission is created
     * Then mission organization user must receive a notification
     * And if organization has a parent then send a copy to his parent organization user
     *
     * @param GenericEvent $event
     */
    public function onMissionPostUpdateCreate(GenericEvent $event)
    {
        $mission = $event->getSubject();
        $organization = $mission->getOrganization();

        // retreive $mission organization user
        $organizationUser = $organization->getUser();

        // if there are no organization user
        // then create an user from invitation
        if (!$organizationUser) {
            $invitation = $organization->getInvitation();

            if (!$invitation) {
                return false;
            }

            // create a ‘fake’ user
            $organizationUser = $this->userRepository->createNew();
            $organizationUser->setEmail($invitation->getEmail());
        }

        $to_send = false;
        if (
            in_array($mission->getStatus(), array(
                Mission::STATUS_UNDER_REVIEW,
                Mission::STATUS_UNDER_VALIDATION
            ))
        ) {
            if (!$mission->isStatusUpdated()) {
                $message = $this->mailer->createNewMessage(
                    'ServiceCiviqueMailerBundle:Notification:mission_post_create.html.twig',
                    array(
                        'firstname'             => $organizationUser->getFirstname(),
                        'mission_title'         => $mission->getTitle(),
                        'mission_dashboard_url' => $this->router->generate('service_civique_organization_mission_index', array(), true)
                    ),
                    null,
                    $organizationUser->getEmail()
                );
                $to_send = true;
            }
        } elseif ($mission->getStatus() == Mission::STATUS_AVAILABLE) {
            $message = $this->mailer->createNewMessage(
                'ServiceCiviqueMailerBundle:Notification:mission_post_validate.html.twig',
                array(
                    'firstname'       => $organizationUser->getFirstname(),
                    'mission_title'   => $mission->getTitle(),
                    'application_url' => $this->router->generate('service_civique_application_missions_applications', array('id' => $mission->getId()), true)
                ),
                null,
                $organizationUser->getEmail()
            );
            $to_send = true;
        }

        if ($to_send) {
            // if organization is an approved organization
            // then add approved organization has cc recipient
            if (!$organization->isApprovedOrganization() && $approvedOrganizationUser = $organization->getApprovedOrganization()->getUser()) {
                $message->addTo(
                    $approvedOrganizationUser->getEmail(),
                    $approvedOrganizationUser->getFullname(),
                    'cc'
                );
            }

            $this->mailer->send($message);
        }
    }
}
