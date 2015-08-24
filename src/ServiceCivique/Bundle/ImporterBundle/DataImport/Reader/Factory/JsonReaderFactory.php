<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport\Reader\Factory;

use ServiceCivique\Bundle\ImporterBundle\DataImport\Reader\JsonReader;

class JsonReaderFactory
{
    public function getReader(\SplFileObject $file)
    {
        $reader = new JsonReader($file);

        return $reader;
    }
}
