<?php

namespace ServiceCivique\Bundle\ExporterBundle\Exporter;

use ServiceCivique\Bundle\UserBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Doctrine\ORM\Query;
use ServiceCivique\Bundle\ExporterBundle\Source\DoctrineORMQuerySourceIterator;
use Exporter\Writer\XlsWriter;
use Exporter\Handler;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractExporter implements ExporterInterface
{
    protected $repository;
    protected $directory;
    protected $translator;

    /**
     * __construct
     *
     * @param ObjectRepository $repository
     * @param Translator       $translator
     * @param string           $directory
     */
    public function __construct(ObjectRepository $repository, Translator $translator, $directory)
    {
        $this->repository         = $repository;
        $this->translator         = $translator;
        $this->directory          = $directory;
    }

    /**
     * createFileResponse
     *
     * @param  string   $filepath
     * @param  bool     $unlink
     * @return Response
     */
    public function createFileResponse($filename, $unlink = true)
    {
        $filepath = $this->directory . '/' . $filename;
        $response = new Response();
        $response->headers->set('Content-type', 'application/octect-stream');
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $filename));
        $response->headers->set('Content-Length', filesize($filepath));
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->setContent(file_get_contents($filepath));

        if ($unlink) {
            unlink($filepath);
        }

        return $response;
    }

    /**
     * createExportFile
     *
     * @param string $filename
     * @param User   $user
     */
    public function createExportFile($filename, $parameters)
    {
        // create temporary file
        $filepath = $this->directory . '/' . $filename;

        $fields   = $this->getFields();
        $formater = $this->getFormater();
        $query    = $this->getQuery($parameters);

        $source = new DoctrineORMQuerySourceIterator($query, $fields, $formater, 'd/m/Y');

        // Prepare the writer
        $writer = new XlsWriter($filepath);

        // Export the data
        Handler::create($source, $writer)->export();
    }
}
