<?php

namespace ServiceCivique\Bundle\CoreBundle\EventListener;

use Symfony\Component\EventDispatcher\GenericEvent;

class DepartementRegionListener
{
    public function prePersist(GenericEvent $event)
    {
        $mission = $event->getSubject();

        if ($mission->getArea() && is_object($mission->getArea())) {
            $mission->setArea($mission->getArea()->getCode());
        }
        if ($this->getDepartment() && is_object($this->getDepartment())) {
            $mission->setDepartment($mission->getDepartment()->getCode());
        }

    }
}
