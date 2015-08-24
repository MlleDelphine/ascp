<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport\Reader;

use Ddeboer\DataImport\Reader\ReaderInterface;

class AppendReader extends \AppendIterator implements ReaderInterface
{
    public function getFields()
    {
        return array();
    }

}
