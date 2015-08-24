<?php

namespace ServiceCivique\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WidgetController extends Controller
{
    public function previewAction(Request $request)
    {
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $organization = null;
        $data = ['width' => 280];

        if ($this->container->get('security.context')->isGranted('ROLE_ORGANIZATION')) {
            $organization = $user->getOrganization();
            $data['organization'] = $organization;
        }
        $form = $this->createForm('service_civique_widget_search_mission', $data);

        return new Response($this->renderView(
            'ServiceCiviqueWebBundle:Frontend/Widget:preview.html.twig',
            [
                'form'         => $form->createView(),
                'organization' => $organization,
            ]
        ));
    }
    /**
     * buildSearchQuery
     *
     * @param string $query
     * @param string $approvalNumber
     */
    protected function buildSearchQuery($query)
    {
        $boolQuery = new \Elastica\Query\Bool();

        $searchQuery = new \Elastica\Query\QueryString();
        $searchQuery->setParam('query', \Elastica\Util::replaceBooleanWordsAndEscapeTerm($query));
        $boolQuery->addShould($searchQuery);

        return $boolQuery;
    }

    public function showAction(Request $request, $id)
    {
        if(!$organization = $this->getDoctrine()
            ->getRepository('ServiceCivique\Bundle\CoreBundle\Entity\Organization')
            ->find($id)
        ) {
            throw new \Exception("Cet organisme n'existe pas.");
        }

        $keyValueStore = $this->container->get('service_civique.key_value_store');
        $banner = null;
        if ($bannerDestination = $keyValueStore->get('banner_destination')) {
            $bannerDestinationValue = stream_get_contents($bannerDestination->getDataValue());
            $banner = ['destination' => $bannerDestinationValue];
        }

        $missionSearchRepository = $this->container->get('service_civique.search_repository.mission_repository');
        $missions = $missionSearchRepository->findLastMissionsFromOrganization($organization);

        return new Response($this->renderView(
            'ServiceCiviqueWebBundle:Frontend/Widget:show.html.twig',
            array(
                'banner'       => $banner,
                'missions'     => $missions,
                'organization' => $organization
            )
        ));
    }

}
