<?php

namespace spec\ServiceCivique\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use ServiceCivique\Bundle\MailerBundle\TwigMandrillMailer;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\GenericEvent;
use ServiceCivique\Bundle\CoreBundle\Entity\Mission;
use ServiceCivique\Bundle\CoreBundle\Entity\Organization;
use ServiceCivique\Bundle\UserBundle\Entity\User;
use ServiceCivique\Bundle\UserBundle\Entity\OrganizationInvitation;
use Hip\MandrillBundle\Message;
use Prophecy\Prophet;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Departements\Provider;

class NotifierSpec extends ObjectBehavior
{
    protected $newUser;

    function let(TwigMandrillMailer $mailer, Router $router, Message $message, RepositoryInterface $repository, User $newUser, Provider $departmentProvider)
    {
        $mailer->createNewMessage(Argument::type('string'), Argument::type('array'), null, Argument::type('string'))->will(function() {
            return new Message();
        });

        $mailer->send(Argument::type('Hip\MandrillBundle\Message'))->willReturn(true);

        $router->generate(Argument::type('string'), Argument::any(), true)->will(function($args) {
            return 'http://' . $args[0];
        });

        $this->newUser = $newUser;
        $repository->createNew()->willReturn($newUser);

        $this->beConstructedWith($mailer, $router, $repository, $departmentProvider);
    }

    function it_should_give_organization_user(GenericEvent $event, Organization $approvedOrganization, Organization $organization1, Organization $organization2, OrganizationInvitation $invitation, User $user1, User $user2) {
        $approvedOrganization->getUser()->willReturn($user1);
        $approvedOrganization->isApprovedOrganization()->willReturn(true);

        $organization1->getUser()->willReturn($user2);
        $organization1->isApprovedOrganization()->willReturn(false);
        $organization1->getApprovedOrganization()->willReturn($approvedOrganization);

        $invitation->getEmail()->willReturn('invitation@foo.com');

        $organization2->getUser()->willReturn(null);
        $organization2->isApprovedOrganization()->willReturn(false);
        $organization2->getApprovedOrganization()->willReturn($approvedOrganization);
        $organization2->getInvitation()->willReturn($invitation);

        $this->getOrganizationUsers($organization1)->shouldReturn(array($user2, $user1));
        $this->getOrganizationUsers($organization2)->shouldReturn(array($this->newUser, $user1));
        $this->getOrganizationUsers($approvedOrganization)->shouldReturn(array($user1));
    }

    function it_should_send_notification_on_mission_post_create(GenericEvent $event, Mission $mission)
    {
        $prophet = new Prophet();

        $organization             = $prophet->prophesize('ServiceCivique\Bundle\CoreBundle\Entity\Organization');
        $approvedOrganization     = $prophet->prophesize('ServiceCivique\Bundle\CoreBundle\Entity\Organization');
        $organizationUser         = $prophet->prophesize('ServiceCivique\Bundle\UserBundle\Entity\User');
        $approvedOrganizationUser = $prophet->prophesize('ServiceCivique\Bundle\UserBundle\Entity\User');

        $organizationUser->getFirstname()->willReturn('Michel');
        $organizationUser->getEmail()->willReturn('michel@gmail.com');

        $approvedOrganizationUser->getFirstname()->willReturn('Jean-Paul');
        $approvedOrganizationUser->getEmail()->willReturn('jean-paul@gmail.com');
        $approvedOrganizationUser->getFullName()->willReturn('Jean-Paul Martin');

        $organization->getUser()->willReturn($organizationUser);
        $organization->isApprovedOrganization()->willReturn(false);
        $approvedOrganization->getUser()->willReturn($approvedOrganizationUser);
        $approvedOrganization->isApprovedOrganization()->willReturn(true);
        $organization->getApprovedOrganization()->willReturn($approvedOrganization);

        $mission->getOrganization()->willReturn($organization);
        $mission->getTitle()->willReturn('un super titre');
        $mission->getStatus()->willReturn(1);
        $mission->getId()->willReturn(1);

        $event->getSubject()->willReturn($mission);

        $this->onMissionPostUpdateCreate($event);
    }
}

