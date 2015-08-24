<?php

namespace ServiceCivique\Bundle\ContentBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class StaticContentController extends ResourceController
{
    public function showAction(Request $request)
    {
        $resource = $this->findOr404($request);

        $seoPageConfigurator = $this->container->get('service_civique.seo_page_configurator');
        $seoPageConfigurator
            ->setParameter('title', $resource->getTitle())
            ->setParameter('description', mb_substr(strip_tags($resource->getContent()), 0, 160));

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('show.html'))
            ->setTemplateVar($this->config->getResourceName())
            ->setData($resource)
        ;

        return $this->handleView($view);
    }

    public function indexAction(Request $request)
    {
        $sorting  = $this->config->getSorting();
        $criteria = $this->config->getCriteria();

        $repository = $this->getRepository();

        $resources = $this->resourceResolver->getResource(
            $repository,
            'createPaginator',
            array($criteria, $sorting)
        );

        $resources->setCurrentPage($request->get('page', 1), true, true);
        $resources->setMaxPerPage(50);

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

    public function uploadImageAction(Request $request)
    {
        $response = new JsonResponse();
        if ($file = $request->files->get('file')) {
            $webRoot   = $this->container->get('kernel')->getRootDir() . '/../web';
            $uploadDir = '/uploads/content/images';

            if($file->getMimeType() == 'image/png'
                || $file->getMimeType() == 'image/jpg'
                || $file->getMimeType() == 'image/gif'
                || $file->getMimeType() == 'image/jpeg'
                || $file->getMimeType() == 'image/pjpeg'
            ) {
                $filename = sha1(uniqid(mt_rand(), true)) . '.' . $file->guessExtension();
                $file->move($webRoot . $uploadDir, $filename);

                $response->setData(array(
                    'filelink' => $uploadDir . '/' . $filename
                ));
            }
        }

        return $response;
    }

    public function getImageAction(Request $request)
    {
        $response = new JsonResponse();
        $webRoot   = $this->container->get('kernel')->getRootDir() . '/../web';
        $uploadDir = '/uploads/content/images';
        $files = array_diff(scandir($webRoot . $uploadDir), array('..', '.', '.gitkeep'));
        $images = array();

        foreach ($files as $file) {
            $images[] = array(
                'thumb' => $uploadDir . '/' . $file,
                'image' => $uploadDir . '/' . $file,
            );
        }

        $response->setData($images);

        return $response;
    }

    public function uploadFileAction(Request $request)
    {
        $response = new JsonResponse();
        if ($file = $request->files->get('file')) {
            $webRoot   = $this->container->get('kernel')->getRootDir() . '/../web';
            $uploadDir = '/uploads/content/files';
            if($file->getMimeType() == 'application/pdf'
                || $file->getMimeType() == 'application/x-pdf'
                || $file->getMimeType() == 'application/msword'
                || $file->getMimeType() == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                || $file->getMimeType() == 'application/rtf'
                || $file->getMimeType() == 'application/zip'
                || $file->getMimeType() == 'image/png'
                || $file->getMimeType() == 'image/jpeg'
                || $file->getMimeType() == 'image/gif'
            ) {
                $filename = sha1(uniqid(mt_rand(), true)) . '.' . $file->guessExtension();
                $file->move($webRoot . $uploadDir, $filename);

                $response->setData(array(
                    'filelink' => $uploadDir . '/' . $filename,
                    'filename' => $filename,
                ));
            }
        }

        return $response;
    }

    public function staticContentListAction(Request $request)
    {
        $response = new JsonResponse();
        $repository = $this->getRepository();
        $staticPages = $repository->findAll();
        $data = array();
        foreach ($staticPages as $staticPage) {
            $data[] = array(
                'url'   => 'page/' . $staticPage->getSlug(),
                'title' => $staticPage->getTitle(),
            );
        }
        $response->setData($data);

        return $response;
    }
}
