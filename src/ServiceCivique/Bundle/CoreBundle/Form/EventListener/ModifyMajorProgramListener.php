<?php

namespace ServiceCivique\Bundle\CoreBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\GenericEvent;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class ModifyMajorProgramListener
{
    /**
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        /* @var $repository \ServiceCivique\Bundle\CoreBundle\Repository\MajorProgramRepository */
        $this->repository = $repository;
    }

    public function onMajorProgramCreateOrUpdate(GenericEvent $event)
    {
        /* @var $major_program \ServiceCivique\Bundle\CoreBundle\Entity\MajorProgram */
        $major_program = $event->getSubject();
        $position = $major_program->getPosition();
        $programs = $this->repository->getMajorProgramsByPosition($major_program->getId());
        $count = $this->repository->getMajorProgramsCount();
        if(!$major_program->getId()) {
            $count += 1;
        }
        $process = 0;
        for ($iteration = 1; $iteration <= $count; $iteration++) {
            if ($iteration != $position) {
                $programs[$process]->setPosition($iteration);
                $process += 1;
            }
        }
    }

    public function onMajorProgramDelete(GenericEvent $event)
    {
        $major_program = $event->getSubject();
        $position = $major_program->getPosition();
        $programs = $this->repository->getMajorProgramsAfterPosition($position);
        /* @var $program \ServiceCivique\Bundle\CoreBundle\Entity\MajorProgram */
        foreach ($programs as $program) {
            $actual_position = $program->getPosition();
            $program->setPosition($actual_position - 1);
        }
    }
}
