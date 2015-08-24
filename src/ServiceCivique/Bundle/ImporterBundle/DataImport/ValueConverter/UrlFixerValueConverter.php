<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter;

use Ddeboer\DataImport\ValueConverter\ValueConverterInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;

class UrlFixerValueConverter implements ValueConverterInterface
{
    public function convert($input)
    {
        $input = strtolower(trim($input));

        if ($input == "") {
            return null;
        }

        if (!preg_match('/^http:\/\//', $input)) {
            $input = 'http://' . $input;
        }

        $validator = Validation::createValidator();
        $violations = $validator->validateValue($input, new Assert\Url());

        if (count($violations) != 0) {
            return null;
        }

        return $input;
    }
}
