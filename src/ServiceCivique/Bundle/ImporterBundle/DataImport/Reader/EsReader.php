<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport\Reader;

use Ddeboer\DataImport\Reader\ReaderInterface;

class EsReader implements ReaderInterface
{
    protected $iterator;
    protected $results;
    protected $total;

    const SIZE = 300;

    /**
     * __construct
     *
     * @param \Elastica\Client $client
     * @param string           $index
     * @param string           $type
     */
    public function __construct(\Elastica\Client $client, $index, $type, $query = null)
    {
        $search = new \Elastica\Search($client);
        $search->addIndex($index)->addType($type);

        if (!$query) {
           $query = new \Elastica\Query\MatchAll();
        }

        $search->setQuery($query);

        $this->total = $search->count();

        $this->iterator = new \Elastica\ScanAndScroll($search, '1m', self::SIZE);
    }

    public function current()
    {
        return $this->results->current()->getSource();
    }

    public function key()
    {
        return $this->results->next();
    }

    public function next()
    {
        $next = $this->results->next();

        if (!$next) {
            $next = $this->iterator->next();
            $this->results = $this->iterator->current();
        }

        return $next;
    }

    public function rewind()
    {
        $this->iterator->rewind();
        $this->results = $this->iterator->current();
    }

    public function valid()
    {
        return ($this->results && $this->results->valid()) || $this->iterator->valid();
    }

    public function count()
    {
        return $this->total;
    }

    public function getFields()
    {
        return array();
    }
}
