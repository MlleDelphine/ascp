<?php

namespace ServiceCivique\Bundle\WebBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\ORM\EntityManager;
use ServiceCivique\Bundle\KeyValueStoreBundle\keyValueStore;

class BannerFormHandler
{
    protected $request;
    protected $form;
    protected $em;
    protected $keyValueStore;

    /**
     * Initialize the handler with the form and the request
     *
     * @param Form          $form
     * @param Request       $request
     * @param EntityManager $em
     * @param EntityManager $em
     */
    public function __construct(Form $form, Request $request, EntityManager $em, KeyValueStore $keyValueStore)
    {
        $this->form          = $form;
        $this->request       = $request;
        $this->em            = $em;
        $this->keyValueStore = $keyValueStore;
    }

    /**
     * Process form
     *
     * @return boolean
     */
    public function process()
    {
        if ('POST' == $this->request->getMethod()) {
            $this->form->bind($this->request);
            if ($this->form->isValid()) {
                $file = $this->form['file']->getData();
                if ($file === null) {
                    return $this->onSuccess($this->form, true);
                }

                if ($this->isValidFile($file)) {
                    return $this->onSuccess($this->form);
                }

                $this->form->get('file')->addError(new FormError('Le fichier doit être au format .gif'));
            }
        }

        return false;
    }

    /**
     * onSuccess
     * @param Form    $form
     * @param bollean $removeBanner
     *
     * @return boolean
     */
    protected function onSuccess(Form $form, $removeBanner = false)
    {
        $hasError = false;
        if ($removeBanner) {
            $fs = new Filesystem();
            $uploadDir = $this->request->server->get('DOCUMENT_ROOT') . '/uploads';
            if (!$fs->remove($uploadDir . '/banniere.gif')) {
                $hasError = true;
            }
        } else {
            if (!$this->upload($form['file']->getData())) {
                $hasError = true;
            }
        }
        if (!$this->keyValueStore->remove('banner_destination') && !$this->keyValueStore->set('banner_destination', $form['destination']->getData())) {
            $hasError = true;
        }

        return $hasError;
    }

    /**
     * upload
     *
     * @param UploadedFile $file
     *
     * @return boolean
     */
    protected function upload(UploadedFile $file)
    {
        $uploadDir = $this->request->server->get('DOCUMENT_ROOT') . '/uploads';
        try {
            $file->move($uploadDir, 'banniere.gif');

            return true;
        } catch (FileException $e) {
            return false;
        }
    }

    /**
     * isValidFile
     *
     * @param  UploadedFile $file
     * @return boolean
     */
    public function isValidFile(UploadedFile $file)
    {
        if ($file->getClientMimeType() == 'image/gif') {
            return true;
        }

        return false;
    }

}
