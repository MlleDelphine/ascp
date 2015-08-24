<?php

namespace ServiceCivique\Bundle\ImporterBundle\Model;

interface ImportableInterface
{
    public function getOriginalId();
    public function setOriginalId($originalId);
}
