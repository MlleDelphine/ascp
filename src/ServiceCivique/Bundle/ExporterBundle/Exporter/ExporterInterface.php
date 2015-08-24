<?php

namespace ServiceCivique\Bundle\ExporterBundle\Exporter;

interface ExporterInterface
{
    /**
     * getQuery
     * @param  array $parameters
     * @return Query
     *
     * @throw InvalidArgumentException
     */
    public function getQuery($parameters);
}
