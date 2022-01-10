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

namespace Mageplaza\AgeVerification\Api\Data;

/**
 * Interface DesignConfigInterface
 * @package Mageplaza\AgeVerification\Api\Data
 */
interface DesignConfigInterface
{
    const VERIFY_TYPE   = 'verify_type';
    const IMAGE         = 'image';
    const TITLE         = 'title';
    const DESCRIPTION   = 'description';
    const CONFIRM_LABEL = 'confirm_label';
    const CANCEL_LABEL  = 'cancel_label';
    const TITLE_BG      = 'title_bg';
    const CONTENT_BG    = 'content_bg';
    const BUTTON_COLOR  = 'button_color';
    const TEXT_COLOR    = 'text_color';

    /**
     * @return int
     */
    public function getVerificationType(): int;

    /**
     * @return string
     */
    public function getIconImage(): string;

    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @return string
     */
    public function getDescription(): string;

    /**
     * @return string
     */
    public function getConfirmButtonLabel(): string;

    /**
     * @return string
     */
    public function getCancelButtonLabel(): string;

    /**
     * @return string
     */
    public function getHeaderBackgroundColor(): string;

    /**
     * @return string
     */
    public function getBodyBackgroundColor(): string;

    /**
     * @return string
     */
    public function getButtonColor(): string;

    /**
     * @return string
     */
    public function getTextColor(): string;
}
