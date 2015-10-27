<?php

namespace ServiceCivique\Bundle\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class UserController extends ResourceController
{

    // public function showAction(Request $request)
    // {
    //     $resource = $this->findOr404($request);

    //     $seoPageConfigurator = $this->container->get('service_civique.seo_page_configurator');
    //     $seoPageConfigurator
    //         ->setParameter('title', $resource->getTitle())
    //         ->setParameter('description', mb_substr(strip_tags($resource->getDescription()), 0, 160));

    //     $view = $this
    //         ->view()
    //         ->setTemplate($this->config->getTemplate('show.html'))
    //         ->setTemplateVar($this->config->getResourceName())
    //         ->setData($resource)
    //     ;

    //     return $this->handleView($view);
    // }

//    /**
//     * @param Request $request
//     *
//     * @return Response
//     */
//    public function indexAction(Request $request)
//    {
//        $criteria = $this->config->getCriteria();
//        $sorting = $this->config->getSorting();
//
//        $repository = $this->getRepository();
//
//        if ($this->config->isPaginated()) {
//
//            $resources = $this->resourceResolver->getResource(
//                $repository,
//                'createPaginator',
//                array($criteria, $sorting)
//            );
//            $resources->setCurrentPage($request->get('page', 1), true, true);
//            if ($request->query->has('paginate')) {
//                $paginate = $request->query->get('paginate');
//            }else{
//                $paginate = $this->config->getPaginationMaxPerPage();
//            }
//
//            $resources->setMaxPerPage($paginate);
//
//            if ($this->config->isApiRequest()) {
//                $resources = $this->getPagerfantaFactory()->createRepresentation(
//                    $resources,
//                    new Route(
//                        $request->attributes->get('_route'),
//                        $request->attributes->get('_route_params')
//                    )
//                );
//            }
//        } else {
//            $resources = $this->resourceResolver->getResource(
//                $repository,
//                'findBy',
//                array($criteria, $sorting, $this->config->getLimit())
//            );
//        }
//
//        $view = $this
//            ->view()
//            ->setTemplate($this->config->getTemplate('index.html'))
//            ->setTemplateVar($this->config->getPluralResourceName())
//            ->setData($resources)
//        ;
//
//        return $this->handleView($view);
//    }

    public function deleteAction(Request $request)
    {
        $resource = $this->findOr404($request);
        $this->domainManager->delete($resource);

        return $this->redirectHandler->redirectToIndex();
    }

    public function deleteFrontAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('is_delete', 'checkbox', array(
                'label'     => 'Je souhaite supprimer mon compte et toutes les données associées',
                'required'  => false,
            ))
            ->add('submit', 'submit', [
                'label' => 'Supprimer mon compte',
                'attr' => [
                    'class' => 'btn btn-sc-red'
                ]
            ])
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isValid()) {
            $isDelete = $form->get('is_delete')->getData();
            if ($isDelete) {
                $repository = $this->container->get('service_civique.repository.user');
                $user       = $this->get('security.context')->getToken()->getUser();
                $resource   = $repository->find($user->getId());

                if ($this->domainManager->delete($resource)) {
                    $this->get('session')->getFlashBag()->set(
                        'success',
                        'Votre compte a bien été supprimé.'
                    );
                }

                return $this->redirect($this->generateUrl('service_civique_homepage'));
            }
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('delete_account.html'))
            ->setData(array(
                'form'     => $form->createView()
            ));
        ;

        return $this->handleView($view);
    }
}
