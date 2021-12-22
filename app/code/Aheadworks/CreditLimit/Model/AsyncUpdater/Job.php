<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    CreditLimit
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Model\AsyncUpdater;

use Aheadworks\CreditLimit\Api\Data\JobInterface;
use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class Job
 *
 * @package Aheadworks\CreditLimit\Model\AsyncUpdater
 */
class Job extends AbstractExtensibleObject implements JobInterface
{
    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->_get(self::TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setConfiguration($configuration)
    {
        return $this->setData(self::CONFIGURATION, $configuration);
    }

    /**
     * @inheritdoc
     */
    public function getConfiguration()
    {
        return $this->_get(self::CONFIGURATION);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(
        \Aheadworks\CreditLimit\Api\Data\JobExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
