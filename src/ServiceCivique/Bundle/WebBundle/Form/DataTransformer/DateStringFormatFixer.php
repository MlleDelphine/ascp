<?php

namespace ServiceCivique\Bundle\WebBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class DateStringFormatFixer implements DataTransformerInterface
{
    protected $inputFormat;
    protected $outputFormat;

    /**
     * __construct
     *
     * @param string $inputFormat
     * @param string $outputFormat
     */
    public function __construct($inputFormat, $outputFormat)
    {
        $this->inputFormat  = $inputFormat;
        $this->outputFormat = $outputFormat;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($string)
    {
        return $string;
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($inputDate)
    {
        $dateObject = \DateTime::createFromFormat($this->inputFormat, $inputDate);

        return $dateObject ? $dateObject->format($this->outputFormat) : $inputDate;
    }
}
