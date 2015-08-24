<?php

namespace ServiceCivique\Bundle\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Departements\Datasource\JsonDatasource;
use Departements\Provider;
use Symfony\Component\HttpFoundation\JsonResponse;

class MissionSearchController extends ResourceController
{
    public function deleteProfileMissionSearchAction(Request $request)
    {
        $missionSearch = $this->getCurrentUserMissionSearch();
        if ($missionSearch) {
            $confirmed = $request->query->get('confirmed');
            if ($confirmed == '1') {
                // OK, vous avez supprimé votre abonnement
                $em = $this->getDoctrine()->getManager();
                $em->remove($missionSearch);
                $em->flush();
                $this->get('session')->getFlashBag()->add(
                    'success',
                    "Vous avez été désabonné de votre recherche de missions"
                );

                return $this->redirect($this->generateUrl('service_civique_profile_mission_search_edit'));
            } else {
                $view = $this
                    ->view()
                    ->setTemplate($this->config->getTemplate('delete.html'))
                    ->setData(array(
                        'mission_search' => $missionSearch,
                    ))
                ;

                return $this->handleView($view);
            }
        } else {
            return $this->redirect($this->generateUrl('service_civique_profile_mission_search_edit'));
        }

    }

    public function editProfileMissionSearchAction(Request $request)
    {
        $missionSearchRepository = $this->container->get('service_civique.repository.mission_search');

        $missionSearch = $this->getCurrentUserMissionSearch();

        if (!$missionSearch) {
            $missionSearch = $this->createNew();
            $missionSearch->setUser($this->getCurrentUser());
        }
        if ($request->query->get('criteria')) {
            $missionSearchRepository->populateWithCriteria($missionSearch, $request->query->get('criteria'));
        }

        $form = $this->get('form.factory')->createNamed('criteria', 'service_civique_mission_search', $missionSearch);

        $criteria = $request->query->get('criteria');
        if ($form->handleRequest($request)->isValid()) {

            $missionSearch = $this->domainManager->create($missionSearch);
            if ($this->config->isApiRequest()) {
                return $this->handleView($this->view($missionSearch));
            }

            return $this->redirect($this->generateUrl('service_civique_mission_list', [
                    'criteria' => $criteria
                ]));
        }

        // Set SEO variables
        $seoPageConfigurator = $this->container->get('service_civique.seo_page_configurator');
        $seoPageConfigurator
            ->setTitle('service_civique.form.mission_search.edit.page_title');

         $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('edit.html'))
            ->setData(array(
                'mission_search' => $missionSearch,
                'form'           => $form->createView(),
                'criteria'       => $criteria,
            ))
         ;

        return $this->handleView($view);
    }

    protected function getCurrentUserMissionSearch()
    {
        $user = $this->getCurrentUser();

        return $this->getRepository()->findOneBy(array(
            'user' => $user
        ));
    }

    protected function getCurrentUser()
    {
        return $this->container->get('security.context')->getToken()->getUser();
    }

    public function listAreaAction(Request $request)
    {
        if ($request->query->get('departement')) {
            $departementCode = $request->query->get('departement');
            $file = $this->container->getParameter('kernel.root_dir') . '/../vendor/polem/departements/src/Departements/Resources/datas/datas-with-communes.json';
            $datasource = new JsonDatasource($file);
            $provider = new Provider($datasource);

            if ($area = $provider->findDepartementByCode($departementCode)) {
                $areaCode = $area->getRegion()->getCode();

                return new JsonResponse($areaCode);
            }

        }

        return new JsonResponse('error');
    }
}
