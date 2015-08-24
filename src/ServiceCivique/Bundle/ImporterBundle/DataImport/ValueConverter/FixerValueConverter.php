<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter;

use Ddeboer\DataImport\ValueConverter\ValueConverterInterface;

class FixerValueConverter implements ValueConverterInterface
{
    protected $fixes;

    public function __construct(array $fixes = array())
    {
        $this->fixes = array_change_key_case($fixes, CASE_LOWER);
    }

    public function convert($input)
    {
        $lower_input = trim(strtolower($input));

        if (!in_array($lower_input, array_keys($this->fixes))) {
            return $input;
        }

        return $this->fixes[$lower_input];
    }
}
