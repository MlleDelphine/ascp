<?php

namespace ServiceCivique\Bundle\ExporterBundle\Source;

use Doctrine\ORM\Query;

use Exporter\Source\DoctrineORMQuerySourceIterator as BaseDoctrineORMQuerySourceIterator;

class DoctrineORMQuerySourceIterator extends BaseDoctrineORMQuerySourceIterator
{
    protected $formater;

    public function __construct(Query $query, array $fields, \Closure $formater = null, $dateTimeFormat = 'r')
    {
        parent::__construct($query, $fields, $dateTimeFormat);
        $this->formater = $formater;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        $data = parent::current();

        if ($this->formater) {
            $data = call_user_func($this->formater, $data);
        }

        return $data;
    }
}
