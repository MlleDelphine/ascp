<?php

namespace ServiceCivique\Bundle\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use ServiceCivique\Bundle\CoreBundle\Entity\MissionReport;
use Symfony\Component\HttpFoundation\JsonResponse;

class MissionReportController extends ResourceController
{
    /**
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request)
    {
        $response = new JsonResponse();
        $missionId = $request->get('id');
        $missionRepository = $this->getDoctrine()->getRepository('ServiceCivique\Bundle\CoreBundle\Entity\Mission');
        if (!$mission = $missionRepository->find($missionId)) {
            return $response->setData("Cette mission n'existe pas");
        }

        // Create or update
        $repository = $this->getRepository();
        if (!$report = $repository->findOneByMission($missionId)) {
            // $report = new MissionReport();
            $report = $this->createNew();
            $report->setMission($mission);
        }
        // var_dump($report);
        // var_dump($mission->getId());

        $status = $request->get('status');
        if ($status == MissionReport::REPORT_DIPLOMA) {
            $report->setCountDiploma($report->getCountDiploma() + 1);
        } else if ($status == MissionReport::REPORT_TASK) {
            $report->setCountTask($report->getCountTask() + 1);
        } else if ($status == MissionReport::REPORT_JOB) {
            $report->setCountJob($report->getCountJob() + 1);
        } else if ($status == MissionReport::REPORT_INTEREST) {
            $report->setCountInterest($report->getCountInterest() + 1);
        }
        $report = $this->domainManager->create($report);
        return $response->setData('ok');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    // public function indexAction(Request $request)
    // {
    //     $criteria = $this->config->getCriteria();
    //     $sorting = $this->config->getSorting();

    //     $repository = $this->getRepository();
    //     $approvalRepository = $this->getDoctrine()->getRepository('ServiceCivique\Bundle\CoreBundle\Entity\Approval');
    //     $organizationRepository = $this->getDoctrine()->getRepository('ServiceCivique\Bundle\CoreBundle\Entity\Organization');

    //     if ($this->config->isPaginated()) {
    //         $resources = $this->resourceResolver->getResource(
    //             $repository,
    //             'createPaginator',
    //             array($criteria, $sorting)
    //         );
    //         $resources->setCurrentPage($request->get('page', 1), true, true);
    //         $resources->setMaxPerPage($this->config->getPaginationMaxPerPage());

    //         if ($this->config->isApiRequest()) {
    //             $resources = $this->getPagerfantaFactory()->createRepresentation(
    //                 $resources,
    //                 new Route(
    //                     $request->attributes->get('_route'),
    //                     $request->attributes->get('_route_params')
    //                 )
    //             );
    //         }
    //     } else {
    //         $resources = $this->resourceResolver->getResource(
    //             $repository,
    //             'findBy',
    //             array($criteria, $sorting, $this->config->getLimit())
    //         );
    //     }

    //     foreach ($resources as $resource) {
    //         $resource->pdf = null;
    //         if ($approval = $approvalRepository->findOneByApprovalNumber($resource->getApprovalNumber())) {
    //             $resource->pdf = $approval->getPdfUrl();
    //         }
    //         $resource->organizations = $organizationRepository->findByApprovalNumber($resource->getApprovalNumber());

    //     }

    //     $view = $this
    //         ->view()
    //         ->setTemplate($this->config->getTemplate('index.html'))
    //         ->setTemplateVar($this->config->getPluralResourceName())
    //         ->setData($resources)
    //     ;

    //     return $this->handleView($view);
    // }

    public function deleteAction(Request $request)
    {
        $resource = $this->findOr404($request);
        $this->domainManager->delete($resource);

        return $this->redirectHandler->redirectToIndex();
    }
}
