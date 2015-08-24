<?php

namespace ServiceCivique\Bundle\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use ServiceCivique\Bundle\KeyValueStoreBundle\Entity\KeyValue;
use ServiceCivique\Bundle\CoreBundle\Form\Type\HeaderVideoboxType;
use Symfony\Component\HttpFoundation\JsonResponse;

class HeaderController extends ResourceController
{

    public function indexAction(Request $request)
    {
        $keyValueStore = $this->container->get('service_civique.key_value_store');
        $criteria = $this->config->getCriteria();
        $sorting = $this->config->getSorting();

        $repository = $this->getRepository();

        if ($this->config->isPaginated()) {
            $resources = $this->resourceResolver->getResource(
                $repository,
                'createPaginator',
                array($criteria, $sorting)
            );
            $resources->setCurrentPage($request->get('page', 1), true, true);
            $resources->setMaxPerPage($this->config->getPaginationMaxPerPage());
        } else {
            $resources = $this->resourceResolver->getResource(
                $repository,
                'findBy',
                array($criteria, $sorting, $this->config->getLimit())
            );
        }

        $videobox = $keyValueStore->get('header_videobox');
        $videoboxValue = $videobox instanceof KeyValue ? unserialize(stream_get_contents($videobox->getDataValue())) : null;
        if (!isset($videoboxValue['title'])) {
            $videoboxValue['title'] = '';
        }
        if (!isset($videoboxValue['url'])) {
            $videoboxValue['url'] = '';
        }
        if (!isset($videoboxValue['videourl'])) {
            $videoboxValue['videourl'] = '';
        }
        if (!isset($videoboxValue['videolinkurl'])) {
            $videoboxValue['videolinkurl'] = '';
        }
        $form = $this->createForm(new HeaderVideoboxType($videoboxValue));
        if ($this->processVideoboxForm($form, $request, $keyValueStore)) {
            return $this->redirect($this->generateUrl('service_civique_backend_header_index'));
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('index.html'))
            ->setTemplateVar($this->config->getPluralResourceName())
            ->setData(array(
                'headers' => $resources,
                'form'    => $form->createView()
            ))
        ;

        return $this->handleView($view);
    }

    public function createAction(Request $request)
    {
        $resource = $this->createNew();
        $form = $this->getForm($resource);

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $this->handleImage($request, $form, $resource);
            $this->handlePinImage($request, $form, $resource);
            $resource = $this->domainManager->create($resource);

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
                'form' => $form->createView()
            ))
        ;

        return $this->handleView($view);
    }

    public function updateAction(Request $request)
    {
        $resource = $this->findOr404($request);
        $form = $this->getForm($resource);
        $oldImage = $resource->getImage();
        $oldPinImage = $resource->getPinImage();

        if (($request->isMethod('PUT') || $request->isMethod('POST')) && $form->submit($request)->isValid()) {
            $this->handleImage($request, $form, $resource, $oldImage);
            $this->handlePinImage($request, $form, $resource, $oldPinImage);
            $this->domainManager->update($resource);

            return $this->redirectHandler->redirectTo($resource);
        }

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($form));
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('update.html'))
            ->setData(array(
                $this->config->getResourceName() => $resource,
                'form'        => $form->createView(),
                'oldImage'    => $resource->getImage(),
                'oldPinImage' => $resource->getPinImage()
            ))
        ;

        return $this->handleView($view);
    }

    public function deleteAction(Request $request)
    {
        $resource = $this->findOr404($request);
        $this->deleteImage($resource->getImage());
        $this->domainManager->delete($resource);

        return $this->redirectHandler->redirectToIndex();
    }

    public function deletePinAction(Request $request)
    {
        $resource = $this->findOr404($request);
        $resource->setPinImage(null);
        $this->domainManager->update($resource);
        $this->deleteImage($resource->getPinImage());

        $response = new JsonResponse();
        $response->setData(array(
            'response' => 'ok'
        ));

        return $response;
    }

    protected function handleImage($request, $form, $resource, $oldImage = null)
    {
        $fileFolder = $this->getFileFolder();
        if ($image = $form['image']->getData()) {
            $uploadDir = $request->server->get('DOCUMENT_ROOT') . '/' . $fileFolder;
            $fileName  = sha1(uniqid(mt_rand(), true)) . '.' . $image->guessExtension();

            $image->move($uploadDir, $fileName);
            $this->deleteImage($oldImage);
            $resource->setImage($fileFolder . '/' . $fileName);
        } else {
            $resource->setImage($oldImage);
        }
    }

    protected function handlePinImage($request, $form, $resource, $oldPinImage = null)
    {
        $fileFolder = $this->getFileFolder();
        if ($pinImage = $form['pinImage']->getData()) {
            $uploadDir = $request->server->get('DOCUMENT_ROOT') . '/' . $fileFolder;
            $fileName  = sha1(uniqid(mt_rand(), true)) . '.' . $pinImage->guessExtension();

            $pinImage->move($uploadDir, $fileName);
            $this->deleteImage($oldPinImage);
            $resource->setPinImage($fileFolder . '/' . $fileName);
        } else {
            $resource->setPinImage($oldPinImage);
        }
    }

    /**
     *
     */
    protected function getFileFolder()
    {
        return 'uploads/headers';
    }

    /**
     * Remove old file
     */
    protected function deleteImage($image = null)
    {
        if ($image == null) {
            return false;
        }
        if (file_exists($image) && is_writable($image)) {
            return unlink($image);
        }
    }

    /**
     *
     */
    protected function processVideoboxForm($form, $request, $store)
    {
        $form->handleRequest($request);

        if ('POST' == $request->getMethod() && $form->isValid()) {
            $store->remove('header_videobox');
            $data = array(
                'title' => $form->get('header_videobox_title')->getData(),
                'url' => $form->get('header_videobox_url')->getData(),
                'videourl' => $form->get('header_videobox_videourl')->getData(),
                'videolinkurl' => $form->get('header_videobox_videolinkurl')->getData()
            );
            $store->set('header_videobox', serialize($data));

            return true;
        }

        return false;
    }

}
