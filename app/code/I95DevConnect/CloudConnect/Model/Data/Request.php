<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_CloudConnect
 */

namespace I95DevConnect\CloudConnect\Model\Data;

use \Magento\Framework\Api\AttributeValueFactory;

/**
 * Class Request to implement Request Interface
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Request extends \Magento\Framework\Api\AbstractExtensibleObject implements
    \I95DevConnect\CloudConnect\Api\Data\RequestInterface
{

    /**
     * {@inheritdoc}
     */
    public function getPacketSize()
    {
        return $this->_get(self::PACKET_SIZE);
    }

    /**
     * {@inheritdoc}
     */
    public function setPacketSize($targetId)
    {
        return $this->_set(self::PACKET_SIZE, $targetId);
    }

    /**
     * {@inheritdoc}
     */
    public function getRequestData()
    {
        return $this->_get(self::REQUEST_DATA);
    }

    /**
     * {@inheritdoc}
     */
    public function setRequestData($resultData)
    {
        return $this->_set(self::REQUEST_DATA, $resultData);
    }

    /**
     * {@inheritdoc}
     */
    public function getContext()
    {
        return $this->_get(self::CONTEXT);
    }

    /**
     * {@inheritdoc}
     */
    public function setContext($context)
    {
        return $this->_set(self::CONTEXT, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->_get(self::TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        return $this->_set(self::TYPE, $type);
    }

    /**
     * Set value for the given key
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function _set($key, $value)
    {
        $this->$key = $value;
        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function _get($key)
    {
        return $this->$key;
    }
}
