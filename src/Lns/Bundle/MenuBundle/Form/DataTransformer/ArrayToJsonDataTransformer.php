<?php

namespace Lns\Bundle\MenuBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ArrayToJsonDataTransformer implements DataTransformerInterface
{
    /**
     * {@inheritDoc}
     */
    public function transform($array)
    {
        return json_encode($array, JSON_PRETTY_PRINT);
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($json)
    {
        $array = json_decode($json, true);

        if (null === $array) {
            throw new TransformationFailedException(sprintf(
                'JSON mal formaté'
            ));
        }

        return $array;
    }
}
