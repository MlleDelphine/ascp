<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter;

use Ddeboer\DataImport\ValueConverter\ValueConverterInterface;
use libphonenumber\PhoneNumberUtil;

class PhoneNumberValueConverter implements ValueConverterInterface
{
    protected $phone_number_util;
    protected $default_region;

    /**
     * __construct
     *
     * @param PhoneNumberUtil $phone_number_util
     * @param string          $default_region
     */
    public function __construct(PhoneNumberUtil $phone_number_util, $default_region = 'FR')
    {
        $this->phone_number_util = $phone_number_util;
        $this->default_region = $default_region;
    }

    public function convert($input)
    {
        $input = preg_replace('/[\.\s-]/', '', $input);

        try {
            $phoneNumber = $this->phone_number_util->parse($input, $this->default_region);

            if (strlen($phoneNumber->__toString()) < 6) {
                return null;
            }

            return $phoneNumber;
        } catch (\Exception $e) {
            return null;
        }
    }
}
