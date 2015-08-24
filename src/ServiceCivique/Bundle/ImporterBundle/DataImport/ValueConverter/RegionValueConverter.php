<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter;

use Ddeboer\DataImport\ValueConverter\ValueConverterInterface;
use Ddeboer\DataImport\Exception\UnexpectedValueException;

class RegionValueConverter implements ValueConverterInterface
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

        $region = $this->provider->findRegionByName($input);
        if ($region) {
            return $region->getCode();
        } else {
            throw new UnexpectedValueException(sprintf('Invalid region code : "%s"', $input));
        }
    }
}
