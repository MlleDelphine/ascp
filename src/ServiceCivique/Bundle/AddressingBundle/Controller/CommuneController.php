<?php

namespace ServiceCivique\Bundle\AddressingBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CommuneController extends ResourceController
{
    public function searchAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new AccessDeniedException();
        }

        $repository = $this->getRepository();

        $communes = $repository->findByZipCode($request->query->get('zipcode'));

        $view = $this
            ->view()
            ->setData($communes)
            ->setFormat('json')
        ;

        return $this->handleView($view);
    }

}
