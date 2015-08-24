<?php

namespace ServiceCivique\Bundle\CoreBundle\Traits;

use ServiceCivique\Bundle\CoreBundle\Entity\Application;

trait ApplicationStats
{
    public function getApplicationCount()
    {
        return count($this->getApplications());
    }

    public function getAcceptedApplications()
    {
        if(!$this->getApplications()) {
            return;
        }

        return $this->getApplications()->filter(function($entry) {
            return $entry->getStatus() === Application::POSITIVE_ANSWER;
        });
    }

    public function getRejectedApplications()
    {
        if(!$this->getApplications()) {
            return;
        }

        return $this->getApplications()->filter(function($entry) {
            return $entry->getStatus() === Application::NEGATIVE_ANSWER;
        });
    }

    public function getPendingApplications()
    {
        if(!$this->getApplications()) {
            return;
        }

        return $this->getApplications()->filter(function($entry) {
            return $entry->getStatus() === Application::WAITING_ANSWER;
        });
    }

    public function getPendingApplicationsRate()
    {
        if($this->getApplicationCount() === 0) {
            return;
        }

        return count($this->getPendingApplications()) / $this->getApplicationCount();
    }

    public function getAcceptedApplicationsRate()
    {
        if($this->getApplicationCount() === 0) {
            return;
        }

        return count($this->getAcceptedApplications()) / $this->getApplicationCount();
    }

    public function getRejectedApplicationsRate()
    {
        if($this->getApplicationCount() === 0) {
            return;
        }

        return count($this->getRejectedApplications()) / $this->getApplicationCount();
    }
}

