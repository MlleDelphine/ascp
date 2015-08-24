<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport\Reader;

use Ddeboer\DataImport\Reader\ReaderInterface;

class JsonLinesReader implements ReaderInterface
{
    protected $resource;
    protected $total;
    protected $line;
    protected $position = -1;
    protected $valid = true;

    public function __construct($file)
    {
        $this->total = (int) exec(sprintf('wc -l < "%s"', $file));
        $this->resource = fopen($file, 'r');

        var_dump($file);
    }

    public function current()
    {
        return $this->line;
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        if (!feof($this->resource)) {
            $this->valid = true;
            $this->line = json_decode(trim(fgets($this->resource), "\r\n"), true);
            $this->position++;
            if (is_null($this->line)) {
                $this->next();
            }
        } else {
            $this->valid = false;
        }
    }

    public function rewind()
    {
        rewind($this->resource);
        $this->position = -1;
        $this->next();
    }

    public function valid()
    {
        return $this->valid;
    }

    public function count()
    {
        return $this->total;
    }

    public function getFields()
    {
        return array();
    }

    public function __destruct()
    {
        if (is_resource($this->resource)) {
            fclose($this->resource);
        }
    }
}
