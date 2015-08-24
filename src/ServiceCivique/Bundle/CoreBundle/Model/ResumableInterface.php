<?php

namespace ServiceCivique\Bundle\CoreBundle\Model;

interface ResumableInterface
{
    public function getCv();
    public function setCv($cv);
    public function getAbsolutePath();
    public function getUser();
}
