<?php

namespace ServiceCivique\Bundle\CoreBundle\Serializer\Namer;

abstract class AbstractNumericValueNamer implements NumericValueNamerInterface
{
    protected $map;

    public function __construct() {
        $this->map = $this->getMap();
    }

    public function getName($value, $default = null)
    {
        if(!isset($this->map[$value])) {
            return $default;
        }

        return $this->map[$value];
    }

    public function getMap() {
        return array();
    }
}
