<?php

namespace ServiceCivique\Bundle\ImporterBundle\DataImport\Writer;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Ddeboer\DataImport\Writer\WriterInterface;

class Writer implements WriterInterface
{
    const MODE_INSERT_AND_UPDATE = 1;
    const MODE_UPDATE_ONLY       = 2;

    /**
     * Doctrine entity manager
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * Fully qualified entity name
     *
     * @var string
     */
    protected $entityName;

    /**
     * @var ClassMetadata
     */
    protected $entityMetadata;

    protected $tableName;

    protected $connection;
    protected $count = 0;
    protected $size = 30000;

    protected $originalLogger;

    protected $logger;

    /**
     * @param string $index
     */
    public function __construct(EntityManager $entityManager, $entityName, $index = null, $logger, $mode = self::MODE_INSERT_AND_UPDATE)
    {
        $this->entityManager    = $entityManager;
        $this->entityName       = $entityName;
        $this->entityMetadata   = $entityManager->getClassMetadata($entityName);
        $this->index            = $index;
        $this->tableName        = $this->entityMetadata->table['name'];
        $this->fieldNames       = $this->entityMetadata->getFieldNames();
        $this->associationNames = $this->entityMetadata->getAssociationNames();
        $this->logger           = $logger;
        $this->mode             = $mode;
    }

    public function prepare()
    {
        $this->disableLogging();

        $this->entityManager->getConnection()->beginTransaction();

        return $this;
    }

    public function writeItem(array $item)
    {
        $values = array();

        $conn = $this->entityManager->getConnection();

        foreach ($this->fieldNames as $fieldName) {

            $fieldMapping = $this->entityMetadata->getFieldMapping($fieldName);

            $value = null;

            if (isset($item[$fieldName])) {
                $value = $item[$fieldName];
            }

            if (null === $value) {
                continue;
            }

            if ($value instanceof \DateTime) {
                $value = date_format($value, 'Y-m-d H:i:s');
            }

            if (is_object($value)) {
                $value = (string) $value;
            }

            $values[$fieldMapping['columnName']] = $value;
        }

        foreach ($this->associationNames as $fieldName) {
            $associationMapping = $this->entityMetadata->getAssociationMapping($fieldName);

            if (!$associationMapping['isOwningSide']) {
                continue;
            }

            if (!isset($item[$fieldName])) {
                continue;
            }

            $value = $item[$fieldName];
            $value = is_object($value) ? $value->getId() : intval($value);

            if (null === $value || false === $value) {
                continue;
            }

            $values[$associationMapping['joinColumns'][0]['name']] = $value;
        }

        // search for record
        $recordId = false;
        $index = 'original_id';

        if (isset($values[$index])) {
            $recordId = $conn->fetchColumn('SELECT id FROM '. $this->tableName .' WHERE '.$index.' = ?', array($values[$index]), 0);
        }

        try {
            if ($recordId) {
                $conn->update($this->tableName, $values, array($index => $values[$index]));
            } elseif ($this->mode != self::MODE_UPDATE_ONLY) {
                $conn->insert($this->tableName, $values);
            }
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Doctrine writter error : %s', $e->getMessage()));
        }

        $this->count++;

        if ($this->count % $this->size == 0) {
            $conn->commit();
            $conn->beginTransaction();
        }
    }

    public function finish()
    {
        $this->entityManager->getConnection()->commit();
        $this->reEnableLogging();
    }

    /**
     * Disable Doctrine logging
     */
    protected function disableLogging()
    {
        $config = $this->entityManager->getConnection()->getConfiguration();
        $this->originalLogger = $config->getSQLLogger();
        $config->setSQLLogger(null);
    }

    /**
     * Re-enable Doctrine logging
     */
    protected function reEnableLogging()
    {
        $config = $this->entityManager->getConnection()->getConfiguration();
        $config->setSQLLogger($this->originalLogger);
    }
}
