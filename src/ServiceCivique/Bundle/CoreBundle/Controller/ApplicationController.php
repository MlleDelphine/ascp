<?php

namespace ServiceCivique\Bundle\CoreBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use ServiceCivique\Bundle\CoreBundle\Form\Type\ApplicationAnswerType;
use ServiceCivique\Bundle\CoreBundle\Form\Handler\ApplicationAnswerFormHandler;
use ServiceCivique\Bundle\CoreBundle\Form\Handler\ApplicationAnswerSingleFormHandler;

use ServiceCivique\Bundle\UserBundle\Entity\User;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

use ServiceCivique\Bundle\CoreBundle\Entity\Mission;
use ServiceCivique\Bundle\CoreBundle\Form\Type\ApplicationPokeType;
use ServiceCivique\Bundle\CoreBundle\Form\Type\ApplicationAnswerSingleType;
use ServiceCivique\Bundle\CoreBundle\Entity\Application;
// use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;

class ApplicationController extends ResourceController
{
    public function indexAction(Request $request)
    {
        $sorting  = $this->config->getSorting();

        // Get current user
        $user = $this->container->get('security.context')->getToken()->getUser();

        $criteria = array_merge(
            [
                'user' => $user->getId(),
                'all'  => $request->attributes->get('all')
            ],
            $this->config->getCriteria([])
        );

        $repository = $this->getRepository();

        $resources = $this->resourceResolver->getResource(
            $repository,
            'findFromNow',
            array($criteria, $sorting)
        );

        $resources->setCurrentPage($request->get('page', 1), true, true);
        $resources->setMaxPerPage($this->config->getPaginationMaxPerPage());

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('index.html'))
            ->setTemplateVar($this->config->getPluralResourceName())
            ->setData($resources)
        ;

        return $this->handleView($view);
    }

    public function indexMissionAction(Request $request)
    {
        $sorting  = $this->config->getSorting();

        $missionRepository = $this->container->get('service_civique.repository.mission');
        if (!$mission = $missionRepository->find($request->attributes->get('id'))) {
            throw $this->createNotFoundException('Cette mission n\'existe pas');
        };

        // Get current user
        $user = $this->container->get('security.context')->getToken()->getUser();

        $criteria = array_merge(
            [
                'mission_id' => $request->attributes->get('id')
            ],
            $this->config->getCriteria([])
        );

        $repository = $this->getRepository();
        $resources = $this->resourceResolver->getResource(
            $repository,
            'findByMissionId',
            array($criteria, $sorting)
        );

        $resources->setCurrentPage($request->get('page', 1), true, true);
        $resources->setMaxPerPage($this->config->getPaginationMaxPerPage());

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('mission_applications.html'))
            ->setTemplateVar($this->config->getPluralResourceName())
            ->setData(array(
                'applications' => $resources,
                'mission' => $mission
            ))
        ;

        return $this->handleView($view);
    }

    public function showMissionAction(Request $request)
    {
        // Trouver l'application par son id.
        $missionRepository = $this->container->get('service_civique.repository.mission');
        if (!$mission = $missionRepository->find($request->attributes->get('id'))) {
            throw $this->createNotFoundException('Cette mission n\'existe pas');
        };

        $user = $this->container->get('security.context')->getToken()->getUser();

        $criteria = array_merge(
            [
                'id' => $request->attributes->get('application_id')
            ],
            $this->config->getCriteria([])
        );

        $repository = $this->getRepository();
        $resource = $repository->findOneBy($criteria);

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('mission_application_show.html'))
            ->setTemplateVar($this->config->getResourceName())
            ->setData([
                $this->config->getResourceName() => $resource,
            ])
        ;

        if (!$resource->isAnswered()) {
            $form = $this->createForm(new ApplicationAnswerSingleType(), $resource);

            $em = $this->getDoctrine()->getManager();
            $formHandler = new ApplicationAnswerSingleFormHandler($form, $request, $em, $this->container->get('service_civique.mailer'));

            $process = $formHandler->process($resource);

            if ($process) {
                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Votre mail de réponse a bien été envoyé au candidat concerné.'
                );

                return $this->handleView($view);
            }
            $answerSettings = [
                'missionTitle'     => $resource->getMission()->getTitle(),
                'url'              => $this->get('router')->generate('service_civique_homepage', array(), true),
                'organizationName' => $resource->getMission()->getOrganization()->getName(),
            ];
            $defaultAnswers = [
                'positive' => $this->get('translator')->trans('service_civique.application.answer.positive', $answerSettings),
                'negative' => $this->get('translator')->trans('service_civique.application.answer.negative', $answerSettings),
            ];

            $view->setData([
                $this->config->getResourceName() => $resource,
                'form'                           => $form->createView(),
                'defaultAnswers'                 => $defaultAnswers,
            ]);
        }

        return $this->handleView($view);
    }

    public function showAction(Request $request)
    {
        $missionRepository = $this->container->get('service_civique.repository.mission');
        if (!$mission = $missionRepository->findOneBySlug($request->attributes->get('mission_slug'))) {
            throw $this->createNotFoundException('Cette mission n\'existe pas');
        };
        $user = $this->container->get('security.context')->getToken()->getUser();

        $criteria = array_merge(
            [
                'user'    => $user->getId(),
                'mission' => $mission->getId()
            ],
            $this->config->getCriteria([])
        );

        $repository = $this->getRepository();
        $resource = $repository->findByMission($criteria);

        // Set SEO variables
        $seoPageConfigurator = $this->container->get('service_civique.seo_page_configurator');
        $seoPageConfigurator->setParameter('title', $mission->getTitle());

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('show.html'))
            ->setData(array(
                $this->config->getResourceName() => $resource
            ))
        ;

        if (!$resource->getIsPoked() && $resource->getStatus() == Application::WAITING_ANSWER) {
            $form = $this->createForm(new ApplicationPokeType(), $resource);

            $messageText = $this->get('translator')->trans('service_civique.application.answer.poke', array(
                    '%missionTitle%'   => $mission->getTitle(),
                    '%url%'            => $this->container->getParameter('base_url'),
                    '%datePoke%'       => $resource->getCreated()->format('d/m/Y'),
                    '%userMail%'       => $resource->getUser()->getEmail(),
                    '%urlApplication%' => $this->get('router')->generate(
                        'service_civique_application_missions_application_show',
                        array(
                            'id'             => $mission->getId(),
                            'application_id' => $resource->getId()
                        ),
                        true
                    ),
                    '%userName%'       => $resource->getUser()->getFullName(),
                )
            );

            $form->get('content')->setData($messageText);
            $form->handleRequest($request);
            if ('POST' == $request->getMethod() && $form->isValid()) {
                $resource->setIsPoked(new \Datetime());
                $mailer = $this->container->get('service_civique.mailer');
                $mail_message = $mailer->createNewMessage(
                    'ServiceCiviqueMailerBundle:Notification:application_poke.html.twig',
                    array(
                        'content'   => nl2br($messageText),
                    ),
                    $resource->getUser()->getEmail(),
                    $this->getOrganizationMails($resource->getMission()->getOrganization())
                );

                if ($this->domainManager->update($resource) && $mailer->send($mail_message)) {
                    $this->get('session')->getFlashBag()->set(
                        'success',
                        'Votre mail de relance a bien été envoyé à l\'organisme.'
                    );

                    return $this->handleView($view);
                }
            }

            $view->setData(array(
                    $this->config->getResourceName() => $resource,
                    'form'                           => $form->createView()
                ))
            ;
        }

        return $this->handleView($view);
    }

    public function getOrganizationMails($organization)
    {
        $mails = array();
        $mails[] = $organization->getContactEmail();
        if ($organization->getUser()) {
            $mails[] = $organization->getUser()->getEmail();
        }
        if ($organization->getApprovedOrganization()) {
            if ($organization->getApprovedOrganization()->getUser()) {
                $mails[] = $organization->getApprovedOrganization()->getUser()->getEmail();
            }
            $mails[] = $organization->getApprovedOrganization()->getContactEmail();
        }

        return array_values(array_filter(array_unique($mails)));
    }

    protected function getApplicationByUserMission($missionSlug)
    {
        $repository  = $this->getRepository();
        $userId      = $this->get('security.context')->getToken()->getUser()->getId();
        $mission = $this->container
            ->get('service_civique.repository.mission')
            ->findOneBySlug($missionSlug);
        if ($application = $repository->findOneBy(['user' => $userId, 'mission' => $mission->getId()])) {
            return $application;
        }

        return false;
    }

    public function createAction(Request $request)
    {
        // if (!$this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
        //     return $this->redirect($this->generateUrl('fos_user_registration_register'));
        // }

        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION') || $this->get('security.context')->isGranted('ROLE_WEBMASTER')) {
            throw new \Exception("Vous n'avez pas accès à cette page.");
        }

        if ($this->getRequest()->get('cancel')) {
            if ($application = $this->getApplicationByUserMission($this->getRequest()->get('mission_slug'))) {
                if ($application->getIsPreview() == 1) {
                    $this->domainManager->delete($application);
                    $this->get('session')->getFlashBag()->clear();
                }
            }
        }

        $resource = $this->createNew();

        if (!$resource->getMission()->isAvailable()) {
            throw new \RuntimeException('Cette mission n\'existe pas');
        }

        $form = $this->getForm($resource);

        // remove preview if exist
        $repository = $this->getRepository();
        if ($this->container->get('security.context')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $user = $this->container->get('security.context')->getToken()->getUser();
            if ($preview = $repository->findPreviewByMission([
                'user'    => $user->getId(),
                'mission' => $resource->getMission()->getId()
            ])) {
                $this->domainManager->delete($preview);
            }
        }

        if ($form->handleRequest($request)->isValid()) {

            if ($form->get('removeResume')->getData() == 1) {
                $resource->setPath(null);
            }
            $resource->setIsPreview(1);
            $resource = $this->domainManager->create($resource);

            if (null === $resource) {
                return $this->redirectHandler->redirectToIndex();
            }

            $this->get('session')->getFlashBag()->clear();

            return $this->redirectHandler->redirectToRoute('service_civique_application_preview', [
                'mission_slug' => $resource->getMission()->getSlug()
            ]);
            // return $this->redirectHandler->redirectTo($resource);
        }

        foreach ($form->getErrors() as $key => $value) {
            if ($value->getMessage() == 'Vous avez déjà candidaté à cette mission.') {

                $this->get('session')->getFlashBag()->set(
                    'success',
                    'Vous avez déjà candidaté à cette mission.'
                );

                return $this->redirectHandler->redirectToRoute('service_civique_application_show', [
                    'mission_slug' => $resource->getMission()->getSlug()
                ]);
            }
        }

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($form));
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('create.html'))
            ->setData(array(
                $this->config->getResourceName() => $resource,
                'form'                           => $form->createView()
            ))
        ;

        return $this->handleView($view);
    }

    public function validationAction(Request $request)
    {
        if ($this->get('security.context')->isGranted('ROLE_ORGANIZATION') || $this->get('security.context')->isGranted('ROLE_WEBMASTER')) {
            throw new \Exception("Vous n'avez pas accès à cette page.");
        }

        if ($application = $this->getApplicationByUserMission($this->getRequest()->get('mission_slug'))) {
            $application->setIsPreview(0);
            $this->domainManager->update($application);
            $event = new GenericEvent($application);
            $dispatcher = $this->container->get('event_dispatcher');
            $dispatcher->dispatch('service_civique.application.validation', $event);
            $this->get('session')->getFlashBag()->set(
                'success',
                $this->get('translator')->trans('service_civique.application.create', array(), 'flashes')
            );
            $this->get('session')->getFlashBag()->add(
                'tags',
                $application->getMission()->getTaxon()->getName()
            );
            $this->get('session')->getFlashBag()->add(
                'trackPageview',
                '/confirmation/postuler'
            );

            return $this->redirectHandler->redirectToRoute('service_civique_application_show', [
                'mission_slug' => $this->getRequest()->get('mission_slug')
            ]);
        }

        throw new \RuntimeException("Une erreur est survenue");
    }

    public function previewAction(Request $request)
    {
        // TODO SECURE

        if ($resource = $this->getApplicationByUserMission($this->getRequest()->get('mission_slug'))) {
            if ($resource->getIsPreview() == 1) {
                $view = $this
                    ->view()
                    ->setTemplate($this->config->getTemplate('preview.html'))
                    ->setData(array(
                        $this->config->getResourceName() => $resource,
                    ))
                ;

                return $this->handleView($view);
            }

            return $this->redirect($this->generateUrl(
                'service_civique_application_show',
                array('mission_slug' => $this->getRequest()->get('mission_slug'))
            ));
        }

        throw new \RuntimeException("Une erreur est survenue");
    }

    /**
     * retrieveUser
     *
     * @param string $usernameOrEmail
     * @param string $password
     */
    protected function retrieveUser($usernameOrEmail, $password)
    {
        return false;
    }

    /**
     * authenticateUser
     *
     * @param User $user
     */
    protected function authenticateUser(User $user)
    {
         // Here, "public" is the name of the firewall in your security.yml
        $token = new UsernamePasswordToken($user, $user->getPassword(), "public", $user->getRoles());
        $this->get("security.context")->setToken($token);

        // Fire the login event
        // Logging the user in above the way we do it doesn't do this automatically
        $event = new InteractiveLoginEvent($request, $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
    }

    /**
     * We override this method to add the current user organistion to the newly created mission
     * {@inheritDoc}
     */
    public function createNew()
    {
        // Retrieve current user to prepropulate the form
        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            $connectedUser = $this->container->get('security.context')->getToken()->getUser();
            if ($profile = $connectedUser->getProfile()) {
                $application = $this->resourceResolver->createResource($this->getRepository(), 'createNewFromProfile', array($profile));
            } else {
                $application = parent::createNew();
            }
        } else {
            $application = parent::createNew();

            $jeune = new User();
            $jeune
                ->setType(User::MISSION_SEEKER_TYPE)
                ->addRole('ROLE_JEUNE');

            $application->setUser($jeune);
        }

        $mission = $this->container
            ->get('service_civique.repository.mission')
            ->findOneBySlug($this->getRequest()->get('mission_slug'));
        if ($mission) {
            $application->setMission($mission);
        }

        $application->setStatus(Application::WAITING_ANSWER);

        return $application;
    }

    public function createAnswerAction(Request $request)
    {
        // retreive current connected user
        $connectedUser = $this->container->get('security.context')->getToken()->getUser();

        $organization = $connectedUser->getOrganization();

        $mission = $this->container
            ->get('service_civique.repository.mission')
            ->findOneBySlug($this->getRequest()->get('mission_slug'));

        if ($mission) {
            $form = $this->createForm(new ApplicationAnswerType());
            $messageText = $this->get('translator')->trans('service_civique.application.answer.negative', array(
                    'missionTitle'     => $mission->getTitle(),
                    'url'              => $this->container->getParameter('base_url'),
                    'organizationName' => $organization->getName(),
                )
            );
            if ($this->getRequest()->get('status') == 'positif') {
                $messageText = $this->get('translator')->trans('service_civique.application.answer.positive', array(
                        'missionTitle'     => $mission->getTitle(),
                        'url'              => $this->get('router')->generate('fos_user_profile_edit', array(), true),
                        'organizationName' => $organization->getName(),
                    )
                );
            }
            $form->get('messageText')->setData($messageText);
            $form->get('mails')->setData($this->getRequest()->get('mails'));
            $em = $this->getDoctrine()->getManager();

            $formHandler = new ApplicationAnswerFormHandler($form, $request, $em, $this->container->get('service_civique.mailer'));

            $process = $formHandler->process();

            if ($process) {
                $this->get('session')->getFlashBag()->add(
                    'success',
                    'Votre mail de réponse a bien été envoyé aux candidats concernés.'
                );

                return $this->redirect($this->generateUrl(
                    'service_civique_application_missions_applications',
                    array('id' => $mission->getId())
                ));
            }

            $view = $this
                ->view()
                ->setTemplate($this->config->getTemplate('index.html'))
                ->setTemplateVar($this->config->getPluralResourceName())
                ->setData(array(
                    'form'    => $form->createView(),
                    'mission' => $mission,
                ))
            ;

            return $this->handleView($view);
        }

        throw new Exception("Il n'y a pas de mission associée");
    }

    /**
     * exportAction
     *
     * @param Request $request
     */
    public function exportAction(Request $request)
    {
        $missionRepository = $this->container->get('service_civique.repository.mission');

        if (!$mission = $missionRepository->find($request->attributes->get('id'))) {
            throw $this->createNotFoundException('Cette mission n\'existe pas');
        };

        $applicationExporter = $this->container->get('service_civique.application.exporter');

        $filename = 'Export_application_'. date('Ymd-His') .'.xls';

        $applicationExporter->createExportFile($filename, array(
            'mission' => $mission
        ));

        return $applicationExporter->createFileResponse($filename);
    }

    public function chooseJeuneMissionAction(Request $request)
    {
        $sorting  = $this->config->getSorting();

        $missionRepository = $this->container->get('service_civique.repository.mission');
        if (!$mission = $missionRepository->find($request->attributes->get('id'))) {
            throw $this->createNotFoundException('Cette mission n\'existe pas');
        };

        // Get current user
        $user = $this->container->get('security.context')->getToken()->getUser();

        $repository = $this->getRepository();
        if ($request->query->get('applications')) {
            $applications = explode(',', $request->query->get('applications'));
            foreach ($applications as $applicationId) {
                $application = $repository->find($applicationId);
                $application->getUser()->setType(User::VOLUNTEER_TYPE);
                if ($application->getIsSelected() != Application::POSITIVE_ANSWER) {
                    $application->setIsSelected(Application::POSITIVE_ANSWER);
                    $this->domainManager->update($application);
                }

            }
        }

        $form = $this->createFormBuilder()
            ->add('comment', 'textarea', array(
                'required' => false,
                'label' => "Indiquer le nom d'un(e) volontaire choisi(e) et qui ne figure pas dans le tableau ci-dessus",
                'data' => $mission->getComment()
            ))
            ->add('save', 'submit', array(
                'label' => 'Valider',
                // 'required' => false,
            ))
            ->getForm()
        ;

        $form->handleRequest($request);


        if ($request->isMethod('POST')) {
            $mission->setComment($form->get('comment')->getData());
            $this->domainManager->update($mission);
        }


        $criteria = array_merge(
            [
                'mission_id' => $request->attributes->get('id')
            ],
            $this->config->getCriteria([])
        );

        $resources = $this->resourceResolver->getResource(
            $repository,
            'findByMissionId',
            array($criteria, $sorting)
        );

        $resources->setCurrentPage($request->get('page', 1), true, true);
        $resources->setMaxPerPage($this->config->getPaginationMaxPerPage());

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('mission_applications.html'))
            ->setTemplateVar($this->config->getPluralResourceName())
            ->setData(array(
                'applications' => $resources,
                'mission' => $mission,
                'form' => $form->createView()
            ))
        ;

        return $this->handleView($view);
    }


}
