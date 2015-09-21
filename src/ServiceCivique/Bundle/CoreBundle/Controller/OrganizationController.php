<?php

namespace ServiceCivique\Bundle\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Form\FormError;
use ServiceCivique\Bundle\CoreBundle\Entity\Organization;

class OrganizationController extends ResourceController
{
    public function ajaxSearchAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            //throw new AccessDeniedException();
        }

        $index = $this->container->get('fos_elastica.index.service_civique.organization');
        $query = $this->buildSearchQuery($request->query->get('q'), $request->query->get('approval_number'), null, true);

        $results = $index->search($query, 25)->getResults();

        // format results
        $output = array();

        foreach ($results as $result) {
            $item = $result->getSource();
            $item['score'] = $result->getScore();

            $output[] = $item;
        }

        $view = $this
            ->view()
            ->setData($output)
            ->setFormat('json')
        ;

        return $this->handleView($view);
    }

    public function ajaxSearchOrganizationAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            //throw new AccessDeniedException();
        }

        $index = $this->container->get('fos_elastica.index.service_civique.organization');
        $query = $this->buildSearchQuery(null, null, $request->query->get('name'));

        $results = $index->search($query)->getResults();
        
        // format results
        $output = array();

        foreach ($results as $result) {
            $item = $result->getSource();
            $item['score'] = $result->getScore();

            $output[] = $item;
        }

        $view = $this
            ->view()
            ->setData($output)
            ->setFormat('json')
        ;

        return $this->handleView($view);
    }

    /**
     * buildSearchQuery
     *
     * @param string $query
     * @param string $approvalNumber
     */
    protected function buildSearchQuery($query = null, $approvalNumber = null, $organizationName = null, $approvedOnly = false)
    {
        $boolQuery = new \Elastica\Query\Bool();

        if ($query) {
            $searchQuery = new \Elastica\Query\QueryString();
            $searchQuery->setParam('query', \Elastica\Util::replaceBooleanWordsAndEscapeTerm($query));
            $boolQuery->addShould($searchQuery);
        }

        $termQuery = new \Elastica\Query\Term();

        if ($approvalNumber) {
            $termQuery->setTerm('approval_number', $approvalNumber, 100);
            $boolQuery->addMust($termQuery);
        }

        if ($approvedOnly) {
            $termQueryApproved = new \Elastica\Query\Term();
            $termQueryApproved->setTerm('type', 1);
            $boolQuery->addMust($termQueryApproved);
        }

        if ($organizationName) {
            $matchQuery = new \Elastica\Query\Match();
            $matchQuery->setField('name', $organizationName);
            $boolQuery->addShould($matchQuery);
        }

        return $boolQuery;
    }

    public function updateApprovalNumber(Request $request)
    {

        $sc = $this->container->get('security.context');
        $user = $sc->getToken()->getUser();

        $successUrl = $this->generateUrl('service_civique_organization_profile_edit');
        if ($sc->isGranted('ROLE_ADMIN')) {
            $organizationRepository = $this->getDoctrine()->getRepository('ServiceCivique\Bundle\CoreBundle\Entity\Organization');
            if (!$organization = $organizationRepository->findOneById($request->query->get('organization_id'))) {
                throw new \Exception('Cette organization n\'existe pas');
            }
            $successUrl = $this->generateUrl('service_civique_backend_user_update', [
                'id'=> $organization->getUser()->getId(),
                'type' => 'organisme'
            ]);
        } else {
            $organization = $user->getOrganization();
        }

        if ($organization->getApprovalNumber() == 'XX-000-00-00000-00') {
            $organization->setApprovalNumber('');
        }

        $options['attr']['pattern']     = '^[A-Z]{2}-[0-9]{3}-[0-9]{2}-[0-9]{5}(?:-[0-9]{2}|$)$';
        $options['attr']['placeholder'] = 'XX-000-00-00000-00';
        $options['attr']['class']       = 'show-tooltip approvalnumberfield';
        $options['attr']['maxlength']   = 18;
        $options['label']       = 'Numéro d\'agrément';

        $form = $this->createFormBuilder($organization)
            ->add('approval_number', 'text', $options)
            ->getForm();

        $form->handleRequest($request);
        $approvalNumber = $form->get('approval_number')->getData();

        if ($request->isMethod('POST')) {
            $approvalNumberFinder = $this->container->get('service_civique.service.approval_number_finder');

            $approvalNumberCheck = $approvalNumberFinder->checkApprovalNumber($approvalNumber, $organization->getType());

            $formErrorTypes = array(1,2,3);
            if (in_array($approvalNumberCheck['errorType'], $formErrorTypes)) {
                $form->get('approval_number')->addError($approvalNumberCheck['formError']);
            } elseif ($approvalNumberCheck['errorType'] == 4) {
                $organization->setApprovedOrganization($approvalNumberCheck['organization']);
            } elseif ($approvalNumberCheck['errorType'] == 5) {
                // nothing
            }
        }

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($organization);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Votre numéro d\'agrément a bien été enregistré.');
            return $this->redirect($successUrl);
        } else {

        }

        return new Response($this->renderView(
            'ServiceCiviqueUserBundle:Profile:update_approval_number.html.twig',
            array(
                'user' => $user,
                'form' => $form->createView(),
            )
        ));
    }

    public function approvalSearchAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            //throw new AccessDeniedException();
        }

        $approvalNumber = $request->query->get('approval_number');
        $approvalRepository = $this->getDoctrine()->getRepository('ServiceCivique\Bundle\CoreBundle\Entity\Approval');
        $organizationRepository = $this->getDoctrine()->getRepository('ServiceCivique\Bundle\CoreBundle\Entity\Organization');
        $output = array();
        $output['approval_number'] = $approvalNumber;
        $output['match'] = false;
        $output['already_used'] = false;

        if ($approvalNumber != 'XX-000-00-00000-00') {
            if ($approvalOrganization = $approvalRepository->findOneByApprovalNumber($approvalNumber)) {
                $output['match'] = true;
                $output['organization']['name'] = $approvalOrganization->getOrganizationName();
                $output['organization']['address'] = $approvalOrganization->getAddress();
                $output['organization']['zip_code'] = $approvalOrganization->getZipCode();
                $output['organization']['city'] = $approvalOrganization->getCity();
            }
            if ($organization = $organizationRepository->findOneByApprovalNumber($approvalNumber)) {
                $output['already_used'] = true;
                // $output['organization']['value'] = $organization->getId();
                if($approvedOrganization = $organizationRepository->findOneBy(
                    array(
                        'approvalNumber' => $approvalNumber,
                        'type' => Organization::TYPE_APPROVED,
                    )
                )) {
                    $output['organization']['value'] = $approvedOrganization->getId();
                }
            }
        } else {
            $output['match'] = null;
            $output['already_used'] = null;
        }

        $view = $this
            ->view()
            ->setData($output)
            ->setFormat('json')
        ;

        return $this->handleView($view);
    }
}
