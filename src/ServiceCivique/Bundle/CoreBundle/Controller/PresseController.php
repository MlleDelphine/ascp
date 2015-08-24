<?php

namespace ServiceCivique\Bundle\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class PresseController extends ResourceController
{

    public function showAction(Request $request)
    {
        $resource = $this->findOr404($request);

        $seoPageConfigurator = $this->container->get('service_civique.seo_page_configurator');
        $seoPageConfigurator
            ->setParameter('title', $resource->getTitle())
            ->setParameter('description', mb_substr(strip_tags($resource->getText()), 0, 160));

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('show.html'))
            ->setTemplateVar($this->config->getResourceName())
            ->setData($resource)
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
