<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_AgeVerification
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\AgeVerification\Model\Api\Data;

use Magento\Framework\DataObject;
use Mageplaza\AgeVerification\Api\Data\PurchaseConfigInterface;

/**
 * Class PurchaseConfig
 * @package Mageplaza\AgeVerification\Model\Api\Data
 */
class PurchaseConfig extends DataObject implements PurchaseConfigInterface
{
    /**
     * @inheritDoc
     */
    public function getIsEnable(): bool
    {
        return (bool)$this->getData(self::ENABLED);
    }

    /**
     * @inheritDoc
     */
    public function getAppliedProducts()
    {
        return $this->getData(self::PRODUCT_IDS);
    }

    /**
     * @inheritDoc
     */
    public function getAgeNotice(): string
    {
        return (string)$this->getData(self::NOTICE_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function getImage(): string
    {
        return (string)$this->getData(self::IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        return (string)$this->getData(self::MESSAGE);
    }
}
