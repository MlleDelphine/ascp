<?php

namespace ServiceCivique\Component\AMPQ;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use ServiceCivique\Bundle\CoreBundle\SearchRepository\MissionRepository;
use ServiceCivique\Bundle\WebBundle\Service\SearchFilterService;
use ServiceCivique\Bundle\MailerBundle\TwigMandrillMailer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class ServiceCiviqueMissionSearchMailerConsumer implements ConsumerInterface
{

    protected $templating;
    protected $scMailer;
    protected $missionRepository;
    protected $searchFilterService;
    protected $router;

    /**
     * Initialize the handler with the form and the request
     *
     * @param Templating $templating
     * @param Mailer     $scMailer
     *
     */
    public function __construct($templating, TwigMandrillMailer $scMailer, MissionRepository $missionRepository, SearchFilterService $searchFilterService, Router $router)
    {
        $this->templating          = $templating;
        $this->scMailer            = $scMailer;
        $this->missionRepository   = $missionRepository;
        $this->searchFilterService = $searchFilterService;
        $this->router              = $router;
    }

    /**
     *  Main execute method
     *  Execute actions for a given message
     *
     *  @param (AMQPMessage) $msg       An instance of `PhpAmqpLib\Message\AMQPMessage` with the $msg->body being the data sent over RabbitMQ.
     *
     *  @return (boolean) Execution status (true if everything's of, false if message should be re-queued)
     */
    public function execute(AMQPMessage $msg)
    {
        // Initialize
        $mailParameters = unserialize($msg->body);

        $missions = $this->missionRepository->findFromFrontend($mailParameters['criteria'])->getCurrentPageResults();
        $missions_list = $this->templating->render(
            'ServiceCiviqueWebBundle:Mail/Mission:list.html.twig',
            array('missions' => $missions)
        );
        $criterias = $this->searchFilterService->getMissionSearchActiveFilters($mailParameters['missionSearch']);
        $criteria_list = $this->templating->render(
            'ServiceCiviqueWebBundle:Mail/Mission:criterias.html.twig',
            array('criterias' => $criterias)
        );
        $context = array();
        $templateName = 'ServiceCiviqueMailerBundle:Mandrill:new_missions_notification.html.twig';
        // Send the mail through
        $message = $this->scMailer->createNewMessage($templateName,
            $context,
            null,
            $mailParameters['recipient']
        );
        $message->addGlobalMergeVar('MISSIONS_LIST',
            $missions_list
        )
        ->addGlobalMergeVar('CRITERIA_LIST',
            $criteria_list
        )
        ->addGlobalMergeVar('MORE_MISSION_URL',
            $this->router->generate('service_civique_mission_list', array('criteria' => $mailParameters['criteria']), true)
        )
        ->addGlobalMergeVar('MORE_DESINSCRIPTION_URL',
            $this->router->generate('service_civique_profile_mission_search_delete', array(), true)
        )
        ->addGlobalMergeVar('EDIT_MISSION_SEARCH_URL',
            $this->router->generate('service_civique_profile_mission_search_edit', array(), true)
        )
        ->addGlobalMergeVar('MORE_WEBSITE_URL',
            $this->router->generate('service_civique_homepage', array(), true)
        );

        return $this->scMailer->send($message, 'new_missions_notification');
    }

}
