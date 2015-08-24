<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport\ItemConverter;

use Ddeboer\DataImport\ItemConverter\MappingItemConverter as BaseMappingItemConverter;
use Symfony\Component\PropertyAccess\PropertyAccess;

class MappingItemConverter extends BaseMappingItemConverter
{

    /**
     * Applies a mapping to an item
     *
     * @param array  $item
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    protected function applyMapping(array $item, $from, $to)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        try {
            $itemMappedValue = $accessor->getValue($item, $from);
        } catch (\Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException $e) {
            return $item;
        }

        // skip fields that dont exist
        if (!isset($itemMappedValue)) {
            return $item;
        }

        // skip equal fields
        if ($from == $to) {
            return $item;
        }

        // standard renaming
        if (!is_array($to)) {
            $accessor->setValue($item, $to, $itemMappedValue);
            $accessor->setValue($item, $from, null);

            return $item;
        }

        // recursive renaming of an array
        foreach ($to as $nestedFrom => $nestedTo) {
            $accessor->setValue($item, $from, $this->applyMapping($itemMappedValue, $nestedFrom, $nestedTo));
        }

        return $item;
    }
}
