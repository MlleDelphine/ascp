<?php

namespace ServiceCivique\Bundle\CoreBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use ServiceCivique\Bundle\CoreBundle\Entity\Mission;
use ServiceCivique\Bundle\CoreBundle\Entity\MissionLog;

class MissionLogService
{

    protected $doctrine;
    protected $em;
    protected $missionLogRepository;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
        $this->em = $this->doctrine->getManager();
        $this->missionLogRepository = $this->doctrine->getRepository('ServiceCivique\Bundle\CoreBundle\Entity\MissionLog');
    }

    public function saveMissionWithLog($originalMission, $newMission, $isDuplicate = false)
    {
        $hasDiff = false;
        $logs = array();
        if ($newMission->getStatus() == Mission::STATUS_UNDER_REVIEW && !$isDuplicate) {
            return $newMission;
        }

        if ($originalMission->getTitle() != $newMission->getTitle()) {
            $logs['title'] = $originalMission->getTitle();
            $hasDiff = true;
        }
        if ($originalMission->getDescription() != $newMission->getDescription()) {
            $logs['description'] = $originalMission->getDescription();
            $hasDiff = true;
        }
        if ($originalMission->getStartDate()->format('Y') != $newMission->getStartDate()->format('Y')) {
            $logs['start_date'] = $originalMission->getStartDate();
            $hasDiff = true;
        }
        if (!$hasDiff) {
            if ($isDuplicate) {
                $newMission->setStatus(Mission::STATUS_AVAILABLE);
            }

            return $newMission;
        }

        $missionLog = new MissionLog();
        $missionLog->setMission($newMission);
        $missionLog->setDataValue(serialize($logs));
        $newMission->setMissionLog($missionLog);
        $newMission->setStatus(Mission::STATUS_UNDER_VALIDATION);
        $this->em->persist($missionLog);
        $this->em->persist($newMission);
        $this->em->flush($missionLog);

        return $newMission;
    }

    public function cancelModifications($mission)
    {
        $missionLog = $this->missionLogRepository->findOneByMission($mission->getId());
        if ($missionLog) {
            $missionLogData = unserialize($missionLog->getDataValue());

            foreach ($missionLogData as $key => $value) {
                if ($key == 'title') {
                    $mission->setTitle($value);
                } elseif ($key == 'description') {
                    $mission->setDescription($value);
                } elseif ($key == 'start_date') {
                    $mission->setStartDate($value);
                }
            }
            $mission->setStatus(MISSION::STATUS_AVAILABLE);
            $mission->setPublished(new \Datetime());
            $this->em->persist($mission);
            $this->em->remove($missionLog);
            $this->em->flush();

            return $mission;
        }

        return false;
    }

}
