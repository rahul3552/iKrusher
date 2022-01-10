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
use Mageplaza\AgeVerification\Api\Data\DesignConfigInterface;

/**
 * Class DesignConfig
 * @package Mageplaza\AgeVerification\Model\Api\Data
 */
class DesignConfig extends DataObject implements DesignConfigInterface
{
    /**
     * @inheritDoc
     */
    public function getVerificationType(): int
    {
        return (int)$this->getData(self::VERIFY_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function getIconImage(): string
    {
        return (string)$this->getData(self::IMAGE);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return (string)$this->getData(self::TITLE);
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return (string)$this->getData(self::DESCRIPTION);
    }

    /**
     * @inheritDoc
     */
    public function getConfirmButtonLabel(): string
    {
        return (string)$this->getData(self::CONFIRM_LABEL);
    }

    /**
     * @inheritDoc
     */
    public function getCancelButtonLabel(): string
    {
        return (string)$this->getData(self::CANCEL_LABEL);
    }

    /**
     * @inheritDoc
     */
    public function getHeaderBackgroundColor(): string
    {
        return (string)$this->getData(self::TITLE_BG);
    }

    /**
     * @inheritDoc
     */
    public function getBodyBackgroundColor(): string
    {
        return (string)$this->getData(self::CONTENT_BG);
    }

    /**
     * @inheritDoc
     */
    public function getButtonColor(): string
    {
        return (string)$this->getData(self::BUTTON_COLOR);
    }

    /**
     * @inheritDoc
     */
    public function getTextColor(): string
    {
        return (string)$this->getData(self::TEXT_COLOR);
    }
}
