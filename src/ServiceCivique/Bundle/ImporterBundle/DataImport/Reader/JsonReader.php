<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport\Reader;

use Ddeboer\DataImport\Reader\ArrayReader;

class JsonReader extends ArrayReader
{
    public function __construct(\SplFileInfo $file, $type)
    {
        $datas = json_decode(file_get_contents($file->getRealPath()), true);

        return parent::__construct($datas[$type]);
    }
}
