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
use Mageplaza\AgeVerification\Api\Data\ConfigsInterface;

/**
 * Class Configs
 * @package Mageplaza\AgeVerification\Model\Api\Data
 */
class Configs extends DataObject implements ConfigsInterface
{
    /**
     * @inheritDoc
     */
    public function getGeneralConfig()
    {
        return $this->getData(self::GENERAL);
    }

    /**
     * @inheritDoc
     */
    public function getPageConfig()
    {
        return $this->getData(self::PAGE_VERIFY);
    }

    /**
     * @inheritDoc
     */
    public function getPurchaseConfig()
    {
        return $this->getData(self::PURCHASE_VERIFY);
    }

    /**
     * @inheritDoc
     */
    public function getDesignConfig()
    {
        return $this->getData(self::DESIGN);
    }
}
