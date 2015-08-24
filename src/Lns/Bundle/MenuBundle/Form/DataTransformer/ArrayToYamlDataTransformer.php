<?php

namespace Lns\Bundle\MenuBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ArrayToYamlDataTransformer implements DataTransformerInterface
{
    protected $parser;
    protected $dumper;

    public function __construct($parser, $dumper)
    {
        $this->parser = $parser;
        $this->dumper = $dumper;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($array)
    {
        $result = $this->dumper->dump($array, 3);

        if ($result == 'null') {
            return null;
        }

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($yaml)
    {
        try {
            return $this->parser->parse($yaml);
        } catch (\Exception $e) {
            throw new TransformationFailedException();
        }
    }
}
