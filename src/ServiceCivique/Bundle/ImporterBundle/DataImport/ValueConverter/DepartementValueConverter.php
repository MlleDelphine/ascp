<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter;

use Ddeboer\DataImport\ValueConverter\ValueConverterInterface;
use Ddeboer\DataImport\Exception\UnexpectedValueException;

class DepartementValueConverter implements ValueConverterInterface
{
    protected $provider;

    public function __construct($provider)
    {
        $this->provider = $provider;
    }

    public function convert($input)
    {
        if (!$input) {
            return null;
        }

        $departement = $this->provider->findDepartementByName($input);

        if ($departement) {
            return $departement->getCode();
        } else {
            throw new UnexpectedValueException(sprintf('Invalid departement code : "%s"', $input));
        }
    }
}
