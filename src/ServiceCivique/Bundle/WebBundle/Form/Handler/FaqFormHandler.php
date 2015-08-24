<?php

namespace ServiceCivique\Bundle\WebBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Form\FormError;

class FaqFormHandler
{
    protected $request;
    protected $form;

    /**
     * Initialize the handler with the form and the request
     *
     * @param Form    $form
     * @param Request $request
     *
     */
    public function __construct(Form $form, Request $request, $type)
    {
        $this->form = $form;
        $this->type = $type;
        $this->request = $request;
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

            $data = $this->form['body']->getData();

            return $this->parseData($data);
        }

        return false;
    }

    protected function parseData($data)
    {
        try {
            $yaml = new Parser();
            $yaml->parse($data);
            $this->writeData($data);
        } catch (ParseException $e) {
            $this->form->get('body')->addError(new FormError("Unable to parse the YAML string: " . $e->getMessage()));
        }
    }

    protected function writeData($data)
    {
        $uploadDir = $this->request->server->get('DOCUMENT_ROOT') . '/uploads';
        $fileName = $uploadDir . '/faq-' . $this->type . '.yml';
        $handle = fopen($fileName, 'w+');
        fwrite($handle, $data);
        fclose($handle);
    }
}
