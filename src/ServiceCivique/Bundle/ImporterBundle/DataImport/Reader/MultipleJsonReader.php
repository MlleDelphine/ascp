<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport\Reader;

use Ddeboer\DataImport\Reader\ReaderInterface;

class MultipleJsonReader implements ReaderInterface
{
    protected $files;
    protected $iterator;
    protected $maxRowsPerFile;

    public function getFields()
    {
        return array();
    }

    public function count()
    {
        return count($this->files) * $this->maxRowsPerFile;
    }

    /**
     * @param string $type
     */
    public function __construct($files, $type, $maxRowsPerFile = 50)
    {
        $this->files = iterator_to_array($files);
        $this->type = $type;
        $this->maxRowsPerFile = $maxRowsPerFile;
    }

    public function valid()
    {
        if (!$this->iterator || !$this->iterator->valid()) {
            if ($this->iterator) {
                // free memory
                unset($this->iterator);
                $this->iterator = null;
                next($this->files);
            }

            $file = current($this->files);

            if (!is_object($file)) {
                return false;
            }

            $this->iterator = new JsonReader($file, $this->type);
        }

        return $this->iterator->valid();
    }

    public function next()
    {
        return $this->iterator->next();
    }

    public function key()
    {
    }

    public function current()
    {
        return $this->iterator->current();
    }

    public function rewind()
    {
        reset($this->files);
    }
}
