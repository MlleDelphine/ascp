<?php

namespace ServiceCivique\Bundle\CoreBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ServiceCivique\Bundle\MailerBundle\TwigMandrillMailer;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\EventDispatcher\GenericEvent;
use ServiceCivique\Bundle\CoreBundle\Service\ContentCheckerService;
use ServiceCivique\Bundle\UserBundle\Entity\User;
use ServiceCivique\Bundle\CoreBundle\Entity\Mission;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Listen events and checks for forbidden keywords in missions
 *
 * @see EventSubscriberInterface
 */
class MissionValidator implements EventSubscriberInterface {

    /**
     * mailer
     *
     * @var TwigMandrillMailer
     */
    protected $mailer;

    /**
     * router
     * 
     * @var Router
     *
     */
    protected $router;

    /**
     * content checker service
     * 
     * @var ContentCheckerService
     */
    protected $contentCheckerService;

    /**
     * __construct
     *
     * @param $contentCheckerService
     */
    public function __construct(ContentCheckerService $contentCheckerService) {
        $this->contentCheckerService = $contentCheckerService;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents() {
        return [
            'service_civique.mission.post_create' => 'onMissionPostUpdateCreate',
            'service_civique.mission.post_update' => 'onMissionPostUpdateCreate',
        ];
    }

    /**
     * When a new mission is created
     * Then mission organization user must receive a notification
     * And if organization has a parent then send a copy to his parent organization user
     *
     * @param GenericEvent $event
     */
    public function onMissionPostUpdateCreate(GenericEvent $event) {
        /* @TODO Essai avec une mission contenant un mot interdit => ne marche pas, voir si tout est bien exécuté dans cette fonction */
        $mission = $event->getSubject();

        $fields_to_watch = array(
            'title' => 'Title',
            'description' => 'Description',
            'organization_description' => 'OrganizationDescription'
        );

        $fields_with_forbidden_keyword = array();

        foreach ($fields_to_watch as $key => $value) {
            $method = 'get' . $value;
            $result = $this->contentCheckerService->checkContent($mission->$method);
            if ($result) {
                $fields_with_forbidden_keyword[] = $key;
            }
        }

        if (!empty($fields_with_forbidden_keyword)) {
            $mission->setStatus(Mission::STATUS_CENSORED);
        }
    }

}
