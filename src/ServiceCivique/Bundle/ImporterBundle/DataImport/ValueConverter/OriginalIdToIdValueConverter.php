<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport\ValueConverter;

use Ddeboer\DataImport\ValueConverter\ValueConverterInterface;

class OriginalIdToIdValueConverter implements ValueConverterInterface
{
    private $connection;
    private $tableName;
    private $idColumn;
    private $originalIdColumn;

    /**
     * __construct
     *
     * @param mixed  $connection
     * @param string $tableName
     * @param string $idColumn
     * @param string $originalIdColumn
     */
    public function __construct($connection, $tableName, $idColumn = 'id', $originalIdColumn = 'original_id')
    {
        $this->connection       = $connection;
        $this->tableName        = $tableName;
        $this->idColumn         = $idColumn;
        $this->originalIdColumn = $originalIdColumn;
    }

    public function convert($input)
    {
        $id = $this->connection->fetchColumn('SELECT ' . $this->idColumn . ' FROM ' . $this->tableName . ' WHERE ' . $this->originalIdColumn . ' = ?', array((int) $input), 0);

        if (!$id) {
            return null;
        }

        return $id;
    }
}
