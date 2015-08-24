<?php

namespace ServiceCivique\Bundle\KeyValueStoreBundle\Entity;

/**
 * KeyValue
 */
class KeyValue
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $dataKey;

    /**
     * @var string
     */
    private $dataValue;

    /**
     * @var \DateTime
     */
    private $created;

    /**
     * @var integer
     */
    private $lifetime = -1; // unlimited lifetime

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dataKey
     *
     * @param  string   $dataKey
     * @return KeyValue
     */
    public function setDataKey($dataKey)
    {
        $this->dataKey = $dataKey;

        return $this;
    }

    /**
     * Get dataKey
     *
     * @return string
     */
    public function getDataKey()
    {
        return $this->dataKey;
    }

    /**
     * Set dataValue
     *
     * @param  string   $dataValue
     * @return KeyValue
     */
    public function setDataValue($dataValue)
    {
        $this->dataValue = $dataValue;

        return $this;
    }

    /**
     * Get dataValue
     *
     * @return string
     */
    public function getDataValue()
    {
        return $this->dataValue;
    }

    /**
     * Set created
     *
     * @param  \DateTime $created
     * @return KeyValue
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set lifetime
     *
     * @param  string   $lifetime
     * @return KeyValue
     */
    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;

        return $this;
    }

    /**
     * Get lifetime
     *
     * @return integer
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }
}
