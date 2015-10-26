<?php

namespace ServiceCivique\Bundle\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ServiceCivique\Bundle\CoreBundle\Entity\Mission;
use ServiceCivique\Bundle\CoreBundle\Entity\MissionLog;
use ServiceCivique\Bundle\CoreBundle\Store\MissionStore;
use Symfony\Component\HttpFoundation\JsonResponse;
use ServiceCivique\Bundle\CoreBundle\Form\Type\MissionAnswerMailType;
use ServiceCivique\Bundle\WebBundle\Form\DataTransformer\DateStringFormatFixer;

class MissionController extends ResourceController
{

    public function createAction(Request $request)
    {
        $organization = $this->container->get('security.context')->getToken()->getUser()->getOrganization();
        if ($organization->getApprovalNumber() == 'XX-000-00-00000-00') {
            return $this->redirect($this->generateUrl('service_civique_organization_update_approval_number'));
        }

        $resource = $this->createNew();

        // Duplicate
        if ($request->query->has('mission') && $missionId = $request->query->get('mission')) {
            $missionRepository = $this->container->get('service_civique.repository.mission');
            $resource = $missionRepository->find($missionId);
            $resource->setDuplicate($missionId);
        }

        $form = $this->getForm($resource);
        if ($request->query->has('key') && $storedResource = $this->getMissionStore()->get($request->query->get('key'), $form)) {
            $resource = $storedResource;
            $confirmed = $request->query->get('confirmed', false); // check confirmed

            if ($confirmed) {
                $resource = $this->publishMission($resource);
                $missionRepository = $this->container->get('service_civique.repository.mission');

                $isDuplicate = true;
                // If not duplicate
                if (!$originalMission = $missionRepository->find($resource->getDuplicate())) {
                    $originalMission = $resource;
                    $isDuplicate = false;
                }

                $missionLogService = $this->container->get('service_civique.service.mission_log_service');
                $resource = $missionLogService->saveMissionWithLog($originalMission, $resource, $isDuplicate);

                $this->domainManager->create($resource);
                // If duplicate without changes
                if ($resource->getStatus() == Mission::STATUS_AVAILABLE) {
                    $this->get('session')->getFlashBag()->set(
                        'success',
                        'Votre mission a bien été créée.'
                    );
                }
                $user = $this->get('security.context')->getToken()->getUser();
                if ($user->isOrganization()) {
                    $organization = $user->getOrganization();
                    $organization = $this->fillOrganizationWithMission($organization, $resource);
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($organization);
                    $em->flush($organization);
                }

                return $this->redirectHandler->redirectTo($resource);
            }
        }

        if ($form->handleRequest($request)->isValid() && !$request->query->has('key')) {

            // save mission in a key value store
            if ($form->get('actions')->get('preview')->isClicked()) {
                $key = $this->getMissionStore()->add($request->request->get('service_civique_mission'));

                return $this->redirectToPreview($resource, $key, true);
            }

            // if save as draft is clicked set mission status as Mission::STATUS_DRAFT
            if ($form->get('actions')->get('saveAsDraft')->isClicked()) {
                $resource->setStatus(Mission::STATUS_DRAFT);
            }
            $this->domainManager->create($resource);

            if ($resource->getStatus() == Mission::STATUS_DRAFT) {
                $this->get('session')->getFlashBag()->set(
                    'success',
                    'Votre mission a été enregistrée dans vos brouillons. Vous pourrez la reprendre plus tard pour l\'envoyer pour validation.'
                );
            }

            if (null === $resource) {
                return $this->redirectHandler->redirectToIndex();
            }

            return $this->redirectHandler->redirectTo($resource);
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

    private function fillOrganizationWithMission($organization, $mission)
    {
        $organization->setDescription($mission->getOrganizationDescription());
        $organization->setWebsite($mission->getWebsite());

        return $organization;
    }

    /**
     * @param  Request          $request
     * @return RedirectResponse
     */
    public function deleteAction(Request $request)
    {
        $resource = $this->findOr404($request);

        if (!$this->get('security.context')->isGranted('OWNER', $resource)) {
            throw $this->createAccessDeniedException();
        }

        $this->domainManager->delete($resource);

        return $this->redirectHandler->redirectToIndex();
    }

    /**
     * @param  Request          $request
     * @return RedirectResponse
     */
    public function deleteAdminAction(Request $request)
    {
        $resource = $this->findOr404($request);

        $this->domainManager->delete($resource);

        return $this->redirectHandler->redirectToIndex();
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function previewAction(Request $request)
    {
        $resource = $this->getMissionStore()->get($request->query->get('key'), $this->getForm(), false);

        $seoPageConfigurator = $this->container->get('service_civique.seo_page_configurator');
        $seoPageConfigurator
            ->setParameter('title', $resource->getTitle());

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('preview.html'))
            ->setData(array(
                $this->config->getResourceName() => $resource,
                'key'                            => $request->query->get('key'),
                'id'                             => $request->query->get('id'),
                'route'                          => $request->query->get('route')
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function updateAction(Request $request)
    {
        $resource = $this->findOr404($request);

        if (!$this->get('security.context')->isGranted('OWNER', $resource)) {
            throw $this->createAccessDeniedException();
        }

        // we can't update STATUS_UNDER_VALIDATION and STATUS_FILLED mission
        if ($resource->getStatus() == Mission::STATUS_FILLED || $resource->getStatus() == Mission::STATUS_UNDER_VALIDATION) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->getForm($resource);
        $method = $request->getMethod();

        $missionRepository = $this->container->get('service_civique.repository.mission');
        $originalMission = clone $missionRepository->find($resource->getId());

        if ($request->query->has('key') && $storedResource = $this->getMissionStore()->get($request->query->get('key'), $form)) {
            // retreive resource in the key store
            $resource = $storedResource;

            $confirmed = $request->query->get('confirmed', false); // check confirmed

            if ($confirmed) {
                if ($resource->getStatus() == Mission::STATUS_DRAFT) { // @Todo refactoring with tests...
                    $resource = $this->publishMission($resource);
                    $resource = $this->domainManager->update($resource);
                    $this->get('session')->getFlashBag()->set(
                        'success',
                        $this->get('translator')->trans('service_civique.mission.create', array(), 'flashes')
                    );
                } else {
                    $resource = $this->publishMission($resource);
                    $missionLogService = $this->container->get('service_civique.service.mission_log_service');
                    $resource = $missionLogService->saveMissionWithLog($originalMission, $resource);
                    $resource = $this->domainManager->update($resource);
                    //beurk
                    $hasDiff = false;
                    if ($originalMission->getTitle() != $resource->getTitle()) {
                        $hasDiff = true;
                    }
                    if ($originalMission->getDescription() != $resource->getDescription()) {
                        $hasDiff = true;
                    }
                    if ($originalMission->getStartDate()->format('Y') != $resource->getStartDate()->format('Y')) {
                        $hasDiff = true;
                    }
                    if ($hasDiff) {
                        $this->get('session')->getFlashBag()->set(
                            'success',
                            'Votre mission a été envoyée pour validation.'
                        );
                    } else {
                        $this->get('session')->getFlashBag()->set(
                            'success',
                            'La mission a correctement été modifiée.'
                        );
                    }
                }

                return $this->redirectHandler->redirectTo($resource);
            }
        }

        if (in_array($method, array('POST', 'PUT', 'PATCH')) &&
            $form->submit($request, !$request->isMethod('PATCH'))->isValid()) {

            // save mission in a key value store
            if ($form->get('actions')->get('preview')->isClicked()) {
                $key = $this->getMissionStore()->add($request->request->get('service_civique_mission'));

                return $this->redirectToPreview($resource, $key);
            }

            $this->domainManager->update($resource);

            return $this->redirectHandler->redirectTo($resource);
        }

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($form));
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('update.html'))
            ->setData(array(
                $this->config->getResourceName() => $resource,
                'form'                           => $form->createView()
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function updateAdminAction(Request $request)
    {
        $resource = $this->findOr404($request);

        $form = $this->get('form.factory')->createNamed('service_civique_mission', 'service_civique_mission_admin', $resource);
        $method = $request->getMethod();

        // find mission log
        $em = $this->getDoctrine()->getManager();
        $missionLogRepository = $em->getRepository('ServiceCivique\Bundle\CoreBundle\Entity\MissionLog');
        $missionLog = $missionLogRepository->findOneByMission($resource->getId());
        $missionLogData = null;

        if ($missionLog) {
            $missionLogData = unserialize($missionLog->getDataValue());
        }

        if (in_array($method, array('POST', 'PUT', 'PATCH')) &&
            $form->submit($request, !$request->isMethod('PATCH'))->isValid()) {

            // Special usecase: it's undervalidation
            if ($form->get('previous_status')->getData() == Mission::STATUS_UNDER_VALIDATION) {
                // If there is a log => 1 of 3 fields is modified
                if ($missionLog) {
                    $em->remove($missionLog);
                    $resource->setMissionLog(null);
                    $em->flush($missionLog);
                }
            }

            $resource->setPublished(new \Datetime());
            $this->domainManager->update($resource);

            return $this->redirectHandler->redirectTo($resource);
        }

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($form));
        }

        $answerForm = $this->createForm(new MissionAnswerMailType());
        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('update.html'))
            ->setData(array(
                $this->config->getResourceName() => $resource,
                'missionLog'                     => $missionLogData,
                'form'                           => $form->createView(),
                'answer_form'                    => $answerForm->createView(),
                'mails'                          => $this->getOrganizationMails($resource->getOrganization()),
            ))
        ;

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

        return array_unique($mails);
    }

    /**
     * searchAction
     *
     * @param Request $request
     */
    public function searchAction(Request $request)
    {
        // set default criteria and sorting
        $default_criteria = array(
            'start_date'  => date('Y-m-d'),
            'is_overseas' => 0,
        );

        $criteria = $this->config->getCriteria([]);

        $criteria = array_merge($default_criteria, $criteria);

        $sorting = $this->config->getSorting(array(
            'start_date' => 'asc'
        ));

        $repository = $this->getRepository();

        $query = $repository->createFrontendQuery($criteria, $sorting);

        $resources = $repository->findPaginated($query);

        // add vacancies count
        $vacanciesCountQuery = clone $query;

        $vacancies = $this
            ->get('fos_elastica.index.service_civique.mission')
            ->search($vacanciesCountQuery->setSize(0))
            ->getAggregation("vacancies_sum")['value']
        ;

        $resources->setCurrentPage($request->get('page', 1), true, true);
        $resources->setMaxPerPage($request->query->get('paginate', 12));

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('index.html'))
            ->setData(array(
                $this->config->getPluralResourceName() => $resources,
                'criteria'                             => $criteria,
                'vacancies'                            => $vacancies,
            ))
        ;

        // Set SEO variables
        $seoPageConfigurator = $this->container->get('service_civique.seo_page_configurator');
        $seoPageConfigurator
            ->setTitle('service_civique.form.mission_search.title');

        return $this->handleView($view);
    }

    /**
     * organizationIndexAction
     *
     * @param Request $request
     */
    public function organizationIndexAction(Request $request)
    {
        // if we come from edit or create page
        // wait for yellow status
        if ($request->query->has('id')) {
            $this->waitForEsStatus('yellow');
        }

        $user = $this->container->get('security.context')->getToken()->getUser();

        $organization = $user->getOrganization();

        if ($organization->getApprovalNumber() == 'XX-000-00-00000-00') {
            return $this->redirect($this->generateUrl('service_civique_organization_update_approval_number'));
        }

        $default_criteria = array(
            'statuses' => array(
                Mission::STATUS_DRAFT,
                Mission::STATUS_AVAILABLE,
                Mission::STATUS_UNDER_REVIEW,
                Mission::STATUS_FILLED,
                Mission::STATUS_UNDER_VALIDATION,
            ),
            //'published' => date('Y-m-d', strtotime('- 6 months'))
        );

        $criteria = array_merge($default_criteria, $this->config->getCriteria([]));

        $sorting = $this->config->getSorting(array('published' => 'desc'));

        $repository = $this->getRepository();

        $resources = $this->resourceResolver->getResource(
            $repository,
            'findFromOrganization',
            array($organization, $criteria, $sorting)
        );

        $resources->setCurrentPage($request->get('page', 1), true, true);
        $resources->setMaxPerPage($this->config->getPaginationMaxPerPage());

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('index.html'))
            ->setData(array(
                $this->config->getPluralResourceName() => $resources,
                'criteria'                             => $criteria,
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * exportAction
     *
     * @param Request $request
     */
    public function exportAction(Request $request)
    {
        $user = $this->container->get('security.context')->getToken()->getUser();

        $missionExporter = $this->container->get('service_civique.mission.exporter');

        $filename = 'Export_missions_'. date('Ymd-His') .'.xls';

        $missionExporter->createExportFile($filename, array(
            'user' => $user
        ));

        return $missionExporter->createFileResponse($filename);
    }

    /**
     * @param Request $request
     *
     * Responds to route {base_url}/missions/search/submit
     * Process Mission Search Form and redirect user to the results page
     *
     * @return Response
     */
    public function searchSubmitAction(Request $request)
    {
        $params = $request->query->all();

        unset($params['page']);

        if ($request->request->has('criteria')) {
            unset($params['page']);
            $params['criteria'] = array();

            $dateFixer = new DateStringFormatFixer('d/m/Y', 'Y-m-d');

            foreach ($request->request->get('criteria') as $key => $value) {
                if (empty($value) || is_null($value)) {
                    continue;
                }

                if ($key == 'location') {
                    foreach ($value as $location_key => $location_value) {
                        $params['criteria'][$location_key] = $location_value;
                    }
                    continue;
                }

                if (in_array($key, array('start_date', 'published'))) {
                    $value = $dateFixer->reverseTransform($value);
                }

                $params['criteria'][$key] = $value;
            }

            if (!isset($params['criteria']['is_overseas']) || !$params['criteria']['is_overseas']) {
                unset($params['criteria']['country']);
                /* Ajout Frédérick Zilbermann Non prise en compte région/département quand on recherche à l'étranger */
            } else {
                unset($params['criteria']['department']);
                unset($params['criteria']['area']);
            }
        }

        if ($request->request->has('sorting')) {
            $params['sorting'] = array($request->request->get('sorting') => 'asc');
        }

        if ($request->request->has('paginate')) {
            $params['paginate'] = $request->request->get('paginate');
        }

        $redirectRoute = $request->query->get('route', 'service_civique_mission_list');
        unset($params['route']);

        return $this->redirect($this->generateUrl($redirectRoute, $params) . '#search-options');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function showAction(Request $request)
    {
        $resource = $this->findOr404($request);
        if ($resource->getStatus() == MISSION::STATUS_DRAFT  && !$this->get('security.context')->isGranted('OWNER', $resource)) {
            throw $this->createNotFoundException('Cette mission n\'existe pas');
        }

        $seoPageConfigurator = $this->container->get('service_civique.seo_page_configurator');

        $seoPageConfigurator
            ->setParameter('title', $resource->getTitle())
            ->setParameter('description', $resource->getDescription());

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('show.html'))
            ->setTemplateVar($this->config->getResourceName())
            ->setData($resource)
        ;

        return $this->handleView($view);
    }

    /**
     * {@inheritDoc}
     */
    public function getRepository()
    {
        $searchRoutes = array('service_civique_mission_list', 'service_civique_organization_mission_index', 'service_civique_backend_mission_index');

        if (in_array($this->config->getRequest()->get('_route'), $searchRoutes)) {
            return $this->getSearchRepository();
        }

        return parent::getRepository();
    }

    public function getSearchRepository()
    {
        return $this->get('fos_elastica.manager.orm')->getRepository('ServiceCivique\Bundle\CoreBundle\Entity\Mission');
    }

    /**
     * We override this method to add the current user organistion to the newly created mission
     * {@inheritDoc}
     */
    public function createNew()
    {
        // retreive current connected user
        $connectedUser = $this->container->get('security.context')->getToken()->getUser();
        $organization = $connectedUser->getOrganization();

        if ($organization) {
            $mission = $this->resourceResolver->createResource($this->getRepository(), 'createNewFromOrganization', array($organization));
        } else {
            $mission = parent::createNew();
        }

        $mission->setStatus(Mission::STATUS_AVAILABLE);

        return $mission;
    }

    /**
     * getMissionStore
     *
     */
    public function getMissionStore()
    {
        return new MissionStore($this->container->get('key_value_store'));
    }

    /**
     * redirectToPreview
     *
     * @param mixed $mission
     * @param mixed $keyValueStoreKey
     */
    protected function redirectToPreview($mission, $keyValueStoreKey, $fromCreate = false)
    {
        $routeParams = array(
            'key' => $keyValueStoreKey
        );

        $routeParams['route'] = 'service_civique_organization_missions_new';

        // is mission is not new add id
        if ($mission->getId()) {
            $routeParams['id']    = $mission->getId();
            $routeParams['route'] = 'service_civique_organization_missions_update';
        }

        if ($fromCreate) {
            $routeParams['create'] = 1;
        }

        return $this->redirectHandler->redirectToRoute('service_civique_mission_preview', $routeParams);
    }

    protected function publishMission($mission)
    {
        // publish draft
        if ($mission->getStatus() == Mission::STATUS_DRAFT) {
            $mission->setStatus(Mission::STATUS_AVAILABLE);
        }

        return $mission;
    }

    public function updateStatusAction(Request $request, $id, $status)
    {
        $response = new JsonResponse();
        if ($request->isXmlHttpRequest()) {
            $missionRepository = $this->container->get('service_civique.repository.mission');
            if ($mission = $missionRepository->find($id)) {
                if (in_array($status, array(MISSION::STATUS_AVAILABLE, MISSION::STATUS_FILLED))) {
                    $em = $this->getDoctrine()->getManager();
                    $mission->setStatus($status);
                    $em->persist($mission);
                    $em->flush();

                    return $response->setData($status);
                }
            }
        }

        return $response->setData('error');
    }

    public function cancelModificationsAction(Request $request, $id)
    {
        $response = new JsonResponse();
        if ($request->isXmlHttpRequest()) {
            $missionRepository = $this->container->get('service_civique.repository.mission');
            if ($mission = $missionRepository->find($id)) {
                $em = $this->getDoctrine()->getManager();
                $missionLogService = $this->container->get('service_civique.service.mission_log_service');
                if ($mission = $missionLogService->cancelModifications($mission)) {
                    return $response->setData('ok');
                }
            }
        }

        return $response->setData('error');
    }

    public function addTagAction(Request $request, $id, $tagSlug)
    {
        $response = new JsonResponse();
        if ($request->isXmlHttpRequest()) {
            $missionRepository = $this->container->get('service_civique.repository.mission');
            $tagRepository = $this->container->get('service_civique.repository.tag');

            if ($mission = $missionRepository->find($id)) {
                if ($tag = $tagRepository->findOneBySlug($tagSlug)) {
                    $em = $this->getDoctrine()->getManager();
                    $mission->setTag($tag);
                    $em->persist($mission);
                    $em->flush();

                    return $response->setData($tag->getSlug());
                }
            }
        }

        return $response->setData('error');
    }

    public function indexAction(Request $request)
    {
        $default_criteria = array(
            'statuses' => array(
                // Mission::STATUS_DRAFT,
                // Mission::STATUS_AVAILABLE,
                Mission::STATUS_UNDER_REVIEW,
                // Mission::STATUS_FILLED,
                Mission::STATUS_UNDER_VALIDATION,
            ),
            /* Modif Frédérick Zilbermann : On doit pouvoir remonter toutes les missions */
            //'published' => date('Y-m-d', strtotime('- 1 month'))
        );

        $criteria = array_merge($default_criteria, $this->config->getCriteria([]));

        $sorting = $this->config->getSorting(array('published' => 'desc'));

        $repository = $this->getRepository();

        $resources = $this->resourceResolver->getResource(
            $repository,
            'findFromAdmin',
            array($criteria, $sorting)
        );

        $resources->setCurrentPage($request->get('page', 1), true, true);
        // $resources->setMaxPerPage($this->config->getPaginationMaxPerPage());
        $resources->setMaxPerPage($request->get('paginate', 20));

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('index.html'))
            ->setData(array(
                $this->config->getPluralResourceName() => $resources,
                'criteria'                             => $criteria,
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * waitForEsStatus
     *
     * @param string $status
     * @param string $timeout
     */
    protected function waitForEsStatus($status, $timeout = '5s')
    {
        $path = sprintf('_cluster/health?wait_for_status=%s&timeout=%s', $status, $timeout);

        return $this->container->get('fos_elastica.client')
            ->request($path, \Elastica\Request::GET);
    }

    public function updateMissionStatus(Request $request)
    {
        if (!$this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $missionIds = $request->query->get('missions');
        $missionIds = explode(',', $missionIds);
        $missionRepository = $this->container->get('service_civique.repository.mission');

        $em = $this->getDoctrine()->getManager();
        foreach ($missionIds as $missionId) {
            if (!$mission = $missionRepository->find($missionId)) {
                throw $this->createNotFoundException('Cette mission n\'existe pas');
            };
            $mission->setStatus(Mission::STATUS_AVAILABLE);
            $em->persist($mission);
        }
        $em->flush();
        $response = new JsonResponse();
        return $response->setData('ok');
    }
}
