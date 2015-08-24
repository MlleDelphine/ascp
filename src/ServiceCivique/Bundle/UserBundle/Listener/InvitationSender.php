<?php

namespace ServiceCivique\Bundle\UserBundle\Listener;

use ServiceCivique\Bundle\UserBundle\Entity\OrganizationInvitation;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerInterface;

class InvitationSender
{
    protected $container;

    /**
     * __construct
     *
     * mailer causes Circular Reference Error so we inject the whole container
     * http://stackoverflow.com/questions/7561013/injecting-securitycontext-into-a-listener-prepersist-or-preupdate-in-symfony2-to
     *
     * @param ContainerInterface $container;
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * postPersist
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof OrganizationInvitation) {

            $this->send($entity);
            $entity->setSent(true);

            $om = $args->getObjectManager();

            $om->merge($entity);
            $om->flush($entity);
        }
    }

    /**
     * send invitation with mandrill
     *
     * @param OrganizationInvitation $invitation
     */
    protected function send(OrganizationInvitation $invitation)
    {
        $context = array(
            'approved_organization_name' => $invitation->getOrganization()->getApprovedOrganization()->getName(),
            'activation_link'            => $this->container->get('router')->generate('service_civique_organization_register', array(
                'invitation_code' => $invitation->getCode()
            ), true)
        );

        return $this->container->get('service_civique.mailer')->sendMessage('ServiceCiviqueUserBundle:Registration:invitation.html.twig', $context, null, $invitation->getEmail());
    }
}
