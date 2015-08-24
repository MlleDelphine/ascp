<?php

namespace ServiceCivique\Bundle\CoreBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use ServiceCivique\Bundle\CoreBundle\Entity\Mission;

class MissionStatusService
{

    protected $doctrine;
    protected $em;
    protected $applicationRepository;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->em = $this->doctrine->getManager();
        $this->applicationRepository = $this->doctrine->getRepository('ServiceCivique\Bundle\CoreBundle\Entity\Application');
    }

    public function setApplicationMissionStatus($mission, $applicationBatchSize = 30)
    {
        $missionId = $mission->getId();
        $missionStatus = $mission->getStatus();

        // If pourvue
        $status = null;
        if ($missionStatus == Mission::STATUS_FILLED) {
            $status = 1;
        } elseif ($missionStatus == Mission::STATUS_AVAILABLE) {
            $status = 0;
        }
        $applicationCount = $this->applicationRepository->getAllCount($missionId);
        // Batching
        for ($i = 0; $i < $applicationCount; $i += $applicationBatchSize) {
            $applications = $this->applicationRepository->findBy(
                array('mission' => $missionId),
                array(),
                $applicationBatchSize,
                $i
            );
            foreach ($applications as $application) {
                $application->setMissionStatus($status);
                $this->em->persist($application);
            }
            $this->em->flush();
            $this->em->clear();
        }
        $this->em->flush();
        $this->em->clear();
    }

}
