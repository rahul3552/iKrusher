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
use Mageplaza\AgeVerification\Api\Data\GeneralConfigInterface;

/**
 * Class GeneralConfig
 * @package Mageplaza\AgeVerification\Model\Api\Data
 */
class GeneralConfig extends DataObject implements GeneralConfigInterface
{

    /**
     * @inheritDoc
     */
    public function getVerificationAge(): float
    {
        return (float)$this->getData(self::VERIFY_AGE);
    }

    /**
     * @inheritDoc
     */
    public function getIsEnableTermCondition(): bool
    {
        return (bool)$this->getData(self::ENABLED_TERM_CONDITION);
    }

    /**
     * @inheritDoc
     */
    public function getLinkTitle(): string
    {
        return (string)$this->getData(self::LINK_TERM);
    }

    /**
     * @inheritDoc
     */
    public function getAnchorText(): string
    {
        return (string)$this->getData(self::ANCHOR_TEXT);
    }

    /**
     * @inheritDoc
     */
    public function getAnchorUrl(): string
    {
        return (string)$this->getData(self::ANCHOR_URL);
    }

    /**
     * @inheritDoc
     */
    public function getCookieLifetime(): float
    {
        return (float)$this->getData(self::COOKIE_TIME);
    }

    /**
     * @inheritDoc
     */
    public function getCustomerGroups(): array
    {
        return (array)$this->getData(self::CUSTOMER_GROUPS);
    }

    /**
     * @inheritDoc
     */
    public function getIsAutoVerify(): bool
    {
        return (bool)$this->getData(self::AUTO_VERIFY);
    }

    /**
     * @inheritDoc
     */
    public function getRedirectUrl(): string
    {
        return (string)$this->getData(self::REDIRECT);
    }
}
