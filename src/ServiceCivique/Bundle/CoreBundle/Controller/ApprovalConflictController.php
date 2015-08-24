<?php

namespace ServiceCivique\Bundle\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class ApprovalConflictController extends ResourceController
{
    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $resource = $this->createNew();

        if ($approvalNumber = $request->query->get('approval_number')) {
            $resource->setApprovalNumber($approvalNumber);
        }

        $form = $this->getForm($resource);

        if ($form->handleRequest($request)->isValid()) {
            $resource = $this->domainManager->create($resource);

            $mailer = $this->container->get('service_civique.mailer');
            $url = $this->container->get('router')->generate('service_civique_backend_approval_conflict_update', array('id' => $resource->getId()), true);

            $mail_message = $mailer->createNewMessage(
                'ServiceCiviqueMailerBundle:Notification:conflit_agrement.html.twig',
                array(
                    'url'   => $url,
                ),
                $this->container->getParameter('mandrill_default_sender'),
                'questions.site@service-civique.gouv.fr'
            );
            $mailer->send($mail_message);

            if ($this->config->isApiRequest()) {
                return $this->handleView($this->view($resource));
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

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $criteria = $this->config->getCriteria();
        $sorting = $this->config->getSorting();

        $repository = $this->getRepository();
        $approvalRepository = $this->getDoctrine()->getRepository('ServiceCivique\Bundle\CoreBundle\Entity\Approval');
        $organizationRepository = $this->getDoctrine()->getRepository('ServiceCivique\Bundle\CoreBundle\Entity\Organization');

        if ($this->config->isPaginated()) {
            $resources = $this->resourceResolver->getResource(
                $repository,
                'createPaginator',
                array($criteria, $sorting)
            );
            $resources->setCurrentPage($request->get('page', 1), true, true);
            $resources->setMaxPerPage($this->config->getPaginationMaxPerPage());

            if ($this->config->isApiRequest()) {
                $resources = $this->getPagerfantaFactory()->createRepresentation(
                    $resources,
                    new Route(
                        $request->attributes->get('_route'),
                        $request->attributes->get('_route_params')
                    )
                );
            }
        } else {
            $resources = $this->resourceResolver->getResource(
                $repository,
                'findBy',
                array($criteria, $sorting, $this->config->getLimit())
            );
        }

        foreach ($resources as $resource) {
            $resource->pdf = null;
            if ($approval = $approvalRepository->findOneByApprovalNumber($resource->getApprovalNumber())) {
                $resource->pdf = $approval->getPdfUrl();
            }
            $resource->organizations = $organizationRepository->findByApprovalNumber($resource->getApprovalNumber());

        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('index.html'))
            ->setTemplateVar($this->config->getPluralResourceName())
            ->setData($resources)
        ;

        return $this->handleView($view);
    }

    public function deleteAction(Request $request)
    {
        $resource = $this->findOr404($request);
        $this->domainManager->delete($resource);

        return $this->redirectHandler->redirectToIndex();
    }
}
