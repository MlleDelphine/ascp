<?php

namespace ServiceCivique\Bundle\WebBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use ServiceCivique\Bundle\CoreBundle\Entity\Approval;
use FOS\ElasticaBundle\Index\Resetter;
use Elastica\Type;

class ImportApprovalFormHandler
{
    protected $request;
    protected $form;
    protected $em;
    protected $resetter;
    protected $type;

    /**
     * Initialize the handler with the form and the request
     *
     * @param Form    $form
     * @param Request $request
     *
     */
    public function __construct(Form $form, Request $request, $em, Resetter $resetter, Type $type)
    {
        $this->form     = $form;
        $this->request  = $request;
        $this->em       = $em;
        $this->resetter = $resetter;
        $this->type     = $type;
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
                $attachment = $this->form['attachment']->getData();
                $this->onSuccess($attachment);

                return true;
            }
        }

        return false;
    }

    protected function onSuccess($attachment)
    {
        $uploadDir = $this->request->server->get('DOCUMENT_ROOT') . '/uploads';
        $newFilename = 'approvals.csv';
        if ($attachment->move($uploadDir, $newFilename)) {
            $this->deleteApprovals();
            $this->processCSV($uploadDir . '/' . $newFilename);
        }
    }

    public function deleteApprovals()
    {
        $query = $this->em->createQuery('DELETE ServiceCiviqueCoreBundle:Approval');
        $query->execute();
        // $this->resetter->resetIndexType('service_civique', 'approval');
        $this->resetter->resetIndexType($this->type->getIndex()->getName(), $this->type->getName());
    }

    public function processCSV($csvPath)
    {
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $batchSize = 50;
        $i = 0;
        if (($handle = fopen($csvPath, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle)) !== FALSE) {
                $decisionDate = new \DateTime();
                $termDate = new \DateTime();
                if ($row[0]) {
                    $approval = new Approval();
                    $approval->setOrganizationName($row[0]);
                    $approval->setApprovalNumber($row[1]);
                    $approval->setDecisionDate($decisionDate->createFromFormat('d/m/Y', $row[2]));
                    $approval->setTermDate($termDate->createFromFormat('d/m/Y', $row[3]));
                    $approval->setJobNumber(nl2br($row[4]));
                    $approval->setMissionLabels(nl2br($row[5]));
                    $approval->setTaxonomy(nl2br($row[6]));
                    $approval->setReferentAddress(nl2br($row[7]));
                    $approval->setPdfUrl($row[8]);
                    $approval->setReview($row[9]);
                    $approval->setOscarUrl($row[10]);
                    $approval->setSiret($row[11]);
                    $approval->setAddress($row[12]);
                    $approval->setZipCode($row[13]);
                    $approval->setCity($row[14]);
                    $this->em->persist($approval);
                }
                unset($row);
                $i++;
                if (($i % $batchSize) == 0) {
                    $this->em->flush();
                    $this->em->clear();
                }
            }
            $this->em->flush();
            $this->em->clear();
            fclose($handle);

            return true;
        }
    }

}
