<?php

namespace ServiceCivique\Bundle\CoreBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;

class ApprovalController extends ResourceController
{

    /**
     * listAction
     *
     * @param Request $request
     */
    public function listAction(Request $request)
    {
        // set default criteria and sorting
        $default_criteria = array();
        if ($request->get('criteria')) {
            $default_criteria = $request->get('criteria');
        }

        $form = $this->get('form.factory')->createNamed('criteria', 'service_civique_approval_search', array(), array(
            'method'          => 'POST',
            'action'          => $this->generateUrl($request->query->get('route', 'service_civique_corporate_approval_search')),
            'csrf_protection' => false,
            'data'            => $default_criteria
        ));

        $form->handleRequest($request);

        $criteria = array_merge($default_criteria, $this->config->getCriteria([]));

        $sorting = $this->config->getSorting(array(
            'organization_name' => 'asc'
        ));

        $repository = $this->getRepository();

        $resources = $this->resourceResolver->getResource(
            $repository,
            'findFromFrontend',
            array($criteria, $sorting)
        );

        $resources->setCurrentPage($request->get('page', 1), true, true);
        $resources->setMaxPerPage($request->query->get('paginate', 10));

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('list.html'))
            ->setData(array(
                $this->config->getPluralResourceName() => $resources,
                'criteria' => $criteria,
                'form'     => $form
            ));

        return $this->handleView($view);
    }

    /**
     * listAction
     *
     * @param Request $request
     */
    public function searchSubmitAction(Request $request)
    {
        $params = $request->query->all();
        $redirectRoute = $request->query->get('route', 'service_civique_corporate_approval_list');

        unset($params['page']);

        if ($request->request->has('criteria')) {
            unset($params['page']);
            $params['criteria'] = array();

            foreach ($request->request->get('criteria') as $key => $value) {
                if (empty($value) || is_null($value)) {
                    continue;
                }
                $params['criteria'][$key] = $value;
            }
        }

        return $this->redirect($this->generateUrl($redirectRoute, $params));
    }

    /**
     * {@inheritDoc}
     */
    public function getRepository()
    {

        if ($this->config->getRequest()->get('_route') == 'service_civique_corporate_approval_list') {
            return $this->getSearchRepository();
        }

        return parent::getRepository();
    }

    public function getSearchRepository()
    {
        return $this->get('fos_elastica.manager.orm')->getRepository('ServiceCivique\Bundle\CoreBundle\Entity\Approval');
    }

}
