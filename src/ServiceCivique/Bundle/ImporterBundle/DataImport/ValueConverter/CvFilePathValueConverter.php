<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter;

use Ddeboer\DataImport\ValueConverter\ValueConverterInterface;

class CvFilePathValueConverter implements ValueConverterInterface
{
    public function convert($input = null)
    {
        if ($input != null) {
            return str_replace('sites/default/files/webform/', '', $input);
        }

        return null;
    }
}
